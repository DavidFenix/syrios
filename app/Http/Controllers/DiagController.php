<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Cookie;

class DiagController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Enviar Cookie de Teste
        |--------------------------------------------------------------------------
        */
        $probeCookie = new Cookie(
            name: 'probe',
            value: 'ok',
            expire: time() + 3600,
            path: '/',
            domain: env('SESSION_DOMAIN', null),
            secure: true,
            httpOnly: false,
            raw: false,
            sameSite: 'None'
        );

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Detectar HTTPS corretamente (mesmo atrás de proxy)
        |--------------------------------------------------------------------------
        */
        $isHttps = 
               $request->isSecure()
            || $request->header('x-forwarded-proto') === 'https'
            || env('APP_URL', '') !== null && str_starts_with(env('APP_URL'), 'https');

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Ler variáveis principais
        |--------------------------------------------------------------------------
        */
        $appEnv = env('APP_ENV', 'unknown');
        $appUrl = env('APP_URL', '');
        $cookieTestReceived = isset($_COOKIE['probe']);
        $cookieSecureEnv = env('SESSION_SECURE_COOKIE');

        $status = [
            'railway'          => str_contains($appUrl, 'railway.app'),
            'https'            => $isHttps,
            'cookie_received'  => $cookieTestReceived,
            'secure_cookie'    => $cookieSecureEnv,
            'env'              => $appEnv,
        ];

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ Variáveis de ambiente mascaradas
        |--------------------------------------------------------------------------
        */
        $rawEnv = $_ENV + getenv();
        ksort($rawEnv);

        $sensitiveKeys = [
            'APP_KEY', 'DB_PASSWORD', 'DB_USERNAME', 'DB_HOST', 'DB_DATABASE',
            'AWS_SECRET_ACCESS_KEY', 'AWS_ACCESS_KEY_ID',
            'MAIL_PASSWORD', 'PUSHER_APP_SECRET', 'REDIS_PASSWORD'
        ];

        $envMasked = collect($rawEnv)->mapWithKeys(function ($value, $key) use ($sensitiveKeys) {
            $isSensitive = collect($sensitiveKeys)->contains(fn($sk) => str_contains($key, $sk));
            return [$key => $isSensitive ? '**********' : $value];
        });

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ Ler config/cors.php
        |--------------------------------------------------------------------------
        */
        $corsConfig = Config::get('cors');

        /*
        |--------------------------------------------------------------------------
        | 6️⃣ Listar arquivos importantes
        |--------------------------------------------------------------------------
        */
        $files = [
            'Dockerfile'         => base_path('Dockerfile'),
            'AppServiceProvider' => app_path('Providers/AppServiceProvider.php'),
            'Kernel.php'         => app_path('Http/Kernel.php'),
            'TrustProxies.php'   => app_path('Http/Middleware/TrustProxies.php'),
            'VerifyCsrfToken.php'=> app_path('Http/Middleware/VerifyCsrfToken.php'),
            'config/cors.php'    => config_path('cors.php'),
            'routes/web.php'     => base_path('routes/web.php'),
            'public/.htaccess'   => public_path('.htaccess'),
        ];

        $fileContents = [];
        foreach ($files as $label => $path) {
            if (File::exists($path)) {
                $content = File::get($path);

                // Máscara automática
                $content = preg_replace(
                    '/(APP_KEY|DB_PASSWORD|DB_USERNAME|DB_HOST|AWS_SECRET_ACCESS_KEY|MAIL_PASSWORD)=([^\n]+)/',
                    '$1=**********',
                    $content
                );

                $fileContents[$label] = $content;
            } else {
                $fileContents[$label] = '[Arquivo não encontrado]';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7️⃣ Montar resposta
        |--------------------------------------------------------------------------
        */
        $response = response()->view('diag.index', [
            'status' => $status,
            'env'    => $envMasked,
            'cors'   => $corsConfig,
            'files'  => $fileContents,
        ]);

        // ✅ Anexar cookie de teste
        $response->headers->setCookie($probeCookie);

        return $response;
    }
}
