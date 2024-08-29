<?php

use App\Http\Controllers\Finance\GetTotalByCurrency;
use Illuminate\Support\Facades\Route;

Route::prefix('finance')->group(
    function () {
        Route::get('/total-by-currency', GetTotalByCurrency::class);
    }
);
