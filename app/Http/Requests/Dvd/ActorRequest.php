<?php

namespace App\Http\Requests\Dvd;

use App\DataTransferObjects\Database\Dvd\ActorDto;
use App\Rules\CapitalFirstLetter;
use App\ValueObjects\PersonName;
use Illuminate\Foundation\Http\FormRequest;

class ActorRequest extends FormRequest
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
            'first_name' => ['required', 'string', new CapitalFirstLetter('attr.actor.first_name.capital_first_letter')],
            'last_name' => ['required', 'string', new CapitalFirstLetter('attr.actor.last_name.capital_first_letter')],
        ];
    }
    
    public function messages(): array
    {
        return [
            'first_name.required' => trans("attr.actor.first_name.required"),
            'first_name.string' => trans("attr.actor.first_name.string"),
            'last_name.required' => trans("attr.actor.last_name.required"),
            'last_name.string' => trans("attr.actor.last_name.string"),
        ];
    }
    
    public function getActorDto(): ActorDto
    {
        return new ActorDto(
                PersonName::create($this->get('first_name'), 'first_name', 'attr.actor.first_name.capital_first_letter'),
                PersonName::create($this->get('last_name'), 'last_name', 'attr.actor.last_name.capital_first_letter'),
            );
    }
}
