<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Epaper extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'published_at',
        'pdf_path',
    ];

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Epaper $epaper) {
            Storage::disk('public')->deleteDirectory("epapers/{$epaper->id}");
            Storage::disk('public')->delete($epaper->pdf_path);
        });
    }

    public function pages() : HasMany
    {
        return $this->hasMany(EpaperPage::class);
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function aricleBoxes() : HasMany
    {
        return $this->hasMany(ArticleBox::class);
    }
}
