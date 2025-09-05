<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePositionRequest;
use App\Http\Requests\UpdatePositionRequest;
use App\Models\Position;
use App\Models\Post;
use HtmlBladeRuntime\Runtime;
use Illuminate\Support\Facades\Gate;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', Position::class);
        return response()->json(Position::with(['post' => fn($q) => $q->with(['featuredImage'])])->get()->toArray());
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePositionRequest $request, Position $position)
    {
        $data = $request->validated();
        $post = Post::with('position')->find($data['postId']);
        if($post->position) {
            $post->position()->update([
                'post_id' => null
            ]);
        };
        $position->post_id = $data['postId'];
        $position->save();
        Runtime::close();
    }

    public function destroy($post)
    {
        $post = Post::with('position')->find($post);
        if($post->position) {
            $post->position()->update([
                'post_id' => null
            ]);
        };
        Runtime::close();
    }
}
