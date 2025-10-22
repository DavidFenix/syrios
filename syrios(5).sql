-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 22-Out-2025 às 23:27
-- Versão do servidor: 10.6.11-MariaDB
-- versão do PHP: 8.1.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `syrios`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_aluno`
--

DROP TABLE IF EXISTS `syrios_aluno`;
CREATE TABLE IF NOT EXISTS `syrios_aluno` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `matricula` varchar(10) NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `nome_a` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_aluno_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_diretor_turma`
--

DROP TABLE IF EXISTS `syrios_diretor_turma`;
CREATE TABLE IF NOT EXISTS `syrios_diretor_turma` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `turma_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `ano_letivo` int(11) NOT NULL DEFAULT 2025,
  `vigente` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diretor_turma` (`professor_id`,`turma_id`),
  KEY `syrios_diretor_turma_turma_id_foreign` (`turma_id`),
  KEY `syrios_diretor_turma_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_disciplina`
--

DROP TABLE IF EXISTS `syrios_disciplina`;
CREATE TABLE IF NOT EXISTS `syrios_disciplina` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `abr` varchar(10) NOT NULL,
  `descr_d` varchar(100) NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_disciplina_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_enturmacao`
--

DROP TABLE IF EXISTS `syrios_enturmacao`;
CREATE TABLE IF NOT EXISTS `syrios_enturmacao` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `ano_letivo` int(11) NOT NULL DEFAULT 2025,
  `vigente` tinyint(1) NOT NULL DEFAULT 1,
  `aluno_id` bigint(20) UNSIGNED NOT NULL,
  `turma_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_enturmacao_aluno_id_foreign` (`aluno_id`),
  KEY `syrios_enturmacao_turma_id_foreign` (`turma_id`),
  KEY `syrios_enturmacao_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_escola`
--

DROP TABLE IF EXISTS `syrios_escola`;
CREATE TABLE IF NOT EXISTS `syrios_escola` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `inep` varchar(20) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `nome_e` varchar(150) NOT NULL,
  `frase_efeito` varchar(255) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `secretaria_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_master` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `syrios_escola_inep_unique` (`inep`),
  UNIQUE KEY `syrios_escola_cnpj_unique` (`cnpj`),
  KEY `syrios_escola_secretaria_id_foreign` (`secretaria_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_modelo_motivo`
--

DROP TABLE IF EXISTS `syrios_modelo_motivo`;
CREATE TABLE IF NOT EXISTS `syrios_modelo_motivo` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_notificacao`
--

DROP TABLE IF EXISTS `syrios_notificacao`;
CREATE TABLE IF NOT EXISTS `syrios_notificacao` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `reg_id` varchar(200) NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_notificacao_usuario_id_foreign` (`usuario_id`),
  KEY `syrios_notificacao_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_ocorrencia`
--

DROP TABLE IF EXISTS `syrios_ocorrencia`;
CREATE TABLE IF NOT EXISTS `syrios_ocorrencia` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `ano_letivo` int(11) NOT NULL DEFAULT 2025,
  `vigente` tinyint(1) NOT NULL DEFAULT 1,
  `aluno_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `oferta_id` bigint(20) UNSIGNED DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `local` varchar(100) DEFAULT NULL,
  `atitude` varchar(100) DEFAULT NULL,
  `outra_atitude` varchar(150) DEFAULT NULL,
  `comportamento` varchar(100) DEFAULT NULL,
  `sugestao` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `nivel_gravidade` tinyint(4) NOT NULL DEFAULT 1,
  `sync` tinyint(4) NOT NULL DEFAULT 1,
  `recebido_em` timestamp NULL DEFAULT NULL,
  `encaminhamentos` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_ocorrencia_school_id_aluno_id_index` (`school_id`,`aluno_id`),
  KEY `syrios_ocorrencia_professor_id_oferta_id_index` (`professor_id`,`oferta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_ocorrencia_motivo`
--

DROP TABLE IF EXISTS `syrios_ocorrencia_motivo`;
CREATE TABLE IF NOT EXISTS `syrios_ocorrencia_motivo` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ocorrencia_id` bigint(20) UNSIGNED NOT NULL,
  `modelo_motivo_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_ocorrencia_motivo_ocorrencia_id_foreign` (`ocorrencia_id`),
  KEY `syrios_ocorrencia_motivo_modelo_motivo_id_foreign` (`modelo_motivo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_oferta`
--

DROP TABLE IF EXISTS `syrios_oferta`;
CREATE TABLE IF NOT EXISTS `syrios_oferta` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `ano_letivo` int(11) NOT NULL DEFAULT 2025,
  `vigente` tinyint(1) NOT NULL DEFAULT 1,
  `turma_id` bigint(20) UNSIGNED NOT NULL,
  `disciplina_id` bigint(20) UNSIGNED NOT NULL,
  `professor_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_oferta_school_id_foreign` (`school_id`),
  KEY `syrios_oferta_turma_id_foreign` (`turma_id`),
  KEY `syrios_oferta_disciplina_id_foreign` (`disciplina_id`),
  KEY `syrios_oferta_professor_id_foreign` (`professor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_professor`
--

DROP TABLE IF EXISTS `syrios_professor`;
CREATE TABLE IF NOT EXISTS `syrios_professor` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_professor_usuario_id_foreign` (`usuario_id`),
  KEY `syrios_professor_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_regimento`
--

DROP TABLE IF EXISTS `syrios_regimento`;
CREATE TABLE IF NOT EXISTS `syrios_regimento` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(191) NOT NULL DEFAULT 'Regimento Escolar',
  `arquivo` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_regimento_school_id_index` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_regstatus`
--

DROP TABLE IF EXISTS `syrios_regstatus`;
CREATE TABLE IF NOT EXISTS `syrios_regstatus` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `descr_s` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_role`
--

DROP TABLE IF EXISTS `syrios_role`;
CREATE TABLE IF NOT EXISTS `syrios_role` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `syrios_role_role_name_unique` (`role_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_sessao`
--

DROP TABLE IF EXISTS `syrios_sessao`;
CREATE TABLE IF NOT EXISTS `syrios_sessao` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_sessao_usuario_id_foreign` (`usuario_id`),
  KEY `syrios_sessao_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_turma`
--

DROP TABLE IF EXISTS `syrios_turma`;
CREATE TABLE IF NOT EXISTS `syrios_turma` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `serie_turma` varchar(20) NOT NULL,
  `turno` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_turma_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_usuario`
--

DROP TABLE IF EXISTS `syrios_usuario`;
CREATE TABLE IF NOT EXISTS `syrios_usuario` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nome_u` varchar(100) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `is_super_master` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_cpf_escola` (`cpf`,`school_id`),
  KEY `syrios_usuario_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_usuario_role`
--

DROP TABLE IF EXISTS `syrios_usuario_role`;
CREATE TABLE IF NOT EXISTS `syrios_usuario_role` (
  `usuario_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`usuario_id`,`role_id`,`school_id`),
  KEY `syrios_usuario_role_role_id_foreign` (`role_id`),
  KEY `syrios_usuario_role_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `syrios_visao_aluno`
--

DROP TABLE IF EXISTS `syrios_visao_aluno`;
CREATE TABLE IF NOT EXISTS `syrios_visao_aluno` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `aluno_id` bigint(20) UNSIGNED NOT NULL,
  `dat_ult_visao` datetime DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `syrios_visao_aluno_aluno_id_foreign` (`aluno_id`),
  KEY `syrios_visao_aluno_school_id_foreign` (`school_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
