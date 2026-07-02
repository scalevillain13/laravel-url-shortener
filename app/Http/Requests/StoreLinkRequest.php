<?php

namespace App\Http\Requests;

use App\Models\Link;
use App\Rules\SafeRedirectUrl;
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
        return self::baseRules();
    }

    /**
     * @return array<string, mixed>
     */
    public static function baseRules(?int $ignoreLinkId = null): array
    {
        return [
            'original_url' => ['required', 'url', 'max:2048', new SafeRedirectUrl],
            'code' => [
                'nullable',
                'alpha_num',
                'min:3',
                'max:16',
                Rule::unique('links', 'code')->ignore($ignoreLinkId),
                Rule::notIn(Link::reservedCodes()),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'utm_source' => ['nullable', 'string', 'max:100'],
            'utm_medium' => ['nullable', 'string', 'max:100'],
            'utm_campaign' => ['nullable', 'string', 'max:100'],
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
            'expires_at.after' => 'Дата истечения должна быть в будущем.',
        ];
    }
}
