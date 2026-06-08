<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Second layer of authorization - middleware already checked in routes
        // This is redundant protection in case route protection is bypassed
        $this->middleware('permission:view kategori')->only(['index']);
        $this->middleware('permission:create kategori')->only(['store']);
        $this->middleware('permission:edit kategori')->only(['update']);
        $this->middleware('permission:delete kategori')->only(['destroy']);
    }

    public function index(): \Illuminate\View\View
    {
        $categories = Category::all();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'slug' => 'nullable|string|max:255|unique:categories',
            'description' => 'nullable|string',
            'sla_hours' => 'required|integer|min:1|max:168',
        ]);

        Category::create($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kategori berhasil ditambahkan']);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan');
    }

    public function update(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
            'slug' => 'nullable|string|max:255|unique:categories,slug,'.$id,
            'description' => 'nullable|string',
            'sla_hours' => 'required|integer|min:1|max:168',
        ]);

        $category->update($validated);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kategori berhasil diperbarui']);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui');
    }

    public function destroy(Request $request, int $id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Kategori berhasil dihapus']);
        }

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus');
    }
}