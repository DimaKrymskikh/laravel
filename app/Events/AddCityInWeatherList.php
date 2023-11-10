<?php

namespace App\Events;

use App\Models\ModelsFields;
use App\Models\Person\UserCity;
use App\Models\Thesaurus\City;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddCityInWeatherList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels, ModelsFields;
    
    public string $message;

    /**
     * Create a new event instance.
     */
    public function __construct
    (
            public UserCity $userCity
    )
    {
        $this->message = 'Вы добавили город '.$this->getModelField(City::class, 'name', $userCity->city_id).' в список просмотра погоды';    
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
