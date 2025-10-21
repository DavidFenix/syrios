
------------------------------------------------------------------------
vamos construir uma página para que se possa disponibilizar o regimento escolar de cada escola.
--cada escola deve ter seu proprio regimento 
--a escola logada pode alterar documento. 
--o professor logado, ou outros usuarios podem abrir o documento(ele é livre para consulta)
--não sei se seria bom criar uma tela de edição para a escola digitar, inserir marcadores, numeração romana, inserir uma imagem, alterar fonte, cores, tamanhos, etc, e salvar, não sei onde, vinculando à escola.
--ou se seria melhor só permitir enviar um documento pdf mesmo. o que vc sugere?

--exemplo de regimento digitado::
[logo da escola]
[nome da escola]
[frase de efeito da escola]
Regimento Escolar - Fevereiro/2025

DO CORPO DOCENTE

Art. 36º - O corpo discente será composto por todos os alunos regulamente matriculados.

Art. 37º - São direitos do Corpo Discente:

    Conhecer o regimento escolar, especificamente no que se refere ao corpo discente.
    Receber em igualdade de condições a orientação necessária para realizar suas atividades escolares e usufruir de todos os benefícios de caráter religiosos, educativo, recreativo e social, sendo respeitados também em sua individualidade, sem comparação nem preferências por toda a comunidade escolar.
...
vamos enviar pdf mesmo
migração-------------------
--cd c:\wamp64\www\syrios
--php artisan migrate --path=database/migrations/2025_10_21_000000_create_regimento_table.php

---------------------------------------------------------------
mais ideias:::
vamos pensar no seguinte: no meio da aula do professor ele quer aplicar uma ocorrencia mas não quer perder muito tempo elaborando-a. ele precisa de rapidez e eficiencia para não esquecer nada, adicionar ou remover fatos com rapidez, ou adicionar ou remover alunos com rapidez. Atualmente o nosso fluxo de aplicar uma ocorrencia começa com:
    --o professor abrindo uma de suas ofertas(1)
    --vai para a lista de alunos e marca quantos precisar(2)
    --descreve e salva a ocorrencia(3)

Talvez podíamos fazer com que após a fase 3 o professor volte para a mesma turma novamente e além disso consiga reaplicar(reaproveitar) as ocorrencias já aplicadas nessa turma fazendo com que o professor não tenha que repetir tudo novamente só pra incluir mais um aluno.

Talvez seja muito proveitoso se 
    --pudesse incluir alunos numa ocorrencia já aplicada em outro(s) aluno(s)
    --fazer uma ocorrencia rascunho(o professor vai fazendo a ocorrencia sem correr perigo de perder dados mas só salva no banco quando ele der o comando final)
    --fazer ocorrencias rascunho diferenciadas de modo que uma parte da ocorrencia é padrao para todos da lista mas alguns tem ums detalhes diferentes
    --adicionar ou remover alunos do rascunho
    --salvar a ocorrencia localmente para não perder caso falte energia ou o professor desista de aplicar naquele momento
    --se estiver sem internet permitir salvar localmente e assim que a internet voltar o app envia para o banco de dados sem precisar novamente da interferencia do professor
    --tem mais alguma ideia? como o laravel ou outros recursos podem turbinar nosso app nesse sentido? vamos discutir primeiro como turbinar esse app antes de começar a codificar!!
---------------------------------------------------------------------------

instalação do laravel-dompdf
-- cd c:\wamp64\www\syrios
-- composer require barryvdh/laravel-dompdf
-- php artisan optimize:clear
-- php artisan cache:clear
-- php artisan view:clear
-- php artisan config:clear
-- php artisan route:clear
-- php artisan optimize:clear
-----------------------------------------------------

