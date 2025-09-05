<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Events\PostCreated;
use App\Events\PostDeleted;
use App\Events\PostUpdated;
use App\Http\Requests\PostRequest;
use App\Models\Position;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $posts = $user->can('viewAny', Post::class) ? Post::with('author') : $user->posts()->with('author');
        $posts = $posts->orderBy('created_at', 'desc')
            ->paginate(20);
        return Inertia::render('posts/Index', [
            'posts' => $posts,
            'canChangeStatus' => $user->can('actionStatus', Post::class)
        ]);
    }

    public function create(Request $request)
    {
        return Inertia::render('posts/Create', [
            'canCreatePost' => $request->user()->can('create', Post::class)
        ]);
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . time();
        }
        $data['author_id'] = $request->user()->id;
        if($data['status'] === PostStatus::PUBLISHED->value) {
            if($user->can('create', Post::class)) {
                $data['status'] = PostStatus::PUBLISHED->value;
                $data['published_at'] = now();
            } else $data['status'] = PostStatus::PENDING->value;
        }
        $post = Post::create($data);
        $post->categories()->sync($data['categories'] ?? []);
        PostCreated::dispatch($post);
        
        return redirect()->route('posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        Gate::authorize('update', $post);
        $post = $post->load(['author', 'featuredImage', 'categories:id']);
        $post->categories->makeHidden(['pivot']);
        return Inertia::render('posts/Edit', [
            'post' => $post,
            'canViewAnyPosition' => request()->user()->can('viewAny', Position::class)
        ]);
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        Gate::authorize('update', $post);

        $user = $request->user();
        $data = $request->validated();
        
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']) . '-' . time();
        }
        if(($post->status !== PostStatus::PUBLISHED) && ($data['status'] === PostStatus::PUBLISHED->value)) {
            if($user->can('update', [$post, true])) {
                $data['status'] = PostStatus::PUBLISHED->value;
                $data['published_at'] = now();
            } else $data['status'] = PostStatus::PENDING->value;
        }
        $oldPost = Post::with(['categories'])->find($post->id); 
        $post->update($data);
        $post->categories()->sync($data['categories'] ?? []);

        PostUpdated::dispatch($post, $oldPost);

        return redirect()->back()->with('success', 'Post updated.');
    }

    public function updateStatus(Request $request, Post $post): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:approve,reject'
        ]);
        if(($post->status === PostStatus::PUBLISHED) || ($post->status === PostStatus::DRAFT)) return redirect()->back()->with('error', 'Post status already updated.');
        $oldPost = Post::with(['categories'])->find($post->id); 
        $post->update([
            'status' => $data['status'] === 'approve' ? PostStatus::PUBLISHED->value : PostStatus::REJECTED->value
        ]);
        PostUpdated::dispatch($post, $oldPost);
        return redirect()->back()->with('success', 'Post ' . $data['status'] . 'ed successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();
        PostDeleted::dispatch($post);
        return redirect()->route('posts.index')->with('success', 'Post deleted.');
    }
}
