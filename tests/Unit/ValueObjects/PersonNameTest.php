<?php

namespace Tests\Unit\ValueObjects;

use App\Exceptions\RuleException;
use App\ValueObjects\PersonName;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PersonNameTest extends TestCase
{
    private const MESSAGE  = 'Некоторое сообщение';
    
    public static function correctProvider(): array
    {
        return [
            ['Иван'],
            ['Testname'],
        ];
    }
    
    #[DataProvider('correctProvider')]
    public function test_success_person_name(string $value): void
    {
        $this->assertEquals($value, PersonName::create($value, 'first_name', self::MESSAGE)->name);
    }
    
    public static function incorrectProvider(): array
    {
        return [
            ['иван'],
            [''],
            [null],
        ];
    }
    
    #[DataProvider('incorrectProvider')]
    public function test_fail_person_name(string|null $value): void
    {
        $this->expectException(RuleException::class);
        $this->expectExceptionMessage(self::MESSAGE);
        PersonName::create($value, 'first_name', self::MESSAGE);
    }
}
