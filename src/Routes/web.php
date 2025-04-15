<?php

use Illuminate\Support\Facades\Route;
use Davidsonts\MercadoPago\Http\Controllers\MercadoPagoController;

Route::group(['middleware' => ['web']], function () {
    Route::prefix('mercadopago')->group(function () {
        // Rotas padrão (redirecionamento e retorno)
        Route::get('/redirect', [MercadoPagoController::class, 'redirect'])
             ->name('mercadopago.redirect');

        Route::get('/success', [MercadoPagoController::class, 'success'])
             ->name('mercadopago.success');

        Route::get('/failure', [MercadoPagoController::class, 'failure'])
             ->name('mercadopago.failure');

        Route::get('/pending', [MercadoPagoController::class, 'pending'])
             ->name('mercadopago.pending');
    });

    // Webhook para notificações do Mercado Pago (exclui CSRF)
    Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook'])
         ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
         ->name('mercadopago.webhook');
});

 