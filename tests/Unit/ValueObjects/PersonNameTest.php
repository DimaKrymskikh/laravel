<?php

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\PersonName;
use Illuminate\Validation\ValidationException;
// Для использования хэлпера trans нужно создать приложение (нужен провайдер Illuminate\Translation\TranslationServiceProvider::class),
// поэтому используем Tests\TestCase вместо PHPUnit\Framework\TestCase
use Tests\TestCase;
//use PHPUnit\Framework\TestCase;

class PersonNameTest extends TestCase
{
    public function test_person_name_can_be_created(): void
    {
        $this->assertEquals('Иван', PersonName::create('Иван', 'first_name', 'attr.actor.first_name.capital_first_letter')->name);
    }
    
    public function test_person_name_cannot_be_created_if_the_name_is_not_capitalized(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(trans('attr.actor.first_name.capital_first_letter'));
        PersonName::create('иван', 'first_name', 'attr.actor.first_name.capital_first_letter');
    }
        
    
    public function test_person_name_cannot_be_created_if_the_name_is_null(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(trans('attr.actor.last_name.capital_first_letter'));
        PersonName::create(null, 'last_name', 'attr.actor.last_name.capital_first_letter');
    }
        
    
    public function test_person_name_cannot_be_created_if_the_name_is_empty(): void
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionCode(0);
        $this->expectExceptionMessage(trans('attr.actor.last_name.capital_first_letter'));
        PersonName::create('', 'last_name', 'attr.actor.last_name.capital_first_letter');
    }
    
    public function test_person_name_cannot_be_changed(): void
    {
        $personName = PersonName::create('Иван', 'first_name', 'attr.actor.first_name.capital_first_letter');
        
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Cannot modify readonly property App\ValueObjects\PersonName::$name');
        $personName->name = 'Роман';
    }
}
