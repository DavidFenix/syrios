
koyeb.com
login com git dei...@gmail.com

APP_NAME=Syrios
APP_ENV=production
APP_KEY=base64:HjvoKQ+HryNsLiKil3mGGjQnbilOAYDnVSI9GWhQqEQ=
APP_DEBUG=false
APP_URL=https://syrios.koyeb.app

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=nozomi.proxy.rlwy.net
DB_PORT=20952
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=MgtBQookiADPygUYMHGoxnCFZLWxZIcf

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=None
--------------------------------------------------------

🚀 Alternativas que funcionam 100% (inclusive cookies)
Provedor  Grátis? Motivo de destaque

Railway.app ✅ plano gratuito com 512 MB RAM Sessões e cookies funcionam, HTTPS real sem proxy

Fly.io  ✅ plano gratuito  Você controla o container (sem Cloudflare intermediando)

Cyclic.sh ✅ free tier Hospeda Laravel completo com MySQL externo (Aiven ou Planetscale)

----------------------------------------------------------
nada ainda
https://syrios.onrender.com/debug-headers
erro 404
https://syrios.onrender.com/debug
erro 404
https://syrios.onrender.com/cookie-test
erro 500
https://syrios.onrender.com/session-debug
{"session_id":"dMZ1jjFMaDtHpR59yHRAptUtAvbFMxmewZo05lVA","has_token":true,"csrf_token":"m4MsqW0rICOu1hNf5xbLj831wHWEqKOI9uZvLRuI","cookies":[],"headers":{"cookie_header":null}}

if (function_exists('ini_set')) {
    ini_set('zlib.output_compression', '0');
    ini_set('output_buffering', '4096');
}
<IfModule mod_headers.c>
    # Corrige bloqueio de Set-Cookie no Render (Apache 2.4.65)
    Header always edit Set-Cookie "(?i)^(.+)$" "$1; SameSite=None; Secure"
</IfModule>
----------------------------------------

Excelente diagnóstico, David — esse curl -I mostra exatamente o que precisávamos ver 👇
Cabeçalho Valor
HTTP/2 200  página foi entregue com sucesso via HTTPS
cf-cache-status: DYNAMIC  ✅ o Cloudflare não serviu cache (requisição foi até o Render)
x-render-origin-server: Apache/2.4.65 (Debian)  ✅ resposta veio diretamente do Apache/PHP, não de cache
❌ não há Set-Cookie:  o Laravel não enviou o cookie na resposta
Portanto agora sabemos com 100% de certeza:
🚫 O cookie não está sendo gerado nem enviado pelo Laravel — o problema não é mais cache.
Até aqui, corrigimos o ambiente (Cloudflare, Render, cache, HTTPS, proxy) — agora é hora de resolver por que o Laravel não emite o Set-Cookie mesmo num request dinâmico.


https://dashboard.render.com/web/srv-d3vmejur433s73d0l9tg/deploys/dep-d3vmeker433s73d0lb5g
ddscosta23@gmail.com


