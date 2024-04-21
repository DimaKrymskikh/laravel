<?php

namespace App\Events;

use App\Models\Person\UserCity;
use App\Models\Thesaurus\City;
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
            private int $user_id,
            private int $city_id,
    )
    {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("auth.{$this->user_id}"),
        ];
    }
    
    public function broadcastWith(): array
    {
        $cityName = City::find($this->city_id)->name;
        
        return ['message' => trans('event_messages.add_city_in_weather_list', ['cityName' => $cityName])];
    }
}
