<?php

namespace App\Http\Requests;

use App\Models\Position;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @method bool isMethod(string $method)
 * @method \App\Models\User|null user(string|null $guard = null)
 * @method \Illuminate\Routing\Route|null route(string|null $param = null)
 * @method void merge(array $input)
 * @method mixed input(string $key = null, $default = null)
 */

class UpdatePositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', Position::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'postId' => ['required', 'numeric', 'exists:posts,id'],
        ];
    }
}
