<?php

namespace App\Http\Controllers\Project\Admin\Content\Quizzes;

use App\Http\Controllers\Controller;
use App\Services\Quiz\Admin\AdminQuizItemService;
use App\Services\Quiz\Admin\AdminQuizService;
use App\Services\Quiz\Enums\ValueObjects\QuizItemStatusValue;
use App\Services\Quiz\Fields\DataTransferObjects\QuizItemDto;
use App\ValueObjects\ScalarTypes\SimpleStringValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuizItemController extends Controller
{
    public function __construct(
            private AdminQuizItemService $adminQuizItemService,
            private AdminQuizService $adminQuizService,
    ) {
    }

    /**
     * Создаёт новый вопрос для опроса с id = $quizId
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $quizId = $request->input('quiz_id');
        
        $dto = new QuizItemDto(
                $quizId,
                SimpleStringValue::create($request->input('description'))
            );
        
        DB::transaction(function() use ($dto) {
            $this->adminQuizItemService->create($dto);
            $this->adminQuizService->changeStatus($dto->quizId);
        });
        
        return redirect()->route('admin.quizzes.show', [
            'quiz' => $quizId
        ]);
    }

    /**
     * Отрисовывает карточку вопроса с таблицей ответов
     * 
     * @param int $id - id вопроса
     * @return Response
     */
    public function show(int $id): Response
    {
        return Inertia::render('Admin/Quizzes/QuizItemCard', [
            'quizItem' => $this->adminQuizItemService->getQuizItemByIdWithAnswers($id),
        ]);
    }

    /**
     * Изменяет текст вопроса
     * 
     * @param Request $request
     * @param int $id - id вопроса
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $quizItem = $this->adminQuizItemService->update(
                SimpleStringValue::create($request->input('description')),
                $id
            );
        
        return redirect()->route('admin.quizzes.show', [
            'quiz' => $quizItem->quiz->id,
        ]);
    }

    /**
     * Задаёт статус вопроса ручным управлением
     * 
     * @param Request $request
     * @param int $id - id вопроса
     * @return RedirectResponse
     */
    public function setFinalStatus(Request $request, int $id): RedirectResponse
    {
        $status = QuizItemStatusValue::create($request->input('status'));
        
        $quizItem = DB::transaction(function() use ($id, $status) {
            $quizItem = $this->adminQuizItemService->setFinalStatus($id, $status);
            $this->adminQuizService->changeStatus($quizItem->quiz->id);
            return $quizItem;
        });
        
        return redirect()->route('admin.quizzes.show', [
            'quiz' => $quizItem->quiz->id,
        ]);
    }

    /**
     * Отменяет статус вопроса ручным управлением
     * 
     * @param int $id - id вопроса
     * @return RedirectResponse
     */
    public function cancelFinalStatus(int $id): RedirectResponse
    {
        $quizItem = DB::transaction(function() use ($id) {
            $quizItem = $this->adminQuizItemService->cancelFinalStatus($id);
            $this->adminQuizService->changeStatus($quizItem->quiz->id);
            return $quizItem;
        });
        
        return redirect()->route('admin.quizzes.show', [
            'quiz' => $quizItem->quiz->id,
        ]);
    }
}
