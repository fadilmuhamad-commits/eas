<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRoleNot
{
  public function handle($request, Closure $next, ...$roles)
  {
    if (Auth::check() && !in_array(Auth::user()->Role->unique_code ?? '', $roles)) {
      return $next($request);
    }

    abort(403, 'Unauthorized action.');
  }
}
