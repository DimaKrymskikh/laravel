<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\Actor;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class ActorsCopyist extends BaseCopyist implements ActorsCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeData(string $file, Actor $actor): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'id' => $actor->id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'first_name' => '$actor->first_name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 16)."'last_name' => '$actor->last_name',");
        Storage::disk('database')->append($file, Str::repeat(' ', 12)."],");
    }
}
