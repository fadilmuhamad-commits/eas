<?php

namespace App\Providers;

use App\Models\M_Role;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
    // if (App::environment('production')) {
    //   URL::forceScheme('https');
    // }

    // Gate::define('manage_users', function (M_Role $role) {
    //   return $role->is_admin == 1;
    // });

    Paginator::useBootstrapFive();
  }
}
