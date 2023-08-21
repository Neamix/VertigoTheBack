<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SessionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    protected $session;

    public function __construct(array $session)
    {
        $this->session = $session;
    }

    /**
     * Get the data to broadcast.
     *
     * @return array<string, mixed>
    */
    public function broadcastWith(): array
    {
        return [
            'total_session_time' => $this->session['total_session_time'],
            'status_id'  => $this->session['status_id'],
            'company_id' => $this->session['company_id'],
            'month'      => $this->session['month'],
            'event'      => 'close_session'
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
    */
    
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('company.'.$this->session['company_id']),
        ];
    }

    public function broadcastAs()
    {
        return 'session';
    }
}
