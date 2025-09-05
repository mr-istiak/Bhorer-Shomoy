<?php

use App\Enums\ArticleBoxType;
use App\Enums\ArticleStatus;
use App\Enums\RasterDPI;
use App\Http\Middleware\VerifyApiRoute;
use App\Models\Article;
use App\Models\Epaper;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->middleware([VerifyApiRoute::class])->group(function () {
    Route::get('/epaper', function () {
        $dpi = RasterDPI::LOW->value;
        $epaper =  Epaper::with([
            'pages' => fn($query) => $query->with('articleBoxes:article_id,bounding_box,epage_id,epaper_id,id,type')->select('id', 'epaper_id', 'page_number', 'image_path')
        ])->latest()->first(['title', 'id', 'created_at']);
        $epaper->pages = $epaper->pages->map(function($page) use($dpi) {
           $page->image_path = $page->image_path[$dpi]; 
        });
        return response()->json(compact('epaper', 'dpi'));
    })->name('api.epaper.index');

    Route::get('/epaper/article/{article}', function (Request $request, Article $article) {
        if($article->status !== ArticleStatus::GENERATED) return response()->json(null, 404);
        $type = $request->input('type', 'text');
        if(array_search($type, ['text', 'image']) === false) return response()->json(null, 404);

        $imgBoxs = $article->boxes()->where('type', ($type === 'text') ? ArticleBoxType::IMAGE->value : ArticleBoxType::TEXT->value)->get(['article_id', 'rasted_image', 'type'])->map(function($box) {
            return '<img src="'.url($box->rasted_image).'" style="max-width: 100%;"/>';
        })->implode('');
        $content = $imgBoxs . (($type === 'text') ? (' ' . $article->text) : '');

        return response()->json([
            'article' => [ 'content' => $content ],
            'next' => $article->next(['id'])?->id,
            'previous' => $article->previous(['id'])?->id
        ]);
    })->name('api.epaper.article.show');

    Route::get('/search', function (Request $request) {
        $search = $request->input('search');
        $posts = Post::with(['categories:slug,id,name,bangla_name', 'featuredImage:id,path,alt,title'])
            ->where('status', 'published')
            ->where('title', 'like', '%'.$search.'%')
            ->get(['id', 'title', 'slug', 'content', 'meta_description', 'published_at', 'featured_image'])
            ->map(function($post) {
                $category = $post->categories->first();
               $post = [
                    ...$post->toArray(),
                    'slug' => ($category ? $category->slug . '/' : '') . $post->slug,
                    'meta_description' => $post->meta_description ?? Str::limit(strip_tags($post->content), 200),
                    'published_at' => $post->published_at->diffForHumans()
                ];
                unset($post['content']);
                return $post;
            });
        return response()->json(['posts' => $posts]);
    })->name('api.search');
});
