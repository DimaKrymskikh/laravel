<?php

namespace App\Models\Quiz;

use App\Models\Quiz\QuizItem;
use App\Support\Collections\Quiz\QuizAnswerCollection;
use Database\Factories\Quiz\QuizAnswerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAnswer extends Model
{
    use HasFactory;
    
    public const EDITABLE_FIELDS = ['description', 'is_correct'];

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
