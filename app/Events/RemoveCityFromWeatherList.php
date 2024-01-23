<?php

namespace App\Events;

use App\Models\Person\UserCity;
use App\Models\Thesaurus\City;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemoveCityFromWeatherList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct
    (
            public UserCity $userCity
    )
    {
        $nameCity = City::find($userCity->city_id)->name;
        $this->message = "Вы удалили город $nameCity из списока просмотра погоды";    
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("auth.{$this->userCity->user_id}"),
        ];
    }
    
    public function broadcastWith(): array
    {
        return ['message' => $this->message];
    }
}
