<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusNotificationEvent implements ShouldBroadcast
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
        ];
    }
  
    public function broadcastAs()
    {
        return 'status';
    }
}
