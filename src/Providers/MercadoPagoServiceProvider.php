<?php

namespace Davidsonts\MercadoPago\Providers;

use Illuminate\Support\ServiceProvider;

class MercadoPagoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        // Registra o caminho das views
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mercadopago');
         // Registra o helper
        $this->app->bind('Davidsonts\MercadoPago\Helpers\OrderHelper', function () {
            return new \Davidsonts\MercadoPago\Helpers\OrderHelper();
        });
        
        $this->publishes([
            __DIR__ . '/../Resources/assets/images' => public_path('vendor/davidsonts/mercadopago/images'),
        ], 'public');
    }
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }
    
    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );
    }
}