vamos construir os blades para views/professor/ofertas/index.blade
   --vai exibir as ofertas do professor, i.e., cada class="btn-group" (uma linha retangular com cantos arredondados que combina texto e botões) terá:
      --número cardinal + nome disciplina(serve de botão para ver lista de alunos noutra página) + turma(primeiros 10 digitos) + "Visão Geral:|1|2|5|3|0(serve de botao para abrir acordion e ver explicação dos badges colorido com número dentro);

   --os badges é um resumo informativo com números em seu interior e cores específicas
   --exemplo dos badges com os números: |1|2|5|3|0 com as seguintes cores e significados:
      --badge cor cinza:representa a quantidade de alunos da turma com 1 ocorrencia ativa: $qtd1=1
      --badge cor amarelo claro:representa a quantidade de alunos da turma com 2 ocorrencias ativas: $qtd2=2
      --badge cor amarelo escuro:representa a quantidade de alunos da turma com 3 ocorrencias ativas: $qtd3=5
      --badge cor laranja:representa a quantidade de alunos da turma com 4 ocorrencias ativas: $qtd4=3
      --badge cor vermelha:representa a quantidade de alunos da turma com 5 ocorrencias ativas ou mais: $qtd5=0
   --ao clicar no texto "Visão Geral" o acordion se abre com os 5 badges e os textos explicativos como se fosse a legenda do significado das cores descritos anteriormente e a quantidade de cada ocorrencia vinda do banco de dados
   --cada oferta possui seu acordion e iniciam todos fechados

   --Exemplo das linhas com textos e botões usando class="btn-group" ou algo similar do bootstrap
      --| 1 | Matemática | 2ª Série A ! Visão Geral:|1|2|5|3|0|
      --| 2 | Matemática | 2ª Série B | Visão Geral:|0|0|1|2|8| 

   --Ao clicar encima da disciplina exibi-se acima de todas as linhas(botões das ofertas) a lista de alunos da turma correspondente ao botão clicado
   --cada linha contem: numero + checkbox + foto do aluno com cantos arredondados + matricula + nome do aluno + Total geral de ocorrencias ativas + botao para depois abrir,em outra página, historico de ocorrencias ativas + botao para depois baixar PDF do histórico de ocorrencias ativas e arquivadas + Turma + Disciplina
   --ao clicar ou passar o mouse sobre a foto permite-se dá zoom para ver melhor o rosto do aluno
   --um botao no topo da pagina para confirmar as escolhas dos alunos que serão aplicadas as ocorrencias(vai para outra página para continuar os detalhes da aplicação da ocorrencia)

   --os botões com a lista de ofertas do professor continuam abaixo da tabela para caso o professor decida acionar outra turma ao inves da primeira que escolheu

--quanto a aplicação da ocorrencia baseada nos campos do banco de dados temos
   --exibe uma listas de checkbox para que o professor escolha um ou mais itens da tabela modelo_motivo, de forma bem rápida.
   --talvez a lista possa ficar retraido dentro de um acrodion pra economizar espaço
   --uma caixa de texto para digitar outra descrição(caso o motivo da ocorrencia não se encaixe na lista definida na tabela modelo_motivo)
   --Opções complementares que podem ficar null no banco de dados como:
      --Local(pode ser um select com a primeira opção já marcada): Sala de aula | Ambientes de apoio | Pátio da escola | Quadra poliesportiva | Galerias | Outro
      --Atitude do professor(pode ser um select com a primeira opção já marcada): Advertencia | Ordem de saida de sala | Outra
      --Outra atitude: local para escrever outra atitude que não estiver no select
      --Comportamento do aluno(pode ser um select com a primeira opção já marcada): 1ª vez | Reincidente (pouco frequente) | Reincidente (frequente)
      --Sugestão de medidas a serem tomadas: local para escrever a sugestão


