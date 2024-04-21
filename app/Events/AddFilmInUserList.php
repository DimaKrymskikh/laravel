<?php

namespace App\Events;

use App\Models\Dvd\Film;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddFilmInUserList implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
            private int $user_id,
            readonly public int $film_id,
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
        $filmTitle = Film::find($this->film_id)->title;
        
        return ['message' => trans('event_messages.add_film_in_user_list', ['filmTitle' => $filmTitle])];
    }
}
