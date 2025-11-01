<?php

namespace App\Models\Quiz;

use App\Support\Collections\Quiz\QuizCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;
    
    public const EDITABLE_FIELDS = ['title', 'description', 'lead_time'];
    
    // Минимальное число вопросов для статуса 'готов'
    public const MINIMUM_ITEMS_FOR_READY_STATUS = 5;
    // Максимальная продолжительность опроса одни сутки
    public const MAX_LAED_TIME = 60 * 24;
    // Время опроса по умолчанию
    public const DEFAULT_LAED_TIME = 20;
    
    protected $table = 'quiz.quizzes';
    
    public $timestamps = false;
    
    public function newCollection(array $models = []): QuizCollection
    {
        return new QuizCollection($models);
    }
    
    public function quizItems(): HasMany
    {
        return $this->hasMany(QuizItem::class);
    }
}
