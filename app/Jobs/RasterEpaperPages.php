<?php

namespace App\Jobs;

use App\Enums\RasterDPI;
use App\Models\Epaper;
use App\Models\EpaperPage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class RasterEpaperPages implements ShouldQueue
{
    use Queueable;

    public string $pdfPath;
    public Collection $chuckPages;
    public Epaper $epaper;

    public function __construct(
        Epaper | Collection $epaper,
        public RasterDPI $dpi = RasterDPI::LOW,
        public int $compression = 75,
        public bool $onlyUnrastered = false,
    ) {
        if(($epaper instanceof Collection) && $epaper->isNotEmpty() && ($epaper->first() instanceof EpaperPage) && $onlyUnrastered) {
            $this->chuckPages = $epaper;
            $this->epaper = $this->chuckPages->first()->epaper;
        } else {
            $this->epaper = $epaper;
        }
        $this->pdfPath = storage_path("app/public/{$this->epaper->pdf_path}");
    }
    /**
     * Get storage path for a page image
     */
    public function getPagePath(int $pageNumber): string
    {
        return "epapers/{$this->epaper->id}/{$this->dpi->value}/epaper_pages/page_{$pageNumber}.webp";
    }
    /**
     * Rasterize a single page and save it
     */
    public function rasterPage(\Imagick $imagickPage, int $pageNumber): string
    {
        $imagickPage->setImageFormat('webp');
        $imagickPage->setImageCompressionQuality($this->compression);

        $fileName = $this->getPagePath($pageNumber);
        Storage::disk('public')->put($fileName, $imagickPage->getImageBlob());
        return $fileName;
    }
    /**
     * Raster only pages missing this DPI
     */
    public function onlyUnrastered(): void
    {
        if(isset($this->chuckPages) && $this->chuckPages?->isNotEmpty()) {
            $epages = $this->chuckPages->keyBy('page_number');
        } else $epages = $this->epaper->pages()
            ->whereRaw("JSON_EXTRACT(image_path, '$.\"{$this->dpi->value}\"') IS NULL")
            ->get()
            ->keyBy('page_number');

        foreach ($epages as $pageNumber => $page) {
            $imagick = new \Imagick();
            $imagick->setResolution($this->dpi->value, $this->dpi->value);
            $imagick->readImage($this->pdfPath . '[' . ($pageNumber - 1) . ']');

            $fileName = $this->rasterPage($imagick, $pageNumber);
            $path = $page->image_path;
            $path[$this->dpi->value] = $fileName;
            $page->image_path = $path;
            $page->save();

            $imagick->clear();
            $imagick->destroy();
        }
    }

    /**
     * Raster all pages in the PDF
     */
    public function all(): void
    {
        $imagick = new \Imagick();
        $imagick->setResolution($this->dpi->value, $this->dpi->value);
        $imagick->readImage($this->pdfPath);

        $epages = $this->epaper->pages()->get()->keyBy('page_number');

        foreach ($imagick as $i => $page) {
            $pageNumber = $i + 1;
            $fileName = $this->rasterPage($page, $pageNumber);
            if ($epages->has($pageNumber)) {
                $path = $epages[$pageNumber]->image_path;
                $path[$this->dpi->value] = $fileName;
                $epages[$pageNumber]->image_path = $path;
                $epages[$pageNumber]->save();
            } else {
                EpaperPage::create([
                    'epaper_id' => $this->epaper->id,
                    'page_number' => $pageNumber,
                    'image_path' => [$this->dpi->value => $fileName],
                ]);
            }
        }
        $imagick->clear();
        $imagick->destroy();
    }

    /**
     * Execute the rasterization job
     */
    public function handle(): void
    {
        $this->onlyUnrastered ? $this->onlyUnrastered() : $this->all();
    }
}
