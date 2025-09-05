<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ArticleType;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\EpaperPage;
use App\Models\PaperPage;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    // Store a headline or full article bounding box
    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:epaper_pages,id',
            'type' => 'required|in:headline,full',
            'coords' => 'required|array', // {x, y, w, h} percentages
            'extracted_text' => 'nullable|string',
        ]);

        $article = Article::create([
            'page_id' => $request->page_id,
            'epaper_id' => EpaperPage::find($request->page_id)->epaper_id,
            'type' => $request->type,
            'coords' => $request->coords,
            'extracted_text' => $request->extracted_text,
        ]);

        return back()->with('success', 'Article region saved.');
    }

    // Link headline → full article
    public function link(Article $headline, Article $fullArticle)
    {
        if ($headline->type !== ArticleType::HEADLINE || $fullArticle->type !== ArticleType::FULL) {
            return back()->withErrors('Invalid article types for linking.');
        }

        $headline->article_id = $fullArticle->id;
        $headline->save();

        return back()->with('success', 'Headline linked to full article.');
    }

    // Show a page for annotation
    public function showPage(EpaperPage $page)
    {
        $page->load('articles');
        return inertia('Epaper/Admin/Annotator', [
            'page' => $page,
        ]);
    }
}
