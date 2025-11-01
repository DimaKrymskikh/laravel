<?php

namespace App\Http\Controllers\Project\Admin\Content\Quizzes;

use App\Http\Controllers\Controller;
use App\Services\Quiz\Admin\AdminQuizAnswerService;
use App\Services\Quiz\Admin\AdminQuizItemService;
use App\Services\Quiz\Admin\AdminQuizService;
use App\Services\Quiz\Fields\DataTransferObjects\QuizAnswerDto;
use App\Services\Quiz\Fields\QuizAnswerField;
use App\ValueObjects\ScalarTypes\BoolValue;
use App\ValueObjects\ScalarTypes\SimpleStringValue;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuizAnswerController extends Controller
{
    public function __construct(
            private AdminQuizAnswerService $adminQuizAnswerService,
            private AdminQuizItemService $adminQuizItemService,
            private AdminQuizService $adminQuizService,
    ) {
    }

    /**
     * Создаёт новый ответ для вопроса с id = $quizItemId
     * 
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $quizItemId = $request->input('quiz_item_id');
        
        $dto = new QuizAnswerDto (
                $quizItemId,
                SimpleStringValue::create($request->input('description')),
                BoolValue::create($request->input('is_correct')),
            );
        
        DB::transaction(function() use ($dto) {
            $this->adminQuizAnswerService->create($dto);
            $this->changeStatuses($dto->quizItemId);
        });
        
        return redirect()->route('admin.quiz_items.show', [
            'quiz_item' => $quizItemId
        ]);
    }

    /**
     * Отрисовывает карточку ответа
     * 
     * @param int $id - id ответа
     * @return Response
     */
    public function show(int $id): Response
    {
        return Inertia::render('Admin/Quizzes/QuizAnswerCard', [
            'quizAnswer' => $this->adminQuizAnswerService->getAnswerCard($id),
        ]);
    }

    /**
     * Изменяет одно поле ответа
     * 
     * @param Request $request
     * @param int $id - id ответа
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $quizAnswerField = QuizAnswerField::create($request->input('field'), $request->input('value'));
        
        $quizAnswer = DB::transaction(function() use ($quizAnswerField, $id) {
            $quizAnswer = $this->adminQuizAnswerService->updateField($id, $quizAnswerField);
            $this->changeStatuses($quizAnswer->quiz_item_id);
            return $quizAnswer;
        });
        
        return redirect()->route('admin.quiz_items.show', [
            'quiz_item' => $quizAnswer->quiz_item_id
        ]);
    }

    /**
     * Удаляет ответ
     * 
     * @param int $id - id ответа
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $quizItemId = DB::transaction(function() use ($id) {
            $quizItemId = $this->adminQuizAnswerService->delete($id);
            $this->changeStatuses($quizItemId);
            return $quizItemId;
        });
        
        return redirect()->route('admin.quiz_items.show', [
            'quiz_item' => $quizItemId
        ]);
    }
    
    private function changeStatuses(int $quizItemId): void
    {
        $quizItem = $this->adminQuizItemService->changeStatus($quizItemId);
        $this->adminQuizService->changeStatus($quizItem->quiz->id);
    }
}
