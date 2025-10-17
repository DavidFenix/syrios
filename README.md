



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
--php artisan make:migration add_ano_vigente_to_temporais_tables --table=syrios_enturmacao
--php artisan migrate

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

