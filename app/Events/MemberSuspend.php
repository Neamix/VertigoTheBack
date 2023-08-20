<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MemberSuspend implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
  
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
    */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->user['user_id'],
            'event'   => $this->user['event']
        ];
    }

  
    public function broadcastOn(): array
    {
        Log::info($this->user);
        return [
            new PresenceChannel('company.'.$this->user['company_id']),
        ];
    }
  
    public function broadcastAs()
    {
        return 'member-suspend';
    }
}
