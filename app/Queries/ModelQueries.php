<?php

namespace App\Queries;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * {@inheritDoc}
 * Содержит основные запросы в базу данных.
 */
abstract class ModelQueries extends DBqueries
{
    protected BaseQueries $queries;
    protected string $className;

    public function __construct()
    {
        $this->queries = new BaseQueries();
        
        $this->setClassName();
    }
    
    abstract protected function setClassName(): void;
    
    public function exists(int $id): bool
    {
        return $this->queries->exists($this->className, $id);
    }
    
    /**
     * По id таблицы создаёт и возвразает ORM-модель.
     * Если в таблице нет записи с id = $id, будет возвращён null.
     * 
     * @param int $id
     * @return Model|null 
     */
    public function find(int $id): Model|null
    {
        return $this->queries->find($this->className, $id);
    }
    
    public function all(): Collection
    {
        return $this->queries->all($this->className);
    }
}
