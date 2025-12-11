<?php

namespace Tests\Unit\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Services\Quiz\Fields\QuizField;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizFieldTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            // $field, $value
            ['title', 'Прямые на плоскости'],
            ['description', 'Изучаются прямые на плоскости'],
            ['lead_time', '77'],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_create(string $field, int|string $value): void
    {
        $quisField = QuizField::create($field, $value);
        
        $this->assertEquals($field, $quisField->field);
        $this->assertEquals($value, $quisField->value->value);
    }
    
    public static function failProvider(): array
    {
        return [
            [null],
            ['fail'],
        ];
    }
    
    #[DataProvider('failProvider')]
    public function test_fail_create(string|null $field): void
    {
        $this->expectException(RuleException::class);
        
        QuizField::create($field, '');
    }
}
