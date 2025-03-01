<?php

namespace App\Http\Services;

class POWService
{
    public function __construct(private int $puzzleDifficuly, private int $puzzleCount) {}

    public function getPOWCaptchaData()
    {
        $puzzles = [];
        $answers = [];
        for ($i = 0; $i < $this->puzzleCount; $i++) {
            $salt = str()->random(32);
            $answer = random_int(1, pow(10, $this->puzzleDifficuly));
            $hash = hash('sha256', $salt.$answer);
            $puzzles[] = [$salt, $hash];
            $answers[] = $answer;
        }

        return [
            'puzzles' => $puzzles,
            'answers' => $answers,
        ];
    }
}
