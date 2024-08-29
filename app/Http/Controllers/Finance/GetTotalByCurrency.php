<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Services\FinanceService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class GetTotalByCurrency extends Controller
{
    public function __construct(private readonly FinanceService $financeService) {}
    public function __invoke()
    {
        try {
            return $this->financeService->getTotalByCurrency();
        } catch (Exception $exception) {
            Log::error('Error al procesar la solicitud de vouchers', [
                'error_message' => $exception->getMessage(),
                'error_file' => $exception->getFile(),
                'error_line' => $exception->getLine(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
            return response([
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
