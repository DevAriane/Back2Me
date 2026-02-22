<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Liste toutes les catégories
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Détail d'une catégorie avec ses objets (optionnel)
     */
    public function show(Category $category)
    {
        $category->load(['objets' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return response()->json($category);
    }
}
