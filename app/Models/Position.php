<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

class Position extends Model
{
    protected $fillable = [
        'post_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i'
    ];

    public static $positions = [
        'left-1',
        'left-2',
        'left-3',
        'left-4',
        'left-5',
        'left-6',
        'left-7',
        'featured',
        'featured-2',
        'main-1',
        'main-2',
        'main-3',
        'main-4',
        'secondary-1',
        'secondary-2',
        'secondary-3',
        'right-1',
        'right-2',
        'right-3'
    ];

    public function post() : HasOne
    {
        return $this->hasOne(Post::class, 'id', 'post_id');
    }
}