-------------------------------------------------------
vamos montar outro histórico, dessa vez resumido, em formato de tabela
    --cabeçalho do resumo
        --imagem 40x40px, circular, da instituição
        --nome da instituição
        --frase de efeito da instituição
    --sessão informações do aluno
        --pode ser destacada dentro de um retangulo com bordas e cantos arredondados
        --dentro do retangulo do lado esquerdo coloca-se a foto do aluno circular
        --a direita da foto, proximo a ela, ainda linhado a esquerda, coloca-se Turma:??
        e abaixo de turma coloca-se talvez matricula, não sei, ajuda ai
    --abaixo da sessão de informação do aluno vem o titulo da tabela "Histórico de Ocorrências do Aluno"
    --agora vem a tabela com as seguintes colunas
        --numeros cardinais + Data dd/mm/AAAA + Descrição que foi digitada da ocorrencias concatenado com cada um dos outros motivos sendo eles separados por uma barra / + Abreviação da Disciplina presente no banco + Nome Sobrenome do professor + status da ocorrencia(Ativa/Arquivada)
        --ordenar as ocorrencias com a data mais recente em cima
    --Disponibilizar lá emcima botões para imprimir ou baixar pdf(não deixar aparecer botões ou outros poluentes na impressão ou pdf)
    cada linha da tabela


-----------------------------------------------------------------------------
montando e usando um debug de query em helpers
    exemplos de uso
    $query = Usuario::whereHas('roles', function($q) use ($roleProfessorId, $schoolId) {
        $q->where(prefix('usuario_role').'.role_id', $roleProfessorId)
          ->where(prefix('usuario_role').'.school_id', $schoolId);
    });

    sql_dump($query);

    ----------
    $q = DB::table(prefix('aluno'))
        ->where('school_id', 5)
        ->where('matricula', '12345');

    sql_dump($q);

    -----------
    Apenas visualizar sem interromper execução
    sql_dump($query, false);
    Isso apenas mostra no console, mas não para o código (útil em loops ou testes PHPUnit).

    ------------
    Se quiser inspecionar todas as queries rodadas numa requisição:
    \DB::enableQueryLog();
    // ... suas operações Eloquent ...
    sql_dump(DB::getQueryLog(), false);




------------------------------------------------------
php artisan route:list



🧱 FASE 1 — ENTURMAÇÃO MANUAL (1:1)
no blade create
vamos acrescentar um modo mais eficiente do que um select para achar um aluno. as duas opções a seguir podem ficar na mesma página
    opção 1:caixar de pesquisa geral(um filtro por nome e outro filtro por matricula)
    --localização imediata no sistema de um ou mais alunos ao clicar no botao de busca
    --carregar lista de alunos com checkbox (com opçao de marcar e desmarcar todos)
    --escolher o ano destino(ou propor o ano vigente) -> escolher a turma de destino
    --botao enturmar
    --enturma todos os alunos marcados na turma e ano escolhidos

    opção 2:pesquisa por turma
    --escolher o ano -> escolher a turma de origem
    --escolher o ano destino(ou propor o ano vigente) -> escolher a turma de destino
    --carregar lista de alunos com checkbox (com opçao de marcar e desmarcar todos) ao clicar no botao de busca
    --botao enturmar
    --enturma todos os alunos marcados na turma e ano escolhidos


-----------------------------------------------------------------------------------------------
migração para incluir ano_letivo e vigente nas tabelas
--cd c:\wamp64\www\syrios
--php artisan migrate --path=database/migrations/2025_10_16_122628_add_ano_vigente_to_temporais_tables.php


------------------------------------------------------------------------------------------------
link simbolico para acessar pasta no storage
	--cd c:\wamp64\www\syrios
	--php artisan storage:link
	--coloque as imagens dos alunos em storage/app/public/img-user
---------------------------------------------------------------------------------------------

🧭 CONTEXTO: Usuário logado em uma escola

O usuário da sessão (auth()->user()) está logado como gestor da escola (role escola) no contexto atual.

Portanto, ele pode gerir os usuários da sua escola, mas dentro de limites.

