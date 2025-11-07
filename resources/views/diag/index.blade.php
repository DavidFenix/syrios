@extends('layouts.app')

@section('content')
<div class="container">
    <h1>🩺 Diagnóstico do Ambiente Syrios</h1>
    <p class="text-muted">Gerado em {{ now()->format('Y-m-d H:i:s') }}</p>
    <hr>

    {{-- ===============================
         STATUS VISUAL
    ================================ --}}
    <h3>✅ Status Geral</h3>
    <ul class="list-group mb-4">
        <li class="list-group-item">
            <strong>Railway:</strong>
            @if($status['railway']) ✅ Ativo @else ❌ Não detectado @endif
        </li>
        <li class="list-group-item">
            <strong>HTTPS:</strong>
            @if($status['https']) ✅ Conexão segura @else ⚠️ Não detectado @endif
        </li>
        <li class="list-group-item">
            <strong>Cookie de Teste:</strong>
            @if($status['cookie_received']) ✅ Recebido @else ⚠️ Ainda não recebido @endif
        </li>
        <li class="list-group-item">
            <strong>SESSION_SECURE_COOKIE:</strong>
            @php $secure = $status['secure_cookie']; @endphp

            @if(in_array($secure, [true, 'true', 1, '1'], true))
                ✅ true
            @elseif(in_array($secure, [false, 'false', 0, '0'], true))
                ⚠️ false
            @else
                null
            @endif

        </li>
        <li class="list-group-item">
            <strong>APP_ENV:</strong>
            @if($status['env'] === 'production')
                ✅ production
            @else
                ⚠️ {{ $status['env'] }}
                <span class="text-muted">(deveria ser <code>production</code>)</span>
            @endif
        </li>
    </ul>

    {{-- ===============================
         VARIÁVEIS DE AMBIENTE
    ================================ --}}
    <h3>🌐 Variáveis de Ambiente (mascaradas)</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-sm small">
            <thead><tr><th>Chave</th><th>Valor</th></tr></thead>
            <tbody>
                @foreach($env as $key => $value)
                    <tr>
                        <td><code>{{ $key }}</code></td>
                        <td>{{ is_bool($value) ? ($value ? 'true' : 'false') : ($value ?? 'null') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <hr>

    {{-- ===============================
         CONFIG CORS
    ================================ --}}
    <h3>🧩 Configuração CORS (config/cors.php)</h3>
    <pre class="bg-dark text-light p-3 rounded"><code>{{ json_encode($cors, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>

    <hr>

    {{-- ===============================
         ARQUIVOS RELEVANTES
    ================================ --}}
    <h3>📁 Arquivos Importantes</h3>
    @foreach($files as $name => $content)
        <details class="mb-3">
            <summary><strong>{{ $name }}</strong></summary>
            <pre class="bg-light border p-3 mt-2" style="max-height: 400px; overflow-y:auto;"><code>{{ $content }}</code></pre>
        </details>
    @endforeach
</div>
@endsection
