<?php

namespace App\Models\Quiz;

use App\Models\Quiz\QuizAnswer;
use App\Models\Quiz\QuizItem;
use App\Models\Quiz\Trial;
use App\Support\Collections\Quiz\TrialAnswerCollection;
use Database\Factories\Quiz\TrialAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Вопросы и ответы, которые дал пользователь при прохождении опросов.
 * 
 * @property int $id Первичный ключ таблицы 'quiz.trial_answers'.
 * @property int $trial_id id опроса (ссылка на quiz.trials.id).
 * @property int $quiz_item_id id вопроса (ссылка на quiz.quiz_items.id).
 * @property string $question Текст вопроса из таблицы quiz.quiz_items.
 * @property int|null $quiz_answer_id id ответа (ссылка на quiz.quiz_answers.id).
 * @property string|null $answer Ответ на вопрос, который дал пользователь.
 * @property bool|null $is_correct Правильный или нет ответ.
 * @property string|null $priority Приоритет вопроса
 */
class TrialAnswer extends Model
{
    use HasFactory;
    
    protected $table = 'quiz.trial_answers';
    
    public $timestamps = false;
    
    protected static function newFactory()
    {
        return TrialAnswerFactory::new();
    }
    
    public function newCollection(array $models = []): TrialAnswerCollection
    {
        return new TrialAnswerCollection($models);
    }
    
    public function quizAnswer(): BelongsTo
    {
        return $this->belongsTo(QuizAnswer::class);
    }
    
    public function quizItem(): BelongsTo
    {
        return $this->belongsTo(QuizItem::class);
    }
    
    public function trial(): BelongsTo
    {
        return $this->belongsTo(Trial::class);
    }
}
