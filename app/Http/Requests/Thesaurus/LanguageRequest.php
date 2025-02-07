<?php

namespace App\Http\Requests\Thesaurus;

use App\Http\Requests\Thesaurus\Filters\LanguageFilterRequest;
use App\Rules\CapitalFirstLetter;

class LanguageRequest extends LanguageFilterRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:App\Models\Thesaurus\Language', new CapitalFirstLetter('attr.language.capital_first_letter')],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => trans("attr.language.required"),
            'name.string' => trans("attr.language.string"),
            'name.unique' => trans("attr.language.unique"),
        ];
    }
}
