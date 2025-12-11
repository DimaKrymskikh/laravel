<?php

namespace App\Models\Quiz;

use App\Models\Quiz\QuizItem;
use App\Support\Collections\Quiz\QuizAnswerCollection;
use Database\Factories\Quiz\QuizAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ответы вопросов.
 * 
 * @property int $id Первичный ключ таблицы 'quiz.quiz_answers'.
 * @property string $description Содержание ответа.
 * @property bool $is_correct Является ли ответ правильным. true - ответ правильный.
 * @property int $quiz_item_id id вопроса (ссылка на quiz.quiz_items.id)
 * @property string|null $priority Приоритет ответа на вопрос
 * @property string $created_at
 * @property string $updated_at
 */
class QuizAnswer extends Model
{
    use HasFactory;
    
    public const EDITABLE_FIELDS = ['description', 'is_correct', 'priority'];

    protected $table = 'quiz.quiz_answers';
    
    public $timestamps = false;
    
    protected $with = ['quizItem'];
    
    protected static function newFactory()
    {
        return QuizAnswerFactory::new();
    }
    
    public function newCollection(array $models = []): QuizAnswerCollection
    {
        return new QuizAnswerCollection($models);
    }
    
    public function quizItem(): BelongsTo
    {
        return $this->belongsTo(QuizItem::class);
    }
}
