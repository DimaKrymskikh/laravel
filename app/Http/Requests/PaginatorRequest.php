<?php

namespace App\Http\Requests;

use App\Pagination\PaginatorDTO;
use App\Pagination\ValueObjects\PageValue;
use App\Pagination\ValueObjects\PerPageValue;
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
    
    public function getPaginatorDto(): PaginatorDTO
    {
        return new PaginatorDTO(
                PageValue::create($this->input('page')),
                PerPageValue::create($this->input('number')),
            );
    }
}
