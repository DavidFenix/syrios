@php
    header('Content-Type: text/html; charset=utf-8');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Histórico de Ocorrências</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .cabecalho {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 6px;
            margin-bottom: 10px;
        }

        .cabecalho img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .dados-aluno {
            border: 1px solid #999;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .dados-aluno img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    @php
        $fotoRel_u = 'storage/img-user/logo1_ubiratan.png';
        $fotoAbsoluto_u = public_path($fotoRel_u);

        if (!file_exists($fotoAbsoluto_u)) {
            $fotoAbsoluto_u = public_path('storage/img-user/logo1_ubiratan.png');
        }
    @endphp

    <div class="cabecalho">
        <img src="{{ public_path('storage/img-user/logo1_ubiratan.png') }}" alt="Logo">
        <h2>{{ $escola->nome_e ?? 'Escola' }}</h2>
        <small>“Educar é transformar o mundo.”</small>
    </div>

    @php
        $fotoRel = 'storage/img-user/' . $aluno->matricula . '.png';
        $fotoAbsoluto = public_path($fotoRel);

        if (!file_exists($fotoAbsoluto)) {
            $fotoAbsoluto = public_path('storage/img-user/padrao.png');
        }
    @endphp

    <div class="dados-aluno">
        <img src="{{ $fotoAbsoluto }}" alt="Foto de {{ $aluno->nome_a }}" style="width:70px; height:70px; border-radius:50%;">
        <div>
            <strong>Aluno:</strong> {{ $aluno->nome_a }}<br>
            <strong>Matrícula:</strong> {{ $aluno->matricula }}<br>
            <strong>Turma:</strong> {{ $aluno->turma->serie_turma ?? '-' }}
        </div>
    </div>

    <h3 style="text-align:center;">Histórico de Ocorrências</h3>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data</th>
                <th>Descrição / Motivos</th>
                <th>Disciplina</th>
                <th>Professor</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ocorrencias as $i => $o)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $o->created_at->format('d/m/Y') }}</td>
                    <td>
                        {{ $o->descricao }}
                        @if($o->motivos->isNotEmpty())
                            / {{ $o->motivos->pluck('descricao')->implode(' / ') }}
                        @endif
                    </td>
                    <td>{{ $o->oferta->disciplina->abr ?? '-' }}</td>
                    <td>{{ $o->professor->nome_u ?? '-' }}</td>
                    <td>{{ $o->status == 1 ? 'Ativa' : 'Arquivada' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
