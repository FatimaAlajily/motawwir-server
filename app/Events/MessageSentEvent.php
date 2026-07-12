<?php

namespace App\Events;

use App\Http\Resources\MessageResource;
use App\Http\Resources\PostUserResource;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSentEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    

    public function __construct(public Message $message)
    {   
        $this->message->load('user');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('global-chat'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }
    
    public function broadcastWith(): array
    {
        // return [
        //     'id' => $this->message->id,
        //     'message' => $this->message->message,
        //     'user' => new PostUserResource($this->message->user),
        //     'created_at' => $this->message->created_at->toDateTimeString(),
        // ];
            return (new MessageResource($this->message))->resolve();
    }
}
