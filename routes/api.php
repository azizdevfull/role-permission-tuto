<?php

use App\Http\Resources\NotificationResource;
use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use App\Notifications\WelcomeNotification;
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

Route::post('/register', function (Request $request) {
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
    ]);

    $user = User::create([
        'name' => $validatedData['name'],
        'email' => $validatedData['email'],
        'password' => bcrypt($validatedData['password']),
    ]);
    $user->notify(new WelcomeNotification($user));
    $admin = User::where('email', 'admin@gm.com')->first();
    if ($admin) {
        $admin->notify(new NewUserRegisteredNotification($user));
    }
    $token = $user->createToken('api-token')->plainTextToken;



    return response()->json([
        'message' => 'User registered successfully',
        'user' => $user,
        'token' => $token,
    ], 201);
});


Route::get('/notifications', function (Request $request) {
    $is_read = $request->boolean('is_read');
    $user = auth()->user();
    $notifications = $user->notifications;

    return response()->json([
        'notifications' => NotificationResource::collection($notifications),
    ]);
})->middleware('auth:sanctum');
Route::get('/unread-notifications', function (Request $request) {
    $is_read = $request->boolean('is_read');
    $user = auth()->user();
    $notifications = $user->unreadNotifications()->get();

    return response()->json([
        'notifications' => NotificationResource::collection($notifications),
    ]);
})->middleware('auth:sanctum');
Route::get('/read-notifications', function (Request $request) {
    $is_read = $request->boolean('is_read');
    $user = auth()->user();
    $notifications = $user->readNotifications()->get();

    return response()->json([
        'notifications' => NotificationResource::collection($notifications),
    ]);
})->middleware('auth:sanctum');

Route::post('/notifications/{notification}/mark-as-read', function (Request $request, $notificationId) {
    $user = auth()->user();
    $notification = $user->notifications()->find($notificationId);
    if (!$notification) {
        return response()->json([
            'message' => 'Notification not found',
        ], 404);
    }

    $notification->markAsRead();

    return response()->json([
        'message' => 'Notification marked as read',
    ]);
})->middleware('auth:sanctum');

require_once __DIR__ . '/v1/admin.php';
