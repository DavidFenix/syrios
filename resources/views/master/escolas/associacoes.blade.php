@extends('layouts.app')

@section('content')
<div class="container">
    <h2>🏫 Associações Escola Mãe ↔ Filhas</h2>

    @php
        $auth = auth()->user();
    @endphp

    {{-- ⚙️ Formulário de associação: visível apenas para Master e Super Master --}}
    @if($auth && ($auth->hasRole('master') || $auth->is_super_master))
        <form method="POST" action="{{ route('master.escolas.associar') }}" class="row g-3 mb-4">
            @csrf

            {{-- ESCOLA MÃE --}}
            <div class="col-md-5">
                <label class="form-label fw-semibold">Escola Mãe (Secretaria)</label>
                <select name="mae_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach($escolasMae as $mae)
                        <option value="{{ $mae->id }}">{{ $mae->nome_e }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Apenas secretarias e escolas-mãe podem receber filhas.</small>
            </div>

            {{-- ESCOLA FILHA --}}
            <div class="col-md-5">
                <label class="form-label fw-semibold">Escola Filha</label>
                <select name="filha_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach($escolasFilhasDisponiveis as $filha)
                        @php
                            // Descobre se essa escola já é filha de alguém
                            $maeAtual = $filha->mae;
                        @endphp
                        <option value="{{ $filha->id }}">
                            {{ $filha->nome_e }}
                            @if($maeAtual)
                                (já filha de {{ $maeAtual->nome_e }})
                            @endif
                        </option>
                    @endforeach
                </select>
                <small class="text-muted">Escolas que já são mães não aparecem aqui.</small>
            </div>

            {{-- BOTÃO --}}
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">🔗 Associar</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning">
            🚫 Apenas usuários <strong>Master</strong> ou <strong>Super Master</strong> podem criar associações entre escolas.
        </div>
    @endif


    {{-- =======================
         Tabela de Associações
    ======================== --}}
    <h3 class="mt-5 mb-3">📋 Situação das Escolas</h3>

    <table class="table table-bordered align-middle shadow-sm">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Nome da Escola</th>
                <th>Tipo</th>
                <th>Secretaria (Mãe)</th>
                <th>Filhas (se houver)</th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\Escola::orderBy('nome_e')->get() as $e)
                @php
                    $temFilhas = $e->filhas()->count() > 0;
                    $mae = $e->mae;
                @endphp
                <tr>
                    <td>{{ $e->id }}</td>
                    <td>{{ $e->nome_e }}</td>
                    <td>
                        @if($e->is_master)
                            <span class="badge bg-danger">Secretaria Master</span>
                        @elseif($temFilhas)
                            <span class="badge bg-primary">MÃE</span>
                        @elseif($mae)
                            <span class="badge bg-success">FILHA</span>
                        @else
                            <span class="badge bg-secondary">ISOLADA</span>
                        @endif
                    </td>
                    <td>{{ $mae->nome_e ?? '—' }}</td>
                    <td>
                        @if($temFilhas)
                            <ul class="mb-0">
                                @foreach($e->filhas as $f)
                                    <li>{{ $f->nome_e }}</li>
                                @endforeach
                            </ul>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection



