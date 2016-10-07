<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Closure;
class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        //'mailchimp/store',
    ];

    private $openRoutes = ['mailchimp/store', 'mailchimp/create'];

    public function handle($request, Closure $next) {
        foreach ($this->openRoutes as $route) {
            if ($request->is($route)) {
                return $next($request);
            }
        }

        return parent::handle($request, $next);
    }
}
