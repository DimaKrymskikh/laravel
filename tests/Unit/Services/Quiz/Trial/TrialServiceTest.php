<?php

namespace Tests\Unit\Services\Quiz\Trial;

use App\Exceptions\DatabaseException;
use App\Models\User;
use App\Models\Quiz\Quiz;
use App\Models\Quiz\Trial;
use App\Modifiers\Quiz\TrialAnswerModifiers;
use App\Modifiers\Quiz\TrialModifiers;
use App\Queries\Quiz\QuizAnswerQueries;
use App\Queries\Quiz\QuizItemQueries;
use App\Queries\Quiz\QuizQueries;
use App\Queries\Quiz\TrialAnswerQueries;
use App\Queries\Quiz\TrialQueries;
use App\Services\Quiz\Trial\TrialService;
use App\Services\ServiceManagerInterface;
use App\Support\Collections\Quiz\QuizCollection;
use App\Support\Collections\Quiz\QuizItemCollection;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;
use Tests\Unit\Services\Quiz\TrialTestCase;

class TrialServiceTest extends TrialTestCase
{
    private ServiceManagerInterface $serviceManager;
    private TrialAnswerModifiers $trialAnswerModifiers;
    private TrialModifiers $trialModifiers;
    private QuizAnswerQueries $quizAnswerQueries;
    private QuizItemQueries $quizItemQueries;
    private QuizQueries $quizQueries;
    private TrialAnswerQueries $trialAnswerQueries;
    private TrialQueries $trialQueries;
    private TrialService $trialService;
    private int $quizId = 3;
    private int $trialId = 12;

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
        
        $this->quizQueries->expects($this->once())
                ->method('getByIdForTrialWithQuizItems')
                ->with($this->quizId)
                ->willReturn($quiz);
        
        $this->trialModifiers->expects($this->once())
                ->method('insertGetId')
                ->with($this->identicalTo($user), $this->identicalTo($quiz))
                ->willReturn($this->trialId);
        
        $this->trialAnswerModifiers->expects($this->exactly($nQuizItems))
                ->method('save');
        
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
        $user = new User();
        
        $this->trialQueries->expects($this->once())
                ->method('getActiveTrialByUserWithAnswers')
                ->with($this->identicalTo($user));
        
        $this->assertInstanceOf(Trial::class, $this->trialService->getActiveTrial($user));
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
        
        $this->trialAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($dto->id)
                ->willReturn($trialAnswer);
        
        $this->quizAnswerQueries->expects($this->once())
                ->method('getById')
                ->with($dto->quiz_answer_id)
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
                ->method('save')
                ->with($this->identicalTo($trial));
        
        $this->assertNull($this->trialService->completeTrial($user));
    }
    
    protected function setUp(): void
    {
        $this->trialAnswerModifiers = $this->createMock(TrialAnswerModifiers::class);
        $this->trialModifiers = $this->createMock(TrialModifiers::class);
        $this->quizAnswerQueries = $this->createMock(QuizAnswerQueries::class);
        $this->quizItemQueries = $this->createMock(QuizItemQueries::class);
        $this->quizQueries = $this->createMock(QuizQueries::class);
        $this->trialAnswerQueries = $this->createMock(TrialAnswerQueries::class);
        $this->trialQueries = $this->createMock(TrialQueries::class);
        
        $this->serviceManager = $this->createStub(ServiceManagerInterface::class);
        $this->serviceManager->method('getQueriesOrModifiers')
                ->willreturn(
                        $this->trialAnswerModifiers, $this->trialModifiers, 
                        $this->quizAnswerQueries, $this->quizItemQueries, $this->quizQueries,
                        $this->trialAnswerQueries, $this->trialQueries
                    );
        
        $this->trialService = new TrialService($this->serviceManager);
    }
}
