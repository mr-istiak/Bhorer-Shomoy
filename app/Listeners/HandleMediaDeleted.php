<?php

namespace App\Listeners;

use App\Enums\PostStatus;
use App\Events\MediaDeleted;
use HtmlBladeRuntime\Runtime;
use Whoops\Run;

class HandleMediaDeleted
{
    /**
     * Handle the event.
     */
    public function handle(MediaDeleted $event): void
    {
        $media = $event->media;
        if(!$media->post) return;
        if($media->post->status !== PostStatus::PUBLISHED) return;
        Runtime::update($media->post, $media->post);
        $post = $media->post;
        $post->load(['categories']);
        Runtime::updateAll($post->categories, $post->categories);
        Runtime::close();
    }
}
