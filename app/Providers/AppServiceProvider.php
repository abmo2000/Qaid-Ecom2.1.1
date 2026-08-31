<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Listeners\OrderCreationListener;
use App\Models\User;
use App\Models\Guest;
use App\Models\Category;
use App\Models\Package;
use App\Models\Product;
use App\Models\SmtpSetting;
use App\Models\ProductTrial;
use App\Services\CartService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);

        //  $this->app->bind('path.public', function() {
        //     return realpath(base_path().'/public_html');
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         View::composer('*', function ($view) {
            try {
                $cartService = app(CartService::class);
                $view->with([
                    'cartCount' => $cartService->getCount(),
                    'cartTotal' => $cartService->getTotal(),
                ]);


            } catch (\Exception $e) {
                $view->with([
                    'cartCount' => 0,
                    'cartTotal' => 0,
                ]);
            }

        });

         Relation::enforceMorphMap([
            'user' => User::class,
            'guest' => Guest::class,
            'product' => Product::class,
                'category' => Category::class,
            'producttrial' => ProductTrial::class,
            'package' => Package::class
         ]);


     Event::listen(
        OrderCreated::class,
        [OrderCreationListener::class, 'handle']
    );

        try {
            $smtp = SmtpSetting::first();
            if ($smtp) {
                Config::set('mail.default', $smtp->driver ?? 'smtp');
                Config::set('mail.mailers.smtp.host', $smtp->host);
                Config::set('mail.mailers.smtp.port', $smtp->port);
                Config::set('mail.mailers.smtp.encryption', $smtp->encryption);
                Config::set('mail.mailers.smtp.username', $smtp->username);
                Config::set('mail.mailers.smtp.password', $smtp->password);
                Config::set('mail.from.address', $smtp->from_address);
                Config::set('mail.from.name', $smtp->from_name);
            }
        } catch (\Exception $e) {
            // Silently fail — table may not exist during migrations
        }

    
               
    }
  
   
}
