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
    <div>
      <ul>
        Lotação:
        <li>
            no nosso caso a lotação do professor ja existe em cada escola que for usar o sistema
o que temos que fazer é só registrar no syrios tal qual ela está
vamos fazer o escola/lotacao/ com os blades index, create, edit

--o index tem um select com os professores disponiveis na escola e um botao para buscar a sua lotacao
--mostra a lista de oferta do professor na escola(pode está vazia)

--para exibir várias ofertas de uma vez só, mostra-se as turmas que o professor leciona agrupadas por disciplina. exemplo:

Disciplina | Turma(s)         | Ação
Matemática | Olho para ver    | Editar

--Ao clicar no olho na tabela exibe-se as turmas: 
  1ª Série A
  1ª Série B
  2ª Série C

--Um botao para nova oferta vai para o blade create
  --um select para disciplina
  --uma lista de checkbox para escolher as turmas
  --o botao para salvar e voltar para o index
  --o index já começa exibindo o professor que ganhou a nova oferta e ja carrega todas suas ofertas

--o botao editar vai para o blade edit
  --exibe-se todas em turmas disponiveis com checkbox para marcar/desmarcar aquelas onde o professor vai lecionar ou nao aquela disciplina que se está editando
  --turmas podem ser acrescentadas ou removidas
  --o botao para salvar e voltar para o index que já começa exibindo o professor que foi editado e ja carrega todas suas ofertas

        </li>
      </ul>
      
    </div>
    
    <div>Tarefas agora: ✅ Sugestão de ordem prática para implementar<br>

Criar ContextService (com decideAfterLogin, setContext, clearContext).<br>

Ajustar LoginController para usar ContextService::decideAfterLogin.<br>

Criar as views de seleção (choose_school, choose_role, no_access).<br>

Criar e registrar o middleware EnsureContextSelected.<br>

Trocar dashboard_route no helpers.php.<br>

Testar com usuários que têm:<br>

1 role em 1 escola,<br>

vários roles na mesma escola,<br>

