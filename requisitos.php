<?php
// ===============================
// CONFIGURAÇÃO DO BANCO
// ===============================
$host = "localhost:3307";
$user = "323966";
$pass = "deivide12";
$db   = "syrios"; // ajuste para o nome exato do seu banco

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Pega todas as tabelas
$tables = $conn->query("SHOW TABLES");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Documento de Requisitos - Syrios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
    <h1 class="mb-4">📄 Documento de Requisitos - Sistema Syrios</h1>

    <div class="alert alert-info">
        Este documento resume as principais regras de negócio e decisões de modelagem já implementadas
        no sistema Syrios. Serve como referência rápida para desenvolvedores e administradores.
    </div>

    <h2>1. Usuários</h2>
    <ul>
        <li>Cada usuário pertence a uma <strong>escola/secretaria de origem</strong>, definida pelo campo <code>school_id</code> na tabela <code>syrios_usuario</code>.</li>
        <li>Esse <code>school_id</code> é fixo e indica <em>quem criou o usuário</em>. Não pode ser alterado posteriormente.</li>
        <li>O usuário pode atuar em <strong>múltiplas escolas</strong> e com <strong>múltiplas roles</strong>, através da tabela pivot <code>syrios_usuario_role</code>.</li>
        <li>Ao criar ou editar, o status do usuário deve ser informado (ativo/inativo).</li>
        <li>Senhas são armazenadas como <code>senha_hash</code> utilizando <strong>bcrypt</strong>.</li>
    </ul>

    <h2>2. Roles</h2>
    <ul>
        <li>Roles existentes: <code>master</code>, <code>secretaria</code>, <code>escola</code>, <code>professor</code>, <code>gestor</code>, <code>pais</code>, <code>admin</code>.</li>
        <li>Um usuário pode ter várias roles.</li>
        <li>No escopo de Secretaria:
            <ul>
                <li>Não é permitido atribuir roles <code>master</code> nem <code>secretaria</code>.</li>
                <li>Somente roles restantes podem ser atribuídas aos usuários criados pela secretaria.</li>
            </ul>
        </li>
    </ul>

    <h2>3. Escolas</h2>
    <ul>
        <li>Uma escola pode ser:
            <ul>
                <li><strong>Secretaria</strong> (escola mãe) → <code>secretaria_id = NULL</code>.</li>
                <li><strong>Escola filha</strong> → vinculada a uma secretaria via <code>secretaria_id</code>.</li>
            </ul>
        </li>
        <li>Secretarias podem criar, editar e excluir apenas suas escolas filhas.</li>
        <li>Exclusão segura: antes de excluir uma escola, o sistema deve verificar vínculos existentes (usuários, roles, etc.).</li>
    </ul>

    <h2>4. Relacionamentos</h2>
    <ul>
        <li><code>Usuario</code> → pertence a uma escola (campo fixo <code>school_id</code>).</li>
        <li><code>Usuario</code> → pode ter muitas roles em muitas escolas via pivot <code>usuario_role</code>.</li>
        <li><code>Escola</code> → pode ter várias escolas filhas (<code>hasMany</code>).</li>
        <li><code>Escola</code> → pode ter uma secretaria mãe (<code>belongsTo</code>).</li>
    </ul>

    <h2>5. Regras de Segurança</h2>
    <ul>
        <li>Operações de CRUD estão restritas por role:
            <ul>
                <li><strong>Master</strong> → gerencia tudo.</li>
                <li><strong>Secretaria</strong> → gerencia suas próprias escolas filhas e usuários.</li>
                <li><strong>Escola</strong> → pode ter usuários, mas não cria secretarias.</li>
            </ul>
        </li>
        <li>Deletes devem sempre respeitar constraints de chave estrangeira (não excluir registros que ainda tenham vínculos).</li>
    </ul>

    <h2>6. Filtros e Funcionalidades Especiais</h2>
    <ul>
        <li>Escolas:
            <ul>
                <li>Filtro por tipo: Todas / Somente Secretarias / Somente Filhas.</li>
            </ul>
        </li>
        <li>Usuários:
            <ul>
                <li>Filtro por: usuários de secretarias, usuários de escolas filhas, ou todos juntos.</li>
            </ul>
        </li>
        <li>Associações:
            <ul>
                <li>Formulário para selecionar uma secretaria e visualizar suas escolas filhas.</li>
                <li>Funciona tanto na tela própria de Associações quanto dentro do Dashboard.</li>
            </ul>
        </li>
    </ul>

    <h2>7. Dashboard Master</h2>
    <ul>
        <li>Exibe:
            <ul>
                <li>Lista de escolas (com filtro).</li>
                <li>Lista de usuários (com filtro).</li>
                <li>Lista de roles.</li>
                <li>Formulário de associações (visualizar filhas de uma secretaria).</li>
            </ul>
        </li>
    </ul>

    <footer class="mt-5 text-muted">
        <hr>
        <p><small>Documento atualizado em <?php echo date('d/m/Y H:i'); ?>.</small></p>
    </footer>
