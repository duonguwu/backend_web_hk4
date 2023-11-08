<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Phương thức để lấy tất cả các categories
    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }

    // Phương thức để lấy một category theo ID
    public function show($id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json(['category' => $category], 200);
        } else {
            return response()->json(['error' => 'Category not found'], 404);
        }
    }
}
