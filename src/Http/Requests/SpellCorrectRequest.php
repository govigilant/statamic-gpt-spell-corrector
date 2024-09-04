<?php

namespace Vigilant\GptSpellCorrector\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array $bard
 */
class SpellCorrectRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'bard' => ['required', 'array'],
            'bard.type' => ['required', 'string'],
            'bard.content' => ['required', 'array'],
        ];
    }
}
