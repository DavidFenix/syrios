<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class ForceCorsAndCookies
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Força CORS - Railway exige cabeçalhos explícitos
        $response->headers->set('Access-Control-Allow-Origin', 'https://syrios.up.railway.app');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Expose-Headers', 'Set-Cookie');

        // Recria cookies com atributos corretos
        foreach ($response->headers->getCookies() as $cookie) {
            $newCookie = new Cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                'syrios.up.railway.app', // domínio do app
                true,  // Secure = true
                $cookie->isHttpOnly(),
                false, // raw
                'None' // SameSite
            );

            $response->headers->setCookie($newCookie);
        }

        return $response;
    }
}
