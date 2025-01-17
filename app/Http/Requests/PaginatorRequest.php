<?php

namespace App\Http\Requests;

use App\DataTransferObjects\Pagination\PaginatorDto;
use App\ValueObjects\Pagination\PageValue;
use App\ValueObjects\Pagination\PerPageValue;
use Illuminate\Foundation\Http\FormRequest;

class PaginatorRequest extends FormRequest
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
    
    public function getPaginatorDto(): PaginatorDto
    {
        return new PaginatorDto(
                PageValue::create($this->input('page')),
                PerPageValue::create($this->input('number')),
            );
    }
}
