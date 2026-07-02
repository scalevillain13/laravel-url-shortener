<?php

namespace App\Http\Requests;

use App\Models\Link;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048'],
            'code' => [
                'nullable',
                'alpha_num',
                'min:3',
                'max:16',
                Rule::unique('links', 'code'),
                Rule::notIn(Link::reservedCodes()),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'original_url.required' => 'Укажите оригинальный URL.',
            'original_url.url' => 'URL должен быть корректным (например, https://example.com).',
            'code.alpha_num' => 'Код может содержать только латинские буквы и цифры.',
            'code.unique' => 'Этот код уже занят.',
            'code.not_in' => 'Этот код зарезервирован системой.',
        ];
    }
}
