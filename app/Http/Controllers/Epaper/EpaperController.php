<?php

namespace App\Http\Controllers\Epaper;

use App\Enums\ArticleBoxType;
use App\Enums\RasterDPI;
use App\Http\Controllers\Controller;
use App\Jobs\GenerateArticle;
use App\Jobs\GenerateArticleBoxesImage;
use App\Jobs\RasterEpaperPages;
use App\Models\Article;
use App\Models\ArticleBox;
use App\Models\Epaper;
use App\Models\EpaperPage;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class EpaperController extends Controller
{
    public function index() 
    {
        Gate::authorize('viewAny', Epaper::class);

        return Inertia::render('epaper/Index', [
            'epapers' => Epaper::latest()->paginate(20, ['id', 'title', 'published_at', 'created_at']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        Gate::authorize('create', Epaper::class);

        $request->validate([
            'title' => 'required|string',
            'pdf' => 'required|file|mimes:pdf|max:20480', // 20MB max
        ]);
        $path = $request->file('pdf')->store('epapers', 'public');

        $epaper = Epaper::create([
            'title' => $request->title,
            'pdf_path' => $path,
        ]);

        RasterEpaperPages::dispatchSync($epaper);

        return redirect()->back()->with('success', 'Epaper uploaded.');
    }

    public function generate(Epaper $epaper) {
        $chunkedEpages = $epaper->pages()->whereRaw("JSON_EXTRACT(image_path, '$.\"".RasterDPI::HIGH->value."\"') IS NULL")->get()->chunk(2);
        $jobs = [];
        $chunkedEpages->each(function($chunk) use (&$jobs) {
            $jobs[] = new RasterEpaperPages($chunk, RasterDPI::HIGH, 100, true);
        });
        $epaper->pages()->with([
            'articleBoxes' => fn($query) => $query->whereNull('rasted_image')
        ])->get()->chunk(2)->each(function($chunk) use (&$jobs, &$epaper) {
            $jobs[] = new GenerateArticleBoxesImage($epaper, $chunk);
        });
        $jobs = [
            ...$jobs,
            new GenerateArticle($epaper) // 0.2s
        ];
        Bus::chain($jobs)->dispatch();
        return back()->with('success', 'Epaper is under generation.');
    }

    public function edit($epaper) {
        Gate::authorize('update', Epaper::class);
        $epaper = Epaper::with(['pages:id,epaper_id,page_number,image_path'])->find($epaper, ['id', 'title']);
        if(!$epaper) return abort(404);
        $epaper->setRelation('pages', $epaper->pages->map(function($page) {
           if (is_array($page->image_path) && array_key_exists(RasterDPI::LOW->value, $page->image_path)) {
               $page->image_path = $page->image_path[RasterDPI::LOW->value];
           } else {
               $page->image_path = $page->image_path ?? null;
           }
           return $page;
        }));
        return Inertia::render('epaper/Edit', [
            'epaper' => $epaper,
        ]);
    }

    public function showPage($epage)
    {
        return Inertia::render('epaper/Epage/Show', [
            'epage' => EpaperPage::with([
                'epaper:id,pdf_path',
                'articleBoxes' => fn($query) => $query->where('type', ArticleBoxType::TEXT->value)->select(['id','epage_id','type','bounding_box'])
            ])->find($epage, ['id', 'epaper_id', 'page_number']),
        ]);
    }

    public function storeArticles(Request $request, EpaperPage $epage) {
        $data = $request->validate([
            'article_boxes' => ['required', 'array', function (string $attribute, mixed $value, Closure $fail) {
                if(count($value) < 1) return $fail('Please select at least one article.');
                foreach($value as $articleBox) {
                    if(!(isset($articleBox['type']) && $articleBox['type'] &&
                        isset($articleBox['bounding_box']) && 
                        isset($articleBox['bounding_box']['x']) &&
                        isset($articleBox['bounding_box']['y'] )&&
                        isset($articleBox['bounding_box']['width']) &&
                        isset($articleBox['bounding_box']['height']) &&
                        is_numeric($articleBox['bounding_box']['x']) &&
                        is_numeric($articleBox['bounding_box']['y']) &&
                        is_numeric($articleBox['bounding_box']['width']) &&
                        is_numeric($articleBox['bounding_box']['height']))) return $fail('Somting is wrong. Reselect the article');
                    if($articleBox['type'] === ArticleBoxType::TEXT->value) {
                        if(!(isset($articleBox['extracted_content']) && is_string($articleBox['extracted_content']) && $articleBox['extracted_content'])) return $fail('Somting is wrong. Reselect the article');
                    }
                }
            }],
            'add_selection_from_other_page' => 'boolean',
            'article' => 'nullable|integer|exists:articles,id'
        ]);
        if($data['article']) $articleId = (int) $data['article'];
        else {
            $article = Article::create([
                'epaper_id' => $epage->epaper_id
            ]);
            $articleId = $article->id;
        }
        foreach($data['article_boxes'] as $articleBox) {
            ArticleBox::create([
                'epaper_id' => $epage->epaper_id,
                'epage_id' => $epage->id,
                'article_id' => $articleId,
                'type' => $articleBox['type'],
                'bounding_box' => $articleBox['bounding_box'],
                'extracted_content' => $articleBox['extracted_content'] ?? null,
            ]);
        }
        if($data['add_selection_from_other_page']) {
            return redirect()->route('epapers.edit', [ 'epaper' => $epage->epaper_id, 'article' => $articleId ])->with('info', 'The selected part of article is saved. You can add more articles from the other page. Click on the other page and select the left part of article');
        }
        return back()->with('success', 'Articles saved.');
    }
}
