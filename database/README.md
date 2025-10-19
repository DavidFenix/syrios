mudanças na base de dados
-----------------------------------------------
CREATE TABLE `syrios_ocorrencia` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  
  -- Contexto institucional
  `school_id` BIGINT UNSIGNED NOT NULL,       -- escola em que a ocorrência foi registrada
  `ano_letivo` INT(4) NOT NULL DEFAULT 2025,  -- ano da ocorrência
  `vigente` BOOLEAN DEFAULT TRUE,             -- se ainda ativa ou arquivada

  -- Relacionamentos
  `aluno_id` BIGINT UNSIGNED NOT NULL,        -- aluno envolvido
  `professor_id` BIGINT UNSIGNED NOT NULL,    -- autor (usuário professor)
  `oferta_id` BIGINT UNSIGNED NULL,           -- vínculo disciplina/turma opcional
  
  -- Informações principais
  `descricao` TEXT NULL,                      -- descrição livre digitada
  `local` VARCHAR(100) NULL,                  -- local (Sala de aula, etc.)
  `atitude` VARCHAR(100) NULL,                -- Advertência, Ordem de saída, etc.
  `outra_atitude` VARCHAR(150) NULL,          -- texto livre
  `comportamento` VARCHAR(100) NULL,          -- reincidente etc.
  `sugestao` TEXT NULL,                       -- sugestões de medidas
  `status` TINYINT DEFAULT 1,                 -- 1=ativa, 0=arquivada, 2=anulada
  `nivel_gravidade` TINYINT DEFAULT 1,        -- 1 a 5 (p/ estatísticas)
  `sync` TINYINT DEFAULT 1,					  -- confirma sincronização com banco via api
  `recebido_em` TIMESTAMP NULL DEFAULT NULL,  -- atualização para o diretor de turma
  `encaminhamentos` TEXT NULL,				  -- atualização para o diretor de turma
  
  -- Controle temporal
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `syrios_ocorrencia_motivo` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ocorrencia_id` BIGINT UNSIGNED NOT NULL,
  `modelo_motivo_id` BIGINT UNSIGNED NOT NULL,
  
  FOREIGN KEY (`ocorrencia_id`) REFERENCES `syrios_ocorrencia` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`modelo_motivo_id`) REFERENCES `syrios_modelo_motivo` (`id`) ON DELETE CASCADE
);

CREATE TABLE `syrios_modelo_motivo` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `school_id` BIGINT UNSIGNED NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `categoria` VARCHAR(100) NULL
);

ALTER TABLE syrios_ocorrencia_motivo
ADD UNIQUE KEY uq_ocorrencia_motivo (ocorrencia_id, modelo_motivo_id);

--preparamos uma migração para isso:
-- php artisan migrate --path=database/migrations/2025_10_18_000001_create_ocorrencia_tables.php
-- php artisan db:seed --class=MotivosPadraoSeeder
---------------------------------------------------------------
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

cd c:\wamp64\www\syrios
composer dump-autoload -o

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

--------------------------------------------------------------------------
cd c:\wamp64\www\syrios
testes rodados com sucesso:
	set TEST_PREFIX=master. && php artisan test --filter=MasterFullProtectionBehaviorTest
	set TEST_PREFIX=master. && php artisan test --filter=MasterSecurityBehaviorTest

agora posso rodar assim, para executar todos os testes de uma vez só:
	php artisan test

outros comandos
	composer dump-autoload
-------------------------------------------------------------------------










“Usuário está desenvolvendo o sistema Laravel Syrios, com prefixo de tabelas dinâmico (syrios_), migrations, factories e seeders completos, e está atualmente refinando o FullDevSeeder com lógica de relacionamentos automáticos entre escolas, usuários e papéis.”

“Vamos continuar o projeto Laravel Syrios. Estamos na parte do FullDevSeeder, já com prefixo dinâmico e relacionamentos entre escolas, usuários e roles.”

“Continuar o projeto Syrios a partir do FullDevSeeder funcional.”

você está desenvolvendo o sistema Laravel Syrios com prefixo dinâmico e está na fase de seeders complexos (FullDevSeeder, relacionamentos automáticos, multi-roles)?

“Continuar o projeto Syrios”




