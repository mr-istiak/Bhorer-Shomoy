<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Model;
/**
 * @mixin IdeHelperCategory
 */
class Category extends Model
{
    protected $fillable = ['name', 'slug', 'bangla_name'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function posts() : BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'category_post');
    }
}