</div>

<div class="container my-4">
  <h1 class="mb-4">📋 Documentação de Requisitos - Estrutura do Banco</h1>
  <p class="text-muted">Gerado automaticamente em <?php echo date("d/m/Y H:i"); ?></p>

  <div class="accordion" id="accordionTabelas">
    <?php 
    $i = 0;
    while ($t = $tables->fetch_array()): 
        $table = $t[0];
        $i++;
        $collapseId = "collapse".$i;
        $headingId  = "heading".$i;
    ?>
      <div class="accordion-item">
        <h2 class="accordion-header" id="<?php echo $headingId; ?>">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="false" aria-controls="<?php echo $collapseId; ?>">
            📂 Tabela: <strong class="ms-2"><?php echo $table; ?></strong>
          </button>
        </h2>
        <div id="<?php echo $collapseId; ?>" 
     class="accordion-collapse collapse" 
     aria-labelledby="<?php echo $headingId; ?>">
          <div class="accordion-body">
            
            <!-- Estrutura das colunas -->
            <h5>📑 Colunas</h5>
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Campo</th>
                  <th>Tipo</th>
                  <th>Nulo</th>
                  <th>Chave</th>
                  <th>Default</th>
                  <th>Extra</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $columns = $conn->query("DESCRIBE $table");
                while ($c = $columns->fetch_assoc()):
              ?>
                <tr>
                  <td><?php echo $c['Field']; ?></td>
                  <td><?php echo $c['Type']; ?></td>
                  <td><?php echo $c['Null']; ?></td>
                  <td><?php echo $c['Key']; ?></td>
                  <td><?php echo $c['Default']; ?></td>
                  <td><?php echo $c['Extra']; ?></td>
                </tr>
              <?php endwhile; ?>
              </tbody>
            </table>

            <!-- Chaves estrangeiras -->
            <h5 class="mt-3">🔗 Chaves Estrangeiras</h5>
            <table class="table table-sm table-bordered">
              <thead class="table-light">
                <tr>
                  <th>Constraint</th>
                  <th>Coluna</th>
                  <th>Ref. Tabela</th>
                  <th>Ref. Coluna</th>
                </tr>
              </thead>
              <tbody>
              <?php
                $fkQuery = "
                  SELECT 
                    k.CONSTRAINT_NAME,
                    k.COLUMN_NAME,
                    k.REFERENCED_TABLE_NAME,
                    k.REFERENCED_COLUMN_NAME
                  FROM information_schema.KEY_COLUMN_USAGE k
                  WHERE k.TABLE_SCHEMA = '$db' 
                    AND k.TABLE_NAME = '$table'
                    AND k.REFERENCED_TABLE_NAME IS NOT NULL
                ";
                $fks = $conn->query($fkQuery);
                if ($fks->num_rows > 0):
                  while ($fk = $fks->fetch_assoc()):
              ?>
                <tr>
                  <td><?php echo $fk['CONSTRAINT_NAME']; ?></td>
                  <td><?php echo $fk['COLUMN_NAME']; ?></td>
                  <td><?php echo $fk['REFERENCED_TABLE_NAME']; ?></td>
                  <td><?php echo $fk['REFERENCED_COLUMN_NAME']; ?></td>
                </tr>
              <?php endwhile; else: ?>
                <tr><td colspan="4" class="text-muted text-center">Nenhuma chave estrangeira</td></tr>
              <?php endif; ?>
              </tbody>
            </table>

          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>


