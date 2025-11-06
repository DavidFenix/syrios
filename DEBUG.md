
1) Verificar arquivos que syrios tem a mais em Http/Middleware
	--Middleware/EnsureContextSelected.php --OK
	--Middleware/RoleMiddleware.php --OK
	--Providers/AppServiceProvider.php --OK
	--Services/ContextService.php --OK
	--config/session.php --OK


--------------------------------------------------------------------------------
como está o rotas web agora
---------------------------------------------------------------------------------
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Cookie;

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

        // 📘 Rota pública (professores e outros) 
        Route::get('regimento/{school}', [RegimentoController::class, 'visualizar']) 
            ->name('regimento.visualizar');

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
Route::middleware(['web'])->group(function () {




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









    Route::get('/header-debug', function (Request $request) {
        // Garante que a sessão está iniciada
        if (!Session::isStarted()) {
            Session::start();
        }

        // Armazena algo na sessão para verificar persistência
        Session::put('debug_test', now()->toDateTimeString());

        // Monta dados de diagnóstico
        $data = [
            'timestamp' => now()->toDateTimeString(),
            'client_ip' => $request->ip(),
            'session_value' => Session::get('debug_test'),
            'cookies_received' => $request->cookies->all(),
            'headers_received' => $request->headers->all(),
        ];

        // Cria resposta JSON (forma correta)
        $response = response()->json([
            'debug_info' => $data,
            'note' => 'Check if headers or cookies below are modified by Koyeb or Cloudflare proxies.'
        ]);

        // Define cabeçalhos adicionais
        $response->headers->set('X-Debug-App', 'Syrios');
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');

        // Cookie de teste (sem parâmetros nomeados)
        $testCookie = new Cookie(
            'test_cookie',   // nome
            'ok',            // valor
            time() + 3600,   // expira em 1h (timestamp Unix)
            '/',             // caminho
            null,            // domínio
            true,            // secure
            false,           // httpOnly
            false,           // raw
            'None'           // SameSite
        );

        $response->headers->setCookie($testCookie);

        return $response;
    });

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/choose-school', [LoginController::class, 'chooseSchool'])->name('choose.school');
    Route::get('/choose-role/{schoolId}', [LoginController::class, 'chooseRole'])->name('choose.role');
    Route::post('/set-context', [LoginController::class, 'setContextPost'])->name('set.context');

    Route::get('regimento/{school}', [RegimentoController::class, 'visualizar']) 
        ->name('regimento.visualizar');

    // Debug simples
    Route::get('/debug', fn() => ['secure' => request()->isSecure(), 'url' => url('/')]);
    Route::get('/debug-headers', fn() => response()->json(['headers' => request()->headers->all()]));

    Route::get('/cookie-test', function (Request $request) {
        $response = response('<h1>cookie test</h1>');
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

    Route::get('/', fn() => redirect()->route('login'));
});

---------------------------------------------------------------------------------------------
como está o .env que mandei para o railway
-------------------------------------------------------------------------------------
APP_NAME=Syrios
APP_ENV=production
APP_KEY=base64:zHgmDP/hcdkRkbaS6mOh9tazXOMvlIudKvZxJ1knAio=
APP_DEBUG=true
APP_URL=https://syrios.up.railway.app

# Sessão SEM banco
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=None

# DESATIVA TOTALMENTE QUALQUER BANCO
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_DRIVER=file
QUEUE_CONNECTION=sync