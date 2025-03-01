<?php

return [
    'difficulty' => env('POW_CAPTCHA_DIFFICULTY', 5),
    'puzzle_count' => env('POW_CAPTCHA_PUZZLE_COUNT', 10),

    /**
     * How long in seconds a captcha can exist from its creation.
     */
    'max_captcha_lifetime' => env('POW_CAPTCHA_MAX_LIFETIME', 300),
];
