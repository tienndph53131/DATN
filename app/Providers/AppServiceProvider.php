<?php

namespace App\Providers;
<<<<<<< HEAD

=======
use Illuminate\Support\Facades\View;
use App\Models\Category;
>>>>>>> origin/tien
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
<<<<<<< HEAD
    public function boot(): void
    {
        //
    }
=======
   public function boot(): void
{
    // Chia sẻ $categories với mọi view header
    View::composer('layouts.partials.header', function ($view) {
        $categories = Category::orderBy('name')->get();
        $view->with('categories', $categories);
    });
}
>>>>>>> origin/tien
}
