<?php

namespace App\Models;

use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    protected $fillable = [
        'epaper_id',
        'status',
        'text',
    ];
     
    protected function casts(): array
    {
        return [
            'status' => ArticleStatus::class,
            'created_at' => 'datetime:Y-m-d H:i',
            'updated_at' => 'datetime:Y-m-d H:i',
        ];
    }

    public function epaper()
    {
        return $this->belongsTo(Epaper::class);
    }

    public function boxes() : HasMany
    {
        return $this->hasMany(ArticleBox::class)->oldest();
    }

    public function next(array $only = ['*']) : Article | null
    {
        return self::where('epaper_id', $this->epaper_id)
            ->where('id', '>', $this->id)
            ->orderBy('id', 'asc')
            ->first($only);
    }

    public function previous(array $only = ['*']) : Article | null
    {
        return self::where('epaper_id', $this->epaper_id)
            ->where('id', '<', $this->id)
            ->orderBy('id', 'desc')
            ->first($only);
    }
}
