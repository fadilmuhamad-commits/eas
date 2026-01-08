<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AuthServiceProvider extends ServiceProvider
{
  /**
   * The model to policy mappings for the application.
   *
   * @var array<class-string, class-string>
   */
  protected $policies = [
    //
  ];

  /**
   * Register any authentication / authorization services.
   */
  public function boot(): void
  {
    $this->registerPolicies();

    // CAN
    Blade::directive('can', function ($expression) {
      return "<?php if (auth()->check() && auth()->user()->Role && auth()->user()->Role->hasPermission({$expression})): ?>";
    });
    Blade::directive('elsecanif', function ($expression) {
      return "<?php elseif (auth()->check() && auth()->user()->Role && auth()->user()->Role->hasPermission({$expression})): ?>";
    });
    Blade::directive('elsecan', function () {
      return "<?php else: ?>";
    });
    Blade::directive('endcan', function () {
      return '<?php endif; ?>';
    });

    // CANANY
    Blade::directive('canany', function ($expression) {
      return "<?php if (auth()->check() && auth()->user()->Role && auth()->user()->Role->hasAnyPermission(explode('|', $expression))): ?>";
    });
    Blade::directive('elsecananyif', function ($expression) {
      return "<?php elseif (auth()->check() && auth()->user()->Role && auth()->user()->Role->hasAnyPermission(explode('|', $expression))): ?>";
    });
    Blade::directive('elsecanany', function () {
      return "<?php else: ?>";
    });
    Blade::directive('endcanany', function () {
      return '<?php endif; ?>';
    });

    // CANNOT
    Blade::directive('cannot', function ($expression) {
      return "<?php if (!auth()->check() || !auth()->user()->Role || !auth()->user()->Role->hasPermission({$expression})): ?>";
    });
    Blade::directive('elsecannotif', function ($expression) {
      return "<?php elseif (!auth()->check() || !auth()->user()->Role || !auth()->user()->Role->hasPermission({$expression})): ?>";
    });
    Blade::directive('elsecannot', function () {
      return "<?php else: ?>";
    });
    Blade::directive('endcannot', function () {
      return '<?php endif; ?>';
    });
  }
}
