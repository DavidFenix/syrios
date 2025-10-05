-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 05/10/2025 às 17:01
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

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
-- Estrutura para tabela `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_aluno`
--

DROP TABLE IF EXISTS `syrios_aluno`;
CREATE TABLE IF NOT EXISTS `syrios_aluno` (
  `id` int NOT NULL AUTO_INCREMENT,
  `matricula` varchar(10) NOT NULL,
  `school_id` int NOT NULL,
  `nome_a` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_aluno_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_aluno`
--

INSERT INTO `syrios_aluno` (`id`, `matricula`, `school_id`, `nome_a`) VALUES
(8, '12345', 27, 'aluno 1 da escola ubiratan'),
(9, '1234', 27, 'aluno 2 da escola ubiratan'),
(10, '1234', 34, 'Aluno 1 FMota'),
(11, '2345', 34, 'Aluno 2 FMota');

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_diretor_turma`
--

DROP TABLE IF EXISTS `syrios_diretor_turma`;
CREATE TABLE IF NOT EXISTS `syrios_diretor_turma` (
  `id` int NOT NULL AUTO_INCREMENT,
  `professor_id` int NOT NULL,
  `turma_id` int NOT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_diretor_turma` (`professor_id`,`turma_id`),
  KEY `fk_dt_turma` (`turma_id`),
  KEY `fk_dt_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_disciplina`
--

DROP TABLE IF EXISTS `syrios_disciplina`;
CREATE TABLE IF NOT EXISTS `syrios_disciplina` (
  `id` int NOT NULL AUTO_INCREMENT,
  `abr` varchar(10) NOT NULL,
  `descr_d` varchar(100) NOT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_disciplina_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_disciplina`
--

INSERT INTO `syrios_disciplina` (`id`, `abr`, `descr_d`, `school_id`) VALUES
(7, 'disc1', 'disciplina 1 da escola ubiratan', 27),
(8, 'disc2', 'disciplina 2 da escola ubiratan', 27),
(10, 'dis1', 'Disciplina 1 FMota', 34),
(11, 'disc2', 'Discipina 2 FMota', 34);

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_enturmacao`
--

DROP TABLE IF EXISTS `syrios_enturmacao`;
CREATE TABLE IF NOT EXISTS `syrios_enturmacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `aluno_id` int NOT NULL,
  `turma_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_enturmacao_aluno` (`aluno_id`),
  KEY `fk_enturmacao_turma` (`turma_id`),
  KEY `fk_enturmacao_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_escola`
--

DROP TABLE IF EXISTS `syrios_escola`;
CREATE TABLE IF NOT EXISTS `syrios_escola` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inep` varchar(20) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `nome_e` varchar(150) NOT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `endereco` varchar(255) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `secretaria_id` int DEFAULT NULL,
  `is_master` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `inep` (`inep`),
  UNIQUE KEY `cnpj` (`cnpj`),
  KEY `fk_escola_secretaria` (`secretaria_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_escola`
--

INSERT INTO `syrios_escola` (`id`, `inep`, `cnpj`, `nome_e`, `cidade`, `estado`, `endereco`, `telefone`, `criado_em`, `secretaria_id`, `is_master`) VALUES
(1, 'asdf', NULL, 'Secretaria do Administrador Master', NULL, NULL, NULL, NULL, '2025-09-21 17:12:49', NULL, 1),
(26, NULL, NULL, 'Secretaria CREDE 08', NULL, NULL, NULL, NULL, '2025-09-21 17:19:41', NULL, 0),
(27, '23054409', '04.004.880/0001-25', 'Escola Ubiratan', NULL, NULL, 'Rua José Saraiva Sobrinho', NULL, '2025-09-21 17:23:09', 26, 0),
(30, NULL, NULL, 'Secretaria SME Capistrano', 'Capistrano', NULL, NULL, NULL, '2025-09-27 12:44:50', NULL, 0),
(34, NULL, NULL, 'Escola Fernando Mota', 'Capistrano', NULL, NULL, NULL, '2025-10-04 19:55:08', 30, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_notificacao`
--

DROP TABLE IF EXISTS `syrios_notificacao`;
CREATE TABLE IF NOT EXISTS `syrios_notificacao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `reg_id` varchar(200) NOT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_notificacao_usuario` (`usuario_id`),
  KEY `fk_notificacao_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_ocorrencia`
--

DROP TABLE IF EXISTS `syrios_ocorrencia`;
CREATE TABLE IF NOT EXISTS `syrios_ocorrencia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `professor_id` int NOT NULL,
  `aluno_id` int NOT NULL,
  `oferta_id` int NOT NULL,
  `registro_id` int DEFAULT NULL,
  `status_id` int NOT NULL DEFAULT '1',
  `data_ocorrencia` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `descricao` text NOT NULL,
  `local` varchar(100) DEFAULT NULL,
  `atitude` varchar(100) DEFAULT NULL,
  `outra_acoes` text,
  `comportamento` varchar(100) DEFAULT NULL,
  `medidas` text,
  `encaminhamento` text,
  `recebido_em` datetime DEFAULT NULL,
  `sync` tinyint(1) NOT NULL DEFAULT '0',
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `atualizado_em` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ocorrencia_registro` (`registro_id`),
  KEY `idx_ocorrencia_prof` (`professor_id`),
  KEY `idx_ocorrencia_aln` (`aluno_id`),
  KEY `idx_ocorrencia_of` (`oferta_id`),
  KEY `idx_ocorrencia_school` (`school_id`),
  KEY `idx_ocorrencia_status` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_oferta`
--

DROP TABLE IF EXISTS `syrios_oferta`;
CREATE TABLE IF NOT EXISTS `syrios_oferta` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `turma_id` int NOT NULL,
  `disciplina_id` int NOT NULL,
  `professor_id` int NOT NULL,
  `status` tinyint NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_oferta_escola` (`school_id`),
  KEY `fk_oferta_turma` (`turma_id`),
  KEY `fk_oferta_disciplina` (`disciplina_id`),
  KEY `fk_oferta_professor` (`professor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_professor`
--

DROP TABLE IF EXISTS `syrios_professor`;
CREATE TABLE IF NOT EXISTS `syrios_professor` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_professor_usuario` (`usuario_id`),
  KEY `fk_professor_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_professor`
--

INSERT INTO `syrios_professor` (`id`, `usuario_id`, `school_id`) VALUES
(21, 58, 30),
(32, 52, 27);

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_registros`
--

DROP TABLE IF EXISTS `syrios_registros`;
CREATE TABLE IF NOT EXISTS `syrios_registros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `descr_r` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_registros_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_regstatus`
--

DROP TABLE IF EXISTS `syrios_regstatus`;
CREATE TABLE IF NOT EXISTS `syrios_regstatus` (
  `id` int NOT NULL,
  `descr_s` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_regstatus`
--

INSERT INTO `syrios_regstatus` (`id`, `descr_s`) VALUES
(1, 'Aberta'),
(2, 'Em análise'),
(3, 'Concluída');

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_role`
--

DROP TABLE IF EXISTS `syrios_role`;
CREATE TABLE IF NOT EXISTS `syrios_role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`role_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_role`
--

INSERT INTO `syrios_role` (`id`, `role_name`) VALUES
(1, 'admin'),
(7, 'escola'),
(3, 'gestor'),
(5, 'master'),
(4, 'pais'),
(2, 'professor'),
(6, 'secretaria');

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_sessao`
--

DROP TABLE IF EXISTS `syrios_sessao`;
CREATE TABLE IF NOT EXISTS `syrios_sessao` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `school_id` int NOT NULL,
  `criado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sessao_usuario` (`usuario_id`),
  KEY `fk_sessao_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_turma`
--

DROP TABLE IF EXISTS `syrios_turma`;
CREATE TABLE IF NOT EXISTS `syrios_turma` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `serie_turma` varchar(20) NOT NULL,
  `turno` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_turma_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_turma`
--

INSERT INTO `syrios_turma` (`id`, `school_id`, `serie_turma`, `turno`) VALUES
(4, 27, 'turma 1 ubira', 'integtal'),
(5, 27, 'turma 2 ubira', 'integral'),
(6, 34, 'Turma 1 Fmota', 'integral'),
(7, 34, 'Turma 2 FMota', 'integral');

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_usuario`
--

DROP TABLE IF EXISTS `syrios_usuario`;
CREATE TABLE IF NOT EXISTS `syrios_usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `school_id` int NOT NULL,
  `cpf` varchar(11) NOT NULL,
  `senha_hash` varchar(255) NOT NULL,
  `nome_u` varchar(100) NOT NULL,
  `status` tinyint NOT NULL,
  `is_super_master` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_usuario_cpf_escola` (`cpf`,`school_id`),
  KEY `fk_usuario_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_usuario`
--

INSERT INTO `syrios_usuario` (`id`, `school_id`, `cpf`, `senha_hash`, `nome_u`, `status`, `is_super_master`) VALUES
(37, 1, 'master', '$2y$10$83QosmliQInrfJM5UsKumOrLZHe.Ef8yGgZaPx2YEHUjw2Bke6KzK', 'Usuario Master', 1, 1),
(52, 27, 'prof1', '$2y$10$rIEq7n11CzgMJR7KGgqy1eK4qTch15m63hzuEpwRAvc.2ClzxofCG', 'professor 1 da ubiratan', 1, 0),
(54, 27, 'prof2', '$2y$10$B38Nx3DaVpYUJhlOunczmenryGpK9jUlmPXVGMwD6uXxgS/9Ab71C', 'professor 2 da ubiratan', 1, 0),
(58, 30, 'prof4', '$2y$10$GE.kYFDR0Z99xUolCNwG2eYoIcLsH7eHyCRfLFkHTcFHJwNDLW2k.', 'Professor 4 orinal do SME capistrano', 1, 0),
(63, 1, 'master1', '$2y$10$8l6KfQrU3zyh7vy9yXrTTuYvBffRxqaMwQTYVnbPTzoei/4kCWF5S', 'Usuário (outro Master)', 1, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_usuario_role`
--

DROP TABLE IF EXISTS `syrios_usuario_role`;
CREATE TABLE IF NOT EXISTS `syrios_usuario_role` (
  `usuario_id` int NOT NULL,
  `role_id` int NOT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`usuario_id`,`role_id`,`school_id`),
  KEY `fk_usr_role_role` (`role_id`),
  KEY `fk_usr_role_escola` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `syrios_usuario_role`
--

INSERT INTO `syrios_usuario_role` (`usuario_id`, `role_id`, `school_id`) VALUES
(52, 2, 27),
(37, 5, 1),
(63, 5, 1),
(58, 7, 27);

-- --------------------------------------------------------

--
-- Estrutura para tabela `syrios_visao_aluno`
--

DROP TABLE IF EXISTS `syrios_visao_aluno`;
CREATE TABLE IF NOT EXISTS `syrios_visao_aluno` (
  `id` int NOT NULL AUTO_INCREMENT,
  `aluno_id` int NOT NULL,
  `dat_ult_visao` datetime DEFAULT NULL,
  `school_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_visao_aluno` (`aluno_id`),
  KEY `fk_visao_escola` (`school_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `syrios_aluno`
--
ALTER TABLE `syrios_aluno`
  ADD CONSTRAINT `fk_aluno_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_diretor_turma`
--
ALTER TABLE `syrios_diretor_turma`
  ADD CONSTRAINT `fk_dt_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_dt_professor` FOREIGN KEY (`professor_id`) REFERENCES `syrios_professor` (`id`),
  ADD CONSTRAINT `fk_dt_turma` FOREIGN KEY (`turma_id`) REFERENCES `syrios_turma` (`id`);

--
-- Restrições para tabelas `syrios_disciplina`
--
ALTER TABLE `syrios_disciplina`
  ADD CONSTRAINT `fk_disciplina_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_enturmacao`
--
ALTER TABLE `syrios_enturmacao`
  ADD CONSTRAINT `fk_enturmacao_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `syrios_aluno` (`id`),
  ADD CONSTRAINT `fk_enturmacao_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_enturmacao_turma` FOREIGN KEY (`turma_id`) REFERENCES `syrios_turma` (`id`);

--
-- Restrições para tabelas `syrios_escola`
--
ALTER TABLE `syrios_escola`
  ADD CONSTRAINT `fk_escola_secretaria` FOREIGN KEY (`secretaria_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_notificacao`
--
ALTER TABLE `syrios_notificacao`
  ADD CONSTRAINT `fk_notificacao_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_notificacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `syrios_usuario` (`id`);

--
-- Restrições para tabelas `syrios_ocorrencia`
--
ALTER TABLE `syrios_ocorrencia`
  ADD CONSTRAINT `fk_ocorrencia_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `syrios_aluno` (`id`),
  ADD CONSTRAINT `fk_ocorrencia_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_ocorrencia_oferta` FOREIGN KEY (`oferta_id`) REFERENCES `syrios_oferta` (`id`),
  ADD CONSTRAINT `fk_ocorrencia_professor` FOREIGN KEY (`professor_id`) REFERENCES `syrios_professor` (`id`),
  ADD CONSTRAINT `fk_ocorrencia_registro` FOREIGN KEY (`registro_id`) REFERENCES `syrios_registros` (`id`),
  ADD CONSTRAINT `fk_ocorrencia_status` FOREIGN KEY (`status_id`) REFERENCES `syrios_regstatus` (`id`);

--
-- Restrições para tabelas `syrios_oferta`
--
ALTER TABLE `syrios_oferta`
  ADD CONSTRAINT `fk_oferta_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `syrios_disciplina` (`id`),
  ADD CONSTRAINT `fk_oferta_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_oferta_professor` FOREIGN KEY (`professor_id`) REFERENCES `syrios_professor` (`id`),
  ADD CONSTRAINT `fk_oferta_turma` FOREIGN KEY (`turma_id`) REFERENCES `syrios_turma` (`id`);

--
-- Restrições para tabelas `syrios_professor`
--
ALTER TABLE `syrios_professor`
  ADD CONSTRAINT `fk_professor_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_professor_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `syrios_usuario` (`id`);

--
-- Restrições para tabelas `syrios_registros`
--
ALTER TABLE `syrios_registros`
  ADD CONSTRAINT `fk_registros_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_sessao`
--
ALTER TABLE `syrios_sessao`
  ADD CONSTRAINT `fk_sessao_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_sessao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `syrios_usuario` (`id`);

--
-- Restrições para tabelas `syrios_turma`
--
ALTER TABLE `syrios_turma`
  ADD CONSTRAINT `fk_turma_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_usuario`
--
ALTER TABLE `syrios_usuario`
  ADD CONSTRAINT `fk_usuario_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);

--
-- Restrições para tabelas `syrios_usuario_role`
--
ALTER TABLE `syrios_usuario_role`
  ADD CONSTRAINT `fk_usr_role_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`),
  ADD CONSTRAINT `fk_usr_role_role` FOREIGN KEY (`role_id`) REFERENCES `syrios_role` (`id`),
  ADD CONSTRAINT `fk_usr_role_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `syrios_usuario` (`id`);

--
-- Restrições para tabelas `syrios_visao_aluno`
--
ALTER TABLE `syrios_visao_aluno`
  ADD CONSTRAINT `fk_visao_aluno` FOREIGN KEY (`aluno_id`) REFERENCES `syrios_aluno` (`id`),
  ADD CONSTRAINT `fk_visao_escola` FOREIGN KEY (`school_id`) REFERENCES `syrios_escola` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
