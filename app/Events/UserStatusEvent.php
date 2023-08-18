<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserStatusEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
  
    public function __construct(array $user)
    {
        $this->user = $user;
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
        return 'status';
    }
}
