<?php

namespace App\Models;

use App\Enums\ArticleBoxType;
use App\Enums\RasterDPI;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleBox extends Model
{
    protected $table = 'article_boxes';

    protected $fillable = [
        'epaper_id',
        'epage_id',
        'article_id',
        'type',
        'bounding_box', // PDF Rect
        'extracted_content',
        'rasted_image' // converted image dpi: `epapers/{epaper_id}/{{RASTERDPI::HIGH}}/article_boxes/{article_box_id}.webp`
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'bounding_box' => 'array',
            'created_at' => 'datetime:Y-m-d H:i',
            'updated_at' => 'datetime:Y-m-d H:i',
            'type' => ArticleBoxType::class
        ];
    }

    protected function rectPDF() : Attribute
    {
        return Attribute::make(
            get: fn () => $this->bounding_box
        );
    }

    public function epaper() : BelongsTo
    {
        return $this->belongsTo(Epaper::class);
    }

    public function epage() : BelongsTo
    {
        return $this->belongsTo(EpaperPage::class, 'epage_id');
    }

    public function article() : BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public static function pdfBoxToImageCoords(array $pdfBox, int $dpi, int $imageHeight): array
    {
        $scale = $dpi / 72; // points -> pixels

        $xImg = $pdfBox['x'] * $scale;
        $yImg = $imageHeight - ($pdfBox['y'] + $pdfBox['height']) * $scale; // flip Y
        $wImg = $pdfBox['width'] * $scale;
        $hImg = $pdfBox['height'] * $scale;
        return [
            'x'      => (int) round($xImg),
            'y'      => (int) round($yImg),
            'width'  => (int) round($wImg),
            'height' => (int) round($hImg),
        ];
    }

    public function rectImage(RasterDPI $dpi, int $imageHeight)
    {
        return self::pdfBoxToImageCoords($this->rectPDF, $dpi->value, $imageHeight);
    }
}
