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


Route::get('/diag/headers', function () {
    // força criar uma sessão
    session(['_diag' => now()->toDateTimeString()]);

    return response('ok', 200)
        ->header('X-Diag', '1')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
});