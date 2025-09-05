<?php

namespace App\Listeners;

use App\Enums\PostStatus;
use App\Events\CategoryDeleted;
use HtmlBladeRuntime\Runtime;
use Illuminate\Support\Facades\Log;

class HandleCategoryDeleted
{
    /**
     * Handle the event.
     */
    public function handle(CategoryDeleted $event): void
    {
        $category = $event->category;
        $newPosts = $event->newPosts;
        $oldPosts = $event->oldPosts;
        Runtime::delete($category);
        Runtime::updateAll($newPosts, $oldPosts);
        Runtime::close();
    }
}
