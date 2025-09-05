<?php

namespace App\Listeners;

use App\Enums\PostStatus;
use App\Events\PostDeleted;
use HtmlBladeRuntime\Runtime;

class HandlePostDeleted
{
    /**
     * Handle the event.
     */
    public function handle(PostDeleted $event): void
    {
        $post = $event->post;
        if($post->status !== PostStatus::PUBLISHED) return;
        Runtime::delete($post);
        Runtime::updateAll($post->categories, $post->categories);
        Runtime::close();
    }
}
