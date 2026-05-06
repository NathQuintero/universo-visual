<?php

namespace App\Mail;

use App\Models\Work;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WorkReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public Work $work;

    public function __construct(Work $work)
    {
        $this->work = $work;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Recibo de tu pedido {$this->work->tracking_code} - Óptica Universo Visual",
        );
    }

    public function build()
    {
        $work = $this->work;
        $work->load(['client', 'laboratory', 'formula', 'payments.user', 'statusChanges.user', 'user']);

        $businessName = Setting::getValue('business_name', 'Óptica Universo Visual');
        $businessPhone = Setting::getValue('business_phone', '6071234567');
        $businessAddress = Setting::getValue('business_address', 'C.C. La Isla, Local 205, Bucaramanga');
        $trackingUrl = route('tracking', $work->tracking_code);

        // Generar PDF
        $pdf = Pdf::loadView('pdf.work', compact('work', 'businessName', 'businessPhone', 'businessAddress'));
        $pdf->setPaper('letter', 'portrait');

        return $this->view('emails.work-receipt', compact('work', 'businessName', 'businessAddress', 'trackingUrl'))
                    ->attachData(
                        $pdf->output(),
                        "Recibo_{$work->tracking_code}.pdf",
                        ['mime' => 'application/pdf']
                    );
    }
}
