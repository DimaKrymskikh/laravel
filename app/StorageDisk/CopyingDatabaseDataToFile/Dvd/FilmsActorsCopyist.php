<?php

namespace App\StorageDisk\CopyingDatabaseDataToFile\Dvd;

use App\Models\Dvd\FilmActor;
use App\StorageDisk\CopyingDatabaseDataToFile\BaseCopyist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class FilmsActorsCopyist extends BaseCopyist implements FilmsActorsCopyistInterface
{
    /**
     * {@inheritDoc}
     * 
     * @inheritDoc
     */
    public function writeData(string $file, FilmActor $filmActor): void
    {
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "(object) [");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'film_id' => $filmActor->film_id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 16) . "'actor_id' => $filmActor->actor_id,");
        Storage::disk('database')->append($file, Str::repeat(' ', 12) . "],");
    }
}
