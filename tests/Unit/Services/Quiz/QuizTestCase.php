<?php

namespace Tests\Unit\Services\Quiz;

use App\Contracts\Services\ServiceManagerInterface;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\QuizItem;
use App\Modifiers\Quiz\QuizAnswerModifiers;
use App\Modifiers\Quiz\QuizItemModifiers;
use App\Modifiers\Quiz\QuizModifiers;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;
use App\Services\Quiz\Enums\QuizItemStatus;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Fields\DataTransferObjects\QuizAnswerDto;
use App\Services\Quiz\Fields\DataTransferObjects\QuizDto;
use App\Services\Quiz\Fields\DataTransferObjects\QuizItemDto;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizLeadTimeValue;
use App\Services\Quiz\Fields\ValueObjects\Quiz\QuizTitleValue;
use App\Services\Quiz\Fields\QuizField;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\QuizCollection;
use App\ValueObjects\ScalarTypes\BoolValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;
use Illuminate\Database\Eloquent\Factories\Sequence;
use PHPUnit\Framework\TestCase;

abstract class QuizTestCase extends TestCase
{
    protected ServiceManagerInterface $serviceManager;
    protected QuizAnswerModifiers $quizAnswerModifiers;
    protected QuizItemModifiers $quizItemModifiers;
    protected QuizModifiers $quizModifiers;
    protected QuizAnswerQueries $quizAnswerQueries;
    protected QuizItemQueries $quizItemQueries;
    protected QuizQueries $quizQueries;
    protected int $quizId = 17;
    protected int $quizItemId = 29;
    protected int $quizAnswerId = 11;
    
    protected function getQuizAnswerDto(): QuizAnswerDto
    {
        $quizItemId = 17;
        
        return new QuizAnswerDto(
                $quizItemId,
                SimpleStringValue::create('Test text answer.'),
                BoolValue::create('false')
            );
    }
    
    protected function getQuizDto(): QuizDto
    {
        return new QuizDto(
                QuizTitleValue::create('Test Title Quiz'),
                SimpleStringValue::create('Test quiz description.'),
                QuizLeadTimeValue::create('77'),
        );
    }
    
    protected function getQuizItemDto(): QuizItemDto
    {
        $quizId = 5;
        
        return new QuizItemDto(
                $quizId,
                SimpleStringValue::create('Test quiz_item description.'));
    }
    
    protected function getQuizField(string $field, string $value): QuizField
    {
        return QuizField::create($field, $value);
    }
    
    protected function factoryQuizzes(int $nQuiz): QuizCollection
    {
        return Quiz::factory()
                ->count($nQuiz)
                ->sequence([
                    'status' => QuizStatus::AtWork->value,
                    'status' => QuizStatus::Ready->value,
                ])
                ->make();
    }
    
    protected function factoryQuiz(QuizStatus $status = QuizStatus::AtWork, int|string $leadTime = Quiz::DEFAULT_LAED_TIME): Quiz
    {
        return Quiz::factory()
                ->state([
                    'status' => $status->value,
                    'lead_time' => $leadTime,
                ])
                ->make();
    }
    
    protected function factoryQuizTitle(QuizStatus $status, string $title): Quiz
    {
        return Quiz::factory()
                ->state([
                    'status' => $status->value,
                    'title' => $title,
                ])
                ->make();
    }
    
    protected function factoryQuizItem(Quiz $quiz, QuizItemStatus $status = QuizItemStatus::AtWork): QuizItem
    {
        $quizItem = QuizItem::factory()
                ->state([
                    'status' => $status->value,
                    'quiz' => $quiz,
                ])
                ->make();
        $quizItem->quiz = $quiz;
        
        return $quizItem;
    }
    
    protected function factoryQuizWithQuizItems(QuizItemCollection $quizItems, QuizStatus $quizStatus): Quiz
    {
        return Quiz::factory()
                ->state([
                    'status' => $quizStatus->value,
                    'quizItems' => $quizItems
                ])
                ->make();
    }
    
    protected function factoryQuizItems(int $nQuizItemsWithStatusReady, int $nQuizItemsWithStatusAtWork): QuizItemCollection
    {
        $nQuizItems = $nQuizItemsWithStatusReady + $nQuizItemsWithStatusAtWork + 1;
        
        return  QuizItem::factory()
                ->count($nQuizItems)
                ->state(new Sequence(
                    function (Sequence $sequence) use ($nQuizItemsWithStatusAtWork) {
                        if($sequence->index === 0) {
                            // Один вопрос в состоянии 'removed'
                            return [
                                'status' => QuizItemStatus::Removed->value,
                            ];
                        }
                        if($sequence->index < $nQuizItemsWithStatusAtWork + 1) {
                            // $nQuizItemsWithStatusAtWork вопросов в состоянии 'at_work'
                            return [
                                'status' => QuizItemStatus::AtWork->value,
                            ];
                        } else {
                            // Остальные вопросы в состоянии 'ready'
                            return [
                                'status' => QuizItemStatus::Ready->value,
                            ];
                        }
                    }
                ))
                ->make();
    }
    
    protected function factoryQuizItemWithAnswers(QuizItemStatus $quizItemStatus, int $nAnswers, bool $isCorrect, bool $isPriority, bool $isPriorityQuizItem): QuizItem
    {
        $quizItem = QuizItem::factory()
                ->state([
                        'status' => $quizItemStatus->value,
                        'priority' => $isPriorityQuizItem ? random_int(1, 99) : 0,
                        'answers' => QuizAnswer::factory()->count($nAnswers)->sequence(
                            [
                                'is_correct' => false,
                                'priority' => random_int(1, 99)
                            ], [
                                'is_correct' => $isCorrect ? true : false,
                                'priority' => $isPriority ? random_int(1, 99) : 0
                            ],
                        )->make()
                    ])
                ->make();
        $quizItem->quiz = Quiz::factory()->make();
        $quizItem->quiz->id = 10;
        $quizItem->quiz->quizItems = collect([$quizItem]);
        
        return $quizItem;
    }
    
    protected function factoryAnswer(bool $isCorrect, QuizStatus $quizStatus = QuizStatus::AtWork, QuizItemStatus $quizItemStatus = QuizItemSataus::AtWork): QuizAnswer
    {
        $quizAnswer = QuizAnswer::factory()
                ->state([
                    'is_correct' => $isCorrect,
                ])
                ->make();
        $quizAnswer->quiz_item_id = 8;
        
        $quiz = $this->factoryQuiz($quizStatus);
        $quizItem = $this->factoryQuizItem($quiz, $quizItemStatus);
        
        $quizAnswer->quizItem = $quizItem;
        
        return $quizAnswer;
    }
    
    protected function setUp(): void
    {
        $this->create();
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willReturn($this->quizAnswerModifiers, $this->quizItemModifiers, $this->quizModifiers,
                        $this->quizAnswerQueries, $this->quizItemQueries, $this->quizQueries);
    }
    
    protected function create(): void
    {
        $this->quizAnswerModifiers = $this->createMock(QuizAnswerModifiers::class);
        $this->quizItemModifiers = $this->createMock(QuizItemModifiers::class);
        $this->quizModifiers = $this->createMock(QuizModifiers::class);
        $this->quizAnswerQueries = $this->createStub(QuizAnswerQueries::class);
        $this->quizItemQueries = $this->createStub(QuizItemQueries::class);
        $this->quizQueries = $this->createStub(QuizQueries::class);
    }
}
