<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return User::with(['roles.permissions'])->find(auth()->id());
})->middleware('auth:sanctum');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
        ]);
    }

    return response()->json([
        'message' => 'Invalid credentials',
    ], 401);
});

require_once __DIR__ . '/v1/admin.php';
