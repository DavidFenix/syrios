<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Master
use App\Http\Controllers\Master\EscolaController as MasterEscolaController;
use App\Http\Controllers\Master\RoleController as MasterRoleController;
use App\Http\Controllers\Master\UsuarioController as MasterUsuarioController;
use App\Http\Controllers\Master\DashboardController as MasterDashboardController;

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
        
        Route::prefix('ofertas')->name('ofertas.')->group(function () {
        Route::get('/', [App\Http\Controllers\Professor\OfertaController::class, 'index'])
            ->name('index');

        Route::get('{oferta}/alunos', [App\Http\Controllers\Professor\OfertaController::class, 'alunos'])
            ->name('alunos');

        // Ocorrências
        Route::get('{oferta}/ocorrencias/create', [App\Http\Controllers\Professor\OcorrenciaController::class, 'create'])
            ->name('ocorrencias.create');

        Route::post('ocorrencias/store', [App\Http\Controllers\Professor\OcorrenciaController::class, 'store'])
            ->name('ocorrencias.store');
    });

    Route::get('ocorrencias', [App\Http\Controllers\Professor\OcorrenciaController::class, 'index'])
        ->name('ocorrencias.index');

    Route::get('ocorrencias/{id}', [App\Http\Controllers\Professor\OcorrenciaController::class, 'show'])
        ->name('ocorrencias.show');

    Route::patch('ocorrencias/{id}/status', [App\Http\Controllers\Professor\OcorrenciaController::class, 'updateStatus'])
        ->name('ocorrencias.updateStatus');

    Route::get('ocorrencias/historico/{aluno}', [OcorrenciaController::class, 'historico'])
    ->name('ocorrencias.historico');



        //antes da mudança no banco
            // 🏠 Painel do professor
            Route::get('dashboard', [App\Http\Controllers\Professor\DashboardController::class, 'index'])
                ->name('dashboard');

            // 📚 Ofertas (disciplinas/turmas)
            Route::get('ofertas', [App\Http\Controllers\Professor\OfertaController::class, 'index'])
                ->name('ofertas.index');

            // 👥 Alunos da turma selecionada
            Route::get('ofertas/{oferta}/alunos', [App\Http\Controllers\Professor\OfertaController::class, 'alunos'])
                ->name('ofertas.alunos');

            // 📝 Aplicar ocorrência em alunos selecionados
            Route::get('ocorrencias/create', [App\Http\Controllers\Professor\OcorrenciaController::class, 'create'])
                ->name('ocorrencias.create');

            Route::post('ocorrencias', [App\Http\Controllers\Professor\OcorrenciaController::class, 'store'])
                ->name('ocorrencias.store');

            // 📄 Histórico de ocorrências de um aluno
            Route::get('ocorrencias/{aluno}/historico', [App\Http\Controllers\Professor\OcorrenciaController::class, 'historico'])
                ->name('ocorrencias.historico');

             // 🧩 Ocorrências
            Route::resource('ocorrencias', App\Http\Controllers\Professor\OcorrenciaController::class)
                ->except(['edit', 'update']);

            // routes/web.php
            Route::post('ofertas/{oferta}/alunos', [App\Http\Controllers\Professor\OfertaController::class, 'alunosPost'])
                ->name('ofertas.alunos.post');


        //sugeridas agora no final
            Route::get('dashboard', [App\Http\Controllers\Professor\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('ofertas', [App\Http\Controllers\Professor\OfertaController::class, 'index'])
                ->name('ofertas.index');

            Route::get('ofertas/{id}/alunos', [App\Http\Controllers\Professor\OfertaController::class, 'alunos'])
                ->name('ofertas.alunos');

            Route::get('ocorrencias', [App\Http\Controllers\Professor\OcorrenciaController::class, 'index'])
                ->name('ocorrencias.index');

            Route::get('relatorios', [App\Http\Controllers\Professor\RelatorioController::class, 'index'])
                ->name('relatorios.index');

            Route::get('perfil', [App\Http\Controllers\Professor\PerfilController::class, 'index'])
                ->name('perfil');

            Route::get('dashboard', [App\Http\Controllers\Professor\DashboardController::class, 'index'])
                ->name('dashboard');

            Route::get('ocorrencias/historico/{aluno}', [App\Http\Controllers\Professor\OcorrenciaController::class, 'historicoResumido'])
                ->name('ocorrencias.historico_resumido');

            // PDF (opcional)
            Route::get('ocorrencias/pdf/{aluno}', [App\Http\Controllers\Professor\OcorrenciaController::class, 'gerarPdf'])
                ->name('ocorrencias.pdf');

    });







// Route::prefix('professor')
//     ->middleware(['auth', 'role:professor', 'ensure.context'])
//     ->name('professor.')
//     ->group(function () {
//         Route::get('dashboard', [App\Http\Controllers\Professor\DashboardController::class, 'index'])
//             ->name('dashboard');

