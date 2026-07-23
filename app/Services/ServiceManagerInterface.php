<?php

namespace App\Services;

interface ServiceManagerInterface
{
    public function getQueriesOrModifiers(string $className): DatabaseQueryInterface;
    
    public function getCopyist(string $className): CopyistInterface;
    
    public function getCurlRequest(string $className): CurlRequestInterface;
}
