<?php

namespace App\Mail;

use App\Models\Work;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public Work $work;
    public string $newStatusName;
    public string $statusEmoji;
    public string $trackingUrl;

    public function __construct(Work $work)
    {
        $this->work = $work;
        $this->newStatusName = $work->status_name;
        $this->statusEmoji = $work->status_emoji;
        $this->trackingUrl = route('tracking', $work->tracking_code);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu pedido {$this->work->tracking_code} ha sido actualizado - Óptica Universo Visual",
        );
    }

    public function build()
    {
        return $this->view('emails.work-status-update');
    }
}
