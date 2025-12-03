<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnduranceController extends Controller
{
    /**
     * Generate an endurance question (mix of riddles and logic)
     * 50 total questions alternating between riddle and logic
     * Returns: { question, hint, answer, explanation, type, page, totalPages }
     */
    public function generate(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $totalPages = 25;

        // Riddles (25 from RiddleController format)
        $riddles = [
            ['question' => 'What has keys but can\'t open locks?', 'options' => '', 'answer' => 'Piano', 'hint' => 'Think of a musical instrument.', 'type' => 'riddle'],
            ['question' => 'I speak without a mouth and hear without ears. What am I?', 'options' => '', 'answer' => 'Echo', 'hint' => 'I repeat what you say.', 'type' => 'riddle'],
            ['question' => 'The more you take, the more you leave behind. What am I?', 'options' => '', 'answer' => 'Footsteps', 'hint' => 'Think about walking.', 'type' => 'riddle'],
            ['question' => 'What comes once in a minute, twice in a moment, and never in one hundred years?', 'options' => '', 'answer' => 'M', 'hint' => 'Count the letters in the words.', 'type' => 'riddle'],
            ['question' => 'I have cities, but no houses. I have forests, but no trees. I have water, but no fish. What am I?', 'options' => '', 'answer' => 'Map', 'hint' => 'It\'s a representation.', 'type' => 'riddle'],
            ['question' => 'The more of this there is, the less you see. What is it?', 'options' => '', 'answer' => 'Darkness', 'hint' => 'It\'s the opposite of light.', 'type' => 'riddle'],
            ['question' => 'What has a head and a tail but no body?', 'options' => '', 'answer' => 'Coin', 'hint' => 'Currency has these.', 'type' => 'riddle'],
            ['question' => 'What is broken when you say its name?', 'options' => '', 'answer' => 'Silence', 'hint' => 'Think about quiet.', 'type' => 'riddle'],
            ['question' => 'I am taken from a mine and shut up in a wooden case. What am I?', 'options' => '', 'answer' => 'Pencil', 'hint' => 'Used for writing.', 'type' => 'riddle'],
            ['question' => 'What gets wetter the more it dries?', 'options' => '', 'answer' => 'Towel', 'hint' => 'Used in the bathroom.', 'type' => 'riddle'],
            ['question' => 'What is a room with no doors and no windows?', 'options' => '', 'answer' => 'Mushroom', 'hint' => 'A type of fungus.', 'type' => 'riddle'],
            ['question' => 'What travels the world while staying in a corner?', 'options' => '', 'answer' => 'Stamp', 'hint' => 'For envelopes.', 'type' => 'riddle'],
            ['question' => 'What has hands but cannot clap?', 'options' => '', 'answer' => 'Clock', 'hint' => 'Tells time.', 'type' => 'riddle'],
          ['question' => 'What is full of keys but cannot open any door?', 'options' => '', 'answer' => 'Piano', 'hint' => 'Musical instrument.', 'type' => 'riddle'],
          ['question' => 'I have a face and two hands, but no arms or legs. What am I?', 'options' => '', 'answer' => 'Clock', 'hint' => 'Tells time.', 'type' => 'riddle'],
          ['question' => 'What has a neck but no head?', 'options' => '', 'answer' => 'Bottle', 'hint' => 'Contains liquid.', 'type' => 'riddle'],
          ['question' => 'What can you hold without using your hands?', 'options' => '', 'answer' => 'Breath', 'hint' => 'Air in your lungs.', 'type' => 'riddle'],
          ['question' => 'The more you take, the more you have. What is it?', 'options' => '', 'answer' => 'Pictures', 'hint' => 'Photography.', 'type' => 'riddle'],
          ['question' => 'What is always coming but never arrives?', 'options' => '', 'answer' => 'Tomorrow', 'hint' => 'Next day.', 'type' => 'riddle'],
          ['question' => 'What begins with T, ends with T, and has T in it?', 'options' => '', 'answer' => 'Teapot', 'hint' => 'Beverage container.', 'type' => 'riddle'],
          ['question' => 'What has a bottom at the top?', 'options' => '', 'answer' => 'Leg', 'hint' => 'Part of body.', 'type' => 'riddle'],
          ['question' => 'What word becomes shorter when you add two letters to it?', 'options' => '', 'answer' => 'Short', 'hint' => 'Add "er".', 'type' => 'riddle'],
          ['question' => 'What can travel around the world while staying in a corner?', 'options' => '', 'answer' => 'Stamp', 'hint' => 'Postal.', 'type' => 'riddle'],
          ['question' => 'I speak all languages. What am I?', 'options' => '', 'answer' => 'Echo', 'hint' => 'Sound repetition.', 'type' => 'riddle'],
          ['question' => 'What is the only mammal that cannot jump?', 'options' => '', 'answer' => 'Elephant', 'hint' => 'Large animal.', 'type' => 'riddle'],
        ];

        // Logic Questions (25 from LogicController format)
        $logicQuestions = [
            ['question' => 'What comes next? △ □ ○ △ □ ___', 'options' => 'A) △  B) □  C) ○  D) ☆', 'answer' => 'C', 'hint' => 'Pattern repeats every 3.', 'type' => 'logic'],
            ['question' => 'Which number? 2, 4, 8, 16, ?', 'options' => 'A) 24  B) 32  C) 28  D) 20', 'answer' => 'B', 'hint' => 'Each doubles.', 'type' => 'logic'],
            ['question' => 'Roses fade, flowers fade. Roses are flowers. True?', 'options' => 'A) True  B) False', 'answer' => 'A', 'hint' => 'Follow logic chain.', 'type' => 'logic'],
            ['question' => 'What next? 1, 1, 2, 3, 5, 8, 13, ?', 'options' => 'A) 18  B) 20  C) 21  D) 19', 'answer' => 'C', 'hint' => 'Fibonacci sum.', 'type' => 'logic'],
            ['question' => '1.5 hens lay 1.5 eggs in 1.5 days. How many per hen per day?', 'options' => 'A) 1  B) 0.5  C) 1.5  D) 2', 'answer' => 'C', 'hint' => 'Proportion.', 'type' => 'logic'],
            ['question' => 'Next letter? A, C, E, G, I, ?', 'options' => 'A) J  B) K  C) L  D) M', 'answer' => 'B', 'hint' => 'Skip one.', 'type' => 'logic'],
            ['question' => 'SILENT and LISTEN are ___?', 'options' => 'A) Anagram  B) Acronym  C) Palindrome  D) Homonym', 'answer' => 'A', 'hint' => 'Same letters.', 'type' => 'logic'],
            ['question' => 'What is 15% of 200?', 'options' => 'A) 15  B) 20  C) 25  D) 30', 'answer' => 'D', 'hint' => 'Multiply by 0.15.', 'type' => 'logic'],
            ['question' => 'Which has no corners?', 'options' => 'A) Circle  B) Square  C) Triangle  D) Rectangle', 'answer' => 'A', 'hint' => 'Smooth.', 'type' => 'logic'],
            ['question' => '2 books ($5) + 3 pens ($3) = ?', 'options' => 'A) $19  B) $17  C) $21  D) $23', 'answer' => 'A', 'hint' => 'Add products.', 'type' => 'logic'],
            ['question' => 'Next? Red, Orange, Yellow, Green, Blue, ?', 'options' => 'A) Purple  B) Pink  C) Brown  D) Gray', 'answer' => 'A', 'hint' => 'Rainbow.', 'type' => 'logic'],
            ['question' => 'Monday + 10 days = ?', 'options' => 'A) Tuesday  B) Wednesday  C) Thursday  D) Friday', 'answer' => 'C', 'hint' => '1 week + 3 days.', 'type' => 'logic'],
            ['question' => 'Odd one out? 2, 4, 6, 8, 9, 10', 'options' => 'A) 2  B) 4  C) 9  D) 10', 'answer' => 'C', 'hint' => 'Only odd.', 'type' => 'logic'],
            ['question' => 'If a+b=10 and a-b=2, a=?', 'options' => 'A) 4  B) 6  C) 8  D) 12', 'answer' => 'B', 'hint' => 'Algebra.', 'type' => 'logic'],
            ['question' => 'Pattern next? Square, Circle, Triangle, Square, Circle, ?', 'options' => 'A) Square  B) Circle  C) Triangle  D) Pentagon', 'answer' => 'C', 'hint' => 'Repeats 3x.', 'type' => 'logic'],
            ['question' => '3 apples + 5 - 2 = ?', 'options' => 'A) 5  B) 6  C) 8  D) 10', 'answer' => 'B', 'hint' => 'Simple math.', 'type' => 'logic'],
            ['question' => 'Next letter? A, B, D, G, K, ?', 'options' => 'A) M  B) N  C) O  D) P', 'answer' => 'D', 'hint' => 'Gaps increase.', 'type' => 'logic'],
            ['question' => '5 workers, 10 days. 10 workers = ? days', 'options' => 'A) 2  B) 4  C) 5  D) 20', 'answer' => 'C', 'hint' => 'Inverse.', 'type' => 'logic'],
            ['question' => 'Triangle angles sum to?', 'options' => 'A) 90°  B) 180°  C) 270°  D) 360°', 'answer' => 'B', 'hint' => 'Geometry.', 'type' => 'logic'],
            ['question' => 'STAR + ___ = new word?', 'options' => 'A) light  B) fish  C) board  D) table', 'answer' => 'B', 'hint' => 'Starfish.', 'type' => 'logic'],
            ['question' => 'Square side 5cm = area?', 'options' => 'A) 10 cm²  B) 20 cm²  C) 25 cm²  D) 30 cm²', 'answer' => 'C', 'hint' => 'Side × side.', 'type' => 'logic'],
            ['question' => 'Next? 100, 90, 81, 73, 66, ?', 'options' => 'A) 55  B) 58  C) 60  D) 62', 'answer' => 'C', 'hint' => 'Diffs decrease.', 'type' => 'logic'],
            ['question' => 'Largest fraction? 1/2, 2/3, 3/4, 4/5', 'options' => 'A) 1/2  B) 2/3  C) 3/4  D) 4/5', 'answer' => 'D', 'hint' => 'Convert decimals.', 'type' => 'logic'],
            ['question' => 'Watch 3:00 + 90° clockwise = hand points?', 'options' => 'A) 12  B) 3  C) 6  D) 9', 'answer' => 'C', 'hint' => 'Rotate mentally.', 'type' => 'logic'],
            ['question' => 'Next letter? Z, X, V, T, R, ?', 'options' => 'A) Q  B) P  C) O  D) N', 'answer' => 'B', 'hint' => 'Backwards skip 1.', 'type' => 'logic'],
        ];

        // Merge into 25 total (alternate riddle/logic for variety)
        $samples = [];
        for ($i = 0; $i < 25; $i++) {
            $samples[] = $riddles[$i];
            if ($i < 25) {
                $samples[] = $logicQuestions[$i];
            }
        }

        // Return only 25 questions total
        $samples = array_slice($samples, 0, 25);

        // Deterministic selection based on page
        $index = ($page - 1) % count($samples);
        $pick = $samples[$index];

        return response()->json(['data' => [
            'question' => $pick['question'],
            'options' => $pick['options'],
            'hint' => $pick['hint'],
            'answer' => $pick['answer'],
            'type' => $pick['type'],
            'explanation' => "The correct answer is {$pick['answer']}.",
            'source' => 'local',
            'page' => $page,
            'totalPages' => $totalPages,
        ]], 200);
    }
}