{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>🏫 Associações Escola Mãe ↔ Filhas</h2>

    {{-- ⚙️ Obtém usuário autenticado -}}
    @php
        $auth = auth()->user();
    @endphp

    {{-- ⚙️ Formulário: só aparece para Masters e Super Masters -}}
    @if($auth && ($auth->hasRole('master') || $auth->is_super_master))
        <form method="POST" action="{{ route('master.escolas.associar') }}" class="row g-3 mb-4">
            @csrf

            <div class="col-md-5">
                <label class="form-label fw-semibold">Escola Mãe (Secretaria)</label>
                <select name="mae_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach($escolasMae as $mae)
                        <option value="{{ $mae->id }}">{{ $mae->nome_e }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Apenas secretarias e escolas-mãe podem receber filhas.</small>
            </div>

            <div class="col-md-5">
                <label class="form-label fw-semibold">Escola Filha</label>
                <select name="filha_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach($escolasFilhasDisponiveis as $filha)
                        <option value="{{ $filha->id }}">{{ $filha->nome_e }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Escolas que já são mães não aparecem nesta lista.</small>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">🔗 Associar</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning">
            🚫 Apenas usuários <strong>Master</strong> ou <strong>Super Master</strong> podem criar associações entre escolas.
        </div>
    @endif


    {{-- =======================
         Tabela de Associações
    ======================== -}}
    <h3 class="mt-5 mb-3">📋 Situação das Escolas</h3>

    <table class="table table-bordered align-middle shadow-sm">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">#</th>
                <th>Nome da Escola</th>
                <th>Tipo</th>
                <th>Secretaria (Mãe)</th>
                <th>Filhas (se houver)</th>
            </tr>
        </thead>
        <tbody>
            @foreach(\App\Models\Escola::orderBy('nome_e')->get() as $e)
                @php
                    $temFilhas = $e->filhas()->count() > 0;
                    $mae = $e->mae;
                @endphp
                <tr>
                    <td>{{ $e->id }}</td>
                    <td>{{ $e->nome_e }}</td>
                    <td>
                        @if($e->is_master)
                            <span class="badge bg-danger">Secretaria Master</span>
                        @elseif($temFilhas)
                            <span class="badge bg-primary">MÃE</span>
                        @elseif($mae)
                            <span class="badge bg-success">FILHA</span>
                        @else
                            <span class="badge bg-secondary">ISOLADA</span>
                        @endif
                    </td>
                    <td>{{ $mae->nome_e ?? '—' }}</td>
                    <td>
                        @if($temFilhas)
                            <ul class="mb-0">
                                @foreach($e->filhas as $f)
                                    <li>{{ $f->nome_e }}</li>
                                @endforeach
                            </ul>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
--}}

{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Associações Escola Mãe ↔ Filhas</h2>

    @php
        $auth = auth()->user();
    @endphp

    {{-- ⚙️ Formulário: só aparece para Masters e Super Masters -}}
    @if($auth->hasRole('master') || $auth->is_super_master)
        <form method="POST" action="{{ route('master.escolas.associar') }}" class="row g-3 mb-4">
            @csrf
            <div class="col-md-5">
                <label class="form-label">Escola Mãe (Secretaria)</label>
                <select name="mae_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach($escolasMae as $mae)
                        <option value="{{ $mae->id }}">{{ $mae->nome_e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-5">
                <label class="form-label">Escola Filha</label>
                <select name="filha_id" class="form-select" required>
                    <option value="">-- escolha --</option>
                    @foreach(\App\Models\Escola::where('is_master', 0)->orderBy('nome_e')->get() as $filha)
                        <option value="{{ $filha->id }}">{{ $filha->nome_e }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Associar</button>
            </div>
        </form>
    @else
        <div class="alert alert-warning">
            🚫 Apenas usuários Master ou Super Master podem criar associações entre escolas.
        </div>
    @endif

    <h2>Ver Escolas Filhas</h2>

    @include('master.escolas._list_assoc', [
        'escolasMae' => $escolasMae,
        'maeSelecionada' => $maeSelecionada,
        'escolasFilhas' => $escolasFilhas,
        'nomeMae' => $nomeMae,
    ])
</div>
@endsection
--}}



{{--
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Associações Escola Mãe ↔ Filhas</h2>

    {{-- Formulário para criar nova associação -}}
    <form method="POST" action="{{ route('master.escolas.associar') }}" class="row g-3 mb-4">
        @csrf
        <div class="col-md-5">
            <label class="form-label">Escola Mãe (Secretaria)</label>
            <select name="mae_id" class="form-select" required>
                <option value="">-- escolha --</option>
                @foreach($escolasMae as $mae)
                    <option value="{{ $mae->id }}">{{ $mae->nome_e }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">Escola Filha</label>
            <select name="filha_id" class="form-select" required>
                @foreach(\App\Models\Escola::whereNotNull('secretaria_id')->get() as $filha)
                    <option value="{{ $filha->id }}">{{ $filha->nome_e }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Associar</button>
        </div>
    </form>

    <h2>Ver Escolas Filhas</h2>
    @include('master.escolas._list_assoc', [
        'escolasMae' => $escolasMae,
        'maeSelecionada' => $maeSelecionada,
        'escolasFilhas' => $escolasFilhas,
        'nomeMae' => $nomeMae,
    ])
    
</div>
@endsection
--}}




{{-- Select para listar filhas de uma mãe -}}
    <form method="GET" action="{{ route('master.escolas.associacoes') }}" class="mb-3">
        <label for="mae_id">Ver Filhas de:</label>
        <select name="mae_id" id="mae_id" class="form-select d-inline w-auto">
            <option value="">-- escolha --</option>
            @foreach($escolasMae as $mae)
                <option value="{{ $mae->id }}" {{ $maeSelecionada == $mae->id ? 'selected' : '' }}>
                    {{ $mae->nome_e }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-secondary">Ver</button>
    </form>

    {{-- Tabela de filhas -}}
    @if($maeSelecionada && $nomeMae)
        <h3>Escolas Filhas de <strong>{{ $nomeMae }}</strong></h3>
        @if($escolasFilhas->isNotEmpty())
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>INEP</th>
                        <th>CNPJ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($escolasFilhas as $filha)
                        <tr>
                            <td>{{ $filha->id }}</td>
                            <td>{{ $filha->nome_e }}</td>
                            <td>{{ $filha->inep }}</td>
                            <td>{{ $filha->cnpj }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>Nenhuma escola filha vinculada.</p>
        @endif
    @endif
    --}}