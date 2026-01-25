<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddCityInWeatherList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    /**
     * Создаёт событие после добавления города в список просмотра погоды пользователя.
     * 
     * @param int $userId
     * @param string $cityName
     */
    public function __construct(
            private int $userId,
            private string $cityName,
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
            new PrivateChannel("auth.{$this->userId}"),
        ];
    }
    
    public function broadcastWith(): array
    {
        return ['message' => "Вы добавили город $this->cityName в список просмотра погоды"];
    }
}
