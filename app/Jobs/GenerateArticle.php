<?php

namespace App\Jobs;

use App\Enums\ArticleBoxType;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\ArticleBox;
use App\Models\Epaper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateArticle implements ShouldQueue
{
    use Queueable;

    public function __construct(public Epaper $epaper) {}
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->epaper->articles()->where('status', ArticleStatus::EMPTY->value) 
                ->with([
                    'boxes' => fn($query) => $query->where('type', ArticleBoxType::TEXT->value)
                ])->get()
                ->each(function ($article) {
                    $article->update([
                        'text'   => $article->boxes->pluck('extracted_content')->filter()->implode(' '),
                        'status' => ArticleStatus::GENERATED->value,
                    ]);
                });
    }
}
