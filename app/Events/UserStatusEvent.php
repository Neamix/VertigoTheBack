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

    public $status;
  
    public function __construct(array $status)
    {
        $this->status = $status;
    }

    public function broadCastWith(): array 
    {
        return [
            'user_id'      => $this->status['user_id'],
            'company_id'   => $this->status['company_id'],
            'status_id'    => $this->status['status_id'],
            'session'      => [
                'id' => $this->status['session']['id'],
                'start_date' => $this->status['session']['start_date']
            ]
        ];
    }
  
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('company.'.$this->status['company_id']),
            new PresenceChannel('member.'.$this->status['user_id']),
        ];
    }
  
    public function broadcastAs()
    {
        return 'status';
    }
}
