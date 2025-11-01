<?php

namespace App\Http\Requests\Quiz;

use App\Services\Quiz\Fields\DataTransferObjects\QuizDto;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
    
    public function getDto(): QuizDto
    {
        return new QuizDto(
                QuizTitleValue::create($this->input('title')),
                SimpleStringValue::create($this->input('description')),
                QuizLeadTimeValue::create($this->input('lead_time')),
            );
    }
}