//     });




/*
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Master
use App\Http\Controllers\Master\EscolaController as MasterEscolaController;
use App\Http\Controllers\Master\RoleController as MasterRoleController;
use App\Http\Controllers\Master\UsuarioController as MasterUsuarioController;
use App\Http\Controllers\Master\DashboardController as MasterDashboardController;

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

//Rotas Públicas (sem login)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

//Seleção de contexto
Route::get('/choose-school', [LoginController::class, 'chooseSchool'])->name('choose.school');
Route::get('/choose-role/{schoolId}', [LoginController::class, 'chooseRole'])->name('choose.role');
Route::post('/set-context', [LoginController::class, 'setContextPost'])->name('set.context');

//Rotas do Master (apenas usuários com role=master)
Route::prefix('master')
    ->middleware(['auth', 'role:master', 'ensure.context'])
    ->name('master.')
    ->group(function () {

        // Dashboard principal do Master
        Route::get('dashboard', [MasterDashboardController::class, 'index'])
            ->name('dashboard');

        // Redireciona /master para o dashboard
        Route::get('/', fn () => redirect()->route('master.dashboard'));

        // CRUDs principais
        Route::resource('escolas', MasterEscolaController::class)->except(['show']);
        Route::resource('roles', MasterRoleController::class);
        Route::resource('usuarios', MasterUsuarioController::class);

        // Associações Escola Mãe ↔ Escola Filha
        Route::get('associacoes', [MasterEscolaController::class, 'associacoes'])
            ->name('escolas.associacoes');
        Route::post('associacoes', [MasterEscolaController::class, 'associarFilha'])
            ->name('escolas.associar');

    });

//Rotas da Secretaria (apenas usuários com role=secretaria)
Route::prefix('secretaria')
    ->middleware(['auth', 'role:secretaria', 'ensure.context'])
    ->name('secretaria.')
    ->group(function () {

        // Redireciona /secretaria para a lista de escolas filhas
        Route::get('/', fn () => redirect()->route('secretaria.escolas.index'))
            ->name('dashboard');

        // CRUDs da Secretaria
        Route::resource('escolas', SecretariaEscolaController::class)->except(['show']);
        Route::resource('usuarios', SecretariaUsuarioController::class)->except(['show']);
    });

//Rotas da Escola (apenas usuários com role=escola)
Route::prefix('escola')
    ->name('escola.')
    ->middleware(['auth', 'role:escola', 'ensure.context'])
    ->group(function () {
        Route::get('/', function () {
            return redirect()->route('escola.dashboard');
        });

        Route::get('dashboard', [App\Http\Controllers\Escola\DashboardController::class, 'index'])
            ->name('dashboard');

        // CRUD de Usuários (professores, pais, etc.)
        Route::resource('professores', App\Http\Controllers\Escola\ProfessorController::class)->except(['show']);
        
        // CRUD de Usuários (professores, pais, etc.)
        Route::resource('usuarios', App\Http\Controllers\Escola\UsuarioController::class)->except(['show']);

        // CRUD de Disciplinas
        Route::resource('disciplinas', App\Http\Controllers\Escola\DisciplinaController::class)->except(['show']);

        // CRUD de Turmas
        Route::resource('turmas', App\Http\Controllers\Escola\TurmaController::class)->except(['show']);

        // CRUD de Alunos
        Route::resource('alunos', App\Http\Controllers\Escola\AlunoController::class)->except(['show']);

        Route::resource('usuarios', EscolaUsuarioController::class)->except(['show']);

        Route::post('usuarios/{usuario}/vincular', [EscolaUsuarioController::class, 'vincular'])->name('usuarios.vincular');


    });





Route::prefix('escola')
    ->middleware(['auth', 'role:escola'])
    ->name('escola.')
    ->group(function () {

        // Redireciona /escola para a lista de usuários da escola
        Route::get('/', fn () => redirect()->route('escola.usuarios.index'));

        // CRUD de usuários da escola (professores, pais, etc.)
        Route::resource('usuarios', EscolaUsuarioController::class)->except(['show']);
    });

Route::prefix('escola')->name('escola.')->middleware(['auth', 'role:escola'])->group(function () {

    //redireciona
    Route::get('/', function () {
            return redirect()->route('escola.dashboard');
        });

    // Dashboard
    Route::get('/dashboard', [App\Http\Controllers\Escola\DashboardController::class, 'index'])
        ->name('dashboard');

    // CRUDs específicos da escola logada
    Route::resource('professores', App\Http\Controllers\Escola\ProfessorController::class)->except(['show']);
    Route::resource('alunos', App\Http\Controllers\Escola\AlunoController::class)->except(['show']);
    Route::resource('disciplinas', App\Http\Controllers\Escola\DisciplinaController::class)->except(['show']);
    Route::resource('turmas', App\Http\Controllers\Escola\TurmaController::class)->except(['show']);
});
*/
