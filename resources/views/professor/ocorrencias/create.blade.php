@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h2 class="mb-4">📝 Aplicar Ocorrência</h2>

    {{-- 🔙 Voltar --}}
    <a href="{{ url()->previous() }}" class="btn btn-secondary mb-3">⬅ Voltar</a>

    {{-- ⚙️ Formulário principal --}}
    <form action="{{ route('professor.ocorrencias.store') }}" method="POST">
        @csrf

        {{-- 🧾 Informações básicas --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">🎯 Informações gerais</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Alunos selecionados</label>
                    <div class="border rounded p-2 bg-light">
                        @forelse($alunos as $a)
                            <span class="badge bg-primary m-1">{{ $a->nome_a }}</span>
                            <input type="hidden" name="alunos[]" value="{{ $a->id }}">
                        @empty
                            <p class="text-muted">Nenhum aluno selecionado.</p>
                        @endforelse
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Disciplina</label>
                    <input type="text" class="form-control" value="{{ $oferta->disciplina->descr_d ?? '-' }}" disabled>
                    <input type="hidden" name="oferta_id" value="{{ $oferta->id }}">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Turma</label>
                    <input type="text" class="form-control" value="{{ $oferta->turma->serie_turma ?? '-' }}" disabled>
                </div>
            </div>
        </div>

        {{-- 📋 Motivos da ocorrência --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <button class="btn btn-link text-decoration-none fw-semibold w-100 text-start" 
                        type="button" data-bs-toggle="collapse" data-bs-target="#motivosCollapse"
                        aria-expanded="true" aria-controls="motivosCollapse">
                    📌 Escolher motivo(s) da ocorrência
                </button>
            </div>
            <div id="motivosCollapse" class="collapse show">
                <div class="card-body">
                    @forelse($motivos as $motivo)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="motivos[]" value="{{ $motivo->id }}" id="motivo{{ $motivo->id }}">
                            <label class="form-check-label" for="motivo{{ $motivo->id }}">
                                {{ $motivo->descr_r }}
                            </label>
                        </div>
                    @empty
                        <p class="text-muted">Nenhum motivo cadastrado.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 🗒 Outra descrição --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">🖊 Descrição adicional</h5>
                <textarea name="descricao_extra" class="form-control" rows="3"
                          placeholder="Descreva a situação, se necessário..."></textarea>
            </div>
        </div>

        {{-- ⚙️ Detalhes complementares --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">⚙️ Informações complementares</h5>

                <div class="row g-3">
                    {{-- Local --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Local</label>
                        <select name="local" class="form-select">
                            <option value="Sala de aula" selected>Sala de aula</option>
                            <option value="Ambientes de apoio">Ambientes de apoio</option>
                            <option value="Pátio da escola">Pátio da escola</option>
                            <option value="Quadra poliesportiva">Quadra poliesportiva</option>
                            <option value="Galerias">Galerias</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>

                    {{-- Atitude --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Atitude do professor</label>
                        <select name="atitude" class="form-select">
                            <option value="Advertência" selected>Advertência</option>
                            <option value="Ordem de saída de sala">Ordem de saída de sala</option>
                            <option value="Outra">Outra</option>
                        </select>
                    </div>

                    {{-- Outra atitude --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Outra atitude (opcional)</label>
                        <input type="text" name="outra_atitude" class="form-control"
                               placeholder="Descreva outra atitude, se necessário">
                    </div>

                    {{-- Comportamento --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Comportamento do aluno</label>
                        <select name="comportamento" class="form-select">
                            <option value="1ª vez" selected>1ª vez</option>
                            <option value="Reincidente (pouco frequente)">Reincidente (pouco frequente)</option>
                            <option value="Reincidente (frequente)">Reincidente (frequente)</option>
                        </select>
                    </div>

                    {{-- Sugestão de medidas --}}
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">Sugestão de medidas</label>
                        <textarea name="sugestao" class="form-control" rows="2"
                                  placeholder="Sugestões de medidas que a escola pode adotar"></textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Botões --}}
        <div class="text-end">
            <button type="submit" class="btn btn-success">✅ Aplicar Ocorrência</button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Cancelar</a>
        </div>
    </form>
</div>

<style>
    .card { border-radius: 1rem; }
    .card-header button { color: #333; }
    .form-check-label { user-select: none; }
</style>
@endsection
