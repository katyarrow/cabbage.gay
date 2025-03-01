<?php

namespace App\Http\Controllers;

use App\Http\Resources\PowCaptchaChallengeResource;
use App\Http\Services\POWService;
use App\Models\PowCaptcha;
use App\Rules\VerifiesCaptcha;
use Illuminate\Http\Request;

class CaptchaController extends Controller
{
    public function index(): PowCaptchaChallengeResource
    {
        $difficulty = intval(config('captcha.difficulty'));
        $length = intval(config('captcha.puzzle_count'));
        $pow = new POWService($difficulty, $length);
        $captcha = $pow->getPOWCaptchaData();
        $powCaptcha = new PowCaptcha;
        $powCaptcha->puzzles_json = json_encode($captcha['puzzles']);
        $powCaptcha->answers_json = json_encode($captcha['answers']);
        $powCaptcha->difficulty = $difficulty;
        $powCaptcha->length = $length;
        $powCaptcha->save();

        return new PowCaptchaChallengeResource($powCaptcha);
    }

    public function verify(PowCaptcha $captcha, Request $request)
    {
        $request->validate([
            'captcha' => [new VerifiesCaptcha(false)],
        ]);
        $captcha->solved_at = now();
        $captcha->solved_token = str()->random(64);
        $captcha->save();

        return response()->json(['success' => true, 'token' => $captcha->solved_token]);
    }
}
