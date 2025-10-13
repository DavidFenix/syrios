vamos para o edit
--usuario filho da escola podemos editar(nome, senha, status)
	--vamos exibir seus vinculos agrupado por escola igual fizemos em secretaria e master
	--lá vamos colocar um botao para gerenciar as roles depois noutra pagina

--usuário apenas vinculado vamos apenas exibir seus dados
	--lá vamos colocar um botao para gerenciar as roles depois noutra pagina

--o proprio usuario logado na sessão pode editar (senha)

--seu colega que gerencia a mesma escola vamos apenas exibir seus dados

--vamos proteger demais usuarios que não estão vinculados nem pertencem a escola, contra edição 

--faça outras regras que posso ter esquecido



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

