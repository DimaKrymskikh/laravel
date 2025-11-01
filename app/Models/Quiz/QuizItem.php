<?php

namespace App\Models\Quiz;

use App\Models\Quiz\Quiz;
use App\Models\Quiz\QuizAnswer;
use App\Support\Collections\Quiz\QuizItemCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuizItem extends Model
{
    use HasFactory;
    
    public const MINIMUM_ANSWERS_FOR_READY_STATUS = 3;
    
    protected $table = 'quiz.quiz_items';
    
    public $timestamps = false;
    
    protected $with = ['quiz'];
    
    public function newCollection(array $models = []): QuizItemCollection
    {
        return new QuizItemCollection($models);
    }
    
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
    
    public function answers(): HasMany
    {
        return $this->hasMany(QuizAnswer::class);
    }
}