🧩 REGRAS DE EDIÇÃO — CLAREZA TOTAL
Situação	Pode editar dados pessoais (nome, status)?	Pode alterar senha?	Pode gerenciar roles?	Observações
👤 Usuário logado (ele mesmo)	❌ Não (mostra apenas leitura)	✅ Sim (alterar senha)	✅ Sim (pode mexer nas próprias roles permitidas, exceto escola)	Pode adicionar/remover “professor”, “aluno” etc., mas não pode remover ou mexer na role escola
👥 Colega gestor (outro com role escola na mesma escola)	❌ Não	❌ Não	❌ Não	Não pode interferir em outro gestor
👨‍🏫 Usuário comum (professor, aluno, pai etc.) da mesma escola	✅ Sim (nome, status, senha)	✅ Sim	✅ Sim	Pode gerenciar seus subordinados
🧱 Usuário apenas vinculado (não criado pela escola, mas vinculado a ela)	❌ Não (modo leitura)	❌ Não	❌ Não	A escola só pode desvincular, não alterar dados
🏛 Usuário superior (secretaria / master)	❌ Não	❌ Não	❌ Não	Intocável no nível escola
🧱 REGRAS DE EXCLUSÃO
Situação	Ação permitida?	Tipo de exclusão
👤 Excluir a si mesmo	❌ Nunca	—
👥 Excluir colega gestor (role escola na mesma escola)	❌ Nunca	—
🧩 Excluir usuário comum criado pela escola	✅ Sim	Exclusão total (se não violar FK)
🧩 Excluir usuário vinculado (não criado pela escola)	✅ Sim	Remove apenas o vínculo (pivot usuario_role e professor)
🏛 Excluir secretaria / master	❌ Nunca	—
🔐 REGRAS DE PROTEÇÃO DE ROLES
Role	Quem pode atribuir / remover	Observações
master	apenas super master	nível do sistema
secretaria	apenas master	nível da secretaria
escola	apenas secretaria	nível da escola
professor, aluno, responsavel, etc.	gestor da escola	a escola pode livremente atribuir e remover
(qualquer outra futura)	conforme hierarquia	manter coerência
⚙️ CONCLUSÃO — O QUE DEVEMOS TER NAS TELAS
🔹 Tela INDEX (listagem de usuários da escola)

Mostrar editar/excluir apenas se permitido conforme tabela acima.

Mostrar 🔒 para usuários protegidos.

Para o usuário logado, mostrar botão especial: “Alterar senha” + “Gerenciar roles”.

🔹 Tela EDIT

Se for o próprio usuário, mostra apenas o campo de senha.

Se for usuário comum da escola, mostra nome, status, senha.

Se for colega gestor, vinculado, secretaria ou master, mostra tudo em modo leitura (view_only).

🔹 Tela de ROLES

Se o usuário logado editar a si mesmo:

Pode marcar/desmarcar roles permitidas.

O checkbox escola aparece desabilitado (cadeado).

Se editar outro usuário:

Aplicam-se as proteções hierárquicas (não mexer em superiores ou iguais).
---------------------------------------------------------------------

vamos para o edit
--usuario filho da escola podemos editar(nome, senha, status) no edit.blade
	--vamos exibir seus vinculos agrupado por escola igual fizemos em secretaria e master
	--lá vamos colocar um botao para gerenciar as roles as roles na roles_edit

--usuário apenas vinculado vamos apenas exibir seus dados
	--lá vamos colocar um botao para gerenciar as roles na roles_edit

--o proprio usuario logado na sessão pode editar (senha)
	--e gerenciar suas roles permitidas na roles_edit exceto sua role escola

--seu colega que gerencia a mesma escola vamos apenas 
	--exibir seus dados na view_only

--vamos proteger demais usuarios que não estão vinculados nem pertencem a escola, contra edição 
	--exibir seus dados na view_only

--faça outras regras que posso ter esquecido


para corrigir
--vamos deixar o usuario logado na escola alterar suas roles permitidas nessa escola, exceto a role escola, que já está protegida e só quem mexe é o secretario, que foi quem o criou


