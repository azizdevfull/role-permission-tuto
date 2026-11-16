<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json([
            'message' => 'Product index',
        ]);
    }

    public function store()
    {
        return response()->json([
            'message' => 'Product created',
        ]);
    }

    public function show()
    {
        return response()->json([
            'message' => 'Product show',
        ]);
    }

    public function update()
    {
        return response()->json([
            'message' => 'Product updated',
        ]);
    }

    public function destroy()
    {
        return response()->json([
            'message' => 'Product deleted',
        ]);
    }
}
