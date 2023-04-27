<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DefaultEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@arkticsolutions.com', 'Vertigo | Unleash your path')
            ->replyTo($this->data['replay_to'] ?? null)
            ->subject($this->data['subject'] ?? 'No Subject')
            ->view($this->data['view'], ['data' => $this->data]);
    }
}
