<?php

namespace App\Services\Database\Thesaurus;

use App\Exceptions\DatabaseException;
use App\DataTransferObjects\Database\Thesaurus\Filters\LanguageFilterDto;
use App\Models\Thesaurus\Language;
use App\Repositories\Thesaurus\LanguageRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class LanguageService
{
    public function __construct(
        private LanguageRepositoryInterface $languageRepository,
    ) {
    }
    
    public function create(string $name): void
    {
        $this->languageRepository->save(new Language(), $name);
    }
    
    public function update(string $name, int $id): void
    {
        $language = $this->languageRepository->getById($id);
        $this->languageRepository->save($language, $name);
    }
    
    public function delete(int $id): void
    {
        if(!$this->languageRepository->exists($id)) {
            throw new DatabaseException("В таблице 'thesaurus.languages' нет записи с id=$id");
        }
        
        $this->languageRepository->delete($id);
    }
    
    public function getAllLanguagesList(LanguageFilterDto $languageDto): Collection
    {
        return $this->languageRepository->getList($languageDto);
    }
}
