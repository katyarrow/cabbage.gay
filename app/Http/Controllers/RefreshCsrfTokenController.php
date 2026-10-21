<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RefreshCsrfTokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->session()->regenerateToken();

        return response()->json();
    }
}
