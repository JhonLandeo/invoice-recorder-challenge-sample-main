<?php

namespace App\Services;

use App\Models\Voucher;
use Illuminate\Support\Facades\Log;

class FinanceService
{
    public function getTotalByCurrency()
    {

        $totalSoles = Voucher::where('currency_id', 115)->sum('total_amount');
        $totalDollars = Voucher::where('currency_id', 151)->sum('total_amount');

        return response()->json([
            'total_soles' => $totalSoles,
            'total_dollars' => $totalDollars
        ], 200);
    }
}
