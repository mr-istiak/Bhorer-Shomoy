<?php

namespace App\Jobs;

use App\Enums\RasterDPI;
use App\Models\ArticleBox;
use App\Models\Epaper;
use App\Models\EpaperPage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateArticleBoxesImage implements ShouldQueue
{
    use Queueable;

    public Collection $epages;
    private RasterDPI $dpi = RasterDPI::HIGH; // 300 dpi
    /**
     * Create a new job instance.
     */
    public function __construct(public Epaper $epaper, public Collection | null $chunkedPages = null) {}

    public function getBoxImagePath(ArticleBox $articleBox) : string
    {
        return "epapers/{$this->epaper->id}/{$this->dpi->value}/article_boxes/{$articleBox->id}.webp";
    }

    protected function processArticleBox(\Imagick $imagick, $articleBox, int $imageHeight): void
    {
        $rectImage = $articleBox->rectImage($this->dpi, $imageHeight);

        $crop = clone $imagick;
        $crop->cropImage($rectImage['width'], $rectImage['height'], $rectImage['x'], $rectImage['y']);
        $crop->setImagePage(0, 0, 0, 0);
        $crop->setImageFormat('webp');

        $outputPath = $this->getBoxImagePath($articleBox);
        Storage::disk('public')->put($outputPath, $crop->getImageBlob());

        $articleBox->update([
            'rasted_image' => $outputPath,
        ]);
        $crop->clear();
        $crop->destroy();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if(isset($this->chunkedPages) && ($this->chunkedPages instanceof Collection) && $this->chunkedPages?->isNotEmpty()){
            $this->epages = $this->chunkedPages;
        } else $this->epages = $this->epaper->pages()->with([
            'articleBoxes' => fn($query) => $query->whereNull('rasted_image')
        ])->get();
        foreach ($this->epages as $epage) {
            $imagick = new \Imagick();
            $imagick->readImageBlob(
                Storage::disk('public')->get($epage->image_path[$this->dpi->value])
            );
            $imageHeight = $imagick->getImageHeight();

            foreach ($epage->articleBoxes as $articleBox) {
                $this->processArticleBox($imagick, $articleBox, $imageHeight);
            }
            $imagick->clear();
            $imagick->destroy();
        }
    }
}
