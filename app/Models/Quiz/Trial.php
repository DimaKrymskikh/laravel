<?php

namespace App\Models\Quiz;

use App\Models\User;
use App\Models\Quiz\TrialAnswer;
use App\Support\Collections\Quiz\TrialCollection;
use Carbon\Carbon;
use Database\Factories\Quiz\TrialFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Результаты, полученные пользователями за прохождение опросов.
 * 
 * @property int $id Первичный ключ таблицы 'quiz.trials'.
 * @property int $user_id id пользователя (ссылка на person.users.id)
 * @property int $quiz_id id опроса (ссылка на quiz.quizzes.id)
 * @property string $start_at Время начала опроса.
 * @property string|null $end_at Время окончания опроса.
 * @property string|null $grade Оценка, полученная пользователем.
 * @property int $total_quiz_items Всего вопросов в опросе.
 * @property int $correct_answers_number Число правильных ответов.
 * @property string $title Название опроса (на момент испытания).
 * @property int $lead_time Время выполнения опроса в минутах (на момент испытания).
 */
class Trial extends Model
{
    use HasFactory;
    
    protected $table = 'quiz.trials';
    
    public $timestamps = false;
    
    protected $casts = [
        'start_at' => 'immutable_datetime:d.m.Y',
    ];
    
    protected static function newFactory()
    {
        return TrialFactory::new();
    }
    
    public function newCollection(array $models = []): TrialCollection
    {
        return new TrialCollection($models);
    }
    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }
    
    public function answers(): HasMany
    {
        return $this->hasMany(TrialAnswer::class);
    }
    
    /**
     * Возвращает секунды, оставшиеся до окончания опроса пользователя
     * 
     * @return int
     */
    public function getSecondsUntilQuizEnd(): int
    {
        return Carbon::parse($this->start_at)->timestamp + ($this->lead_time * 60) - Carbon::now()->timestamp;
    }
}
