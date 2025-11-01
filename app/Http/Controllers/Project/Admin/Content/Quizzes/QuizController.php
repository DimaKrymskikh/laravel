<?php

namespace App\Http\Controllers\Project\Admin\Content\Quizzes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quiz\StoreQuizRequest;
use App\Services\Quiz\Fields\QuizField;
use App\Services\Quiz\Admin\AdminQuizService;
use App\Services\Quiz\Enums\ValueObjects\QuizStatusValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class QuizController extends Controller
{
    public function __construct(
            private AdminQuizService $adminQuizService,
    ) {
    }
    
    /**
     * Отрисовывает таблицу опросов
     * 
     * @return Response
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Quizzes/Quizzes', [
            'quizzes' => $this->adminQuizService->getList(),
        ]);
    }

    /**
     * Создаёт новый опрос
     * 
     * @param StoreQuizRequest $request
     * @return RedirectResponse
     */
    public function store(StoreQuizRequest $request): RedirectResponse
    {
        $this->adminQuizService->create($request->getDto());
        
        return redirect()->route('admin.quizzes.index');
    }

    /**
     * Отрисовывает карточку опроса с таблицей вопросов
     * 
     * @param int $id - id опроса
     * @return Response
     */
    public function show(int $id): Response
    {
        return Inertia::render('Admin/Quizzes/QuizCard', [
            'quiz' => $this->adminQuizService->getQuizByIdWithQuizItems($id),
        ]);
    }

    /**
     * Изменяет одно поле опроса
     * 
     * @param Request $request
     * @param int $id - id опроса
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $quizField = QuizField::create($request->input('field'), $request->input('value'));
        $this->adminQuizService->updateField($id, $quizField);
        
        return redirect()->route('admin.quizzes.index');
    }

    /**
     * Задаёт статус опроса ручным управлением
     * 
     * @param Request $request
     * @param int $id - id опроса
     * @return RedirectResponse
     */
    public function setFinalStatus(Request $request, int $id): RedirectResponse
    {
        $this->adminQuizService->setFinalStatus(QuizStatusValue::create($request->input('status')), $id);
        
        return redirect()->route('admin.quizzes.index');
    }
    
    /**
     * Отменяет статус опроса ручным управлением
     * 
     * @param int $id - id опроса
     * @return RedirectResponse
     */
    public function cancelFinalStatus(int $id): RedirectResponse
    {
        $this->adminQuizService->cancelFinalStatus($id);
        
        return redirect()->route('admin.quizzes.index');
    }
}
