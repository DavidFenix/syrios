---------------------------------------------------------
nova mudança na base de dados
--não permitir mesmo aluno, em turmas diferentes, na mesma escola(pode mudar no futuro)
--verificar se há duplicidade antes
SELECT aluno_id, turma_id, school_id, COUNT(*) AS qtd
FROM syrios_enturmacao
GROUP BY aluno_id, turma_id, school_id
HAVING COUNT(*) > 1;
--removar a constraint com esse nome, caso já existe, aplicar a unicidade decidida
ALTER TABLE syrios_enturmacao
DROP INDEX uq_enturmacao_unica,
ADD CONSTRAINT uq_enturmacao_unica
UNIQUE (aluno_id, turma_id, school_id);


--cd c:\wamp64\www\syrios
--php artisan migrate --path=database/migrations/2025_10_26_00001_update_unique_constraint_on_enturmacao_table.php

--mais uma restrição importantes por enturmacao_aluno_ano_escola
--php artisan migrate --path=database/migrations/2025_10_26_184125_add_unique_constraint_aluno_ano_escola_to_enturmacao_table.php


-----------------------------------------------------------
novos modelos de motivos para o DB

  TRUNCATE TABLE syrios_modelo_motivo;
  INSERT INTO syrios_modelo_motivo (id, school_id, descricao, categoria, created_at, updated_at) VALUES
  (1, 1, 'Conversas paralelas durante a explicação do conteúdo', 'Comportamento', NOW(), NOW()),
  (2, 1, 'Brincadeiras durante a explicação do conteúdo', 'Comportamento', NOW(), NOW()),
  (3, 1, 'Falta de respeito com colegas', 'Disciplina', NOW(), NOW()),
  (4, 1, 'Falta de respeito com professores', 'Disciplina', NOW(), NOW()),
  (5, 1, 'Não realizou as atividades propostas', 'Desempenho', NOW(), NOW()),
  (6, 1, 'Saiu da sala sem permissão', 'Disciplina', NOW(), NOW()),
  (7, 1, 'Mau comportamento em sala', 'Comportamento', NOW(), NOW()),
  (8, 1, 'Não cumpriu o tempo do intervalo', 'Pontualidade', NOW(), NOW()),
  (9, 1, 'Atraso frequente', 'Pontualidade', NOW(), NOW()),
  (10, 1, 'Desrespeito ao professor', 'Disciplina', NOW(), NOW()),
  (11, 1, 'Fuga do ambiente escolar', 'Grave', NOW(), NOW()),
  (12, 1, 'Uso indevido de celular em sala', 'Comportamento', NOW(), NOW()),
  (13, 1, 'Não trouxe o material didático', 'Desempenho', NOW(), NOW()),
  (14, 1, 'Indisciplina em sala de aula', 'Comportamento', NOW(), NOW()),
  (15, 1, 'Solicitou sair da sala repetidas vezes (beber água, ir ao banheiro etc.)', 'Comportamento', NOW(), NOW()),
  (16, 1, 'Comportamento agressivo e criação de conflitos com colegas', 'Grave', NOW(), NOW()),
  (17, 1, 'Prática de bullying com colegas', 'Grave', NOW(), NOW()),
  (18, 1, 'Interrompe o professor constantemente', 'Comportamento', NOW(), NOW()),
  (19, 1, 'Recusa-se a realizar as atividades', 'Desempenho', NOW(), NOW()),
  (20, 1, 'Desobediência às regras da sala', 'Disciplina', NOW(), NOW()),
  (21, 1, 'Dificuldade em respeitar a fila ou a ordem de entrada', 'Comportamento', NOW(), NOW()),
  (22, 1, 'Recusa-se a entregar o celular quando solicitado', 'Disciplina', NOW(), NOW()),
  (23, 1, 'Usa linguagem inadequada em sala', 'Disciplina', NOW(), NOW()),
  (24, 1, 'Apresenta desatenção constante durante as aulas', 'Desempenho', NOW(), NOW()),
  (25, 1, 'Alimenta-se em sala sem autorização', 'Comportamento', NOW(), NOW()),
  (26, 1, 'Lança objetos ou perturba o ambiente físico da sala', 'Grave', NOW(), NOW()),
  (27, 1, 'Apresenta descuido com o uniforme escolar', 'Uniforme', NOW(), NOW()),
  (28, 1, 'Comparece sem uniforme completo', 'Uniforme', NOW(), NOW()),
  (29, 1, 'Dificuldade em manter o material organizado', 'Material', NOW(), NOW()),
  (30, 1, 'Não trouxe o caderno ou livro da disciplina', 'Material', NOW(), NOW()),
  (31, 1, 'Falta de interesse nas atividades', 'Desempenho', NOW(), NOW()),
  (32, 1, 'Rude ou irônico com funcionários da escola', 'Disciplina', NOW(), NOW()),
  (33, 1, 'Discussão com colegas durante a aula', 'Comportamento', NOW(), NOW()),
  (34, 1, 'Desatenção constante e conversa durante avaliações', 'Comportamento', NOW(), NOW()),
  (35, 1, 'Interfere negativamente na concentração dos colegas', 'Comportamento', NOW(), NOW()),
  (36, 1, 'Abandono de sala sem justificativa', 'Grave', NOW(), NOW()),
  (37, 1, 'Desacato a funcionário ou servidor da escola', 'Grave', NOW(), NOW()),
  (38, 1, 'Danificou material escolar ou patrimônio público', 'Grave', NOW(), NOW()),
  (39, 1, 'Desobedeceu orientações de segurança escolar', 'Grave', NOW(), NOW()),
  (40, 1, 'Tentativa de evasão ou ausência prolongada sem justificativa', 'Grave', NOW(), NOW());

--cd c:\wamp64\www\syrios
--php artisan db:seed --class=ModeloMotivoSeeder

--------------------------------------------------------------
novas mudanças de myisam para innodb para ativar de vez a proteção por chave estrangeira
--cd c:\wamp64\www\syrios
--php artisan migrate --path=database/migrations/2025_10_23_000000_convert_to_innodb_and_add_foreign_keys.php
--php artisan migrate --path=database/migrations/2025_10_23_200000_add_unique_constraints_syrios.php

---------------------------------------------
criando regimento da escola
migração-------------------
--cd c:\wamp64\www\syrios
--php artisan migrate --path=database/migrations/2025_10_21_000000_create_regimento_table.php

-----------------------------------------------
mudanças na base de dados
ALTER TABLE syrios_escola
ADD COLUMN frase_efeito VARCHAR(255) NULL AFTER nome_e,
ADD COLUMN logo_path VARCHAR(255) NULL AFTER frase_efeito;

--migração da frase de efeito e do logo_path
  --cd c:\wamp64\www\syrios
  --php artisan migrate --path=database/migrations/2025_10_20_200117_add_frase_efeito_to_escola_table.php

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
--cd c:\wamp64\www\syrios
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




