<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Master
use App\Http\Controllers\Master\EscolaController as MasterEscolaController;
use App\Http\Controllers\Master\RoleController as MasterRoleController;
use App\Http\Controllers\Master\UsuarioController as MasterUsuarioController;
use App\Http\Controllers\Master\DashboardController as MasterDashboardController;
use App\Http\Controllers\Master\ImagemController;

// Secretaria
use App\Http\Controllers\Secretaria\EscolaController as SecretariaEscolaController;
use App\Http\Controllers\Secretaria\UsuarioController as SecretariaUsuarioController;

// Escola
use App\Http\Controllers\Escola\AlunoController;
use App\Http\Controllers\Escola\DashboardController;
use App\Http\Controllers\Escola\DisciplinaController;
use App\Http\Controllers\Escola\ProfessorController;
use App\Http\Controllers\Escola\TurmaController;
use App\Http\Controllers\Escola\UsuarioController as EscolaUsuarioController;
use App\Http\Controllers\Escola\RegimentoController;
use App\Http\Controllers\Escola\ModeloMotivoController;
use App\Http\Controllers\Escola\AlunoFotoController;
use App\Http\Controllers\Escola\AlunoFotoLoteController;

use App\Http\Controllers\Professor\{
    DashboardController as ProfessorDashboardController,
    OfertaController,
    OcorrenciaController,
    RelatorioController,
    PerfilController
};


/*
|--------------------------------------------------------------------------
| Rotas do Master
|--------------------------------------------------------------------------
*/
Route::prefix('master')
    ->middleware(['auth', 'role:master', 'ensure.context'])
    ->name('master.')
    ->group(function () {
        Route::get('dashboard', [MasterDashboardController::class, 'index'])->name('dashboard');
        Route::get('/', fn () => redirect()->route('master.dashboard'));

        Route::resource('escolas', MasterEscolaController::class)->except(['show']);
        Route::get('escolas/{escola}/detalhes', [MasterEscolaController::class, 'detalhes'])
    ->name('escolas.detalhes');
        Route::resource('roles', MasterRoleController::class)->only(['index']);
        Route::resource('usuarios', MasterUsuarioController::class);

        // Associações Escola Mãe ↔ Escola Filha
        Route::get('associacoes', [MasterEscolaController::class, 'associacoes'])->name('escolas.associacoes');
        Route::post('associacoes', [MasterEscolaController::class, 'associarFilha'])->name('escolas.associar');

        Route::post('usuarios/{usuario}/vincular', [MasterUsuarioController::class, 'vincular'])
    ->name('usuarios.vincular');

    // Gestão de roles específicas por usuario
    Route::get('usuarios/{usuario}/roles', [MasterUsuarioController::class, 'editRoles'])
        ->name('usuarios.roles.edit');
    Route::post('usuarios/{usuario}/roles', [MasterUsuarioController::class, 'updateRoles'])
        ->name('usuarios.roles.update');

    // 🗑 Mostrar confirmação antes da exclusão
    Route::get('usuarios/{usuario}/confirm-destroy', [MasterUsuarioController::class, 'confirmDestroy'])
            ->name('usuarios.confirmDestroy');

    // 🧹 Executar exclusão
    Route::delete('usuarios/{usuario}', [MasterUsuarioController::class, 'destroy'])
            ->name('usuarios.destroy');

    Route::get('imagens', [ImagemController::class, 'index'])->name('imagens.index');
    Route::post('imagens/limpar', [ImagemController::class, 'limpar'])->name('imagens.limpar');
   





    });

/*
|--------------------------------------------------------------------------
| Rotas da Secretaria
|--------------------------------------------------------------------------
*/
Route::prefix('secretaria')
    ->middleware(['auth', 'role:secretaria', 'ensure.context'])
    ->name('secretaria.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('secretaria.escolas.index'))->name('dashboard');

        Route::resource('escolas', SecretariaEscolaController::class)->except(['show']);
        Route::resource('usuarios', SecretariaUsuarioController::class)->except(['show']);

        Route::post('usuarios/{usuario}/vincular', [SecretariaUsuarioController::class, 'vincular'])
    ->name('usuarios.vincular');


        Route::get('usuarios/{usuario}/roles', [SecretariaUsuarioController::class, 'editRoles'])
        ->name('usuarios.roles.edit');
        Route::post('usuarios/{usuario}/roles', [SecretariaUsuarioController::class, 'updateRoles'])
        ->name('usuarios.roles.update');


    });

