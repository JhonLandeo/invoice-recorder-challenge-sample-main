<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VoucherReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */

    public $successfulVouchers;
    public $failedVouchers;
    public function __construct(array $successfulVouchers, array $failedVouchers)
    {
        $this->successfulVouchers = $successfulVouchers;
        $this->failedVouchers = $failedVouchers;
    }

    public function build()
    {
        return $this->view('emails.vouchers_report')
            ->subject('Reporte de Comprobantes Procesados')
            ->with([
                'successfulVouchers' => $this->successfulVouchers,
                'failedVouchers' => $this->failedVouchers,
            ]);
    }
}
