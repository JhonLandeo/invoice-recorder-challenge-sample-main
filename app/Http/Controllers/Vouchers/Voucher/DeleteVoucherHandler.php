<?php

namespace App\Http\Controllers\Vouchers\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class DeleteVoucherHandler
{
    public function __construct(private readonly VoucherService $voucherService) {}

    public function __invoke($id): Response
    {
        try {
            $this->voucherService->deleteVoucher($id);

            return response([
                'message' => 'Voucher deleted successfully',
            ], 200);
        } catch (\Exception $exception) {
            Log::error('Error al procesar la solicitud de vouchers', [
                'error_message' => $exception->getMessage(),
                'error_file' => $exception->getFile(),
                'error_line' => $exception->getLine(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
            return response([
                'error' => 'Failed to delete voucher',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