/*
|--------------------------------------------------------------------------
| Rotas da Escola
|--------------------------------------------------------------------------
*/
Route::prefix('escola')
    ->middleware(['auth', 'role:escola', 'ensure.context'])
    ->name('escola.')
    ->group(function () {
        Route::get('/', fn () => redirect()->route('escola.dashboard'));
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Usuários (professores, pais, etc.)
        Route::resource('usuarios', EscolaUsuarioController::class)->except(['show']);
        Route::post('usuarios/{usuario}/vincular', [EscolaUsuarioController::class, 'vincular'])->name('usuarios.vincular');

        // Professores (listagem/gestão específica)
        Route::resource('professores', ProfessorController::class)->except(['show']);

        // Disciplinas
        Route::resource('disciplinas', DisciplinaController::class)->except(['show']);

        // Turmas
        Route::resource('turmas', TurmaController::class)->except(['show']);

        // Alunos
        Route::resource('alunos', AlunoController::class)->except(['show']);


        Route::get('usuarios/{usuario}/roles', [EscolaUsuarioController::class, 'editRoles'])
            ->name('usuarios.roles.edit');
        Route::post('usuarios/{usuario}/roles', [EscolaUsuarioController::class, 'updateRoles'])
            ->name('usuarios.roles.update');

        // Vincular aluno existente à escola atual
        Route::post('alunos/{aluno}/vincular', [AlunoController::class, 'vincular'])
            ->name('alunos.vincular');

        // Enturmações (vínculos aluno–turma)
        Route::resource('enturmacao', \App\Http\Controllers\Escola\EnturmacaoController::class)
            ->except(['show']);

        // Enturmação
        Route::resource('enturmacao', \App\Http\Controllers\Escola\EnturmacaoController::class)->except(['show']);

        // Rota especial para enturmação em lote
        Route::post('enturmacao/storeBatch', [\App\Http\Controllers\Escola\EnturmacaoController::class, 'storeBatch'])
            ->name('enturmacao.storeBatch');

        Route::resource('lotacao', \App\Http\Controllers\Escola\LotacaoController::class)->except(['show']);

        Route::prefix('lotacao')->name('lotacao.')->group(function () {
            Route::get('diretor_turma', [\App\Http\Controllers\Escola\DiretorTurmaController::class, 'index'])
                ->name('diretor_turma.index');
            Route::post('diretor_turma/update', [\App\Http\Controllers\Escola\DiretorTurmaController::class, 'update'])
                ->name('diretor_turma.update');
            Route::delete('diretor_turma/{id}', [\App\Http\Controllers\Escola\DiretorTurmaController::class, 'destroy'])
                ->name('diretor_turma.destroy');
        });

        Route::get('identidade', [App\Http\Controllers\Escola\IdentidadeController::class, 'edit'])
            ->name('identidade.edit');

        Route::post('identidade', [App\Http\Controllers\Escola\IdentidadeController::class, 'update'])
            ->name('identidade.update');

        Route::get('regimento', [RegimentoController::class, 'index'])->name('regimento.index');
        Route::post('regimento', [RegimentoController::class, 'update'])->name('regimento.update');

        //Motivos de Ocorrência
        Route::resource('motivos', ModeloMotivoController::class)
            ->except(['show']);
   
        // 📸 Upload de foto do aluno
        Route::get('alunos/{aluno}/foto', [AlunoFotoController::class, 'edit'])->name('alunos.foto.edit');
        Route::post('alunos/{aluno}/foto', [AlunoFotoController::class, 'update'])->name('alunos.foto.update');
        
        // 📦 Upload em massa de fotos
        Route::get('alunos/fotos-lote', [AlunoFotoLoteController::class, 'index'])->name('alunos.fotos.lote');
        Route::post('alunos/fotos-lote', [AlunoFotoLoteController::class, 'store'])->name('alunos.fotos.lote.store');
  
        // Motivos: importar de outras escolas
        Route::get('motivos/importar', [\App\Http\Controllers\Escola\ModeloMotivoController::class, 'importar'])
            ->name('motivos.importar');

        Route::post('motivos/importar', [\App\Http\Controllers\Escola\ModeloMotivoController::class, 'importarSalvar'])
            ->name('motivos.importar.salvar');





           
    });




