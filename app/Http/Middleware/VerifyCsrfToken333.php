<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken333 extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken {

    protected $except_urls = [
        // your excluded URLs go here
        'auth/login-por-telefene',
        'pedido/finalizar-pedido/{id}'
    ];

    public function handle($request, Closure $next)
    {
        $regex = '#' . implode('|', $this->except_urls) . '#';

        if ($this->isReading($request) || $this->tokensMatch($request) || preg_match($regex, $request->path()))
        {
            return $this->addCookieToResponse($request, $next($request));
        }

        throw new TokenMismatchException;
    }

}