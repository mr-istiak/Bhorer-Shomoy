<?php

namespace App\Http\Requests;

use App\Enums\PostStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\Post;

/**
 * @method bool isMethod(string $method)
 * @method \App\Models\User|null user(string|null $guard = null)
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 * @method void merge(array $input)
 * @method mixed input(string $key = null, $default = null)
 */

class PostRequest extends FormRequest
{
/*     public function authorize(): bool
    {
        // This is an admin panel; require authenticated users (controller also uses auth middleware)
        return auth()->check();
    } */

    /**
     * Prepare and normalize data before validation.
     * - generate slug from title if not provided
     * - slugify input
     * - append numeric suffix if slug already exists (e.g. slug, slug-1, slug-2, ...)
     */
    protected function prepareForValidation(): void
    {
        $postId = $this->route('post')?->id;

        // derive base slug from provided slug or title
        $raw = $this->input('slug') ?: $this->input('title', '');
        $base = Str::slug((string) $raw);

        if ($base === '') {
            // nothing to do (title validation will catch empty title)
            $this->merge(['slug' => null]);
            return;
        }

        $slug = $base;
        $counter = 1;

        // ensure slug length stays within 255 when adding suffixes
        while (Post::where('slug', $slug)
            ->when($postId, fn($q) => $q->where('id', '<>', $postId))
            ->exists()
        ) {
            $suffix = '-' . $counter;
            $maxBaseLength = 255 - strlen($suffix);
            $trimmedBase = Str::substr($base, 0, $maxBaseLength);
            $slug = $trimmedBase . $suffix;
            $counter++;
        }

        $this->merge(['slug' => $slug]);

        // If meta_Description not provided, use the first 160 characters of the content
        if (empty($this->input('meta_description') ?? '')) {
            $this->merge(['meta_description' => Str::limit(strip_tags($this->input('content')), 190)]);
        }
    }

    public function rules(): array
    {
        $postId = $this->route('post')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($postId),
            ],
            'meta_description' => ['nullable', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in([PostStatus::DRAFT, PostStatus::PUBLISHED])],
            'featured_image' => ['nullable', 'exists:media,id'],
            'categories' => ['array'],
            'categories.*' => ['exists:categories,id'],
        ];
    }
}
