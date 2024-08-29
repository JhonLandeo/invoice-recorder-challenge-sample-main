<?php

namespace App\Jobs;

use App\Mail\VoucherReportMail;
use App\Mail\VouchersCreatedMail;
use App\Models\Currency;
use App\Models\TypeDocument;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherLine;
use App\Services\VoucherService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use SimpleXMLElement;

class ProcessVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $xmlContents;
    protected $user;
    protected $voucherService;

    /**
     * Create a new job instance.
     */
    public function __construct(array $xmlContents, User $user, VoucherService $voucherService)
    {
        $this->xmlContents = $xmlContents;
        $this->user = $user;
        $this->voucherService = $voucherService;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $successfulVouchers = [];
        $failedVouchers = [];
        foreach ($this->xmlContents as $xmlContent) {
            try {
                $voucher = $this->voucherService->storeVoucherFromXmlContent($xmlContent, $this->user);
                $successfulVouchers[] = $voucher;
            } catch (ModelNotFoundException $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'reason' => 'Modelo no encontrado: ' . $e->getMessage(),
                ];
            } catch (ValidationException $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'reason' => 'Error de validación: ' . implode(', ', $e->errors()),
                ];
            } catch (QueryException $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'reason' => 'Error de base de datos: ' . $e->getMessage(),
                ];
            } catch (Exception $e) {
                $failedVouchers[] = [
                    'xmlContent' => $xmlContent,
                    'reason' => 'Error general: ' . $e->getMessage(),
                ];
            }
        }
        Mail::to($this->user->email)->send(new VoucherReportMail($successfulVouchers, $failedVouchers));
    }

}
