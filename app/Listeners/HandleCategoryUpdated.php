<?php

namespace App\Listeners;

use App\Events\CategoryUpdated;
use HtmlBladeRuntime\Runtime;

class HandleCategoryUpdated
{
    /**
     * Handle the event.
     */
    public function handle(CategoryUpdated $event): void
    {
        $newCategory = $event->newCategory;
        $oldCategory = $event->oldCategory;
        Runtime::update($newCategory, $oldCategory);
        //Update all posts under this category
        Runtime::updateAll($newCategory->posts, $oldCategory->posts);
        Runtime::close();
    }
}
