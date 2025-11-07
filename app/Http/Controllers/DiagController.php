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
        | 1) Verifica e cria cookie automaticamente
        |--------------------------------------------------------------------------
        */
        $cookieName = 'probe';
        $cookieExists = $request->cookies->has($cookieName);

        // Criar cookie probe (Secure + SameSite=None para Railway)
        $probeCookie = cookie(
            name: $cookieName,
            value: '1',
            minutes: 60,
            path: '/',
            domain: null,               // usa domínio atual automaticamente
            secure: true,               // ⚠️ obrigatório p/ SameSite=None
            httpOnly: false,            // só leitura — ok
            raw: false,
            sameSite: 'None'
        );

        /*
        |--------------------------------------------------------------------------
        | 2) Status do sistema
        |--------------------------------------------------------------------------
        */
        $isHttps = $request->isSecure() || $request->header('x-forwarded-proto') === 'https';

        $status = [
            'railway'       => str_contains(env('APP_URL', ''), 'railway.app'),
            'https'         => $isHttps,
            'cookie_received' => $cookieExists,
            'secure_cookie' => env('SESSION_SECURE_COOKIE'),
            'env'           => env('APP_ENV', 'unknown'),
        ];

        /*
        |--------------------------------------------------------------------------
        | 3) Variáveis de ambiente mascaradas
        |--------------------------------------------------------------------------
        */
        $rawEnv = $_ENV + getenv();
        ksort($rawEnv);

        $sensitiveKeys = [
            'APP_KEY', 'DB_PASSWORD', 'DB_USERNAME', 'DB_HOST', 'DB_DATABASE',
            'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY',
            'PUSHER_APP_SECRET', 'MAIL_PASSWORD', 'REDIS_PASSWORD',
        ];

        $envMasked = collect($rawEnv)->mapWithKeys(function ($v, $k) use ($sensitiveKeys) {
            $isSensitive = collect($sensitiveKeys)->contains(fn($sk) => str_contains($k, $sk));
            return [$k => $isSensitive ? '**********' : $v];
        });

        /*
        |--------------------------------------------------------------------------
        | 4) Configuração CORS
        |--------------------------------------------------------------------------
        */
        $corsConfig = Config::get('cors');

        /*
        |--------------------------------------------------------------------------
        | 5) Arquivos relevantes
        |--------------------------------------------------------------------------
        */
        $filesToRead = [
            'Dockerfile' => base_path('Dockerfile'),
            'AppServiceProvider.php' => app_path('Providers/AppServiceProvider.php'),
            'Kernel.php' => app_path('Http/Kernel.php'),
            'TrustProxies.php' => app_path('Http/Middleware/TrustProxies.php'),
            'VerifyCsrfToken.php' => app_path('Http/Middleware/VerifyCsrfToken.php'),
            'config/cors.php' => config_path('cors.php'),
            'routes/web.php' => base_path('routes/web.php'),
            'public/.htaccess' => public_path('.htaccess'),
        ];

        $fileContents = [];
        foreach ($filesToRead as $label => $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                $content = preg_replace('/(APP_KEY|DB_PASSWORD|DB_USERNAME|DB_HOST|AWS_SECRET_ACCESS_KEY)=([^\n]+)/', '$1=**********', $content);
                $fileContents[$label] = $content;
            } else {
                $fileContents[$label] = '[Arquivo não encontrado]';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6) Renderização da view + adicionar cookie
        |--------------------------------------------------------------------------
        */
        $response = response()->view('diag.index', [
            'status' => $status,
            'env' => $envMasked,
            'cors' => $corsConfig,
            'files' => $fileContents,
        ]);

        return $response->cookie($probeCookie);
    }

}
