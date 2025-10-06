# Syrios — Factories e Seeders Refatorados

Pacote de factories e seeders compatível com Laravel 8.x e com o sistema de prefixos de tabela do Syrios (`BaseModel` e `config('prefix.tabelas')`).

## Estrutura incluída
- **Factories:** Escola, Usuario, Professor, Turma, Disciplina, Aluno  
- **Seeders:** RolesSeeder, DevSeeder, TestDataSeeder, DatabaseSeeder  

## Objetivo
Facilitar a popular o banco de dados de desenvolvimento e testes, criando:
- Estrutura base com roles e escolas
- Usuário super master (CPF: `master`, senha: `123456`)
- 20 usuários Faker vinculados a escolas
- Professores, disciplinas, turmas e alunos de exemplo

## Comandos principais

Popular roles e base:
	php artisan db:seed

Popular estrutura completa (usuário master + escolas):
	php artisan db:seed --class="DevSeeder"

Popular estrutura completa (completa):
php artisan db:seed --class=FullDevSeeder

Gerar massa de dados de teste (20 usuários Faker):
	php artisan db:seed --class="TestDataSeeder"


💡 Dica extra para o seu fluxo de testes

Como agora o sistema Syrios está com seeders e factories funcionando, você pode:
	php artisan migrate:fresh --seed

👉 Isso vai:

Apagar todas as tabelas,
Recriar o esquema,
Executar o DevSeeder automaticamente.
É o jeito mais rápido de testar tudo do zero em poucos segundos — ideal pra validar regras de exclusão, foreign keys, permissões, etc.

sempre tive interesse em criar testes automatizado para tornar o sistema a prova de falhas mas nunca tentei fazer isso por falta de tempo para pesquisar e ler sobre o assunto

“Usuário está desenvolvendo o sistema Laravel Syrios, com prefixo de tabelas dinâmico (syrios_), migrations, factories e seeders completos, e está atualmente refinando o FullDevSeeder com lógica de relacionamentos automáticos entre escolas, usuários e papéis.”

“Vamos continuar o projeto Laravel Syrios. Estamos na parte do FullDevSeeder, já com prefixo dinâmico e relacionamentos entre escolas, usuários e roles.”

“Continuar o projeto Syrios a partir do FullDevSeeder funcional.”

você está desenvolvendo o sistema Laravel Syrios com prefixo dinâmico e está na fase de seeders complexos (FullDevSeeder, relacionamentos automáticos, multi-roles)?

“Continuar o projeto Syrios”