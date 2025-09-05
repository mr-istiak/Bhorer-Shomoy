<?php

namespace App\Models;

use App\Enums\PostStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPost
 */
class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'meta_description',
        'content',
        'status',
        'published_at',
        'author_id',
        'featured_image'
    ];

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
        'status' => PostStatus::class
    ];

    protected $appends = [
        'can_delete'
    ];

    protected function canDelete() : Attribute
    {
        return Attribute::make(
            get: fn () => request()->user()?->can('delete', $this)
        );
    }

    public function author()
    {
        return $this->belongsTo(\App\Models\User::class, 'author_id');
    }

    public function featuredImage()
    {
        return $this->hasOne(Media::class, 'id', 'featured_image');
    }

    public function categories() : BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_post');
    }

    public function position() : BelongsTo
    {
        return $this->belongsTo(Position::class, 'id', 'post_id');
    }

    public static function public() : Builder
    {
        return self::with(['categories', 'featuredImage'])->where('status', PostStatus::PUBLISHED->value);
    }
}
