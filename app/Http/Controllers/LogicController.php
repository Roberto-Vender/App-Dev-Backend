<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogicController extends Controller
{
    /**
     * Generate a logic question (sequence, pattern, etc.)
     * Returns: { question, hint, answer, explanation, page, totalPages }
     */
    public function generate(Request $request)
    {
        $page = (int) $request->query('page', 1);
        $totalPages = 25;

        // Local logic questions with hints and explanations
        $samples = [
            ['question' => 'What comes next in the sequence? △ □ ○ △ □ ___', 'options' => 'A) △  B) □  C) ○  D) ☆', 'answer' => 'C', 'hint' => 'Look at the pattern: it repeats three shapes.'],
            ['question' => 'Which number should replace the question mark? 2, 4, 8, 16, ?', 'options' => 'A) 24  B) 32  C) 28  D) 20', 'answer' => 'B', 'hint' => 'Each number is double the previous one.'],
            ['question' => 'If all roses are flowers, and all flowers fade, then all roses fade. This is: True or False?', 'options' => 'A) True  B) False', 'answer' => 'A', 'hint' => 'Follow the logical chain carefully.'],
            ['question' => 'What comes next? 1, 1, 2, 3, 5, 8, 13, ?', 'options' => 'A) 18  B) 20  C) 21  D) 19', 'answer' => 'C', 'hint' => 'Each number is the sum of the previous two (Fibonacci).'],
            ['question' => 'If a hen and a half lays an egg and a half in a day and a half, how many eggs does one hen lay in one day?', 'options' => 'A) 1  B) 0.5  C) 1.5  D) 2', 'answer' => 'C', 'hint' => 'Use proportional reasoning.'],
            ['question' => 'What is the next letter in the series? A, C, E, G, I, ?', 'options' => 'A) J  B) K  C) L  D) M', 'answer' => 'B', 'hint' => 'Skip one letter each time.'],
            ['question' => 'If you rearrange the letters in "SILENT" and "LISTEN", you get the same word. What is this called?', 'options' => 'A) Anagram  B) Acronym  C) Palindrome  D) Homonym', 'answer' => 'A', 'hint' => 'Words with the same letters in different order.'],
            ['question' => 'What is 15% of 200?', 'options' => 'A) 15  B) 20  C) 25  D) 30', 'answer' => 'D', 'hint' => 'Multiply 200 by 0.15.'],
            ['question' => 'Which shape has no corners? Circle, Square, Triangle, or Rectangle?', 'options' => 'A) Circle  B) Square  C) Triangle  D) Rectangle', 'answer' => 'A', 'hint' => 'A corner is a point where two lines meet.'],
            ['question' => 'If a book costs $5 and a pen costs $3, what is the total cost of 2 books and 3 pens?', 'options' => 'A) $19  B) $17  C) $21  D) $23', 'answer' => 'A', 'hint' => '(2 × 5) + (3 × 3) = ?'],
            ['question' => 'What comes next? Red, Orange, Yellow, Green, Blue, ?', 'options' => 'A) Purple  B) Pink  C) Brown  D) Gray', 'answer' => 'A', 'hint' => 'Think of the rainbow spectrum.'],
            ['question' => 'If today is Monday, what day will it be in 10 days?', 'options' => 'A) Tuesday  B) Wednesday  C) Thursday  D) Friday', 'answer' => 'C', 'hint' => '10 days = 1 week + 3 days.'],
            ['question' => 'Which number is the odd one out? 2, 4, 6, 8, 9, 10', 'options' => 'A) 2  B) 4  C) 9  D) 10', 'answer' => 'C', 'hint' => 'Five of these are even numbers.'],
            ['question' => 'If a + b = 10 and a - b = 2, what is a?', 'options' => 'A) 4  B) 6  C) 8  D) 12', 'answer' => 'B', 'hint' => 'Solve using algebra: add the equations.'],
            ['question' => 'What is the next shape in the pattern? Square, Circle, Triangle, Square, Circle, ?', 'options' => 'A) Square  B) Circle  C) Triangle  D) Pentagon', 'answer' => 'C', 'hint' => 'The pattern repeats every three shapes.'],
            ['question' => 'If you have 3 apples and someone gives you 5 more, but you eat 2, how many do you have?', 'options' => 'A) 5  B) 6  C) 8  D) 10', 'answer' => 'B', 'hint' => '3 + 5 - 2 = ?'],
            ['question' => 'What letter comes next? A, B, D, G, K, ?', 'options' => 'A) M  B) N  C) O  D) P', 'answer' => 'D', 'hint' => 'The gap increases: +1, +2, +3, +4, +5.'],
            ['question' => 'If it takes 5 workers 10 days to build a house, how many days for 10 workers?', 'options' => 'A) 2  B) 4  C) 5  D) 20', 'answer' => 'C', 'hint' => 'Inverse proportion: more workers = less time.'],
            ['question' => 'What is the sum of all angles in a triangle?', 'options' => 'A) 90°  B) 180°  C) 270°  D) 360°', 'answer' => 'B', 'hint' => 'A fundamental property of triangles.'],
            ['question' => 'What word can follow "STAR" to make a new word? Star ___', 'options' => 'A) light  B) fish  C) board  D) table', 'answer' => 'B', 'hint' => 'An underwater creature.'],
            ['question' => 'If a square has a side of 5 cm, what is its area?', 'options' => 'A) 10 cm²  B) 20 cm²  C) 25 cm²  D) 30 cm²', 'answer' => 'C', 'hint' => 'Area = side × side.'],
            ['question' => 'What is the next number? 100, 90, 81, 73, 66, ?', 'options' => 'A) 55  B) 58  C) 60  D) 62', 'answer' => 'C', 'hint' => 'The difference decreases: -10, -9, -8, -7, -6.'],
            ['question' => 'Which is the largest fraction? 1/2, 2/3, 3/4, or 4/5?', 'options' => 'A) 1/2  B) 2/3  C) 3/4  D) 4/5', 'answer' => 'D', 'hint' => 'Convert to decimals to compare.'],
            ['question' => 'If a watch shows 3:00, and you turn it 90 degrees clockwise, what will the hour hand point to?', 'options' => 'A) 12  B) 3  C) 6  D) 9', 'answer' => 'C', 'hint' => 'Imagine rotating the watch.'],
            ['question' => 'What is the next letter? Z, X, V, T, R, ?', 'options' => 'A) Q  B) P  C) O  D) N', 'answer' => 'B', 'hint' => 'Move backwards through the alphabet, skipping one each time.'],
        ];

        // Deterministic selection based on page
        $index = ($page - 1) % count($samples);
        $pick = $samples[$index];

        return response()->json(['data' => [
            'question' => $pick['question'],
            'options' => $pick['options'],
            'hint' => $pick['hint'],
            'answer' => $pick['answer'],
            'explanation' => "The correct answer is {$pick['answer']}.",
            'source' => 'local',
            'page' => $page,
            'totalPages' => $totalPages,
        ]], 200);
    }
}
