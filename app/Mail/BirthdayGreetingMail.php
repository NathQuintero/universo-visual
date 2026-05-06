<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BirthdayGreetingMail extends Mailable
{
    use Queueable, SerializesModels;

    public Client $client;
    public string $discountExpiry;

    public function __construct(Client $client)
    {
        $this->client = $client;
        // Vigencia: desde el cumpleaños hasta 10 días después
        $birthday = $client->birth_date->copy()->year(now()->year);
        $this->discountExpiry = $birthday->addDays(10)->translatedFormat('d \\d\\e F \\d\\e Y');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Feliz Cumpleaños {$this->client->first_name}! - Óptica Universo Visual",
        );
    }

    public function build()
    {
        $mail = $this->view('emails.birthday-greeting');

        // Adjuntar tarjeta de cumpleaños
        $cardPath = public_path('images/cumple_universo.png');
        if (file_exists($cardPath)) {
            $mail->attach($cardPath, [
                'as' => 'Feliz_Cumpleanos_Universo_Visual.png',
                'mime' => 'image/png',
            ]);
        }

        return $mail;
    }
}
