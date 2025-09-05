<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Events\CategoryCreated;
use App\Events\CategoryDeleted;
use App\Events\CategoryUpdated;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if((bool) $request->input('select')) return response()->json(Category::all('id', 'name', 'bangla_name'));
        return Inertia::render('categories/Index', [
            'categories' => Category::orderBy('created_at', 'desc')->paginate(20),
            'can' => [
                'create' => $request->user()->can('create', Category::class),
                'delete' => $request->user()->can('delete', Category::class),
                'update' => $request->user()->can('update', Category::class),
            ]
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();
        $category = Category::create($data);

        CategoryCreated::dispatch($category);
        return redirect()->back()->with('success', 'Category created.');
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $data = $request->validated();
        
        $oldCategory = Category::with([
            'posts' => fn($query) => $query->with(['categories'])
            ->where('status', PostStatus::PUBLISHED->value)
            ->orderBy('published_at', 'desc')
        ])->find($category->id);
        $category->update($data);
        $category->load([
            'posts' => fn($query) => $query->with(['categories'])
            ->where('status', PostStatus::PUBLISHED->value)
            ->orderBy('published_at', 'desc')
        ]);
        CategoryUpdated::dispatch($category, $oldCategory);
        return redirect()->back()->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        Gate::authorize('delete', Category::class);
        
        $deletedCategory = $category->replicate();
        // capture posts + categories BEFORE delete
        $oldPosts = $category->posts()
            ->where('status', PostStatus::PUBLISHED->value)
            ->with('categories')
            ->get();
        // delete category (pivot cleanup happens via cascade or observer)
        $category->delete();
        // reload the same posts to see their NEW categories after detach
        $newPosts = Post::whereIn('id', $oldPosts->pluck('id'))
            ->where('status', PostStatus::PUBLISHED->value)
            ->with('categories')
            ->get();
        CategoryDeleted::dispatch($deletedCategory, $newPosts, $oldPosts);

        return redirect()->back()->with('success', 'Category deleted.');
    }
}
