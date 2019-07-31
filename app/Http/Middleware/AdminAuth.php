<?php


namespace App\Http\Middleware;


use Closure;

class AdminAuth
{
    public function handle($request, Closure $next, $guard = null) {

        $admin = session()->get('admin');

        if (isset($admin)) {
            return $next($request);
        }

        return redirect('/login');
    }
}
