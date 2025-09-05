<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryDeleted
{
    use Dispatchable, SerializesModels;

    public Category $category;
    public Collection $newPosts;
    public Collection $oldPosts;
    /**
     * Create a new event instance.
     */
    public function __construct(Category $category, Collection $newPosts, Collection $oldPosts)
    {
        $this->category = $category;
        $this->newPosts = $newPosts;
        $this->oldPosts = $oldPosts;
    }
}
