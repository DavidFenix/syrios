<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Indica se o CSRF deve ser verificado apenas para rotas web
     */
    protected $addHttpCookie = true;

    /**
     * URIs que devem ser ignoradas pela verificação CSRF.
     *
     * Ex: endpoints públicos, webhooks, etc.
     */
    protected $except = [
        //
    ];

    /**
     * Corrige o problema de proxy HTTPS (Render, Railway, etc.)
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        // se for uma requisição HTTPS atrás de proxy, forçar confiança
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            $request->server->set('HTTPS', true);
        }

        return is_string($request->session()->token()) &&
               is_string($token) &&
               hash_equals($request->session()->token(), $token);
    }
}
