@extends('layouts.app')

@section('content')
<div class="container py-3">

    <h1 class="mb-4">📚 Minhas Ofertas</h1>

    {{-- ✅ Mensagens de feedback::ja tenho no layout padrao
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    --}}

    {{-- 🧮 Resumo rápido --}}
    @if($ofertas->count() > 0)
        <div class="alert alert-info d-flex align-items-center justify-content-between">
            <div>
                Você possui <strong>{{ $ofertas->count() }}</strong> oferta(s) ativa(s).
            </div>
            <small class="text-muted">Ano letivo: {{ session('ano_letivo_atual') ?? date('Y') }}</small>
        </div>
    @endif

    {{-- 🧱 Listagem das ofertas --}}
    @forelse($ofertas as $i => $oferta)
        @php
            $total = ($oferta->qtd1 ?? 0) + ($oferta->qtd2 ?? 0) + ($oferta->qtd3 ?? 0) + ($oferta->qtd4 ?? 0) + ($oferta->qtd5 ?? 0);
            $alerta = $oferta->qtd4 > 0 || $oferta->qtd5 > 0;
        @endphp

        <div class="card mb-3 shadow-sm rounded-3 border-{{ $alerta ? 'danger' : 'success' }}">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">

                {{-- 📘 Botão principal: disciplina/turma --}}
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="{{ route('professor.ofertas.alunos', $oferta->id) }}" 
                       class="btn {{ $alerta ? 'btn-outline-danger' : 'btn-outline-primary' }} fw-semibold">
                        {{ $i+1 }}. {{ $oferta->disciplina->descr_d ?? 'Sem Disciplina' }}
                    </a>
                    <span class="text-muted">{{ Str::limit($oferta->turma->serie_turma ?? '-', 12) }}</span>

                    {{-- ⚠️ Alerta de turma crítica --}}
                    @if($alerta)
                        <span class="badge bg-danger text-white">⚠️ Alunos em atenção</span>
                    @endif
                </div>

                {{-- 🔢 Visão Geral --}}
                <div class="mt-2 mt-md-0">
                    <button class="btn btn-sm btn-outline-secondary" 
                            data-bs-toggle="collapse"
                            data-bs-target="#visao{{ $oferta->id }}"
                            aria-expanded="false"
                            aria-controls="visao{{ $oferta->id }}">
                        Visão Geral:
                        <span class="badge bg-secondary">{{ $oferta->qtd1 ?? 0 }}</span>
                        <span class="badge bg-warning text-dark">{{ $oferta->qtd2 ?? 0 }}</span>
                        <span class="badge bg-amber text-dark">{{ $oferta->qtd3 ?? 0 }}</span>
                        <span class="badge bg-orange text-white">{{ $oferta->qtd4 ?? 0 }}</span>
                        <span class="badge bg-danger">{{ $oferta->qtd5 ?? 0 }}</span>
                    </button>
                </div>
            </div>

            {{-- 🎨 Accordion (legenda) --}}
            <div id="visao{{ $oferta->id }}" class="collapse">
                <div class="card-body border-top small bg-light-subtle">
                    <div class="row text-center g-2">
                        <div class="col-6 col-md-2">
                            <span class="badge bg-secondary">&nbsp;</span><br>
                            1 ocorrência ativa ({{ $oferta->qtd1 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-warning text-dark">&nbsp;</span><br>
                            2 ocorrências ativas ({{ $oferta->qtd2 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-amber text-dark">&nbsp;</span><br>
                            3 ocorrências ativas ({{ $oferta->qtd3 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-orange text-white">&nbsp;</span><br>
                            4 ocorrências ativas ({{ $oferta->qtd4 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-danger">&nbsp;</span><br>
                            5+ ocorrências ativas ({{ $oferta->qtd5 ?? 0 }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center">
            Nenhuma oferta cadastrada para este professor.
        </div>
    @endforelse
</div>

{{-- 🔸 Estilos adicionais --}}
<style>
    .bg-orange { background-color: #ff9800 !important; color: #fff !important; }
    .bg-amber { background-color: #ffc107 !important; }
    .collapse {
        transition: all 0.25s ease-in-out !important;
    }
</style>

@endsection




{{--
@extends('layouts.app')

@section('content')
<div class="container py-3">

    <h1 class="mb-4">📚 Minhas Ofertas</h1>

    {{-- ✅ Mensagem de retorno -}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    {{-- 🧱 Listagem das ofertas -}}
    @forelse($ofertas as $i => $oferta)
        <div class="card mb-3 shadow-sm rounded-3">
            <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center">

                {{-- 📘 Botão principal: disciplina/turma -}}
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <a href="{{ route('professor.ofertas.alunos', $oferta->id) }}" 
                       class="btn btn-outline-primary fw-semibold">
                        {{ $i+1 }}. {{ $oferta->disciplina->descr_d ?? 'Sem Disciplina' }}
                    </a>

                    <span class="text-muted">
                        {{ Str::limit($oferta->turma->serie_turma ?? '-', 10) }}
                    </span>
                </div>

                {{-- 🔢 Visão Geral dos badges -}}
                <div class="mt-2 mt-md-0">
                    <button class="btn btn-sm btn-outline-secondary" 
                            data-bs-toggle="collapse"
                            data-bs-target="#visao{{ $oferta->id }}"
                            aria-expanded="false"
                            aria-controls="visao{{ $oferta->id }}">
                        Visão Geral:
                        <span class="badge bg-secondary">{{ $oferta->qtd1 ?? 0 }}</span>
                        <span class="badge bg-warning text-dark">{{ $oferta->qtd2 ?? 0 }}</span>
                        <span class="badge bg-warning">{{ $oferta->qtd3 ?? 0 }}</span>
                        <span class="badge bg-orange text-white">{{ $oferta->qtd4 ?? 0 }}</span>
                        <span class="badge bg-danger">{{ $oferta->qtd5 ?? 0 }}</span>
                    </button>
                </div>
            </div>

            {{-- 🎨 Accordion (legenda das cores) -}}
            <div id="visao{{ $oferta->id }}" class="collapse">
                <div class="card-body border-top small">
                    <div class="row text-center">
                        <div class="col-6 col-md-2">
                            <span class="badge bg-secondary">&nbsp;</span><br>
                            1 ocorrência ativa ({{ $oferta->qtd1 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-warning text-dark">&nbsp;</span><br>
                            2 ocorrências ativas ({{ $oferta->qtd2 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-warning">&nbsp;</span><br>
                            3 ocorrências ativas ({{ $oferta->qtd3 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-orange text-white">&nbsp;</span><br>
                            4 ocorrências ativas ({{ $oferta->qtd4 ?? 0 }})
                        </div>
                        <div class="col-6 col-md-2">
                            <span class="badge bg-danger">&nbsp;</span><br>
                            5+ ocorrências ativas ({{ $oferta->qtd5 ?? 0 }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-secondary text-center">
            Nenhuma oferta cadastrada para este professor.
        </div>
    @endforelse

</div>

{{-- 🔸 Cores adicionais personalizadas -}}
<style>
    .bg-orange { background-color: #ff9800 !important; color: #fff !important; }
    .card-body button:focus { box-shadow: 0 0 0 0.2rem rgba(25,135,84,.25); }
</style>

@endsection
--}}