-----------------------------------------------------------
testes/corrigir
	--remover sincronização automatica, senão não consigo deletar usuario e sempre que deletar professor a sincronização lhe adiciona novamente. 
	--o usuario logado não consegue editar sua senha
	--testar regras de edição de usuarios e role

--------------------------------------------------------------------
vamos proteger o destroy
	--a exclusão total do usuario só pode ser feita se a escola for dona do usuario
	e não violar chaves
	--caso o usuario seja apenas vinculado, deve-se remover somente o vinculo com a escola, com muito cuidado e se não violar chaves
	--se o usuario for professor na escola deve-se remover o vinculo de professor também da tabela professor
	--tente lembrar de alguma regra importante que eu esteja esquecendo de aplicar
	esqueci de dizer também
	--não deixar o usuario excluir a si mesmo(pois ele controla a escola)
	--não deixar o usuario com role escola(usuario logado) excluir seu colega de trabalho(usuario que tem role escola para esta escola logafa)















<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


┌────────────────────────────────────────────────────────────┐
│                         MASTER                             │
│  - ID: 1                                                    │
│  - Cria / gerencia todas as Secretarias                     │
│  - Pode tudo (inclui Super Master)                          │
└────────────────────────────────────────────────────────────┘
                │
                │ (cada master gerencia várias secretarias)
                ▼
┌────────────────────────────────────────────────────────────┐
│                   SECRETARIA (Escola-mãe)                  │
│  Tabela: syrios_escola                                     │
│  Exemplo: SEDUC - Secretaria da Educação                   │
│  Campos: id, nome_e, cidade, estado, secretaria_id = NULL   │
│                                                            │
│  Usuários (syrios_usuario)                                 │
│  - Roles: secretaria (obrigatória)                         │
│  - Podem criar/gerenciar escolas filhas                    │
│  - Não podem se editar entre si                            │
└────────────────────────────────────────────────────────────┘
                │
                │ (uma secretaria pode ter várias escolas filhas)
                ▼
┌────────────────────────────────────────────────────────────┐
│                      ESCOLAS FILHAS                        │
│  Tabela: syrios_escola                                     │
│  Exemplo: EEMTI Ubiratan Diniz de Aguiar                   │
│  Campos: id, nome_e, secretaria_id = (id da secretaria)     │
│                                                            │
│  Usuários (syrios_usuario)                                 │
│  - Roles: escola, professor, pai, aluno, etc.              │
│  - Criados/vinculados pela secretaria                      │
│  - Só podem editar dados básicos                           │
└────────────────────────────────────────────────────────────┘
                │
                │ (relações via pivot syrios_usuario_role)
                ▼
┌────────────────────────────────────────────────────────────┐
│                      USUÁRIOS                              │
│  Tabela: syrios_usuario                                    │
│  Exemplo: David Costa, Ravi Costa                          │
│  Campos: id, nome_u, cpf, school_id, status, senha_hash     │
│                                                            │
│  Relações:                                                 │
│  usuario_role (pivot) → [usuario_id, role_id, school_id]    │
│                                                            │
│  Exemplo de múltiplas roles:                               │
│   - David Costa → secretaria@SEDUC                         │
│   - David Costa → escola@Ubiratan, professor@Ubiratan      │
│   - David Costa → escola@FMota, professor@FMota            │
└────────────────────────────────────────────────────────────┘
                │
                │ (role_id referencia syrios_role)
                ▼
┌────────────────────────────────────────────────────────────┐
│                      ROLES                                 │
│  Tabela: syrios_role                                       │
│  Exemplo: master, secretaria, escola, professor, pai, etc.  │
│  Campos: id, role_name, descricao                           │
│                                                            │
│  Aplicação dinâmica via tabela pivot                       │
│  syrios_usuario_role (com campo school_id contextualizado)  │
└────────────────────────────────────────────────────────────┘

