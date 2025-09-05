<?php

namespace App\Listeners;

use App\Events\CategoryCreated;
use HtmlBladeRuntime\Runtime;

class HandleCategoryCreated
{
    /**
     * Handle the event.
     */
    public function handle(CategoryCreated $event): void
    {
        $category = $event->category;
        Runtime::create($category);
        Runtime::close();
    }
}
