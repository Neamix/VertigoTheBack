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
  
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('company.'.$this->user['company_id']),
        ];
    }
  
    public function broadcastAs()
    {
        return 'member-suspend-toggle';
    }
}
