<?php

namespace App\Services;

use LogicException;
use App\Contracts\Curl\CurlRequestInterface;
use App\Contracts\Database\DatabaseQueryInterface;
use App\Contracts\Services\ServiceManagerInterface;
use App\Contracts\StorageDisk\CopyistInterface;

final class ServiceManager implements ServiceManagerInterface
{
    public function getQueriesOrModifiers(string $className): DatabaseQueryInterface
    {
        $ob = new $className();
        
        if (! $ob instanceof DatabaseQueryInterface) {
            throw new LogicException(sprintf('Класс "%s" не выполняет запросы в базу данных.', $className));
        }
        
        return $ob;
    }
    
    public function getCopyist(string $className): CopyistInterface
    {
        $ob = new $className();
        
        if (! $ob instanceof CopyistInterface) {
            throw new LogicException(sprintf('Класс "%s" не выполняет запись данных на диск.', $className));
        }
        
        return $ob;
    }
    
    public function getCurlRequest(string $className): CurlRequestInterface
    {
        $ob = new $className();
        
        if (! $ob instanceof CurlRequestInterface) {
            throw new LogicException(sprintf('Класс "%s" не выполняет curl-запрос.', $className));
        }
        
        return $ob;
    }
}
