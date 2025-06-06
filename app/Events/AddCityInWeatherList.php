<?php

namespace App\Events;

use App\Services\Database\Thesaurus\CityService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddCityInWeatherList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct
    (
            private int $userId,
            private int $cityId,
            private CityService $cityService,
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
        $cityName = $this->cityService->getCityById($this->cityId)->name;
        
        return ['message' => "Вы добавили город $cityName в список просмотра погоды"];
    }
}
