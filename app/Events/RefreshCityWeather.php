<?php

namespace App\Events;

use App\Models\Thesaurus\City;
use App\Repositories\OpenWeather\WeatherRepository;
use App\Services\Database\Thesaurus\TimezoneService;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RefreshCityWeather implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    private City $city;
    private int $userId;
    private TimezoneService $timezoneService;

    /**
     * Create a new event instance.
     */
    public function __construct(int $cityId, int $userId, TimezoneService $timezoneService)
    {
        $this->city = City::find($cityId);
        $this->userId = $userId;
        $this->timezoneService = $timezoneService;
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
            'weather' => (new WeatherRepository($this->timezoneService))->getLatestWeatherForOneCity($this->city),
            'cityId' => $this->city->id,
        ];
    }
}
