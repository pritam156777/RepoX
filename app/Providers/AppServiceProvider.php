<?php

namespace App\Providers;

use App\Models\Product;
use App\Policies\ProductPolicy;
use Illuminate\Support\ServiceProvider;

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
  public function boot()
{
    if (app()->environment('production')) {
        URL::forceScheme('https');
    }
}
    protected $policies = [
        Product::class => ProductPolicy::class,
    ];
}