vários roles em várias escolas.<br>
</div>

    <div class="accordion" id="accordionFluxos">

      <!-- Fluxo Master -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingMaster">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaster" aria-expanded="false" aria-controls="collapseMaster">
            👑 Fluxo padrão do Usuário Master (Role: master)
          </button>
        </h2>
        <div id="collapseMaster" class="accordion-collapse collapse" aria-labelledby="headingMaster">
          <div class="accordion-body">
            <ul>
              <li><strong>Cria Secretarias (escolas mães)</strong> → <code>secretaria_id = null</code></li>
              <li><strong>Restrição</strong>: nenhum outro usuário pode criar Secretarias.</li>
              <li><strong>Cria Usuário para administrar uma Secretaria</strong>
                <ul>
                  <li>Define a <code>RoleUsuarioEscola = secretaria</code></li>
                  <li>Associa <code>RoleUsuarioEscola</code> com <code>id.secretaria</code> de destino.</li>
                  <li>O Master sempre é o dono → <code>school_id(usuario) = id(master)</code></li>
                  <li>Ninguém mais pode criar esse tipo de usuário.</li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Fluxo Secretaria -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingSecretaria">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSecretaria" aria-expanded="false" aria-controls="collapseSecretaria">
            🏢 Fluxo padrão do Usuário Secretaria (Role: secretaria)
          </button>
        </h2>
        <div id="collapseSecretaria" class="accordion-collapse collapse" aria-labelledby="headingSecretaria">
          <div class="accordion-body">
            <ul>
              <li><strong>Cria Escola filha</strong> → uso obrigatório do seu <code>secretaria_id</code>.</li>
              <li><strong>Cria Usuário para administrar Escola</strong>
                <ul>
                  <li>Define a <code>Role_usuario = escola</code></li>
                  <li>Associa com <code>id_escola</code> de destino.</li>
                  <li>A Secretaria sempre é dona → <code>school_id(usuario) = id(secretaria)</code></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Fluxo Escola -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="headingEscola">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEscola" aria-expanded="false" aria-controls="collapseEscola">
            🏫 Fluxo padrão do Usuário Escola (Role: escola)
          </button>
        </h2>
        <div id="collapseEscola" class="accordion-collapse collapse" aria-labelledby="headingEscola">
          <div class="accordion-body">
            <ul>
              <li><strong>Cria Usuário Professor</strong>
                <ul>
                  <li>Define a <code>Role_usuario = professor</code></li>
                  <li>A Escola sempre é dona → <code>school_id(usuario) = id(escola)</code></li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>

    </div>

   


   <div class="accordion" id="accordionLogin">

  <!-- Termos -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTermos">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTermos">
        📖 Termos (para não confundir)
      </button>
    </h2>
    <div id="collapseTermos" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
      <div class="accordion-body">
        <ul>
          <li><strong>Usuário.owner_school_id</strong>: hoje é <code>syrios_usuario.school_id</code>. É quem <strong>criou</strong>/“dono” do usuário (Secretaria, Escola ou Master). <em>Não</em> determina acesso.</li>
          <li><strong>Contexto (school + role)</strong>: a combinação da <strong>escola</strong> em que o usuário vai atuar agora (da pivot <code>syrios_usuario_role.school_id</code>) + <strong>qual papel</strong> ele vai usar naquela escola.</li>
          <li><strong>Escola Mãe (Secretaria)</strong>: <code>syrios_escola</code> com <code>secretaria_id = NULL</code>.</li>
          <li><strong>Escola Filha</strong>: <code>syrios_escola</code> com <code>secretaria_id = &lt;id da mãe&gt;</code>.</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Dados -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingDados">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDados">
        📊 Dados que usamos no login
      </button>
    </h2>
    <div id="collapseDados" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
      <div class="accordion-body">
        <ul>
          <li><code>syrios_usuario</code> → identifica o usuário e o <em>owner_school_id</em>.</li>
          <li><code>syrios_usuario_role</code> (<em>usuario_id, role_id, school_id</em>) → define <strong>em quais escolas</strong> ele tem <strong>quais papéis</strong> (acesso real).</li>
          <li><code>syrios_role</code> → nomes dos papéis (‘master’, ‘secretaria’, ‘escola’, ‘professor’, ‘gestor’, ‘pais’…)</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Fluxo -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingFluxo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFluxo">
        🔐 Fluxo do Login (organizado e com melhorias)
      </button>
    </h2>
    <div id="collapseFluxo" class="accordion-collapse collapse" data-bs-parent="#accordionLogin">
      <div class="accordion-body">
        <div class="[--thread-content-max-width:40rem] thread-lg:[--thread-content-max-width:48rem] mx-auto max-w-(--thread-content-max-width) flex-1 group/turn-messages focus-visible:outline-hidden relative flex w-full min-w-0 flex-col agent-turn" tabindex="-1"><div class="flex max-w-full flex-col grow"><div data-message-author-role="assistant" data-message-id="0069fc2a-2a13-4977-b1bb-130fd58577a5" dir="auto" class="min-h-8 text-message relative flex w-full flex-col items-end gap-2 text-start break-words whitespace-normal [.text-message+&amp;]:mt-5" data-message-model-slug="gpt-5-thinking"><div class="flex w-full flex-col gap-1 empty:hidden first:pt-[1px]"><div class="markdown prose dark:prose-invert w-full break-words light markdown-new-styling"><p data-start="0" data-end="254">perfeito — vamos organizar tudo em uma “especificação de login/seleção de contexto” clara, apontar melhorias e fechar com um plano de implementação. Vou manter seus nomes e sentido (school_id do usuário = “dono/criador”), mas sugerir alguns refinamentos.</p>
        <h1 data-start="256" data-end="285">Termos (para não confundir)</h1>
        <ul data-start="286" data-end="778">
        <li data-start="286" data-end="442">
        <p data-start="288" data-end="442"><strong data-start="288" data-end="315">Usuário.owner_school_id</strong>: hoje é <code data-start="324" data-end="350">syrios_usuario.school_id</code>. É quem <strong data-start="359" data-end="368">criou</strong>/“dono” do usuário (Secretaria, Escola ou Master). <em data-start="419" data-end="424">Não</em> determina acesso.</p>
        </li>
        <li data-start="443" data-end="632">
        <p data-start="445" data-end="632"><strong data-start="445" data-end="473">Contexto (school + role)</strong>: a combinação <strong data-start="488" data-end="531">escola em que o usuário vai atuar agora</strong> (proveniente da pivot <code data-start="554" data-end="585">syrios_usuario_role.school_id</code>) + <strong data-start="589" data-end="603">qual papel</strong> ele vai usar naquela escola.</p>
        </li>
        <li data-start="633" data-end="707">
        <p data-start="635" data-end="707"><strong data-start="635" data-end="662">Escola Mãe (Secretaria)</strong>: <code data-start="664" data-end="679">syrios_escola</code> com <code data-start="684" data-end="706">secretaria_id = NULL</code>.</p>
        </li>
        <li data-start="708" data-end="778">
        <p data-start="710" data-end="778"><strong data-start="710" data-end="726">Escola Filha</strong>: <code data-start="728" data-end="743">syrios_escola</code> com <code data-start="748" data-end="777">secretaria_id = &lt;id da mãe&gt;</code>.</p>
        </li>
        </ul>
        <h1 data-start="780" data-end="807">Dados que usamos no login</h1>
        <ul data-start="808" data-end="1106">
        <li data-start="808" data-end="874">
        <p data-start="810" data-end="874"><code data-start="810" data-end="826">syrios_usuario</code> → identifica o usuário e o <strong data-start="854" data-end="873">owner_school_id</strong>.</p>
        </li>
        <li data-start="875" data-end="1001">
        <p data-start="877" data-end="1001"><code data-start="877" data-end="931">syrios_usuario_role (usuario_id, role_id, school_id)</code> → define <strong data-start="941" data-end="961">em quais escolas</strong> ele tem <strong data-start="970" data-end="986">quais papéis</strong> (acesso real).</p>
        </li>
        <li data-start="1002" data-end="1106">
        <p data-start="1004" data-end="1106"><code data-start="1004" data-end="1017">syrios_role</code> → nomes dos papéis (‘master’, ‘secretaria’, ‘escola’, ‘professor’, ‘gestor’, ‘pais’, …).</p>
        </li>
        </ul>
        <h1 data-start="1108" data-end="1160">Fluxo que você propôs (organizado e com melhorias)</h1>
        <ol data-start="1162" data-end="3135">
        <li data-start="1162" data-end="1342">
        <p data-start="1165" data-end="1190"><strong data-start="1165" data-end="1188">Login (CPF + senha)</strong></p>
        <ul data-start="1194" data-end="1342">
        <li data-start="1194" data-end="1251">
        <p data-start="1196" data-end="1251">Autentica o usuário normalmente (<code data-start="1229" data-end="1249">Auth::attempt(...)</code>).</p>
        </li>
        <li data-start="1255" data-end="1342">
        <p data-start="1257" data-end="1342">Carrega todas as suas <strong data-start="1279" data-end="1294">associações</strong> (pivot <code data-start="1302" data-end="1318">usuario-&gt;roles</code> com <code data-start="1323" data-end="1340">pivot.school_id</code>).</p>
        </li>
        </ul>
        </li>
        <li data-start="1344" data-end="2589">
        <p data-start="1347" data-end="1403"><strong data-start="1347" data-end="1370">Decisão de contexto</strong><br data-start="1370" data-end="1373">
        Agrupe as roles por escola:</p>
        <pre class="overflow-visible!" data-start="1407" data-end="1505"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="flex items-center text-token-text-secondary px-4 py-2 text-xs font-sans justify-between h-9 bg-token-sidebar-surface-primary select-none rounded-t-2xl">php</div><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"><button class="flex gap-1 items-center select-none py-1" aria-label="Copiar"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="icon-sm"><path d="M12.668 10.667C12.668 9.95614 12.668 9.46258 12.6367 9.0791C12.6137 8.79732 12.5758 8.60761 12.5244 8.46387L12.4688 8.33399C12.3148 8.03193 12.0803 7.77885 11.793 7.60254L11.666 7.53125C11.508 7.45087 11.2963 7.39395 10.9209 7.36328C10.5374 7.33197 10.0439 7.33203 9.33301 7.33203H6.5C5.78896 7.33203 5.29563 7.33195 4.91211 7.36328C4.63016 7.38632 4.44065 7.42413 4.29688 7.47559L4.16699 7.53125C3.86488 7.68518 3.61186 7.9196 3.43555 8.20703L3.36524 8.33399C3.28478 8.49198 3.22795 8.70352 3.19727 9.0791C3.16595 9.46259 3.16504 9.95611 3.16504 10.667V13.5C3.16504 14.211 3.16593 14.7044 3.19727 15.0879C3.22797 15.4636 3.28473 15.675 3.36524 15.833L3.43555 15.959C3.61186 16.2466 3.86474 16.4807 4.16699 16.6348L4.29688 16.6914C4.44063 16.7428 4.63025 16.7797 4.91211 16.8027C5.29563 16.8341 5.78896 16.835 6.5 16.835H9.33301C10.0439 16.835 10.5374 16.8341 10.9209 16.8027C11.2965 16.772 11.508 16.7152 11.666 16.6348L11.793 16.5645C12.0804 16.3881 12.3148 16.1351 12.4688 15.833L12.5244 15.7031C12.5759 15.5594 12.6137 15.3698 12.6367 15.0879C12.6681 14.7044 12.668 14.211 12.668 13.5V10.667ZM13.998 12.665C14.4528 12.6634 14.8011 12.6602 15.0879 12.6367C15.4635 12.606 15.675 12.5492 15.833 12.4688L15.959 12.3975C16.2466 12.2211 16.4808 11.9682 16.6348 11.666L16.6914 11.5361C16.7428 11.3924 16.7797 11.2026 16.8027 10.9209C16.8341 10.5374 16.835 10.0439 16.835 9.33301V6.5C16.835 5.78896 16.8341 5.29563 16.8027 4.91211C16.7797 4.63025 16.7428 4.44063 16.6914 4.29688L16.6348 4.16699C16.4807 3.86474 16.2466 3.61186 15.959 3.43555L15.833 3.36524C15.675 3.28473 15.4636 3.22797 15.0879 3.19727C14.7044 3.16593 14.211 3.16504 13.5 3.16504H10.667C9.9561 3.16504 9.46259 3.16595 9.0791 3.19727C8.79739 3.22028 8.6076 3.2572 8.46387 3.30859L8.33399 3.36524C8.03176 3.51923 7.77886 3.75343 7.60254 4.04102L7.53125 4.16699C7.4508 4.32498 7.39397 4.53655 7.36328 4.91211C7.33985 5.19893 7.33562 5.54719 7.33399 6.00195H9.33301C10.022 6.00195 10.5791 6.00131 11.0293 6.03809C11.4873 6.07551 11.8937 6.15471 12.2705 6.34668L12.4883 6.46875C12.984 6.7728 13.3878 7.20854 13.6533 7.72949L13.7197 7.87207C13.8642 8.20859 13.9292 8.56974 13.9619 8.9707C13.9987 9.42092 13.998 9.97799 13.998 10.667V12.665ZM18.165 9.33301C18.165 10.022 18.1657 10.5791 18.1289 11.0293C18.0961 11.4302 18.0311 11.7914 17.8867 12.1279L17.8203 12.2705C17.5549 12.7914 17.1509 13.2272 16.6553 13.5313L16.4365 13.6533C16.0599 13.8452 15.6541 13.9245 15.1963 13.9619C14.8593 13.9895 14.4624 13.9935 13.9951 13.9951C13.9935 14.4624 13.9895 14.8593 13.9619 15.1963C13.9292 15.597 13.864 15.9576 13.7197 16.2939L13.6533 16.4365C13.3878 16.9576 12.9841 17.3941 12.4883 17.6982L12.2705 17.8203C11.8937 18.0123 11.4873 18.0915 11.0293 18.1289C10.5791 18.1657 10.022 18.165 9.33301 18.165H6.5C5.81091 18.165 5.25395 18.1657 4.80371 18.1289C4.40306 18.0962 4.04235 18.031 3.70606 17.8867L3.56348 17.8203C3.04244 17.5548 2.60585 17.151 2.30176 16.6553L2.17969 16.4365C1.98788 16.0599 1.90851 15.6541 1.87109 15.1963C1.83431 14.746 1.83496 14.1891 1.83496 13.5V10.667C1.83496 9.978 1.83432 9.42091 1.87109 8.9707C1.90851 8.5127 1.98772 8.10625 2.17969 7.72949L2.30176 7.51172C2.60586 7.0159 3.04236 6.6122 3.56348 6.34668L3.70606 6.28027C4.04237 6.136 4.40303 6.07083 4.80371 6.03809C5.14051 6.01057 5.53708 6.00551 6.00391 6.00391C6.00551 5.53708 6.01057 5.14051 6.03809 4.80371C6.0755 4.34588 6.15483 3.94012 6.34668 3.56348L6.46875 3.34473C6.77282 2.84912 7.20856 2.44514 7.72949 2.17969L7.87207 2.11328C8.20855 1.96886 8.56979 1.90385 8.9707 1.87109C9.42091 1.83432 9.978 1.83496 10.667 1.83496H13.5C14.1891 1.83496 14.746 1.83431 15.1963 1.87109C15.6541 1.90851 16.0599 1.98788 16.4365 2.17969L16.6553 2.30176C17.151 2.60585 17.5548 3.04244 17.8203 3.56348L17.8867 3.70606C18.031 4.04235 18.0962 4.40306 18.1289 4.80371C18.1657 5.25395 18.165 5.81091 18.165 6.5V9.33301Z"></path></svg>Copiar código</button></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-php"><span><span><span class="hljs-variable">$porEscola</span></span><span> = </span><span><span class="hljs-variable">$usuario</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">roles</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">with</span></span><span>(</span><span><span class="hljs-string">'pivot'</span></span><span>)-&gt;</span><span><span class="hljs-title function_ invoke__">get</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">groupBy</span></span><span>(</span><span><span class="hljs-string">'pivot.school_id'</span></span><span>);
        </span></span></code></div></div></pre>
        <p data-start="1510" data-end="1520"><strong data-start="1510" data-end="1520">Casos:</strong></p>
        <ul data-start="1524" data-end="2231">
        <li data-start="1524" data-end="1761">
        <p data-start="1526" data-end="1761"><strong data-start="1526" data-end="1546">Sem nenhum papel</strong><br data-start="1546" data-end="1549">
        → mostrar página “Sem acesso” com instruções (“contate o administrador da sua escola/secretaria”).<br data-start="1652" data-end="1655">
        (Opcional: se o usuario for <code data-start="1688" data-end="1705">owner_school_id</code> de uma secretaria, oferecer fluxo de pedido de acesso.)</p>
        </li>
        <li data-start="1766" data-end="1979">
        <p data-start="1768" data-end="1803"><strong data-start="1768" data-end="1801">Tem papéis em apenas 1 escola</strong></p>
        <ul data-start="1809" data-end="1979">
        <li data-start="1809" data-end="1881">
        <p data-start="1811" data-end="1881"><strong data-start="1811" data-end="1844">E apenas 1 papel nessa escola</strong> → <strong data-start="1847" data-end="1863">entra direto</strong> nesse contexto.</p>
        </li>
        <li data-start="1887" data-end="1979">
        <p data-start="1889" data-end="1979"><strong data-start="1889" data-end="1920">&gt;1 papel nessa mesma escola</strong> → tela <strong data-start="1928" data-end="1946">Escolher Papel</strong> (lista de roles daquela escola).</p>
        </li>
        </ul>
        </li>
        <li data-start="1984" data-end="2231">
        <p data-start="1986" data-end="2015"><strong data-start="1986" data-end="2013">Tem papéis em &gt;1 escola</strong></p>
        <ul data-start="2021" data-end="2231">
        <li data-start="2021" data-end="2231">
        <p data-start="2023" data-end="2059">Tela <strong data-start="2028" data-end="2047">Escolher Escola</strong> primeiro.</p>
        <ul data-start="2067" data-end="2231">
        <li data-start="2067" data-end="2149">
        <p data-start="2069" data-end="2149">Se ao escolher a escola houver <strong data-start="2100" data-end="2118">apenas 1 papel</strong> nessa escola → entra direto.</p>
        </li>
        <li data-start="2157" data-end="2231">
        <p data-start="2159" data-end="2231">Se houver <strong data-start="2169" data-end="2181">&gt;1 papel</strong>, vai para tela <strong data-start="2197" data-end="2215">Escolher Papel</strong> daquela escola.</p>
        </li>
        </ul>
        </li>
        </ul>
        </li>
        </ul>
        <p data-start="2236" data-end="2260"><strong data-start="2236" data-end="2260">Melhorias sugeridas:</strong></p>
        <ul data-start="2264" data-end="2589">
        <li data-start="2264" data-end="2589">
        <p data-start="2266" data-end="2441"><strong data-start="2266" data-end="2292">Lembrar última escolha</strong> (escola/role) e tentar <strong data-start="2316" data-end="2327">reabrir</strong> direto na próxima sessão (<em data-start="2354" data-end="2364">fallback</em> para telas de escolha se a combinação não existir mais).<br data-start="2421" data-end="2424">
        Onde salvar:</p>
        <ul data-start="2447" data-end="2589">
        <li data-start="2447" data-end="2498">
        <p data-start="2449" data-end="2498">simples: em <code data-start="2461" data-end="2476">syrios_sessao</code> (último contexto), ou</p>
        </li>
        <li data-start="2504" data-end="2589">
        <p data-start="2506" data-end="2589">prático: colunas em <code data-start="2526" data-end="2542">syrios_usuario</code> (<code data-start="2544" data-end="2560">last_school_id</code>, <code data-start="2562" data-end="2576">last_role_id</code>) – opcional.</p>
        </li>
        </ul>
        </li>
        </ul>
        </li>
        <li data-start="2591" data-end="2905">
        <p data-start="2594" data-end="2642"><strong data-start="2594" data-end="2617">Sessão após seleção</strong><br>
        Guarde chaves claras:</p>
        <ul data-start="2646" data-end="2905">
        <li data-start="2646" data-end="2714">
        <p data-start="2648" data-end="2714"><code data-start="2648" data-end="2678">session('current_school_id')</code> → escola <strong data-start="2688" data-end="2702">de atuação</strong> (da PIVOT).</p>
        </li>
        <li data-start="2718" data-end="2792">
        <p data-start="2720" data-end="2792"><code data-start="2720" data-end="2745">session('current_role')</code> (ou <code data-start="2750" data-end="2767">current_role_id</code>) → papel <strong data-start="2777" data-end="2791">de atuação</strong>.</p>
        </li>
        <li data-start="2796" data-end="2905">
        <p data-start="2798" data-end="2905"><code data-start="2798" data-end="2829">session('available_contexts')</code> (opcional) → mapear escolas e papéis para montar um <strong data-start="2882" data-end="2894">switcher</strong> na navbar.</p>
        </li>
        </ul>
        </li>
        <li data-start="2907" data-end="3135">
        <p data-start="2910" data-end="2928"><strong data-start="2910" data-end="2928">Usuário logado</strong></p>
        <ul data-start="2932" data-end="3135">
        <li data-start="2932" data-end="2996">
        <p data-start="2934" data-end="2996"><strong data-start="2934" data-end="2953">owner_school_id</strong> = <code data-start="2956" data-end="2982">syrios_usuario.school_id</code> (quem criou).</p>
        </li>
        <li data-start="3000" data-end="3085">
        <p data-start="3002" data-end="3085"><strong data-start="3002" data-end="3025">host/context school</strong> = <code data-start="3028" data-end="3058">session('current_school_id')</code> (onde ele atua <strong data-start="3074" data-end="3083">agora</strong>).</p>
        </li>
        <li data-start="3089" data-end="3135">
        <p data-start="3091" data-end="3135"><strong data-start="3091" data-end="3106">papel atual</strong> = <code data-start="3109" data-end="3134">session('current_role')</code>.</p>
        </li>
        </ul>
        </li>
        </ol>
        <h1 data-start="3137" data-end="3161">Segurança / Middleware</h1>
        <ul data-start="3162" data-end="3770">
        <li data-start="3162" data-end="3538">
        <p data-start="3164" data-end="3211">Crie um middleware <code data-start="3183" data-end="3206">EnsureContextSelected</code> que:</p>
        <ul data-start="3214" data-end="3538">
        <li data-start="3214" data-end="3303">
        <p data-start="3216" data-end="3303">exige <code data-start="3222" data-end="3252">session('current_school_id')</code> e <code data-start="3255" data-end="3280">session('current_role')</code> para áreas protegidas;</p>
        </li>
        <li data-start="3306" data-end="3435">
        <p data-start="3308" data-end="3435">verifica se <strong data-start="3320" data-end="3338">aquele usuário</strong> realmente possui aquela associação <strong data-start="3374" data-end="3410">(usuario_id, role_id, school_id)</strong> na pivot (defesa dupla);</p>
        </li>
        <li data-start="3438" data-end="3538">
        <p data-start="3440" data-end="3538">caso não possua, limpa sessão de contexto e envia para a tela de seleção (ou dashboard “seletor”).</p>
        </li>
        </ul>
        </li>
        <li data-start="3539" data-end="3770">
        <p data-start="3541" data-end="3770">Continue usando <code data-start="3557" data-end="3570">role:master</code>, <code data-start="3572" data-end="3589">role:secretaria</code>, <code data-start="3591" data-end="3604">role:escola</code> <strong data-start="3605" data-end="3636">com verificação do contexto</strong> (ex.: ‘secretaria’ só acessa se o <code data-start="3671" data-end="3685">current_role</code> == ‘secretaria’ <strong data-start="3702" data-end="3707">e</strong> a escola do contexto for uma escola-mãe criada por ela, etc.).</p>
        </li>
        </ul>
        <h1 data-start="3772" data-end="3821">Páginas necessárias (simples e reaproveitáveis)</h1>
        <ul data-start="3822" data-end="4378">
        <li data-start="3822" data-end="4056">
        <p data-start="3824" data-end="3935"><code data-start="3824" data-end="3849">choose_school.blade.php</code><br data-start="3849" data-end="3852">
        Lista as escolas (por nome) nas quais o usuário tem <strong data-start="3906" data-end="3915">algum</strong> papel. Ao escolher:</p>
        <ul data-start="3938" data-end="4056">
        <li data-start="3938" data-end="3994">
        <p data-start="3940" data-end="3994">se existir 1 papel só → define contexto e redireciona.</p>
        </li>
        <li data-start="3997" data-end="4056">
        <p data-start="3999" data-end="4056">senão → redireciona para <code data-start="4024" data-end="4037">choose_role</code> com aquela escola.</p>
        </li>
        </ul>
        </li>
        <li data-start="4057" data-end="4223">
        <p data-start="4059" data-end="4135"><code data-start="4059" data-end="4082">choose_role.blade.php</code><br data-start="4082" data-end="4085">
        Lista os papéis <strong data-start="4103" data-end="4121">daquela escola</strong>. Ao escolher:</p>
        <ul data-start="4138" data-end="4223">
        <li data-start="4138" data-end="4223">
        <p data-start="4140" data-end="4223">define <code data-start="4147" data-end="4166">current_school_id</code>, <code data-start="4168" data-end="4182">current_role</code> e redireciona ao dashboard daquela role.</p>
        </li>
        </ul>
        </li>
        <li data-start="4224" data-end="4378">
        <p data-start="4226" data-end="4272"><strong data-start="4226" data-end="4250">Switcher de contexto</strong> na navbar (dropdown):</p>
        <ul data-start="4275" data-end="4378">
        <li data-start="4275" data-end="4378">
        <p data-start="4277" data-end="4378">Exibe <strong data-start="4283" data-end="4303">todas as escolas</strong> (em seções) e <strong data-start="4318" data-end="4328">papéis</strong> do usuário; ao clicar, troca contexto sem logout.</p>
        </li>
        </ul>
        </li>
        </ul>
        <h1 data-start="4380" data-end="4442">Banco – pequenos ajustes recomendados (opcionais, mas úteis)</h1>
        <ul data-start="4443" data-end="4906">
        <li data-start="4443" data-end="4686">
        <p data-start="4445" data-end="4467"><code data-start="4445" data-end="4466">syrios_usuario_role</code>:</p>
        <ul data-start="4470" data-end="4686">
        <li data-start="4470" data-end="4542">
        <p data-start="4472" data-end="4542">adicionar <code data-start="4482" data-end="4490">status</code> (tinyint) para <strong data-start="4506" data-end="4518">inativar</strong> um vínculo sem deletar;</p>
        </li>
        <li data-start="4545" data-end="4609">
        <p data-start="4547" data-end="4609">adicionar <code data-start="4557" data-end="4580">created_at/updated_at</code> (timestamps) para auditoria;</p>
        </li>
        <li data-start="4612" data-end="4686">
        <p data-start="4614" data-end="4686">opcional: <code data-start="4624" data-end="4638">last_used_at</code> para lembrar automaticamente o último contexto.</p>
        </li>
        </ul>
        </li>
        <li data-start="4687" data-end="4906">
        <p data-start="4689" data-end="4906">Se possível, <strong data-start="4702" data-end="4714">renomear</strong> <code data-start="4715" data-end="4741">syrios_usuario.school_id</code> para algo como <code data-start="4757" data-end="4774">owner_school_id</code> (apenas para clareza). <strong data-start="4798" data-end="4820">Se não puder agora</strong>, mantenha como está e <strong data-start="4843" data-end="4856">documente</strong> a semântica (como você já fez no requisitos.php).</p>
        </li>
        </ul>
        <h1 data-start="4908" data-end="4940">“Mapa” de dashboards por papel</h1>
        <ul data-start="4941" data-end="5221">
        <li data-start="4941" data-end="4975">
        <p data-start="4943" data-end="4975"><strong data-start="4943" data-end="4953">master</strong> → <code data-start="4956" data-end="4975">/master/dashboard</code></p>
        </li>
        <li data-start="4976" data-end="5037">
        <p data-start="4978" data-end="5037"><strong data-start="4978" data-end="4992">secretaria</strong> → <code data-start="4995" data-end="5008">/secretaria</code> (ou <code data-start="5013" data-end="5036">/secretaria/dashboard</code>)</p>
        </li>
        <li data-start="5038" data-end="5087">
        <p data-start="5040" data-end="5087"><strong data-start="5040" data-end="5050">escola</strong> → <code data-start="5053" data-end="5062">/escola</code> (ou <code data-start="5067" data-end="5086">/escola/dashboard</code>)</p>
        </li>
        <li data-start="5088" data-end="5137">
        <p data-start="5090" data-end="5137"><strong data-start="5090" data-end="5103">professor</strong> → <code data-start="5106" data-end="5128">/professor/dashboard</code> (futuro)</p>
        </li>
        <li data-start="5138" data-end="5181">
        <p data-start="5140" data-end="5181"><strong data-start="5140" data-end="5150">gestor</strong> → <code data-start="5153" data-end="5172">/gestor/dashboard</code> (futuro)</p>
        </li>
        <li data-start="5182" data-end="5221">
        <p data-start="5184" data-end="5221"><strong data-start="5184" data-end="5192">pais</strong> → <code data-start="5195" data-end="5212">/pais/dashboard</code> (futuro)</p>
        </li>
        </ul>
        <p data-start="5223" data-end="5390">Implemente um helper <code data-start="5244" data-end="5272">dashboard_route_for($role)</code> e um <code data-start="5278" data-end="5297">dashboard_route()</code> que usa <code data-start="5306" data-end="5331">session('current_role')</code>.<br data-start="5332" data-end="5335">
        Isso já está compatível com a navbar e com o pós-login.</p>
        <h1 data-start="5392" data-end="5430">Algoritmo (pseudocódigo) – pós login</h1>
        <pre class="overflow-visible!" data-start="5431" data-end="6430"><div class="contain-inline-size rounded-2xl relative bg-token-sidebar-surface-primary"><div class="flex items-center text-token-text-secondary px-4 py-2 text-xs font-sans justify-between h-9 bg-token-sidebar-surface-primary select-none rounded-t-2xl">php</div><div class="sticky top-9"><div class="absolute end-0 bottom-0 flex h-9 items-center pe-2"><div class="bg-token-bg-elevated-secondary text-token-text-secondary flex items-center gap-4 rounded-sm px-2 font-sans text-xs"><button class="flex gap-1 items-center select-none py-1" aria-label="Copiar"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor" xmlns="http://www.w3.org/2000/svg" class="icon-sm"><path d="M12.668 10.667C12.668 9.95614 12.668 9.46258 12.6367 9.0791C12.6137 8.79732 12.5758 8.60761 12.5244 8.46387L12.4688 8.33399C12.3148 8.03193 12.0803 7.77885 11.793 7.60254L11.666 7.53125C11.508 7.45087 11.2963 7.39395 10.9209 7.36328C10.5374 7.33197 10.0439 7.33203 9.33301 7.33203H6.5C5.78896 7.33203 5.29563 7.33195 4.91211 7.36328C4.63016 7.38632 4.44065 7.42413 4.29688 7.47559L4.16699 7.53125C3.86488 7.68518 3.61186 7.9196 3.43555 8.20703L3.36524 8.33399C3.28478 8.49198 3.22795 8.70352 3.19727 9.0791C3.16595 9.46259 3.16504 9.95611 3.16504 10.667V13.5C3.16504 14.211 3.16593 14.7044 3.19727 15.0879C3.22797 15.4636 3.28473 15.675 3.36524 15.833L3.43555 15.959C3.61186 16.2466 3.86474 16.4807 4.16699 16.6348L4.29688 16.6914C4.44063 16.7428 4.63025 16.7797 4.91211 16.8027C5.29563 16.8341 5.78896 16.835 6.5 16.835H9.33301C10.0439 16.835 10.5374 16.8341 10.9209 16.8027C11.2965 16.772 11.508 16.7152 11.666 16.6348L11.793 16.5645C12.0804 16.3881 12.3148 16.1351 12.4688 15.833L12.5244 15.7031C12.5759 15.5594 12.6137 15.3698 12.6367 15.0879C12.6681 14.7044 12.668 14.211 12.668 13.5V10.667ZM13.998 12.665C14.4528 12.6634 14.8011 12.6602 15.0879 12.6367C15.4635 12.606 15.675 12.5492 15.833 12.4688L15.959 12.3975C16.2466 12.2211 16.4808 11.9682 16.6348 11.666L16.6914 11.5361C16.7428 11.3924 16.7797 11.2026 16.8027 10.9209C16.8341 10.5374 16.835 10.0439 16.835 9.33301V6.5C16.835 5.78896 16.8341 5.29563 16.8027 4.91211C16.7797 4.63025 16.7428 4.44063 16.6914 4.29688L16.6348 4.16699C16.4807 3.86474 16.2466 3.61186 15.959 3.43555L15.833 3.36524C15.675 3.28473 15.4636 3.22797 15.0879 3.19727C14.7044 3.16593 14.211 3.16504 13.5 3.16504H10.667C9.9561 3.16504 9.46259 3.16595 9.0791 3.19727C8.79739 3.22028 8.6076 3.2572 8.46387 3.30859L8.33399 3.36524C8.03176 3.51923 7.77886 3.75343 7.60254 4.04102L7.53125 4.16699C7.4508 4.32498 7.39397 4.53655 7.36328 4.91211C7.33985 5.19893 7.33562 5.54719 7.33399 6.00195H9.33301C10.022 6.00195 10.5791 6.00131 11.0293 6.03809C11.4873 6.07551 11.8937 6.15471 12.2705 6.34668L12.4883 6.46875C12.984 6.7728 13.3878 7.20854 13.6533 7.72949L13.7197 7.87207C13.8642 8.20859 13.9292 8.56974 13.9619 8.9707C13.9987 9.42092 13.998 9.97799 13.998 10.667V12.665ZM18.165 9.33301C18.165 10.022 18.1657 10.5791 18.1289 11.0293C18.0961 11.4302 18.0311 11.7914 17.8867 12.1279L17.8203 12.2705C17.5549 12.7914 17.1509 13.2272 16.6553 13.5313L16.4365 13.6533C16.0599 13.8452 15.6541 13.9245 15.1963 13.9619C14.8593 13.9895 14.4624 13.9935 13.9951 13.9951C13.9935 14.4624 13.9895 14.8593 13.9619 15.1963C13.9292 15.597 13.864 15.9576 13.7197 16.2939L13.6533 16.4365C13.3878 16.9576 12.9841 17.3941 12.4883 17.6982L12.2705 17.8203C11.8937 18.0123 11.4873 18.0915 11.0293 18.1289C10.5791 18.1657 10.022 18.165 9.33301 18.165H6.5C5.81091 18.165 5.25395 18.1657 4.80371 18.1289C4.40306 18.0962 4.04235 18.031 3.70606 17.8867L3.56348 17.8203C3.04244 17.5548 2.60585 17.151 2.30176 16.6553L2.17969 16.4365C1.98788 16.0599 1.90851 15.6541 1.87109 15.1963C1.83431 14.746 1.83496 14.1891 1.83496 13.5V10.667C1.83496 9.978 1.83432 9.42091 1.87109 8.9707C1.90851 8.5127 1.98772 8.10625 2.17969 7.72949L2.30176 7.51172C2.60586 7.0159 3.04236 6.6122 3.56348 6.34668L3.70606 6.28027C4.04237 6.136 4.40303 6.07083 4.80371 6.03809C5.14051 6.01057 5.53708 6.00551 6.00391 6.00391C6.00551 5.53708 6.01057 5.14051 6.03809 4.80371C6.0755 4.34588 6.15483 3.94012 6.34668 3.56348L6.46875 3.34473C6.77282 2.84912 7.20856 2.44514 7.72949 2.17969L7.87207 2.11328C8.20855 1.96886 8.56979 1.90385 8.9707 1.87109C9.42091 1.83432 9.978 1.83496 10.667 1.83496H13.5C14.1891 1.83496 14.746 1.83431 15.1963 1.87109C15.6541 1.90851 16.0599 1.98788 16.4365 2.17969L16.6553 2.30176C17.151 2.60585 17.5548 3.04244 17.8203 3.56348L17.8867 3.70606C18.031 4.04235 18.0962 4.40306 18.1289 4.80371C18.1657 5.25395 18.165 5.81091 18.165 6.5V9.33301Z"></path></svg>Copiar código</button></div></div></div><div class="overflow-y-auto p-4" dir="ltr"><code class="whitespace-pre! language-php"><span><span><span class="hljs-variable">$user</span></span><span> = </span><span><span class="hljs-title class_">Auth</span></span><span>::</span><span><span class="hljs-title function_ invoke__">user</span></span><span>();
        </span><span><span class="hljs-variable">$roles</span></span><span> = </span><span><span class="hljs-variable">$user</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">roles</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">with</span></span><span>(</span><span><span class="hljs-string">'pivot'</span></span><span>)-&gt;</span><span><span class="hljs-title function_ invoke__">get</span></span><span>(); </span><span><span class="hljs-comment">// tem role_id e pivot.school_id</span></span><span>

        </span><span><span class="hljs-keyword">if</span></span><span> (</span><span><span class="hljs-variable">$roles</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">isEmpty</span></span><span>()) {
            </span><span><span class="hljs-keyword">return</span></span><span> </span><span><span class="hljs-title function_ invoke__">view</span></span><span>(</span><span><span class="hljs-string">'auth.no_access'</span></span><span>); </span><span><span class="hljs-comment">// ou redirect com mensagem</span></span><span>
        }

        </span><span><span class="hljs-variable">$porEscola</span></span><span> = </span><span><span class="hljs-variable">$roles</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">groupBy</span></span><span>(</span><span><span class="hljs-string">'pivot.school_id'</span></span><span>);

        </span><span><span class="hljs-keyword">if</span></span><span> (</span><span><span class="hljs-variable">$porEscola</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">count</span></span><span>() === </span><span><span class="hljs-number">1</span></span><span>) {
            </span><span><span class="hljs-variable">$sid</span></span><span> = </span><span><span class="hljs-variable">$porEscola</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">keys</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">first</span></span><span>();
            </span><span><span class="hljs-variable">$rolesDaEscola</span></span><span> = </span><span><span class="hljs-variable">$porEscola</span></span><span>[</span><span><span class="hljs-variable">$sid</span></span><span>];

            </span><span><span class="hljs-keyword">if</span></span><span> (</span><span><span class="hljs-variable">$rolesDaEscola</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">count</span></span><span>() === </span><span><span class="hljs-number">1</span></span><span>) {
                </span><span><span class="hljs-title function_ invoke__">setContext</span></span><span>(</span><span><span class="hljs-variable">$sid</span></span><span>, </span><span><span class="hljs-variable">$rolesDaEscola</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">first</span></span><span>()-&gt;role_name);
                </span><span><span class="hljs-keyword">return</span></span><span> </span><span><span class="hljs-title function_ invoke__">redirect</span></span><span>(</span><span><span class="hljs-title function_ invoke__">dashboard_route</span></span><span>());
            }

            </span><span><span class="hljs-comment">// mais de um papel na mesma escola</span></span><span>
            </span><span><span class="hljs-keyword">return</span></span><span> </span><span><span class="hljs-title function_ invoke__">redirect</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">route</span></span><span>(</span><span><span class="hljs-string">'choose.role'</span></span><span>, [</span><span><span class="hljs-string">'school_id'</span></span><span> =&gt; </span><span><span class="hljs-variable">$sid</span></span><span>]);
        }

        </span><span><span class="hljs-comment">// mais de uma escola</span></span><span>
        </span><span><span class="hljs-comment">// tente reabrir último contexto válido</span></span><span>
        </span><span><span class="hljs-keyword">if</span></span><span> (</span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_school_id &amp;&amp; </span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_role_id &amp;&amp; </span><span><span class="hljs-variable">$user</span></span><span>-&gt;</span><span><span class="hljs-title function_ invoke__">hasContext</span></span><span>(</span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_school_id, </span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_role_id)) {
            </span><span><span class="hljs-title function_ invoke__">setContext</span></span><span>(</span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_school_id, </span><span><span class="hljs-title function_ invoke__">roleName</span></span><span>(</span><span><span class="hljs-variable">$user</span></span><span>-&gt;last_role_id));
            </span><span><span class="hljs-keyword">return</span></span><span> </span><span><span class="hljs-title function_ invoke__">redirect</span></span><span>(</span><span><span class="hljs-title function_ invoke__">dashboard_route</span></span><span>());
        }

        </span><span><span class="hljs-comment">// senão peça escolha da escola</span></span><span>
        </span><span><span class="hljs-keyword">return</span></span><span> </span><span><span class="hljs-title function_ invoke__">redirect</span></span><span>()-&gt;</span><span><span class="hljs-title function_ invoke__">route</span></span><span>(</span><span><span class="hljs-string">'choose.school'</span></span><span>);
        </span></span></code></div></div></pre>
        <blockquote data-start="6432" data-end="6562">
        <p data-start="6434" data-end="6562"><code data-start="6434" data-end="6463">setContext($sid, $roleName)</code> grava <code data-start="6470" data-end="6489">current_school_id</code>, <code data-start="6491" data-end="6505">current_role</code>, atualiza <code data-start="6516" data-end="6524">last_*</code> se quiser, e faz qualquer log/sessão.</p>
        </blockquote>
        <h1 data-start="6564" data-end="6611">Casos especiais e regras que fecham o sistema</h1>
        <ul data-start="6612" data-end="7066">
        <li data-start="6612" data-end="6734">
        <p data-start="6614" data-end="6734"><strong data-start="6614" data-end="6672">Secretaria não pode dar role ‘master’ nem ‘secretaria’</strong> (isso já fazemos filtrando roles visíveis para a secretaria).</p>
        </li>
        <li data-start="6735" data-end="6819">
        <p data-start="6737" data-end="6819"><strong data-start="6737" data-end="6761">Troca de escola/role</strong>: sempre validar contra a pivot. Não é só setar na sessão.</p>
        </li>
        <li data-start="6820" data-end="6931">
        <p data-start="6822" data-end="6931"><strong data-start="6822" data-end="6847">Acesso direto por URL</strong>: middleware garante que <code data-start="6872" data-end="6886">current_role</code> e <code data-start="6889" data-end="6908">current_school_id</code> existem e são válidos.</p>
        </li>
        <li data-start="6932" data-end="7066">
        <p data-start="6934" data-end="7066"><strong data-start="6934" data-end="6958">Usuário dono (owner)</strong> não necessariamente tem acesso àquela escola <strong data-start="7004" data-end="7021">como contexto</strong>. Só a pivot decide <em data-start="7041" data-end="7047">onde</em> e <em data-start="7050" data-end="7056">como</em> ele atua.</p>
        </li>
        </ul>
        <h1 data-start="7068" data-end="7095">Próximos passos concretos</h1>
        <ol data-start="7096" data-end="7666">
        <li data-start="7096" data-end="7183">
        <p data-start="7099" data-end="7183">Criar as telas <code data-start="7114" data-end="7129">choose_school</code> e <code data-start="7132" data-end="7145">choose_role</code> (simples, reaproveitando seu visual).</p>
        </li>
        <li data-start="7184" data-end="7359">
        <p data-start="7187" data-end="7281">Ajustar LoginController para chamar <strong data-start="7223" data-end="7237">um serviço</strong> que roda esse algoritmo e decide o destino:</p>
        <ul data-start="7285" data-end="7359">
        <li data-start="7285" data-end="7359">
        <p data-start="7287" data-end="7359"><code data-start="7287" data-end="7328">ContextService::decideAfterLogin($user)</code> retorna um <code data-start="7340" data-end="7358">RedirectResponse</code>.</p>
        </li>
        </ul>
        </li>
        <li data-start="7360" data-end="7468">
        <p data-start="7363" data-end="7468">Criar <code data-start="7369" data-end="7392">EnsureContextSelected</code> e reforçar seus middlewares de role para consultarem <strong data-start="7446" data-end="7456">também</strong> o contexto.</p>
        </li>
        <li data-start="7469" data-end="7573">
        <p data-start="7472" data-end="7573">Navbar: dropdown “Trocar contexto” usando <code data-start="7514" data-end="7545">session('available_contexts')</code> (ou consultando no clique).</p>
        </li>
        <li data-start="7574" data-end="7666">
        <p data-start="7577" data-end="7666">(Opcional) Persistir <code data-start="7598" data-end="7627">last_school_id/last_role_id</code> no usuário ou <code data-start="7642" data-end="7656">last_used_at</code> na pivot.</p>
        </li>
        </ol>
        <p data-start="7668" data-end="7696">Se quiser, eu já te entrego:</p>
        <ul data-start="7697" data-end="7939">
        <li data-start="7697" data-end="7784">
        <p data-start="7699" data-end="7784">o <strong data-start="7701" data-end="7719">ContextService</strong> com os métodos <code data-start="7735" data-end="7753">decideAfterLogin</code>, <code data-start="7755" data-end="7767">setContext</code>, <code data-start="7769" data-end="7783">clearContext</code>;</p>
        </li>
        <li data-start="7785" data-end="7816">
        <p data-start="7787" data-end="7816">as <strong data-start="7790" data-end="7804">duas views</strong> de escolha;</p>
        </li>
        <li data-start="7817" data-end="7860">
        <p data-start="7819" data-end="7860">o <strong data-start="7821" data-end="7835">middleware</strong> <code data-start="7836" data-end="7859">EnsureContextSelected</code>;</p>
        </li>
        <li data-start="7861" data-end="7939">
        <p data-start="7863" data-end="7939">os <strong data-start="7866" data-end="7877">ajustes</strong> no LoginController e na navbar para usar <code data-start="7919" data-end="7938">dashboard_route()</code>.</p>
        </li>
        </ul>
        <p data-start="7941" data-end="7964" data-is-last-node="" data-is-only-node="">Assim você cola e roda.</p></div></div></div></div></div>
      </div>
    </div>
  </div>

</div>

 






<!--
<br>
    1.  O usuários faz login. Temos acesso ao seu “school_id” (“id” da Escola Mãe, ou seja, “id” do Criador do usuário)
    2.  Seus roles são carregados (Seu Papel/Suas Funções em uma ou mais escola)
    a.  Se (tem papel em apenas uma escola) 
    i.  Se (tem papel único na escola) => vai para dashboard da Instituição associada na tabela UsuarioRole(school_id)
    ii. Se (tem papeis diferentes na mesma escola) => Mostra ao usuário a Lista de papeis e direciona para o dashboard correto.

    b.  Se (tem papel em mais de uma escola) => Mostra a lista de Instituições vinculadas ao Usuário para que ele escolha em qual vai se logar. 
    i.  Se (tem papel único na escola escolhida) => vai para dashboard da Instituição associada na tabela UsuarioRole(school_id)
    ii. Se (tem papeis diferentes na escola escolhida) => Mostra ao usuário a Lista de papeis e direciona para o dashboard correto.
    c.  Se (não tem papel) => ainda não definido
    3.  O usuário está logado no sistema
    4.  Temos acesso ao “school_id” do usuário(mãe::tabela Usuario) e ao “school_id” da escola logada(anfitriã::tabela RoleUsuario)<br>


        Cria os Usuários: Professor, Aluno, Pai, etc.

        Cria um Usuário para administrar a Escola(defindo a Role_Usuario 'escola' junto com o id_escola de destino)
            
            Secretaria sempre é seu dono (school_id(Usuario)=id(Secretaria))
            
        

        Cria Escolas para uma Secretaria(opcional::Essa é função de uma Secretaria=>criar escolas para si mesma)

            Associa uma Secretaria para ser a mãe (secretaria_id(Escola)=id(Secretaria))-nesse caso o Master fez o papel da Secretaria

        Cria um Usuário para administrar uma Escola de uma Secretaria(definindo a)(opcional::Essa é função de uma Secretaria=>criar usuários para administrar suas Escolas)
            
            Associa uma Secretaria para ser a dona (school_id(Usuario)=id(Secretaria))-nesse caso o Master fez o papel da Secretaria

        Cria Escolas para uma Secretaria(opcional::Essa é função de uma Secretaria)
            Associa a Escola a uma Secretaria
        Associa Escola a uma Secretaria(opcional-é papel da Secretaria)
        
        
        Associa um Usuário a uma Role em uma Escola(opcional-este é papel da Secretaria)

        Cria Escolas(escolas filhas)
    Associa -->
    
    <ol>
        <li>
            <h2>Usuário Master</h2>
            <ol>
                <li>Dashboard</li>
                O dashboard o usuário master tem as seguintes funções
                <li>Escolas</li>

                <li>Usuários</li>
                <li>Funções/Papeis</li>
            </ol>
        </li>
        
    </ol>

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
