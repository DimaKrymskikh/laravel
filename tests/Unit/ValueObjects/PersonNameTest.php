<?php

namespace Tests\Unit\ValueObjects;

use App\Exceptions\RuleException;
use App\ValueObjects\PersonName;
use PHPUnit\Framework\TestCase;

class PersonNameTest extends TestCase
{
    private string $message = 'Некоторое сообщение';
    
    public function test_person_name_can_be_created(): void
    {
        $this->assertEquals('Иван', PersonName::create('Иван', 'first_name', $this->message)->name);
    }
    
    public function test_person_name_cannot_be_created_if_the_name_is_not_capitalized(): void
    {
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage($this->message);
        PersonName::create('иван', 'first_name', $this->message);
    }
        
    
    public function test_person_name_cannot_be_created_if_the_name_is_null(): void
    {
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage($this->message);
        PersonName::create(null, 'first_name', $this->message);
    }
        
    
    public function test_person_name_cannot_be_created_if_the_name_is_empty(): void
    {
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage($this->message);
        PersonName::create('', 'last_name', $this->message);
    }
}
