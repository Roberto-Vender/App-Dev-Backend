<?php

namespace App\Http\Controllers;

use App\Models\Riddle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class RiddleController extends Controller
{
    /**
     * Return a list of riddles.
     */
    public function index()
    {
        $riddles = Riddle::orderBy('id', 'asc')->get(['id', 'question', 'hint', 'source']);
        return response()->json(['data' => $riddles], 200);
    }

    /**
     * Store a new riddle.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'answer' => 'nullable|string',
            'hint' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        $riddle = Riddle::create($data);

        return response()->json(['data' => $riddle], 201);
    }

    /**
     * Show a single riddle.
     */
    public function show($id)
    {
        $riddle = Riddle::find($id);
        if (! $riddle) {
            return response()->json(['message' => 'Riddle not found'], 404);
        }
        return response()->json(['data' => $riddle], 200);
    }

    /**
     * Update a riddle.
     */
    public function update(Request $request, $id)
    {
        $riddle = Riddle::find($id);
        if (! $riddle) {
            return response()->json(['message' => 'Riddle not found'], 404);
        }

        $data = $request->validate([
            'question' => 'sometimes|required|string',
            'answer' => 'nullable|string',
            'hint' => 'nullable|string',
            'source' => 'nullable|string',
        ]);

        $riddle->update($data);

        return response()->json(['data' => $riddle], 200);
    }

    /**
     * Delete a riddle.
     */
    public function destroy($id)
    {
        $riddle = Riddle::find($id);
        if (! $riddle) {
            return response()->json(['message' => 'Riddle not found'], 404);
        }

        $riddle->delete();
        return response()->json(['message' => 'Riddle deleted'], 200);
    }

    /**
     * Generate a riddle using OpenAI (if configured) or fallback to a local generator.
     * Returns: { question, hint, answer (optional), source, page, totalPages }
     */
    public function generate(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $totalPages = 25;

        // If OPENAI_API_KEY is set, try to use OpenAI Chat Completions
        $openaiKey = env('OPENAI_API_KEY');

        if ($openaiKey) {
            try {
                $prompt = "Generate a single short riddle and provide a brief Hint-by-Hint working hint (not the full answer). Respond in JSON with keys: question, hint, answer. Example: {\"question\":\"I have ...\",\"hint\":\"Think about ...\",\"answer\":\"...\" }";

                $resp = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                    'Content-Type' => 'application/json',
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant that outputs JSON only.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 400,
                ]);

                if ($resp->successful()) {
                    $body = $resp->json();
                    $text = data_get($body, 'choices.0.message.content', '');

                    // Try to extract JSON from the model output
                    $json = null;
                    $decoded = json_decode($text, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $json = $decoded;
                    } else {
                        if (preg_match('/\{[\s\S]*\}/', $text, $m)) {
                            $maybe = $m[0];
                            $decoded2 = json_decode($maybe, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded2)) {
                                $json = $decoded2;
                            }
                        }
                    }

                    if ($json) {
                        return response()->json(['data' => [
                            'question' => $json['question'] ?? ($json['q'] ?? null),
                            'hint' => $json['hint'] ?? ($json['h'] ?? null),
                            'answer' => $json['answer'] ?? ($json['a'] ?? null),
                            'source' => 'chatgpt',
                            'page' => $page,
                            'totalPages' => $totalPages,
                        ]], 200);
                    }
                }
            } catch (\Exception $e) {
                // fallthrough to local generator
            }
        }

        // Fallback local generator: pick a random sample and produce a Hint-by-Hint working hint
        $samples = [
            ['question' => "What has keys but can't open locks?", 'answer' => 'Piano'],
            ['question' => "I speak without a mouth and hear without ears. I have nobody, but I come alive with wind. What am I?", 'answer' => 'Echo'],
            ['question' => "The more of this there is, the less you see. What is it?", 'answer' => 'Darkness'],
            ['question' => "What can travel around the world while staying in a corner?", 'answer' => 'Stamp'],
            ['question' => "What has hands but can't clap?", 'answer' => 'Clock'],
            ['question' => "What gets wetter as it dries?", 'answer' => 'Towel'],
            ['question' => "I have cities, but no houses. I have mountains, but no trees. I have water, but no fish. What am I?", 'answer' => 'Map'],
            ['question' => "What has a head and a tail but no body?", 'answer' => 'Coin'],
            ['question' => "What has to be broken before you can use it?", 'answer' => 'Egg'],
            ['question' => "I'm tall when I'm young and short when I'm old. What am I?", 'answer' => 'Candle'],
            ['question' => "What has many teeth but cannot bite?", 'answer' => 'Comb'],
            ['question' => "What goes up but never comes down?", 'answer' => 'Age'],
            ['question' => "What begins with T, ends with T, and has T in it?", 'answer' => 'Teapot'],
            ['question' => "What runs but never walks, has a mouth but never talks?", 'answer' => 'River'],
            ['question' => "What can you catch but not throw?", 'answer' => 'Cold'],
            ['question' => "The more you take, the more you leave behind. What are they?", 'answer' => 'FootHints'],
            ['question' => "I have branches, but no fruit, trunk or leaves. What am I?", 'answer' => 'Bank'],
            ['question' => "What has one eye but cannot see?", 'answer' => 'Needle'],
            ['question' => "I fly without wings. I cry without eyes. Wherever I go, darkness follows me. What am I?", 'answer' => 'Cloud'],
            ['question' => "What invention lets you look right through a wall?", 'answer' => 'Window'],
            ['question' => "What can be cracked, made, told, and played?", 'answer' => 'Joke'],
            ['question' => "What has a neck but no head?", 'answer' => 'Bottle'],
            ['question' => "What is full of holes but still holds water?", 'answer' => 'Sponge'],
            ['question' => "What gets bigger the more you take away?", 'answer' => 'Hole'],
            ['question' => "What goes all around the house but never moves?", 'answer' => 'Fence'],
        ];

        $pick = $samples[array_rand($samples)];

        $answer = $pick['answer'];
        $hint = $this->buildWorkingHint($pick['question'], $answer);

        return response()->json(['data' => [
            'question' => $pick['question'],
            'hint' => $hint,
            'answer' => $answer,
            'source' => 'local',
            'page' => $page,
            'totalPages' => $totalPages,
        ]], 200);
    }

    /**
     * Build a concise Hint-by-Hint working hint for the given riddle.
     * This intentionally avoids revealing the answer directly, but guides the solver.
     */
    protected function buildWorkingHint($question, $answer)
    {
        $q = Str::lower($question);

        if (Str::contains($q, 'keys')) {
    return " Consider objects that have 'keys' but are not for locks.";
}
if (Str::contains($q, 'speak without a mouth')) {
    return " Think of something that repeats sound.";
}
if (Str::contains($q, 'less you see')) {
    return " Think about what blocks light.";
}
if (Str::contains($q, 'corner')) {
    return " Think of something small that sits in the corner.";
}
if (Str::contains($q, 'hands but')) {
    return " These hands move but do not clap.";
}
if (Str::contains($q, 'wetter as it dries')) {
    return " It absorbs water while drying you.";
}
if (Str::contains($q, 'cities, but no houses')) {
    return " It contains cities, rivers, and mountains.";
}
if (Str::contains($q, 'head and a tail')) {
    return " It has 'heads' and 'tails'.";
}
if (Str::contains($q, 'broken before')) {
    return " You break it to cook or eat.";
}
if (Str::contains($q, 'short when i\'m old')) {
    return " It gets shorter as time passes.";
}
if (Str::contains($q, 'many teeth')) {
    return " It is used on hair.";
}
if (Str::contains($q, 'never comes down')) {
    return " You can never reduce it.";
}
if (Str::contains($q, 'begins with t')) {
    return " It holds something hot.";
}
if (Str::contains($q, 'runs but never')) {
    return " It has a 'mouth' but doesn’t speak.";
}
if (Str::contains($q, 'catch but not')) {
    return " You can 'catch' it but not hold it.";
}
if (Str::contains($q, 'leave behind')) {
    return " The more you walk, the more marks you make.";
}
if (Str::contains($q, 'branches, but no fruit')) {
    return " It deals with money, not trees.";
}
if (Str::contains($q, 'one eye')) {
    return " It has a tiny 'eye' for thread.";
}
if (Str::contains($q, 'fly without wings')) {
    return " It floats above you.";
}
if (Str::contains($q, 'wall')) {
    return " It is part of a house and transparent.";
}
if (Str::contains($q, 'cracked, made, told')) {
    return " People share it to make others laugh.";
}
if (Str::contains($q, 'neck but no head')) {
    return " It holds liquids.";
}
if (Str::contains($q, 'full of holes')) {
    return " It absorbs water easily.";
}
if (Str::contains($q, 'bigger the more you take')) {
    return " The more you remove, the larger it becomes.";
}
if (Str::contains($q, 'around the house')) {
    return " It is built for protection or border.";
}

return " Read the riddle carefully and think of common objects.";
    }
}