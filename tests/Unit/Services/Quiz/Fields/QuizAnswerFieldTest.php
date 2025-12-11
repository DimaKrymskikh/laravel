<?php

namespace Tests\Unit\Services\Quiz\Fields;

use App\Exceptions\RuleException;
use App\Services\Quiz\Fields\QuizAnswerField;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class QuizAnswerFieldTest extends TestCase
{
    public static function successProvider(): array
    {
        return [
            // $field, $value
            ['description', 'Это ответ на вопрос'],
            ['is_correct', true],
            ['priority', '4'],
        ];
    }
    
    #[DataProvider('successProvider')]
    public function test_success_create(string $field, mixed $value): void
    {
        $quisField = QuizAnswerField::create($field, $value);
        
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
        
        QuizAnswerField::create($field, '');
    }
}
