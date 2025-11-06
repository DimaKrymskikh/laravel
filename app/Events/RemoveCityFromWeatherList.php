<?php

namespace App\Events;

use App\Services\Database\Person\Dto\UserCityDto;
use App\Services\Database\Thesaurus\CityService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveCityFromWeatherList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private int $userId;
    private int $cityId;
    private CityService $cityService;
    
    /**
     * Create a new event instance.
     */
    public function __construct(UserCityDto $dto, CityService $cityService)
    {
        $this->userId = $dto->userId;
        $this->cityId = $dto->cityId;
        $this->cityService = $cityService;
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
        
        return ['message' => "Вы удалили город $cityName из списка просмотра погоды"];
    }
}
