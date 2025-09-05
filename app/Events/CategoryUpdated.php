<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated
{
    use Dispatchable, SerializesModels;

    public Category $newCategory;
    public Category $oldCategory;
    /**
     * Create a new event instance.
     */
    public function __construct(Category $newCategory,Category $oldCategory)
    {
        $this->newCategory = $newCategory;
        $this->oldCategory = $oldCategory;
    }
}
