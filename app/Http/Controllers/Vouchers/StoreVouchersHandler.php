<?php

namespace App\Http\Controllers\Vouchers;

use App\Http\Resources\Vouchers\VoucherResource;
use App\Services\VoucherService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class StoreVouchersHandler
{
    public function __construct(private readonly VoucherService $voucherService)
    {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $xmlFiles = $request->file('files');
    
            if (!is_array($xmlFiles)) {
                $xmlFiles = [$xmlFiles];
            }

            $xmlContents = [];
            foreach ($xmlFiles as $xmlFile) {
                $xmlContents[] = file_get_contents($xmlFile->getRealPath());
            }

            $user = auth()->user();
            $this->voucherService->storeVouchersFromXmlContents($xmlContents, $user);

            return response([
                'message' => "Se subieron los archivos",
            ], 201);
        } catch (Exception $exception) {
            Log::error('Error al procesar la solicitud de vouchers', [
                'error_message' => $exception->getMessage(),
                'error_file' => $exception->getFile(),
                'error_line' => $exception->getLine(),
                'stack_trace' => $exception->getTraceAsString(),
            ]);
            return response([
                'message' => $exception->getMessage(),
            ], 400);
        }
    }
}
