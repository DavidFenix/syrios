@extends('layouts.app')

@section('content')
<div class="container py-3">

    @include('components.pdf_header')
    
    {{-- 🔙 Cabeçalho --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>📜 Histórico de Ocorrências</h2>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">⬅ Voltar</a>
    </div>

    

    {{-- 👨‍🎓 Informações do aluno --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body d-flex align-items-center">
        @php
            $fotoNome = $aluno->matricula . '.png';
            $fotoPath = public_path('storage/img-user/' . $fotoNome);
            $fotoUrl = file_exists($fotoPath)
                ? asset('storage/img-user/' . $fotoNome)
                : asset('storage/img-user/padrao.png');

            $turma = optional($aluno->enturmacao()->with('turma')->first())->turma->serie_turma ?? '-';
        @endphp
        <img src="{{ $fotoUrl }}" class="rounded-circle me-3"
                 width="70" height="70" style="object-fit: cover;">
        <div class="card-body">
            <h5 class="card-title mb-2"><strong>{{ $aluno->nome_a }}</strong></h5>
            @php
                $turma = optional($aluno->enturmacao()->with('turma')->first())->turma->serie_turma ?? '-';
            @endphp
            <p class="mb-0">
                <strong>Matrícula:</strong> {{ $aluno->matricula }}<br>
                <strong>Turma:</strong> {{ $turma }}
            </p>
        </div>
        </div>
    </div>

    {{-- 📋 Lista de ocorrências --}}
    @forelse($ocorrencias as $oc)
        @php
            $statusColors = [
                1 => 'success',   // ativa
                0 => 'secondary', // arquivada
                2 => 'danger',    // anulada
            ];
            $statusLabels = [
                1 => 'Ativa',
                0 => 'Arquivada',
                2 => 'Anulada',
            ];
        @endphp

        <div class="card mb-3 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <strong>Data:</strong> {{ $oc->created_at->format('d/m/Y H:i') }} <br>
                    <strong>Disciplina:</strong> {{ $oc->oferta->disciplina->descr_d ?? '-' }} <br>
                    <strong>Professor:</strong> {{ $oc->professor->usuario->nome_u ?? '-' }}
                </div>
                <span class="badge bg-{{ $statusColors[$oc->status] ?? 'secondary' }}">
                    {{ $statusLabels[$oc->status] ?? 'Desconhecido' }}
                </span>
            </div>

            <div class="card-body">
                {{-- 🧾 Motivos --}}
                <p class="mb-2"><strong>Motivos:</strong></p>
                <ul>
                    @foreach($oc->motivos as $motivo)
                        <li>
                            <span class="badge bg-info text-dark" title="{{ $motivo->categoria }}">
                                {{ $motivo->descricao }}
                            </span>
                        </li>
                    @endforeach
                </ul>

                {{-- 🏫 Outros campos --}}
                @if($oc->descricao)
                    <p><strong>Descrição:</strong> {{ $oc->descricao }}</p>
                @endif

                @if($oc->local)
                    <p><strong>Local:</strong> {{ $oc->local }}</p>
                @endif

                @if($oc->atitude)
                    <p><strong>Atitude:</strong> {{ $oc->atitude }}</p>
                @endif

                @if($oc->comportamento)
                    <p><strong>Comportamento:</strong> {{ $oc->comportamento }}</p>
                @endif

                @if($oc->sugestao)
                    <p><strong>Sugestão:</strong> {{ $oc->sugestao }}</p>
                @endif
            </div>
        </div>

    @empty
        <div class="alert alert-secondary text-center">
            Nenhuma ocorrência registrada para este aluno.
        </div>
    @endforelse
</div>

{{-- 🎨 Estilos opcionais --}}
<style>
.card-header strong { font-weight: 600; }
.card-body ul { padding-left: 1.3rem; margin-bottom: 0.5rem; }
</style>

@endsection
