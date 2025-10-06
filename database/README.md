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