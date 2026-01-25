<?php

namespace App\Events;

use App\ValueObjects\Broadcast\Weather\CurrentWeatherData;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshCityWeather implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Создаёт событие после обновления записи в таблице 'open_weather.weather'.
     * 
     * @param int $userId
     * @param Weather $weather
     */
    public function __construct(
            private int $userId,
            private CurrentWeatherData $weather
    ) {
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
        return [
            'weather' => $this->weather->data,
        ];
    }
}
