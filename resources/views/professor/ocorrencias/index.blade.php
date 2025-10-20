@extends('layouts.app')

@section('content')
<div class="container py-3">

    <h2 class="mb-4">📘 Minhas Ocorrências Registradas</h2>

    {{-- ✅ Mensagens de retorno --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🔍 Filtros de pesquisa -}}
    <form method="GET" class="mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label for="turma" class="form-label fw-semibold">Turma</label>
                <input type="text" name="turma" id="turma" value="{{ request('turma') }}" class="form-control" placeholder="Ex: 2ª Série A">
            </div>
            <div class="col-md-3">
                <label for="disciplina" class="form-label fw-semibold">Disciplina</label>
                <input type="text" name="disciplina" id="disciplina" value="{{ request('disciplina') }}" class="form-control" placeholder="Ex: Matemática">
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todas</option>
                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Ativas</option>
                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Arquivadas</option>
                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>Anuladas</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <button class="btn btn-outline-primary">🔍 Filtrar</button>
                <a href="{{ route('professor.ocorrencias.index') }}" class="btn btn-outline-secondary">🔄 Limpar</a>
            </div>
        </div>
    </form>--}}

    {{-- 🧱 Tabela de ocorrências --}}
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover align-middle mb-0" id="tabela-ocorrencias">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Aluno</th>
                    <th>Disciplina</th>
                    <th>Turma</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Motivos</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ocorrencias as $index => $oc)
                    <tr>
                        <td>{{ $index + $ocorrencias->firstItem() }}</td>
                        <td class="fw-semibold">
                            {{ $oc->aluno->nome_a ?? '—' }}
                            <br>
                            <small class="text-muted">{{ $oc->aluno->matricula ?? '' }}</small>
                        </td>
                        <td>{{ $oc->oferta->disciplina->descr_d ?? '—' }}</td>
                        <td>{{ $oc->oferta->turma->serie_turma ?? '—' }}</td>
                        <td>{{ $oc->created_at->format('d/m/Y H:i') }}</td>

                        {{-- Status visual com cores --}}
                        <td>
                            @if($oc->status == 1)
                                <span class="badge bg-success">Ativa</span>
                            @elseif($oc->status == 0)
                                <span class="badge bg-secondary">Arquivada</span>
                            @elseif($oc->status == 2)
                                <span class="badge bg-danger">Anulada</span>
                            @endif
                        </td>

                        {{-- Lista resumida dos motivos --}}
                        <td>
                            @if($oc->motivos->count() > 0)
                                <ul class="list-unstyled mb-0 small">
                                    @foreach($oc->motivos->take(2) as $m)
                                        <li>• {{ Str::limit($m->descricao, 30) }}</li>
                                    @endforeach
                                    @if($oc->motivos->count() > 2)
                                        <li class="text-muted">+{{ $oc->motivos->count() - 2 }} outros</li>
                                    @endif
                                </ul>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- ⚙️ Ações --}}
                        <td class="text-end">
                            <a href="{{ route('professor.ocorrencias.show', $oc->id) }}" class="btn btn-sm btn-outline-primary">
                                🔍 Ver
                            </a>

                            @if($oc->status == 1)
                                <form action="{{ route('professor.ocorrencias.updateStatus', $oc->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="0">
                                    <button type="submit" class="btn btn-sm btn-outline-warning">
                                        🗃 Arquivar
                                    </button>
                                </form>

                                <form action="{{ route('professor.ocorrencias.updateStatus', $oc->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="2">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        ❌ Anular
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            Nenhuma ocorrência registrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- 📄 Paginação --}}
    <div class="mt-3">
        {{ $ocorrencias->links() }}
    </div>

</div>

{{-- 💅 CSS adicional --}}
<style>
    th, td {
        vertical-align: middle !important;
    }
</style>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    initDataTable('#tabela-ocorrencias', { order: [[4, 'asc'],[1, 'asc']] }, [1, 2, 3, 4, 5, 6]);
});
</script>
@endpush