<div class="alert alert-info">
    1️⃣Autenticação & Sessão

    Essencial para login seguro e controle de sessão:

    login.php → recebe CPF e senha, valida no syrios_usuario, retorna token/sessão.

    logout.php → encerra a sessão do usuário.

    check_session.php → valida se a sessão/token ainda é válido.

    recuperar_senha.php → opcional, mas importante para app multi-escola.

    2️⃣ Dashboard / Página inicial

    Cada tipo de usuário terá uma visão inicial diferente:

    dashboard_admin.php → resumo da escola:

    Total de alunos, professores, turmas.

    Últimas ocorrências.

    Alertas e notificações.

    dashboard_professor.php → resumo do professor:

    Turmas que ele ministra.

    Ocorrências recentes.

    Alunos em destaque.

    Suas ofertas.

    dashboard_gestor.php → resumo do gestor:

    Ocorrências por turma ou professor.

    Estatísticas de frequência e comportamento.

    Painel de relatórios.

    dashboard_pais.php → resumo dos filhos:

    Últimas ocorrências.

    Turmas e professores.

    Mensagens / notificações.

    3️⃣ Gestão de Usuários (admin/gestor)

    usuarios_list.php → listar todos os usuários da escola.

    usuario_add.php / usuario_edit.php / usuario_delete.php → CRUD de usuários.

    perfis.php → alterar papel do usuário (admin/professor/gestor/pais).

    4️⃣ Gestão de Turmas e Ofertas

    turmas_list.php → listar turmas da escola.

    turma_detail.php → detalhes da turma:

    Alunos enturmados.

    Professores responsáveis.

    Ofertas de disciplinas.

    oferta_list.php → listar ofertas por turma.

    oferta_add.php / oferta_edit.php / oferta_delete.php → CRUD de ofertas.

    diretor_turma.php → indicar professor como diretor de turma.

    5️⃣ Gestão de Alunos

    alunos_list.php → lista de alunos da escola.

    aluno_detail.php → detalhes do aluno:

    Turmas em que está.

    Ocorrências.

    Notificações.

    aluno_add.php / aluno_edit.php / aluno_delete.php → CRUD de alunos.

    enturmacao.php → vincular aluno a uma turma.

    6️⃣ Ocorrências

    ocorrencias_list.php → listar ocorrências:

    Por professor, turma, aluno ou escola.

    ocorrencia_add.php → registrar nova ocorrência.

    ocorrencia_edit.php / ocorrencia_delete.php → atualizar ou excluir.

    ocorrencia_detail.php → detalhes completos de uma ocorrência.

    7️⃣ Notificações

    notificacoes_list.php → listar notificações para usuário.

    notificacao_add.php → criar notificação push para usuário.

    notificacao_delete.php → remover notificações antigas.

    8️⃣ Registros e Status de Ocorrência

    registros_list.php → listar motivos de ocorrência da escola.

    registro_add.php / registro_edit.php / registro_delete.php → CRUD.

    regstatus_list.php → listar status possíveis (Aberta, Em análise, Concluída).

    9️⃣ Relatórios (opcional mas importante para gestores/admins)

    relatorio_ocorrencias.php → resumo por turma, professor ou período.

    relatorio_alunos.php → frequência de ocorrências por aluno.

    relatorio_turmas.php → desempenho e ocorrências por turma.

    🔹 Observações importantes

    Todas as páginas precisam considerar o school_id do usuário para garantir isolamento de dados.

    Todas as consultas devem filtrar por papel do usuário (role) para exibir apenas dados permitidos.

    Usar joins entre tabelas syrios_ para mostrar informações completas (ex.: ocorrência → aluno → turma → professor → escola).

    <br>

    1️⃣ Autenticação & Sessão

    login.php → recebe CPF e senha, valida no syrios_usuario, retorna token/sessão.

    logout.php → encerra a sessão do usuário.

    check_session.php → valida se a sessão/token ainda é válido.

    recuperar_senha.php → opcional, mas importante para app multi-escola.

    2️⃣ Dashboard / Página inicial

    dashboard_admin.php → resumo da escola: Total de alunos, professores, turmas, Últimas ocorrências, Alertas e notificações.

    dashboard_professor.php → resumo do professor: Turmas que ele ministra, Ocorrências recentes, Alunos em destaque, Suas ofertas.

    dashboard_gestor.php → resumo do gestor: Ocorrências por turma ou professor, Estatísticas de frequência e comportamento, Painel de relatórios.

    dashboard_pais.php → resumo dos filhos: Últimas ocorrências, Turmas e professores, Mensagens / notificações.

    3️⃣ Gestão de Usuários (admin/gestor)

    usuarios_list.php → listar todos os usuários da escola.

    usuario_add.php / usuario_edit.php / usuario_delete.php → CRUD de usuários.

    perfis.php → alterar papel do usuário (admin/professor/gestor/pais).

    4️⃣ Gestão de Turmas e Ofertas

    turmas_list.php → listar turmas da escola.

    turma_detail.php → detalhes da turma: Alunos enturmados, Professores responsáveis, Ofertas de disciplinas.

    oferta_list.php → listar ofertas por turma.

    oferta_add.php / oferta_edit.php / oferta_delete.php → CRUD de ofertas.

    diretor_turma.php → indicar professor como diretor de turma.

    5️⃣ Gestão de Alunos

    alunos_list.php → lista de alunos da escola.

    aluno_detail.php → detalhes do aluno: Turmas em que está, Ocorrências, Notificações.

    aluno_add.php / aluno_edit.php / aluno_delete.php → CRUD de alunos.

    enturmacao.php → vincular aluno a uma turma.

    6️⃣ Ocorrências

    ocorrencias_list.php → listar ocorrências: Por professor, turma, aluno ou escola.

    ocorrencia_add.php → registrar nova ocorrência.

    ocorrencia_edit.php / ocorrencia_delete.php → atualizar ou excluir.

    ocorrencia_detail.php → detalhes completos de uma ocorrência.

    7️⃣ Notificações

    notificacoes_list.php → listar notificações para usuário.

    notificacao_add.php → criar notificação push para usuário.

    notificacao_delete.php → remover notificações antigas.

    8️⃣ Registros e Status de Ocorrência

    registros_list.php → listar motivos de ocorrência da escola.

    registro_add.php / registro_edit.php / registro_delete.php → CRUD.

    regstatus_list.php → listar status possíveis (Aberta, Em análise, Concluída).

    9️⃣ Relatórios (opcional mas importante para gestores/admins)

    relatorio_ocorrencias.php → resumo por turma, professor ou período.

    relatorio_alunos.php → frequência de ocorrências por aluno.

    relatorio_turmas.php → desempenho e ocorrências por turma.

    <br>

    ✅ Observações importantes:

    Senha segura: usamos password_hash() e depois no login password_verify().

    Filtragem por escola: o admin só vê os usuários da sua school_id.

    Proteção de exclusão: só exclui se school_id do usuário bater com o admin logado.

    ✅ Observações:

    Apenas usuários com role professor podem ser vinculados à tabela syrios_professor.

    A lista de usuários no <select> filtra quem ainda não está cadastrado como professor na escola.

    oferta_id é opcional, caso queira já vincular o professor a uma disciplina/turma específica.

    ✅ Observações:

    Cada Turma pertence a uma escola (school_id).

    Cada Oferta (disciplina em turma) vincula: turma + disciplina + professor.

    Apenas usuários com role admin podem gerenciar turmas/ofertas.

    O sistema lista apenas dados da escola do admin logado, respeitando multi-escolas.

    É possível estender este CRUD para editar e excluir turmas ou ofertas, adicionando botões e tratamento update/delete.

    ✅ Observações importantes:

    Multi-escola: os school_id garantem que cada admin só veja os alunos e turmas da sua escola.

    Enturmação: conecta alunos a turmas, respeitando a escola.

    Validação de duplicidade: você pode criar uma constraint UNIQUE(aluno_id, turma_id, school_id) para evitar que o mesmo aluno seja vinculado duas vezes na mesma turma.

    Pronto para extensão: podemos adicionar editar e excluir alunos/enturmação facilmente.

    ✅ Observações:

    Multi-escola: todas as consultas filtram por school_id.

    Controle de acesso: apenas professor e gestor podem registrar ocorrências.

    Relacionamentos:

    aluno_id → syrios_aluno

    oferta_id → syrios_oferta (turma + disciplina)

    registro_id → syrios_registros (motivo)

    status_id → syrios_regstatus

    Extensível: você pode adicionar editar, excluir e filtrar por período ou aluno.

    Timestamp automático: data_ocorrencia e criado_em são gravados automaticamente.

    Benefícios:

    Um mesmo usuário pode ter múltiplos papéis (ex.: professor + pai + gestor).

    Cada role é vinculada à escola correta (school_id) para manter multi-escola consistente.

    Facilita a lógica de dashboards e permissões: você só precisa verificar se o usuário possui o role X para exibir determinadas páginas.

    ✅ O que esse código cobre:

    Usuário pode ter mais de uma escola → seleção dinâmica

    Usuário pode ter vários roles → pega todos os roles da escola

    Senha é verificada com password_verify()

    Redireciona para o dashboard de acordo com o primeiro role

    ✅ Benefícios deste fluxo:

    Usuário com múltiplos roles não precisa logar novamente para trocar dashboards.

    Usuário com múltiplas escolas seleciona a escola no login.

    Dashboard genérico (dashboard.php) decide qual dashboard carregar com base em current_role.

    Fácil de manter e expandir para novos roles ou funcionalidades.

    Usuário digita CPF + senha.

    ✅ Se pertence a várias escolas, ele escolhe a escola em um segundo passo, sem digitar a senha de novo.

    Senha e CPF são guardados temporariamente na sessão.

    Depois da escolha, o login é validado e ele é redirecionado para o dashboard correto.

    ✅ Pontos importantes deste login

    Suporta usuários com múltiplos papéis e múltiplas escolas.

    A senha não precisa ser digitada novamente na escolha da escola (Opção B).

    Armazena na sessão: usuario_id, school_id, roles[], nome_u.

    Redireciona para o dashboard do primeiro role. Você pode depois implementar troca de dashboards para múltiplos roles sem logout.

    Como funciona:

    Ao logar, $_SESSION['roles'] contém todos os roles do usuário naquela escola.

    $_SESSION['role_atual'] armazena o role que ele está usando no momento.

    Cada dashboard (dashboard_admin.php, dashboard_professor.php, etc.) pode verificar $_SESSION['role_atual'] para definir menus e permissões.

    O usuário pode trocar de dashboard sem precisar logar novamente.

    ✅ Benefícios dessa abordagem

    Usuário logado mantém sessão ativa e pode alternar dashboards sem relogar.

    Cada dashboard verifica role_atual, garantindo segurança.

    Código limpo e centralizado (auth.php) para todas páginas protegidas.

    Fácil expansão para novas roles ou novos tipos de usuários.

    🔹 Passo 1 – Banco de Dados já está pronto para flexibilidade

    Repare que nosso modelo já é flexível, porque:

    syrios_role guarda as roles, você pode inserir qualquer role nova sem alterar tabelas.

    syrios_usuario_role liga usuário ↔ role ↔ escola.

    O sistema não precisa mais ser alterado toda vez que você criar uma nova role, só as páginas de dashboard que tratam cada role específica.

    Vou atualizar o crud_usuario_master.php incluindo um formulário logo no início para adicionar escolas. Assim a ordem natural fica:

    Adicionar escola

    Adicionar role (se precisar de uma nova)

    Adicionar usuário (já selecionando a escola)

    Vincular usuário ↔ role ↔ escola

    🔹 Próximos passos

    Depois dessa base funcionando, podemos expandir a dashboard da secretaria para:

    Criar usuários com role escola já vinculados automaticamente às escolas criadas.

    Dar permissão para a secretaria editar/excluir escolas filhas.

    Controlar que usuários escola só enxerguem sua própria escola.

    1️⃣ Gestão de Escolas Filhas

    Lista todas as escolas vinculadas à secretaria-mãe.

    Formulário para adicionar uma nova escola vinculada automaticamente à secretaria (preenchendo secretaria_id com o ID da secretaria-mãe).

    Opção de editar/excluir escolas filhas.

    2️⃣ Gestão de Usuários da Escola

    Formulário para cadastrar um usuário com role “escola”.

    Dropdown ou seleção da escola filha à qual o usuário será vinculado.

    Ao salvar, o sistema:

    Insere o usuário em syrios_usuario com o school_id da escola escolhida.

    Cria a ligação em syrios_usuario_role para a role “escola”.

    💡 Benefício:

    Cada secretaria controla suas próprias escolas e usuários vinculados sem afetar outras secretarias.

    Automatiza a associação usuario → escola → role.

    Mantém flexível para futuras roles: se precisar criar outra role vinculada à secretaria, basta criar em syrios_role e escolher na lista.

    ✅ Observações:

    O cadastro de professor:

    Cria um usuário na tabela syrios_usuario vinculado à escola.

    Insere na tabela syrios_professor.

    Cria a associação na tabela syrios_usuario_role com a role “professor” e school_id da escola.

    A listagem só mostra os professores vinculados à escola logada.

    Outras funcionalidades (disciplinas, turmas, ofertas, alunos, enturmação) podem ser acessadas por links ou botões que direcionam para CRUDs específicos filtrando school_id.

    Se quiser, posso já preparar a versão completa do dashboard_escola.php que inclua CRUDs de disciplinas, turmas, ofertas, alunos e enturmação, tudo na mesma página ou separadas em abas, mantendo o filtro por escola.

    ✅ O que temos agora:

    Secretaria pode criar usuários vinculados à sua escola.

    Abaixo do formulário, aparece a tabela com todos os usuários daquela escola, incluindo:

    Nome, CPF, Status

    Escola vinculada (sempre a mesma, mas exibida)

    Roles (concatenadas)

    Links para editar/excluir

    Agora você quer todos os usuários de todas as escolas filhas de uma secretaria.
    Ou seja:

    syrios_escola tem secretaria_id (aponta para a escola mãe).

    syrios_usuario tem school_id (aponta para uma escola filha).

    Precisamos listar todos os usuários cujas escolas têm secretaria_id = X.

    ✅ Com isso, você terá no crud_master.php:

Tabela de usuários com Editar/Excluir.

Formulário de edição que aparece quando clica em Editar.

Atualização de dados + role num só passo.

Exclusão limpa (remove roles e usuário).

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html-->