/*
|--------------------------------------------------------------------------
| Rotas do Professor
|--------------------------------------------------------------------------
*/
Route::prefix('professor')
    ->middleware(['auth', 'role:professor', 'ensure.context'])
    ->name('professor.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD E PERFIL
        |--------------------------------------------------------------------------
        */
        // Route::get('dashboard', [DashboardController::class, 'index'])
        //     ->name('dashboard');

        // 🏠 Painel do professor
        Route::get('dashboard', [ProfessorDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('perfil', [PerfilController::class, 'index'])
            ->name('perfil');

        /*
        |--------------------------------------------------------------------------
        | OFERTAS (disciplinas/turmas)
        |--------------------------------------------------------------------------
        */
        Route::prefix('ofertas')->name('ofertas.')->group(function () {
            Route::get('/', [OfertaController::class, 'index'])
                ->name('index');

            Route::get('{oferta}/alunos', [OfertaController::class, 'alunos'])
                ->name('alunos');

            Route::post('{oferta}/alunos', [OfertaController::class, 'alunosPost'])
                ->name('alunos.post');

            // Aplicar ocorrência em alunos selecionados (tela e gravação)
            Route::get('{oferta}/ocorrencias/create', [OcorrenciaController::class, 'create'])
                ->name('ocorrencias.create');

            Route::post('ocorrencias/store', [OcorrenciaController::class, 'store'])
                ->name('ocorrencias.store');
        });

        /*
        |--------------------------------------------------------------------------
        | OCORRÊNCIAS
        |--------------------------------------------------------------------------
        */
        Route::prefix('ocorrencias')->name('ocorrencias.')->group(function () {

            // Listagem geral (autor + diretor de turma)
            Route::get('/', [OcorrenciaController::class, 'index'])
                ->name('index');

            // Detalhes
            Route::get('{id}', [OcorrenciaController::class, 'show'])
                ->name('show');

            // Edição
            Route::get('{id}/edit', [OcorrenciaController::class, 'edit'])
                ->name('edit');
            Route::put('{id}', [OcorrenciaController::class, 'update'])
                ->name('update');

            // Exclusão
            Route::delete('{id}', [OcorrenciaController::class, 'destroy'])
                ->name('destroy');

            // Atualização de status (arquivar/anular)
            Route::patch('{id}/status', [OcorrenciaController::class, 'updateStatus'])
                ->name('updateStatus');

            // Encaminhar / arquivar (somente diretor)
            Route::get('{id}/encaminhar', [OcorrenciaController::class, 'encaminhar'])
                ->name('encaminhar');
            Route::post('{id}/encaminhar', [OcorrenciaController::class, 'salvarEncaminhamento'])
                ->name('encaminhar.salvar');

            // Histórico do aluno
            Route::get('historico/{aluno}', [OcorrenciaController::class, 'historico'])
                ->name('historico');

            // Histórico resumido (visual e PDF)
            Route::get('historico-resumido/{aluno}', [OcorrenciaController::class, 'historicoResumido'])
                ->name('historico_resumido');
            Route::get('pdf/{aluno}', [OcorrenciaController::class, 'gerarPdf'])
                ->name('pdf');
        });

        /*
        |--------------------------------------------------------------------------
        | RELATÓRIOS
        |--------------------------------------------------------------------------
        */
        Route::get('relatorios', [RelatorioController::class, 'index'])
            ->name('relatorios.index');


    });


/*
|--------------------------------------------------------------------------
| Rotas Públicas (sem login)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Seleção de contexto (após login, caso haja múltiplos vínculos)
Route::get('/choose-school', [LoginController::class, 'chooseSchool'])->name('choose.school');
Route::get('/choose-role/{schoolId}', [LoginController::class, 'chooseRole'])->name('choose.role');
Route::post('/set-context', [LoginController::class, 'setContextPost'])->name('set.context');

// 📘 Rota pública (professores e outros)
Route::get('regimento/{school}', [RegimentoController::class, 'visualizar'])
    ->middleware('auth')
    ->name('regimento.visualizar');

/*
|--------------------------------------------------------------------------
| Rota raiz
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});


use Illuminate\Http\Request;

Route::get('/diag/csrf', function (Request $r) {
    return response()->json([
        'app_url'        => config('app.url'),
        'session_driver' => config('session.driver'),
        'session_cookie' => config('session.cookie'),
        'session_domain' => config('session.domain'),
        'session_secure' => config('session.secure'),
        'session_same'   => config('session.same_site'),
        'session_id'     => session()->getId(),
        'has_token'      => csrf_token() ? true : false,
        'csrf_token'     => csrf_token(),
        'cookies_in'     => $r->cookies->all(),
    ]);
});

Route::post('/diag/csrf', function (Request $r) {
    return response()->json([
        'posted__token'  => $r->input('_token'),
        'session_token'  => $r->session()->token(),  // token válido da sessão
        'match'          => hash_equals((string)$r->session()->token(), (string)$r->input('_token')),
        'session_id'     => session()->getId(),
        'cookies_in'     => $r->cookies->all(),
    ]);
})->name('diag.csrf.post');

Route::get('/diag/form', function () {
    return <<<HTML
<!doctype html>
<meta charset="utf-8">
<title>Diag Form</title>
<form method="POST" action="/diag/csrf">
    <input type="hidden" name="_token" value="__TOKEN__">
    <button>Enviar</button>
</form>
<script>
fetch('/diag/csrf',{credentials:'include'})
 .then(r=>r.json())
 .then(j=>{
   document.querySelector('input[name="_token"]').value = j.csrf_token;
 });
</script>
HTML;
});


Route::get('/diag/server', function (Request $request) {
    return response()->json([
        'https' => $_SERVER['HTTPS'] ?? null,
        'http_x_forwarded_proto' => $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? null,
        'request_scheme' => $_SERVER['REQUEST_SCHEME'] ?? null,
        'url_scheme' => $request->getScheme(),
        'secure' => $request->isSecure(),
        'trusted_proxies' => Request::getTrustedProxies(),
        'app_url' => config('app.url'),
    ]);
});

Route::get('/debug', function () {
    return [
        'isSecure' => request()->isSecure(),
        'url' => url('/'),
        'scheme' => request()->getScheme(),
        'server' => request()->server(),
    ];
});

Route::get('/session-debug', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'has_token' => session()->has('_token'),
        'csrf_token' => csrf_token(),
        'cookies' => request()->cookies->all(),
        'headers' => [
            'cookie_header' => request()->header('cookie')
        ]
    ]);
});

Route::get('/cookie-test', function (\Illuminate\Http\Request $request) {
    $response = response()->json([
        'input_cookies' => $request->cookies->all(),
        'session_id' => session()->getId(),
    ]);
    $response->cookie('cookie_test', 'ok', 10, '/', null, true, true, false, 'None');
    return $response;
});

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

Route::get('/session-debug-pro', function (Request $r) {
    $sessionId = session()->getId();
    $cookies = $r->cookies->all();
    $hasCookie = isset($cookies[Config::get('session.cookie')]);
    $csrf = csrf_token();
    $sameSite = Config::get('session.same_site');
    $secure = Config::get('session.secure');
    $domain = Config::get('session.domain');

    $html = <<<HTML
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Diagnóstico de Sessão - Syrios</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f7f9fb;
    color: #222;
    margin: 40px;
}
h1 {
    color: #2c3e50;
}
.card {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.ok {color: #2ecc71; font-weight: bold;}
.fail {color: #e74c3c; font-weight: bold;}
.info {color: #2980b9;}
code {
    background: #f1f1f1;
    padding: 4px 8px;
    border-radius: 4px;
}
</style>
</head>
<body>
    <h1>🧠 Diagnóstico de Sessão Syrios</h1>
    <div class="card">
        <h2>Status Geral</h2>
        <p><strong>ID da Sessão:</strong> <code>{$sessionId}</code></p>
        <p><strong>Token CSRF:</strong> <code>{$csrf}</code></p>
        <p><strong>Cookie Recebido:</strong> 
            <span class="{$hasCookie ? 'ok' : 'fail'}">
                {$hasCookie ? '✅ Sim' : '❌ Não'}
            </span>
        </p>
    </div>

    <div class="card">
        <h2>Configuração Atual</h2>
        <ul>
            <li><strong>Session Driver:</strong> <code>{config('session.driver')}</code></li>
            <li><strong>Domínio:</strong> <code>{$domain}</code></li>
            <li><strong>SameSite:</strong> <code>{$sameSite}</code></li>
            <li><strong>Secure Cookie:</strong> <code>{$secure ? 'true ✅' : 'false ❌'}</code></li>
        </ul>
    </div>

    <div class="card">
        <h2>Cookies Recebidos</h2>
        <pre style="background:#f1f1f1;padding:10px;border-radius:6px;">{json_encode($cookies, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES)}</pre>
    </div>

    <div class="card">
        <h2>Diagnóstico Automático</h2>
        <p>
HTML;

    if (!$hasCookie) {
        $html .= "<span class='fail'>🚨 O navegador não enviou o cookie de sessão. O Laravel cria uma nova sessão a cada request, causando erro 419.</span>
        <ul>
            <li>Verifique se o navegador aceita cookies.</li>
            <li>Garanta que o cookie apareça em DevTools → Aplicativo → Cookies.</li>
            <li>Confirme que <code>SESSION_SAME_SITE=none</code> e <code>SESSION_SECURE_COOKIE=true</code> estão ativos.</li>
        </ul>";
    } else {
        $html .= "<span class='ok'>🟢 O cookie de sessão foi recebido com sucesso! O CSRF deve funcionar corretamente agora.</span>";
    }

    $html .= <<<HTML
        </p>
    </div>

    <p class="info">Atualize a página duas vezes: o <strong>ID da sessão</strong> deve permanecer igual. Se mudar, o cookie ainda não foi aceito.</p>
</body>
</html>
HTML;

    return response($html);
});

