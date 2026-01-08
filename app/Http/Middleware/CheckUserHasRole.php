<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckUserHasRole
{
  public function handle($request, Closure $next)
  {
    if (Auth::check() && Auth::user()->role_id !== null) {
      return $next($request);
    }

    abort(403, 'Unauthorized action.');
  }
}
