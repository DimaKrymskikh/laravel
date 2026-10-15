<?php

namespace App\Contracts\Services;

use App\Contracts\Curl\CurlRequestInterface;
use App\Contracts\Database\DatabaseQueryInterface;
use App\Contracts\StorageDisk\CopyistInterface;

interface ServiceManagerInterface
{
    public function getQueriesOrModifiers(string $className): DatabaseQueryInterface;
    
    public function getCopyist(string $className): CopyistInterface;
    
    public function getCurlRequest(string $className): CurlRequestInterface;
}
