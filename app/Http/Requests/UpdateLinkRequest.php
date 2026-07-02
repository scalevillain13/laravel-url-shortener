<?php

namespace App\Http\Requests;

use App\Models\Link;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Link|null $link */
        $link = $this->route('link');

        return $link !== null && $this->user()?->can('update', $link) === true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Link $link */
        $link = $this->route('link');

        return StoreLinkRequest::baseRules($link->id);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return (new StoreLinkRequest)->messages();
    }
}
