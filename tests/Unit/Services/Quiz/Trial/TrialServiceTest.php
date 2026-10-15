<?php

namespace Tests\Unit\Services\Quiz\Trial;

use App\Contracts\Services\ServiceManagerInterface;
use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\TrialAnswer;
use App\Models\Quiz\Trial;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;
use App\Queries\Quiz\TrialAnswerQueries;
use App\Queries\Quiz\TrialQueries;
use App\Services\Quiz\Enums\QuizStatus;
use App\Services\Quiz\Trial\TrialService;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;
use Tests\Unit\Services\Quiz\TrialTestCase;

class TrialServiceTest extends TrialTestCase
{
    private TrialAnswerModifiers $trialAnswerModifiers;
    private TrialModifiers $trialModifiers;
    private TrialAnswerQueries $trialAnswerQueries;
    private TrialQueries $trialQueries;
    private TrialService $trialService;
    private int $trialId = 12;

    public function test_success_getTrialAnswerById(): void
    {
        $trialAnswer = new TrialAnswer();
        
        $this->trialAnswerQueries->method('find')
                ->willReturn($trialAnswer);
        
        $this->assertInstanceOf(TrialAnswer::class, $this->trialService->getTrialAnswerById($this->quizId));
    }

    public function test_fail_getTrialAnswerById(): void
    {
        $this->expectException(DatabaseException::class);
        
        $this->trialAnswerQueries->method('find')
                ->willReturn(null);
        
        $this->trialService->getTrialAnswerById($this->quizId);
    }

    public function test_success_getQuizzes(): void
    {
        $this->quizQueries->expects($this->once())
                ->method('getListForTrials');
        
        $this->assertInstanceOf(QuizCollection::class, $this->trialService->getQuizzes());
    }

    public function test_success_getQuiz(): void
    {
        $user = new User();
        $quiz = $this->factoryQuiz();
        
        $this->quizQueries->expects($this->once())
                ->method('getByIdForTrial')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(true);
        
        $this->assertInstanceOf(Quiz::class, $this->trialService->getQuiz($user, $this->quizId));
        $this->assertTrue($quiz->isActiveTrial);
    }

    public function test_success_startTrial(): void
    {
        $user = new User();
        $quizItems = $this->factoryQuizItems(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS, 0);
        $quiz = $this->factoryQuizWithQuizItems($quizItems, QuizStatus::Approved);
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(false);
        
        $this->quizQueries->expects($this->once())
                ->method('getByIdForTrialWithQuizItems')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->trialModifiers->expects($this->once())
                ->method('insertGetId')
                ->with($this->identicalTo($user), $this->identicalTo($quiz))
                ->willReturn($this->trialId);
        
        $this->trialAnswerModifiers->expects($this->exactly(Quiz::MINIMUM_ITEMS_FOR_READY_STATUS + 1))
                ->method('save');
        
        $this->assertNull($this->trialService->startTrial($user, $this->quizId));
    }

    public function test_fail_startTrial(): void
    {
        $user = new User();
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(true);
        
        $this->quizQueries->expects($this->never())
                ->method('getByIdForTrialWithQuizItems');
        
        $this->trialModifiers->expects($this->never())
                ->method('insertGetId');
        
        $this->trialAnswerModifiers->expects($this->never())
                ->method('save');
        
        $this->trialService->startTrial($user, $this->quizId);
    }

    public function test_success_getActiveTrial(): void
    {
        $this->assertInstanceOf(Trial::class, $this->trialService->getActiveTrial(new User()));
    }

    public function test_success_getListQuizItemsForActiveTrial(): void
    {
        $this->quizItemQueries->expects($this->once())
                ->method('getListByQuizIdWithAnswersForTrial')
                ->with($this->identicalTo($this->quizId));
        
        $this->assertInstanceOf(QuizItemCollection::class, $this->trialService->getListQuizItemsForActiveTrial($this->quizId));
    }

    public function test_success_getTrialsForUserResults(): void
    {
        $this->assertInstanceOf(TrialCollection::class, $this->trialService->getTrialsForUserResults(new User()));
    }

    public function test_success_chooseAnswer(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getAnswerDto($user, $id, $quizAnswerId);
        
        $trial = $this->factoryTrial();
        $trial->start_at = Carbon::now();
        $trial->lead_time = 5;
        
        $answer = $this->factoryQuizAnswer();
        $trialAnswer = $this->factoryTrialAnswer();
        $trialAnswer->quiz_answer_id = $dto->quiz_answer_id;
        $trialAnswer->answer = $answer->description;
        $trialAnswer->is_correct = $answer->is_correct;
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(true);
        
        $this->trialQueries->method('getActiveTrialByUserWithAnswers')
                ->willReturn($trial);
        
        $this->trialAnswerQueries->method('find')
                ->willReturn($trialAnswer);
        
        // Используется в $this->getQuizAnswerById
        $this->quizAnswerQueries->method('find')
                ->willReturn($answer);
        
        $this->trialAnswerModifiers->expects($this->once())
                ->method('save')
                ->with($trialAnswer);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_fail_chooseAnswer_not_active_trial(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getAnswerDto($user, $id, $quizAnswerId);
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(false);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_fail_chooseAnswer_time_is_up(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getAnswerDto($user, $id, $quizAnswerId);
        
        $trial = $this->factoryTrial();
        $trial->start_at = Carbon::now();
        // Отрицательные минуты приведут к возврату отрицательного числа функцией $trial->getTimeUntilQuizEnd()
        $trial->lead_time = -5;
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->method('existsActiveTrialByUser')
                ->willReturn(true);
        
        $this->trialQueries->method('getActiveTrialByUserWithAnswers')
                ->willReturn($trial);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_success_completeTrial(): void
    {
        $user = new User();
        $trial = $this->factoryTrialWithAnswers(20);
        
        $this->trialQueries->method('getActiveTrialByUserWithAnswers')
                ->willReturn($trial);
        
        $this->trialModifiers->expects($this->once())
                ->method('save')
                ->with($this->identicalTo($trial));
        
        $this->assertNull($this->trialService->completeTrial($user));
    }
    
    protected function setUp(): void
    {
        $this->create();
        
        $this->trialAnswerModifiers = $this->createMock(TrialAnswerModifiers::class);
        $this->trialModifiers = $this->createMock(TrialModifiers::class);
        $this->trialAnswerQueries = $this->createStub(TrialAnswerQueries::class);
        $this->trialQueries = $this->createStub(TrialQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willreturn(
                        $this->quizAnswerModifiers, $this->quizItemModifiers, $this->quizModifiers,
                        $this->quizAnswerQueries, $this->quizItemQueries, $this->quizQueries,
                        $this->trialAnswerModifiers, $this->trialModifiers, 
                        $this->trialAnswerQueries, $this->trialQueries
                    );
        
        $this->trialService = new TrialService($this->serviceManager);
    }
}
