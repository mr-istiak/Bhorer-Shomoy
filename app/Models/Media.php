<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperMedia
 */
class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'title',
        'alt'
    ];

    protected $appends = [
        'can_delete'
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d H:i'
        ];
    }

    protected function canDelete() : Attribute
    {
        return Attribute::make(
            get: fn () => request()->user()?->can('delete', $this)
        );
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'id', 'featured_image');
    }

    public function isBelongsTo(User $user): bool
    {
        return $this->user_id === $user->id;
    }
}
