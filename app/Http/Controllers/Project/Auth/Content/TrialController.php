<?php

namespace App\Http\Controllers\Project\Auth\Content;

use App\Http\Controllers\Controller;
use App\Services\Quiz\Trial\DataTransferObjects\AnswerDto;
use App\Services\Quiz\Trial\TrialService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TrialController extends Controller
{
    public function __construct(
            private TrialService $trialService
    ) {
    }
    
    /**
     * Отрисовывает страницу со списком опросов, которые доступны пользователю
     * 
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Auth/Trials/Trials', [
            'user' => $request->user(),
            'quizzes' => $this->trialService->getQuizzes(),
        ]);
    }
    
    /**
     * Отрисовывает страницу, на которой пользователь запускает опрос
     * 
     * @param Request $request
     * @param int $quizId
     * @return Response
     */
    public function show(Request $request, int $quizId): Response
    {
        $user = $request->user();
        
        return Inertia::render('Auth/Trials/TrialStart', [
            'user' => $user,
            'quiz' => $this->trialService->getQuiz($user, $quizId),
        ]);
    }
    
    /**
     * Создаёт опрос для пользователя
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function start(Request $request): RedirectResponse
    {
        $user = $request->user();
        $quizId = $request->input('quiz_id');
        
        DB::transaction(function() use ($user, $quizId) {
            $this->trialService->startTrial($user, $quizId);
        });
        
        return redirect()->route('trials.trial');
    }
    
    /**
     * Отрисовывает страницу опроса, который проходит пользователь
     * 
     * @param Request $request
     * @return Response
     */
    public function showTrial(Request $request): Response
    {
        $user = $request->user();
        $trial = $this->trialService->getActiveTrial($user);
        
        return Inertia::render('Auth/Trials/TrialPage', [
            'user' => $user,
            'trial' => $trial,
            'quizItems' => $this->trialService->getListQuizItemsForActiveTrial($trial->quiz_id),
        ]);
    }
    
    /**
     * Отрисовывает страницу завершённых опросов пользователя с результатами
     * 
     * @param Request $request
     * @return Response
     */
    public function getResults(Request $request): Response
    {
        $user = $request->user();
        
        return Inertia::render('Auth/Account/TrialResults', [
            'user' => $user,
            'trials' => $this->trialService->getTrialsForUserResults($user),
        ]);
    }
    
    /**
     * Записывает ответ на вопрос, который дал пользователь, в базу данных
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function chooseAnswer(Request $request): RedirectResponse
    {
        $dto = new AnswerDto($request->user(), $request->id, $request->quiz_answer_id);
        
        DB::transaction(function() use ($dto) {
            $this->trialService->chooseAnswer($dto);
        });
        
        return redirect()->route('trials.trial');
    }
    
    /**
     * Завершает текущий опрос пользователя
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function complete(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        DB::transaction(function() use ($user) {
            $this->trialService->completeTrial($user);
        });
        
        return redirect()->route('trials.results');
    }
}
