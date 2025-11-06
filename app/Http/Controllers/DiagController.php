<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

class DiagController extends Controller
{
    public function index(Request $request)
    {
        // ------------------------------
        // 🔍 1️⃣ Status geral do sistema
        // ------------------------------
        $isHttps = $request->isSecure() || $request->header('x-forwarded-proto') === 'https';
        $appEnv = env('APP_ENV', 'unknown');
        $appUrl = env('APP_URL', '');
        $cookieTest = isset($_COOKIE['probe']);
        $cookieSecure = env('SESSION_SECURE_COOKIE');

        $status = [
            'railway' => $appUrl && str_contains($appUrl, 'railway.app'),
            'https' => $isHttps,
            'cookie_received' => $cookieTest,
            'secure_cookie' => $cookieSecure,
            'env' => $appEnv,
        ];

        // ------------------------------
        // 🌍 2️⃣ Variáveis de ambiente
        // ------------------------------
        $rawEnv = $_ENV + getenv(); // mistura as duas fontes possíveis
        ksort($rawEnv);

        $sensitiveKeys = [
            'APP_KEY', 'DB_PASSWORD', 'DB_USERNAME', 'DB_HOST', 'DB_DATABASE',
            'AWS_ACCESS_KEY_ID', 'AWS_SECRET_ACCESS_KEY', 'PUSHER_APP_SECRET',
            'MAIL_PASSWORD', 'REDIS_PASSWORD',
        ];

        $envMasked = collect($rawEnv)->mapWithKeys(function ($v, $k) use ($sensitiveKeys) {
            $isSensitive = collect($sensitiveKeys)->contains(fn($sk) => str_contains($k, $sk));
            return [$k => $isSensitive ? '**********' : $v];
        });

        // ------------------------------
        // 🧩 3️⃣ Configuração CORS
        // ------------------------------
        $corsConfig = Config::get('cors');

        // ------------------------------
        // 📁 4️⃣ Arquivos relevantes
        // ------------------------------
        $files = [
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
        foreach ($files as $label => $path) {
            if (File::exists($path)) {
                $content = File::get($path);
                $content = preg_replace('/(APP_KEY|DB_PASSWORD|DB_USERNAME|DB_HOST|AWS_SECRET_ACCESS_KEY)=([^\n]+)/', '$1=**********', $content);
                $fileContents[$label] = $content;
            } else {
                $fileContents[$label] = '[Arquivo não encontrado]';
            }
        }

        // ------------------------------
        // 🧾 5️⃣ Renderização
        // ------------------------------
        return view('diag.index', [
            'status' => $status,
            'env' => $envMasked,
            'cors' => $corsConfig,
            'files' => $fileContents,
        ]);
    }
}
