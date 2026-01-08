<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
  public function handle(Request $request, Closure $next, ...$permissions)
  {
    $role = $request->user()->Role;

    if ($role) {
      foreach ($permissions as $permission) {
        if ($role->hasPermission($permission)) {
          return $next($request);
        }
      }
    }


    abort(403, 'Unauthorized');
  }
}