| Nome          | Valor                                                                |
| ------------- | -------------------------------------------------------------------- |
| APP_NAME      | Syrios                                                               |
| APP_ENV       | production                                                           |
| APP_DEBUG     | false                                                                |
| APP_KEY       | (pegue do seu .env local)                                            |
| APP_URL       | [https://seu-servico.onrender.com](https://seu-servico.onrender.com) |
| DB_CONNECTION | mysql                                                                |
| DB_HOST       | nozomi.proxy.rlwy.net                                                |
| DB_PORT       | 20952                                                                |
| DB_DATABASE   | railway                                                              |
| DB_USERNAME   | root                                                                 |
| DB_PASSWORD   | MgtBQookiADPygUYMHGoxnCFZLWxZIcf                                     |
| TZ            | America/Sao_Paulo                                                    |


MYSQL_PUBLIC_URL=mysql://root:MgtBQookiADPygUYMHGoxnCFZLWxZIcf@nozomi.proxy.rlwy.net:20952/railway 
MYSQL_ROOT_PASSWORD=MgtBQookiADPygUYMHGoxnCFZLWxZIcf 
MYSQL_URL=mysql://root:MgtBQookiADPygUYMHGoxnCFZLWxZIcf@mysql.railway.internal:3306/railway MYSQLDATABASE=railway 
MYSQLHOST=mysql.railway.internal 
MYSQLPASSWORD=MgtBQookiADPygUYMHGoxnCFZLWxZIcf 
MYSQLPORT=3306 MYSQLUSER=root

"C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe" ^
  -h nozomi.proxy.rlwy.net -P 20952 -u root -p --ssl-mode=REQUIRED ^
  railway < "C:\wamp64\www\syrios\dump_syrios.sql"

MgtBQookiADPygUYMHGoxnCFZLWxZIcf

--------------------------------------------------

c:\wamp64\www\syrios>php artisan --version
Laravel Framework 8.83.29

c:\wamp64\www\syrios>php -v
PHP 8.1.31 (cli) (built: Nov 19 2024 16:44:13) (ZTS Visual C++ 2019 x64)
Copyright (c) The PHP Group
Zend Engine v4.1.31, Copyright (c) Zend Technologies
    with Zend OPcache v8.1.31, Copyright (c), by Zend Technologies
    with Xdebug v3.3.2, Copyright (c) 2002-2024, by Derick Rethans

mysql> c:\wamp64\www\syrios>mysql -V
Show warnings disabled.
Show warnings disabled.
--------------
c:/wamp64/bin/mysql/mysql9.1.0/bin/mysql.exe  Ver 9.1.0 for Win64 on x86_64 (MySQL Community Server - GPL)

Connection id:          8268
Current database:
Current user:           root@localhost
SSL:                    Cipher in use is TLS_AES_128_GCM_SHA256
Using delimiter:        ;
Server version:         9.1.0 MySQL Community Server - GPL
Protocol version:       10
Connection:             localhost via TCP/IP
Server characterset:    utf8mb4
Db     characterset:    utf8mb4
Client characterset:    cp850
Conn.  characterset:    cp850
TCP port:               3306
Binary data as:         Hexadecimal
Uptime:                 18 days 6 hours 12 min 55 sec

Threads: 2  Questions: 615442  Slow queries: 0  Opens: 5409  Flush tables: 3  Open tables: 1230  Queries per second avg: 0.390

APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:HjvoKQ+HryNsLiKil3mGGjQnbilOAYDnVSI9GWhQqEQ=
APP_DEBUG=false
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syrios
DB_USERNAME=******
DB_PASSWORD=*******

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DRIVER=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"


> config('app')
= [
    "name" => "Laravel",
    "env" => "local",
    "debug" => true,
    "url" => "http://localhost",
    "asset_url" => null,
    "timezone" => "America/Sao_Paulo",
    "locale" => "pt_BR",
    "fallback_locale" => "en",
    "faker_locale" => "en_US",
    "key" => "base64:HjvoKQ+HryNsLiKil3mGGjQnbilOAYDnVSI9GWhQqEQ=",
    "cipher" => "AES-256-CBC",
    "providers" => [
      "Illuminate\Auth\AuthServiceProvider",
      "Illuminate\Broadcasting\BroadcastServiceProvider",
      "Illuminate\Bus\BusServiceProvider",
      "Illuminate\Cache\CacheServiceProvider",
      "Illuminate\Foundation\Providers\ConsoleSupportServiceProvider",
      "Illuminate\Cookie\CookieServiceProvider",
      "Illuminate\Database\DatabaseServiceProvider",
      "Illuminate\Encryption\EncryptionServiceProvider",
      "Illuminate\Filesystem\FilesystemServiceProvider",
      "Illuminate\Foundation\Providers\FoundationServiceProvider",
      "Illuminate\Hashing\HashServiceProvider",
      "Illuminate\Mail\MailServiceProvider",
      "Illuminate\Notifications\NotificationServiceProvider",
      "Illuminate\Pagination\PaginationServiceProvider",
      "Illuminate\Pipeline\PipelineServiceProvider",
      "Illuminate\Queue\QueueServiceProvider",
      "Illuminate\Redis\RedisServiceProvider",
      "Illuminate\Auth\Passwords\PasswordResetServiceProvider",
      "Illuminate\Session\SessionServiceProvider",
      "Illuminate\Translation\TranslationServiceProvider",
      "Illuminate\Validation\ValidationServiceProvider",
      "Illuminate\View\ViewServiceProvider",
      "Barryvdh\DomPDF\ServiceProvider",
      "App\Providers\AppServiceProvider",
      "App\Providers\AuthServiceProvider",
      "App\Providers\EventServiceProvider",
      "App\Providers\RouteServiceProvider",
    ],
    "aliases" => [
      "App" => "Illuminate\Support\Facades\App",
      "Arr" => "Illuminate\Support\Arr",
      "Artisan" => "Illuminate\Support\Facades\Artisan",
      "Auth" => "Illuminate\Support\Facades\Auth",
      "Blade" => "Illuminate\Support\Facades\Blade",
      "Broadcast" => "Illuminate\Support\Facades\Broadcast",
      "Bus" => "Illuminate\Support\Facades\Bus",
      "Cache" => "Illuminate\Support\Facades\Cache",
      "Config" => "Illuminate\Support\Facades\Config",
      "Cookie" => "Illuminate\Support\Facades\Cookie",
      "Crypt" => "Illuminate\Support\Facades\Crypt",
      "Date" => "Illuminate\Support\Facades\Date",
      "DB" => "Illuminate\Support\Facades\DB",
      "Eloquent" => "Illuminate\Database\Eloquent\Model",
      "Event" => "Illuminate\Support\Facades\Event",
      "File" => "Illuminate\Support\Facades\File",
      "Gate" => "Illuminate\Support\Facades\Gate",
      "Hash" => "Illuminate\Support\Facades\Hash",
      "Http" => "Illuminate\Support\Facades\Http",
      "Js" => "Illuminate\Support\Js",
      "Lang" => "Illuminate\Support\Facades\Lang",
      "Log" => "Illuminate\Support\Facades\Log",
      "Mail" => "Illuminate\Support\Facades\Mail",
      "Notification" => "Illuminate\Support\Facades\Notification",
      "Password" => "Illuminate\Support\Facades\Password",
      "Queue" => "Illuminate\Support\Facades\Queue",
      "RateLimiter" => "Illuminate\Support\Facades\RateLimiter",
      "Redirect" => "Illuminate\Support\Facades\Redirect",
      "Request" => "Illuminate\Support\Facades\Request",
      "Response" => "Illuminate\Support\Facades\Response",
      "Route" => "Illuminate\Support\Facades\Route",
      "Schema" => "Illuminate\Support\Facades\Schema",
      "Session" => "Illuminate\Support\Facades\Session",
      "Storage" => "Illuminate\Support\Facades\Storage",
      "Str" => "Illuminate\Support\Str",
      "URL" => "Illuminate\Support\Facades\URL",
      "Validator" => "Illuminate\Support\Facades\Validator",
      "View" => "Illuminate\Support\Facades\View",
      "PDF" => "Barryvdh\DomPDF\Facade\Pdf",
    ],
  ]