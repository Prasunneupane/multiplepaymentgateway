<?php

use Illuminate\Support\Facades\Route;



// routes/web.php (Updated routes)

use App\Http\Controllers\PaymentController;

Route::get('/', [PaymentController::class, 'index']);
Route::post('/payment/initiate', [PaymentController::class, 'initiate'])->name('payment.initiate');
Route::get('/payment/verify/{gateway}', [PaymentController::class, 'verify'])->name('payment.verify');
Route::get('/payment/failure', [PaymentController::class, 'failure'])->name('payment.failure');
