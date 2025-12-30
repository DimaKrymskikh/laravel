<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class BaseCopyist implements BaseCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeHeader(string $file, string $namespace, string $table, string $className): void
    {
        Storage::disk('database')->put($file, "<?php\n");
        Storage::disk('database')->append($file, "namespace $namespace;\n");
        
        Storage::disk('database')->append($file, "// Данные таблицы $table");
        Storage::disk('database')->append($file, "class $className");
        Storage::disk('database')->append($file, "{");
        Storage::disk('database')->append($file, Str::repeat(' ', 4)."public function __invoke(): array");
        Storage::disk('database')->append($file, Str::repeat(' ', 4)."{");
        Storage::disk('database')->append($file, Str::repeat(' ', 8)."return [");
    }
    
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeFooter(string $file): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 8)."];");
        Storage::disk('database')->append($file, Str::repeat(' ', 4)."}");
        Storage::disk('database')->append($file, "}\n");
    }
}
