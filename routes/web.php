<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Cookie;

// Auth
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
use App\Http\Controllers\Escola\EnturmacaoController;
use App\Http\Controllers\Escola\LotacaoController;
use App\Http\Controllers\Escola\DiretorTurmaController;
use App\Http\Controllers\Escola\IdentidadeController;

// Professor
use App\Http\Controllers\Professor\{
    DashboardController as ProfessorDashboardController,
    OfertaController,
    OcorrenciaController,
    RelatorioController,
    PerfilController
};

/*
|--------------------------------------------------------------------------
| Rotas Públicas (sem login)
|--------------------------------------------------------------------------
| Observação: por padrão, o RouteServiceProvider aplica o grupo "web".
| Mantemos explícito aqui para clareza do fluxo.
*/
Route::middleware(['web'])->group(function () {

    // Página inicial → login
    Route::get('/', fn() => redirect()->route('login'));

    Route::prefix('diag')->group(function () { 

        Route::get('/', [DiagController::class, 'index'])->name('diag.index'); 
        Route::get('/headers', [DiagController::class, 'headers'])->name('diag.headers'); 
        Route::get('/cookies', [DiagController::class, 'cookies'])->name('diag.cookies'); 
        Route::get('/set-cookie', [DiagController::class, 'setCookie'])->name('diag.setcookie'); 
        Route::get('/configs', [DiagController::class, 'configs'])->name('diag.configs'); 
        
        Route::get('/cookie-test', function () { return response('ok')->cookie( 'probe', '1', 0, null, null, true, true, false, 'None' ); 
            
    }); 

    Route::get('/cache-clear', function () {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        return "Cache limpo!";
    });


    // Login / Logout (públicas)
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rotas de diagnóstico e testes rápidos (públicas)
    Route::get('/way', function () {
        return '
        <h2>Login Teste</h2>
        <form method="post" action="/waylogin">
          <input type="email" name="email" placeholder="Email" required><br><br>
          <input type="password" name="password" placeholder="Senha" required><br><br>
          <button type="submit">Entrar</button>
        </form>
        <hr><a href="/waydiag">Diagnóstico</a>';
    });

    Route::post('/waylogin', function (Request $request) {
        Session::put('user', [
            'email' => $request->email,
            'logged_at' => now()->toDateTimeString()
        ]);
        return redirect('/waydashboard');
    });

    Route::get('/waydashboard', function () {
        if (!Session::has('user')) {
            return redirect('/way');
        }
        $u = Session::get('user');
        return "
        <h2>Área Protegida</h2>
        <p>Email: <b>{$u['email']}</b></p>
        <p>Login em: {$u['logged_at']}</p>
        <a href='/waylogout'>Sair</a> | <a href='/waydiag'>Diagnóstico</a>";
    });

    Route::get('/waylogout', function () {
        Session::flush();
        return redirect('/way');
    });

    Route::get('/waydiag', function (Request $r) {
        $headers = [];
        foreach ($r->headers->all() as $k => $v) {
            $headers[$k] = implode('; ', $v);
        }

        return response()->make("
        <h2>Diagnóstico</h2>
        <p>HTTPS detectado: " . ($r->isSecure() ? 'Sim' : 'Não') . "</p>
        <h3>Cookies</h3><pre>" . print_r($r->cookies->all(), true) . "</pre>
        <h3>Sessão</h3><pre>" . print_r(session()->all(), true) . "</pre>
        <h3>Headers</h3><pre>" . print_r($headers, true) . "</pre>
        <a href='/way'>Voltar</a>", 200, ['Content-Type' => 'text/html']);
    });

    // Debugs e testes auxiliares
    Route::get('/header-debug', function (Request $request) {
        if (!Session::isStarted()) {
            Session::start();
        }
        Session::put('debug_test', now()->toDateTimeString());

        $data = [
            'timestamp' => now()->toDateTimeString(),
            'client_ip' => $request->ip(),
            'session_value' => Session::get('debug_test'),
            'cookies_received' => $request->cookies->all(),
            'headers_received' => $request->headers->all(),
        ];

        $response = response()->json([
            'debug_info' => $data,
            'note' => 'Check if headers or cookies below are modified by proxies.'
        ]);

        $response->headers->set('X-Debug-App', 'Syrios');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        $testCookie = new Cookie(
            'test_cookie',
            'ok',
            time() + 3600,
            '/',
            null,
            true,
            false,
            false,
            'None'
        );

        $response->headers->setCookie($testCookie);

        return $response;
    });

    Route::get('/debug', fn() => ['secure' => request()->isSecure(), 'url' => url('/')]);
    Route::get('/debug-headers', fn() => response()->json(['headers' => request()->headers->all()]));

    Route::get('/cookie-test', function (Request $request) {
        $response = response('<h1>cookie test</h1>');
        // Observação: domínio aqui era "syrios.onrender.com" no original; mantive como estava,
        // mas considere ajustar para o domínio atual quando usar este teste.
        $response->cookie('cookie_test', 'ok', 10, '/', 'syrios.onrender.com', true, true, false, 'None');
        return $response;
    });

    Route::get('/session-debug', function () {
        return response()->json([
            'session_id' => session()->getId(),
            'has_token'  => session()->has('_token'),
            'csrf_token' => csrf_token(),
            'cookies'    => request()->cookies->all(),
        ]);
    });

    Route::get('/cookie-proxy-test', function () {
        $response = new Response('<h1>Teste manual de cookie</h1><p>Verifique se o navegador recebeu o cookie chamado <b>proxy_test_cookie</b>.</p>');
        $response->header('Set-Cookie', 'proxy_test_cookie=OK_FROM_SERVER; Path=/; Max-Age=600; SameSite=None; Secure');
        return $response;
    });

    // Regimento público
    Route::get('regimento/{school}', [RegimentoController::class, 'visualizar'])
        ->name('regimento.visualizar');
});

/*
|--------------------------------------------------------------------------
| Pós-Login (com sessão carregada) — Escolha de Contexto
|--------------------------------------------------------------------------
| Essas rotas precisam apenas do usuário autenticado, sem exigir contexto.
| Aqui o cookie de sessão já foi entregue ao navegador.
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/choose-school', [LoginController::class, 'chooseSchool'])->name('choose.school');
    Route::get('/choose-role/{schoolId}', [LoginController::class, 'chooseRole'])->name('choose.role');
    Route::post('/set-context', [LoginController::class, 'setContextPost'])->name('set.context');
});

/*
|--------------------------------------------------------------------------
| Rotas Protegidas por Contexto (auth + ensure.context)
|--------------------------------------------------------------------------
| A partir daqui, o contexto (current_school_id/current_role) já deve existir.
| Evitamos rodar ensure.context antes do cookie ser entregue (problema original).
*/
Route::middleware(['auth', 'ensure.context'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rotas do Master
    |--------------------------------------------------------------------------
    */
    Route::prefix('master')
        ->middleware(['role:master'])
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

            // Confirmação/Exclusão
            Route::get('usuarios/{usuario}/confirm-destroy', [MasterUsuarioController::class, 'confirmDestroy'])
                ->name('usuarios.confirmDestroy');
            Route::delete('usuarios/{usuario}', [MasterUsuarioController::class, 'destroy'])
                ->name('usuarios.destroy');

            // Imagens
            Route::get('imagens', [ImagemController::class, 'index'])->name('imagens.index');
            Route::post('imagens/limpar', [ImagemController::class, 'limpar'])->name('imagens.limpar');
        });

    /*
    |--------------------------------------------------------------------------
    | Rotas da Secretaria
    |--------------------------------------------------------------------------
    */
    Route::prefix('secretaria')
        ->middleware(['role:secretaria'])
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
        ->middleware(['role:escola'])
        ->name('escola.')
        ->group(function () {
            Route::get('/', fn () => redirect()->route('escola.dashboard'));
            Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

            // Usuários (professores, pais, etc.)
            Route::resource('usuarios', EscolaUsuarioController::class)->except(['show']);
            Route::post('usuarios/{usuario}/vincular', [EscolaUsuarioController::class, 'vincular'])->name('usuarios.vincular');

            // Professores
            Route::resource('professores', ProfessorController::class)->except(['show']);

            // Disciplinas
            Route::resource('disciplinas', DisciplinaController::class)->except(['show']);

            // Turmas
            Route::resource('turmas', TurmaController::class)->except(['show']);

            // Alunos
            Route::resource('alunos', AlunoController::class)->except(['show']);

            // Roles por usuário da Escola
            Route::get('usuarios/{usuario}/roles', [EscolaUsuarioController::class, 'editRoles'])
                ->name('usuarios.roles.edit');
            Route::post('usuarios/{usuario}/roles', [EscolaUsuarioController::class, 'updateRoles'])
                ->name('usuarios.roles.update');

            // Vincular aluno existente à escola atual
            Route::post('alunos/{aluno}/vincular', [AlunoController::class, 'vincular'])
                ->name('alunos.vincular');

            // Enturmações (vínculos aluno–turma)
            Route::resource('enturmacao', EnturmacaoController::class)->except(['show']);
            Route::post('enturmacao/storeBatch', [EnturmacaoController::class, 'storeBatch'])
                ->name('enturmacao.storeBatch');

            // Lotação
            Route::resource('lotacao', LotacaoController::class)->except(['show']);
            Route::prefix('lotacao')->name('lotacao.')->group(function () {
                Route::get('diretor_turma', [DiretorTurmaController::class, 'index'])
                    ->name('diretor_turma.index');
                Route::post('diretor_turma/update', [DiretorTurmaController::class, 'update'])
                    ->name('diretor_turma.update');
                Route::delete('diretor_turma/{id}', [DiretorTurmaController::class, 'destroy'])
                    ->name('diretor_turma.destroy');
            });

            // Identidade visual
            Route::get('identidade', [IdentidadeController::class, 'edit'])
                ->name('identidade.edit');
            Route::post('identidade', [IdentidadeController::class, 'update'])
                ->name('identidade.update');

            // Regimento (painel da escola)
            Route::get('regimento', [RegimentoController::class, 'index'])->name('regimento.index');
            Route::post('regimento', [RegimentoController::class, 'update'])->name('regimento.update');

            // Motivos de Ocorrência
            Route::resource('motivos', ModeloMotivoController::class)->except(['show']);
            // Importar motivos de outras escolas
            Route::get('motivos/importar', [ModeloMotivoController::class, 'importar'])
                ->name('motivos.importar');
            Route::post('motivos/importar', [ModeloMotivoController::class, 'importarSalvar'])
                ->name('motivos.importar.salvar');

            // Uploads de fotos
            Route::get('alunos/{aluno}/foto', [AlunoFotoController::class, 'edit'])->name('alunos.foto.edit');
            Route::post('alunos/{aluno}/foto', [AlunoFotoController::class, 'update'])->name('alunos.foto.update');

            Route::get('alunos/fotos-lote', [AlunoFotoLoteController::class, 'index'])->name('alunos.fotos.lote');
            Route::post('alunos/fotos-lote', [AlunoFotoLoteController::class, 'store'])->name('alunos.fotos.lote.store');
        });

    /*
    |--------------------------------------------------------------------------
    | Rotas do Professor
    |--------------------------------------------------------------------------
    */
    Route::prefix('professor')
        ->middleware(['role:professor'])
        ->name('professor.')
        ->group(function () {

            // Painel e perfil
            Route::get('dashboard', [ProfessorDashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('perfil', [PerfilController::class, 'index'])
                ->name('perfil');

            // Ofertas
            Route::prefix('ofertas')->name('ofertas.')->group(function () {
                Route::get('/', [OfertaController::class, 'index'])->name('index');
                Route::get('{oferta}/alunos', [OfertaController::class, 'alunos'])->name('alunos');
                Route::post('{oferta}/alunos', [OfertaController::class, 'alunosPost'])->name('alunos.post');

                // Ocorrências por oferta
                Route::get('{oferta}/ocorrencias/create', [OcorrenciaController::class, 'create'])
                    ->name('ocorrencias.create');
                Route::post('ocorrencias/store', [OcorrenciaController::class, 'store'])
                    ->name('ocorrencias.store');
            });

            // Ocorrências (rotas gerais)
            Route::prefix('ocorrencias')->name('ocorrencias.')->group(function () {
                Route::get('/', [OcorrenciaController::class, 'index'])->name('index');
                Route::get('{id}', [OcorrenciaController::class, 'show'])->name('show');
                Route::get('{id}/edit', [OcorrenciaController::class, 'edit'])->name('edit');
                Route::put('{id}', [OcorrenciaController::class, 'update'])->name('update');
                Route::delete('{id}', [OcorrenciaController::class, 'destroy'])->name('destroy');
                Route::patch('{id}/status', [OcorrenciaController::class, 'updateStatus'])->name('updateStatus');

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

            // Rota pública sob /professor (mantida como no original)
            Route::get('regimento/{school}', [RegimentoController::class, 'visualizar'])
                ->name('regimento.visualizar');

            // Relatórios
            Route::get('relatorios', [RelatorioController::class, 'index'])
                ->name('relatorios.index');
        });

});
