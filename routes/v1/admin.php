<?php

use App\Http\Controllers\Api\V1\Admin\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

Route::prefix('v1/admin')->middleware('auth:sanctum')->group(function () {

    // Route::apiResource('products', ProductController::class);

    Route::get('products', [ProductController::class, 'index'])->middleware('can:admin-get-products');
    Route::post('products', [ProductController::class, 'store'])->middleware('can:admin-create-products');
    Route::get('products/{id}', [ProductController::class, 'show'])->middleware('can:admin-view-products');
    Route::put('products/{id}', [ProductController::class, 'update'])->middleware('can:admin-update-products');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->middleware('can:admin-delete-products');

    Route::get('/permissions', function () {
        $permissions = Permission::select('id', 'name')->get();

        return response()->json($permissions);
    })->middleware('can:admin-get-permissions');

    Route::get('/roles', function () {
        $roles = Role::select('id', 'name')->get();

        return response()->json($roles);
    })->middleware('can:admin-get-roles');

    Route::post('/roles', function (Request $request) {
        $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->has('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->givePermissionTo($permissions);
        }

        return response()->json($role->load('permissions'), 201);
    })->middleware('can:admin-create-roles');

    Route::post('/assign-role', function (Request $request) {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $user = \App\Models\User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        $user->assignRole($role);

        return response()->json(['message' => 'Role assigned successfully.']);
    })->middleware('can:admin-assign-roles');
});
