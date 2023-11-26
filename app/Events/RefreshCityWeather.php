<?php

namespace App\Events;

use App\Contracts\Support\Timezone as TimezoneInterface;
use App\Models\OpenWeather\Weather;
use App\Models\Thesaurus\City;
use Carbon\Carbon;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshCityWeather implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $tzName;

    /**
     * Create a new event instance.
     */
    public function __construct(
            public Weather $weather,
            public int $cityId,
            public int $userId
    )
    {
        $city = City::find($cityId);
        $this->tzName = $city->timezone_id ? $city->timezone->name : TimezoneInterface::DEFAULT_TIMEZONE_NAME;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("auth.$this->userId"),
        ];
    }
    
    public function broadcastWith(): array
    {
        // Устанавливаем временной пояс города
        // (!!! В конструкторе изменение временного пояса работает неправильно, почему-то)
        $this->weather->created_at = Carbon::parse($this->weather->created_at)->setTimezone($this->tzName);
        
        return [
            'weather' => $this->weather,
            'cityId' => $this->cityId,
        ];
    }
}
