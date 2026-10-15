<?php

namespace App\Queries;

use App\Contracts\Database\QueriesInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Содержит основные select-запросы.
 */
final class BaseQueries implements QueriesInterface
{
    /**
     * {@inheritDoc}
     */
    public function exists(string $className, int $id): bool
    {
        return $className::where('id', $id)->exists();
    }
    
    /**
     * {@inheritDoc}
     */
    public function find(string $className, int $id): Model|null
    {
        return $className::find($id);
    }
    
    /**
     * {@inheritDoc}
     */
    public function all(string $className): Collection
    {
        return $className::all();
    }
}
