<?php

namespace App\Providers;

use App\Interfaces\PaymentProviderInterface;
use App\Repositories\PaymentRepository;
use App\Services\Payment\StripeProvider;
use App\Services\PaymentService;
use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
       //
    }




    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
