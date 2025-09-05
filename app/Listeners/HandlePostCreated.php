<?php

namespace App\Listeners;

use App\Enums\PostStatus;
use App\Events\PostCreated;
use HtmlBladeRuntime\Runtime;

class HandlePostCreated
{
    /**
     * Handle the event.
     */
    public function handle(PostCreated $event): void
    {
        $post = $event->post;
        if($post->status !== PostStatus::PUBLISHED) return;
        Runtime::create($post);
        Runtime::updateAll($post->categories, $post->categories);
        Runtime::close();
    }
}
