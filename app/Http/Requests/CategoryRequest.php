<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
 

/**
 * @method bool isMethod(string $method)
 * @method \App\Models\User|null user(string|null $guard = null)
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 * @method void merge(array $input)
 * @method mixed input(string $key = null, $default = null)
 */

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if($this->isMethod('PUT')) return $this->user()->can('update', Category::class);
        if($this->isMethod('POST')) return $this->user()->can('create', Category::class);
        return false;
    }

    protected function prepareForValidation()
    {
        $categoryId = $this->route('category')?->id;

        // derive base slug from provided slug or name
        $raw = $this->input('slug') ?: $this->input('name', '');
        $base = Str::slug((string) $raw);

        if ($base === '') {
            // empty name/slug → let validation handle it
            $this->merge(['slug' => null]);
            return;
        }

        $slug = $base;
        $counter = 1;

        while (Category::where('slug', $slug)
            ->when($categoryId, fn($q) => $q->where('id', '<>', $categoryId))
            ->exists()
        ) {
            $suffix = '-' . $counter;
            $maxBaseLength = 255 - strlen($suffix);
            $trimmedBase = Str::substr($base, 0, $maxBaseLength);
            $slug = $trimmedBase . $suffix;
            $counter++;
        }

        $this->merge(['slug' => $slug]);
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'bangla_name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', // allow auto-generation
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ]
        ];
    }
}
