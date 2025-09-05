<?php

namespace App\Models;

use App\Enums\RasterDPI;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpaperPage extends Model
{
    protected $fillable = [
        'epaper_id',
        'page_number',
        'image_path' // converted image { dpi: `epapers/{epaper_id}/{dpi}/epaper_pages/page_{page_number}.webp` }
    ];

    protected function casts(): array
    {
        return [
            'image_path' => 'array',
            'created_at' => 'datetime:Y-m-d H:i',
            'updated_at' => 'datetime:Y-m-d H:i',
        ];
    }

    public function epaper()
    {
        return $this->belongsTo(Epaper::class);
    }

    public function articleBoxes() : HasMany
    {
        return $this->hasMany(ArticleBox::class, 'epage_id');
    }
}
