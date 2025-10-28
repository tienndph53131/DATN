<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use App\Models\Category;
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
   public function boot(): void
{
    // Chia sẻ $categories với mọi view header
    View::composer('layouts.partials.header', function ($view) {
        $categories = Category::orderBy('name')->get();
        $view->with('categories', $categories);
    });
}
}
