<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile;

interface BaseCopyistInterface 
{
    /**
     * Создаёт шапку класса, хранящего данные таблицы.
     * 
     * @param string $file Файл данного класса (disk: 'database').
     * @param string $namespace Пространство имён данного класса.
     * @param string $table Таблица из которой берутся данные.
     * @param string $className Имя данного класса.
     * @return void
     */
    public function writeHeader(string $file, string $namespace, string $table, string $className): void;
    
    /**
     * Создаёт окончание класса, хранящего данные таблицы.
     * 
     * @param string $file Файл данного класса (disk: 'database').
     * @return void
     */
    public function writeFooter(string $file): void;
}
