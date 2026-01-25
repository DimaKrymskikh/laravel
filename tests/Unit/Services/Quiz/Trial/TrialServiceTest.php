<?php

namespace Tests\Unit\Services\Quiz\Trial;

use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Modifiers\Quiz\Facades\TrialModifiersFacadeInterface;
use App\Support\Facades\Queries\Quiz\TrialQueriesFacadeInterface;
use App\Services\Quiz\Trial\TrialService;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;
use Tests\Unit\Services\Quiz\TrialTestCase;

class TrialServiceTest extends TrialTestCase
{
    private TrialModifiersFacadeInterface $trialModifiers;
    private TrialQueriesFacadeInterface $trialQueries;
    private TrialService $trialService;
    private int $quizId = 3;
    private int $trialId = 12;

    public function test_success_getQuizzes(): void
    {
        $this->trialQueries->expects($this->once())
                ->method('getListQuizzesForTrials');
        
        $this->assertInstanceOf(QuizCollection::class, $this->trialService->getQuizzes());
    }

    public function test_success_getQuiz(): void
    {
        $user = new User();
        $quiz = $this->factoryQuiz();
        
        $this->trialQueries->expects($this->once())
                ->method('getQuizForTrial')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($user))
                ->willReturn(true);
        
        $this->assertInstanceOf(Quiz::class, $this->trialService->getQuiz($user, $this->quizId));
        $this->assertTrue($quiz->isActiveTrial);
    }

    public function test_success_startTrial(): void
    {
        $nQuizItems = 5;
        
        $user = new User();
        $quizItems = $this->factoryQuizItems($nQuizItems);
        $quiz = $this->factoryQuizWithQuizItems($quizItems);
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($user))
                ->willReturn(false);
        
        $this->trialQueries->expects($this->once())
                ->method('getQuizForTrialWithQuizItems')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->trialModifiers->expects($this->once())
                ->method('insertInTrialTableGetId')
                ->with($this->identicalTo($user), $this->identicalTo($quiz))
                ->willReturn($this->trialId);
        
        $this->trialModifiers->expects($this->exactly($nQuizItems))
                ->method('saveInTrialAnswerTable');
        
        $this->assertNull($this->trialService->startTrial($user, $this->quizId));
    }

    public function test_fail_startTrial(): void
    {
        $user = new User();
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($user))
                ->willReturn(true);
        
        $this->trialQueries->expects($this->never())
                ->method('getQuizForTrialWithQuizItems');
        
        $this->trialModifiers->expects($this->never())
                ->method('insertInTrialTableGetId');
        
        $this->trialModifiers->expects($this->never())
                ->method('saveInTrialAnswerTable');
        
        $this->trialService->startTrial($user, $this->quizId);
    }

    public function test_success_getActiveTrial(): void
    {
        $user = new User();
        
        $this->trialQueries->expects($this->once())
                ->method('getActiveTrialByUserWithAnswers')
                ->with($this->identicalTo($user));
        
        $this->assertInstanceOf(Trial::class, $this->trialService->getActiveTrial($user));
    }

    public function test_success_getListQuizItemsForActiveTrial(): void
    {
        $this->trialQueries->expects($this->once())
                ->method('getListQuizItemsForActiveTrial')
                ->with($this->identicalTo($this->quizId));
        
        $this->assertInstanceOf(QuizItemCollection::class, $this->trialService->getListQuizItemsForActiveTrial($this->quizId));
    }

    public function test_success_getTrialsForUserResults(): void
    {
        $user = new User();
        
        $this->trialQueries->expects($this->once())
                ->method('getListByUserForResults')
                ->with($this->identicalTo($user));
        
        $this->assertInstanceOf(TrialCollection::class, $this->trialService->getTrialsForUserResults($user));
    }

    public function test_success_chooseAnswer(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getQuizAnswerDto($user, $id, $quizAnswerId);
        
        $trial = $this->factoryTrial();
        $trial->start_at = Carbon::now();
        $trial->lead_time = 5;
        
        $answer = $this->factoryQuizAnswer();
        $trialAnswer = $this->factoryTrialAnswer();
        $trialAnswer->quiz_answer_id = $dto->quiz_answer_id;
        $trialAnswer->answer = $answer->description;
        $trialAnswer->is_correct = $answer->is_correct;
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($dto->user))
                ->willReturn(true);
        
        $this->trialQueries->expects($this->once())
                ->method('getActiveTrialByUserWithAnswers')
                ->with($this->identicalTo($dto->user))
                ->willReturn($trial);
        
        $this->trialQueries->expects($this->once())
                ->method('getTrialAnswerTableRow')
                ->with($dto->id)
                ->willReturn($trialAnswer);
        
        $this->trialQueries->expects($this->once())
                ->method('getQuizAnswerTableRow')
                ->with($dto->quiz_answer_id)
                ->willReturn($answer);
        
        $this->trialModifiers->expects($this->once())
                ->method('saveInTrialAnswerTable')
                ->with($trialAnswer);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_fail_chooseAnswer_not_active_trial(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getQuizAnswerDto($user, $id, $quizAnswerId);
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($dto->user))
                ->willReturn(false);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_fail_chooseAnswer_time_is_up(): void
    {
        $user = new User();
        $id = 1;
        $quizAnswerId = 4;
        $dto = $this->getQuizAnswerDto($user, $id, $quizAnswerId);
        
        $trial = $this->factoryTrial();
        $trial->start_at = Carbon::now();
        // Отрицательные минуты приведут к возврату отрицательного числа функцией $trial->getTimeUntilQuizEnd()
        $trial->lead_time = -5;
        
        $this->expectException(DatabaseException::class);
        
        $this->trialQueries->expects($this->once())
                ->method('existsActiveTrialByUser')
                ->with($this->identicalTo($dto->user))
                ->willReturn(true);
        
        $this->trialQueries->expects($this->once())
                ->method('getActiveTrialByUserWithAnswers')
                ->with($this->identicalTo($dto->user))
                ->willReturn($trial);
        
        $this->assertNull($this->trialService->chooseAnswer($dto));
    }

    public function test_success_completeTrial(): void
    {
        $user = new User();
        $trial = $this->factoryTrialWithAnswers(20);
        
        $this->trialQueries->expects($this->once())
                ->method('getActiveTrialByUserWithAnswers')
                ->with($this->identicalTo($user))
                ->willReturn($trial);
        
        $this->trialModifiers->expects($this->once())
                ->method('saveInTrialTable')
                ->with($this->identicalTo($trial));
        
        $this->assertNull($this->trialService->completeTrial($user));
    }
    
    protected function setUp(): void
    {
        $this->trialModifiers = $this->createMock(TrialModifiersFacadeInterface::class);
        $this->trialQueries = $this->createMock(TrialQueriesFacadeInterface::class);
        
        $this->trialService = new TrialService($this->trialModifiers, $this->trialQueries);
    }
}
