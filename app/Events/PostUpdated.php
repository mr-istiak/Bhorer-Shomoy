<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostUpdated
{
    use Dispatchable, SerializesModels;
    public Post $newPost;
    public Post $oldPost;
    /**
     * Create a new event instance.
     */
    public function __construct(Post $newPost, Post $oldPost)
    {
        $this->newPost = $newPost;
        $this->oldPost = $oldPost;
    }
}
