<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;
use App\Http\Controllers\MpesaCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// STK Push Callback (Daraja calls this after STK payment)
Route::post('/mpesa/stk-callback', [MpesaController::class, 'handleCallback']);

// C2B Confirmation Callback (when someone pays directly to Paybill)
Route::post('/mpesa/c2b/confirmation', [MpesaCallbackController::class, 'handleC2BConfirmation']);

// C2B Validation Callback (optional - if enabled in Daraja)
Route::post('/mpesa/c2b/validation', [MpesaCallbackController::class, 'handleC2BValidation']);

//  Add this STK Push trigger route
Route::post('/mpesa/stk-push', [MpesaController::class, 'stkPush']);

