<?php

namespace App\Listeners;

use App\Enums\PostStatus;
use App\Events\PostUpdated;
use HtmlBladeRuntime\Runtime;

class HandlePostUpdated
{
    /**
     * Handle the event.
     */
    public function handle(PostUpdated $event): void
    {
        $newPost = $event->newPost;
        $oldPost = $event->oldPost;
        if($oldPost->status === PostStatus::PUBLISHED) {
            Runtime::delete($oldPost);
            Runtime::updateAll($oldPost->categories, $oldPost->categories);
        }
        if($newPost->status === PostStatus::PUBLISHED) {
            $newPost->load(['categories']);
            Runtime::create($newPost);
            Runtime::updateAll($newPost->categories, $newPost->categories);
        }
        Runtime::close();
    }
}
