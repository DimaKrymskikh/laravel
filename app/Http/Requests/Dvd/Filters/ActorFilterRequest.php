<?php

namespace App\Http\Requests\Dvd\Filters;

use App\DataTransferObjects\Database\Dvd\Filters\ActorFilterDto;
use App\Http\Requests\PaginatorRequest;

class ActorFilterRequest extends PaginatorRequest
{
    public function getActorFilterDto(): ActorFilterDto
    {
        return new ActorFilterDto($this->query('name'));
    }
}
