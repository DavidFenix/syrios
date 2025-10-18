--escola ubiratan
---------------------------------------------------------
INSERT IGNORE INTO `syrios_usuario` (`school_id`, `cpf`, `senha_hash`, `nome_u`, `status`, `created_at`, `updated_at`)
SELECT 
  1 AS `school_id`,
  LPAD(p.`id_user_p`, 11, '0') AS `cpf`,
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' AS `senha_hash`, -- senha padrão: "password"
  p.`nome_p` AS `nome_u`,
  1 AS `status`,
  NOW(), NOW()
FROM `323966`.`professor` p;

--escola fcm
------------------------------------------------------
INSERT IGNORE INTO `syrios_usuario` (`school_id`, `cpf`, `senha_hash`, `nome_u`, `status`, `created_at`, `updated_at`)
SELECT 
  6 AS `school_id`,
  LPAD(p.`id_user_p`, 11, '0') AS `cpf`,
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' AS `senha_hash`, -- senha padrão: "password"
  p.`nome_p` AS `nome_u`,
  1 AS `status`,
  NOW(), NOW()
FROM `323966fcm`.`professor` p;

--escola ubiratan
---------------------------------------------------------------------
INSERT IGNORE INTO `syrios_aluno` (`matricula`, `nome_a`, `school_id`, `created_at`, `updated_at`)
SELECT 
  a.`id_user_a`       AS `matricula`,
  a.`nome_a`          AS `nome_a`,
  1                   AS `school_id`,  -- todas para escola 1 por padrão
  NOW()               AS `created_at`,
  NOW()               AS `updated_at`
FROM `323966`.`aluno` a;

--escola fcm
--------------------------------------------------------------------
INSERT IGNORE INTO `syrios_aluno` (`matricula`, `nome_a`, `school_id`, `created_at`, `updated_at`)
SELECT 
  a.`id_user_a`       AS `matricula`,
  a.`nome_a`          AS `nome_a`,
  6                   AS `school_id`,  -- todas para escola 1 por padrão
  NOW()               AS `created_at`,
  NOW()               AS `updated_at`
FROM `323966fcm`.`aluno` a;

--escola ubiratan
----------------------------------------------------------------
INSERT IGNORE INTO `syrios_professor` (`usuario_id`, `school_id`, `created_at`, `updated_at`)
SELECT 
  su.`id` AS `usuario_id`,
  1       AS `school_id`,
  NOW(),
  NOW()
FROM `syrios_usuario` su
INNER JOIN `323966`.`professor` p 
  ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0');

--escola fcm
----------------------------------------------------------------
INSERT IGNORE INTO `syrios_professor` (`usuario_id`, `school_id`, `created_at`, `updated_at`)
SELECT 
  su.`id` AS `usuario_id`,
  6       AS `school_id`,
  NOW(),
  NOW()
FROM `syrios_usuario` su
INNER JOIN `323966fcm`.`professor` p 
  ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0');

--escola ubiratan
-----------------------------------------------------------------
INSERT IGNORE INTO `syrios_usuario_role` (`usuario_id`, `role_id`, `school_id`, `created_at`, `updated_at`)
SELECT 
  su.`id`,
  r.`id` AS `role_id`,
  1 AS `school_id`,
  NOW(), NOW()
FROM `syrios_usuario` su
INNER JOIN `323966`.`professor` p ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0')
INNER JOIN `syrios_role` r ON r.`role_name` = 'professor';

--escola fcm
-----------------------------------------------------------------
INSERT IGNORE INTO `syrios_usuario_role` (`usuario_id`, `role_id`, `school_id`, `created_at`, `updated_at`)
SELECT 
  su.`id`,
  r.`id` AS `role_id`,
  6 AS `school_id`,
  NOW(), NOW()
FROM `syrios_usuario` su
INNER JOIN `323966fcm`.`professor` p ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0')
INNER JOIN `syrios_role` r ON r.`role_name` = 'professor';

--escola ubiratan
---------------------------------------------------------------
INSERT IGNORE INTO `syrios_disciplina` (`abr`, `descr_d`, `school_id`, `created_at`, `updated_at`)
SELECT 
  d.`abr`,
  d.`descr_d`,
  1 AS `school_id`,   -- todas atribuídas à escola padrão
  NOW() AS `created_at`,
  NOW() AS `updated_at`
FROM `323966`.`disciplina` d;

--escola fcm
---------------------------------------------------------------
INSERT IGNORE INTO `syrios_disciplina` (`abr`, `descr_d`, `school_id`, `created_at`, `updated_at`)
SELECT 
  d.`abr`,
  d.`descr_d`,
  6 AS `school_id`,   -- todas atribuídas à escola padrão
  NOW() AS `created_at`,
  NOW() AS `updated_at`
FROM `323966fcm`.`disciplina` d;

--escola ubiratan
-----------------------------------------------------------------------
INSERT IGNORE INTO `syrios_turma` 
(`school_id`, `serie_turma`, `turno`, `created_at`, `updated_at`)
SELECT
  1 AS `school_id`,
  t.`oferta_t` AS `serie_turma`,
  t.`descr_t` AS `turno`,
  NOW(),
  NOW()
FROM `323966`.`turno` t;

--escola fcm
-----------------------------------------------------------------------
INSERT IGNORE INTO `syrios_turma` 
(`school_id`, `serie_turma`, `turno`, `created_at`, `updated_at`)
SELECT
  6 AS `school_id`,
  t.`oferta_t` AS `serie_turma`,
  t.`descr_t` AS `turno`,
  NOW(),
  NOW()
FROM `323966fcm`.`turno` t;

------inicio acho que não é esse
INSERT IGNORE INTO `syrios_oferta`
(`school_id`, `turma_id`, `disciplina_id`, `professor_id`, `status`, `created_at`, `updated_at`)
SELECT 
  1 AS `school_id`,
  st.`id` AS `turma_id`,
  sd.`id` AS `disciplina_id`,
  sp.`id` AS `professor_id`,
  o.`status`,
  NOW(),
  NOW()
FROM `323966`.`oferta` o
INNER JOIN `323966`.`turno` t 
  ON o.`oferta` = t.`oferta_t`                 -- mapeia a turma textual
INNER JOIN `syrios_turma` st 
  ON st.`serie_turma` = t.`oferta_t`
INNER JOIN syrios_disciplina sd ON sd.descr_d = (
  SELECT descr_d FROM 323966.disciplina WHERE id = o.id_disc LIMIT 1
)
INNER JOIN `323966`.`professor` p 
  ON p.`id_user_p` = o.`id_user_p`
INNER JOIN `syrios_usuario` su 
  ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0')
INNER JOIN `syrios_professor` sp 
  ON sp.`usuario_id` = su.`id`;
------fim acho que não é esse

--escola ubiratan
-----------------------------------------------------------
INSERT IGNORE INTO `syrios_oferta`
(`school_id`, `turma_id`, `disciplina_id`, `professor_id`, `status`, `created_at`, `updated_at`)
SELECT 
  1 AS `school_id`,
  st.`id` AS `turma_id`,
  sd.`id` AS `disciplina_id`,
  sp.`id` AS `professor_id`,
  o.`status`,
  NOW(),
  NOW()
FROM `323966`.`oferta` o
INNER JOIN `323966`.`turno` t 
  ON CONVERT(o.`oferta` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4)
INNER JOIN `syrios_turma` st 
  ON CONVERT(st.`serie_turma` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4)
INNER JOIN `syrios_disciplina` sd 
  ON CONVERT(sd.`descr_d` USING utf8mb4) = (
       SELECT CONVERT(d.`descr_d` USING utf8mb4)
       FROM `323966`.`disciplina` d
       WHERE d.`id` = o.`id_disc`
       LIMIT 1
     )
INNER JOIN `323966`.`professor` p 
  ON p.`id_user_p` = o.`id_user_p`
INNER JOIN `syrios_usuario` su 
  ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0')
INNER JOIN `syrios_professor` sp 
  ON sp.`usuario_id` = su.`id`;

--escola fcm
-----------------------------------------------------------
INSERT IGNORE INTO `syrios_oferta`
(`school_id`, `turma_id`, `disciplina_id`, `professor_id`, `status`, `created_at`, `updated_at`)
SELECT 
  6 AS `school_id`,
  st.`id` AS `turma_id`,
  sd.`id` AS `disciplina_id`,
  sp.`id` AS `professor_id`,
  o.`status`,
  NOW(),
  NOW()
FROM `323966fcm`.`oferta` o
INNER JOIN `323966fcm`.`turno` t 
  ON CONVERT(o.`oferta` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4)
INNER JOIN `syrios_turma` st 
  ON CONVERT(st.`serie_turma` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4)
INNER JOIN `syrios_disciplina` sd 
  ON CONVERT(sd.`descr_d` USING utf8mb4) = (
       SELECT CONVERT(d.`descr_d` USING utf8mb4)
       FROM `323966fcm`.`disciplina` d
       WHERE d.`id` = o.`id_disc`
       LIMIT 1
     )
INNER JOIN `323966fcm`.`professor` p 
  ON p.`id_user_p` = o.`id_user_p`
INNER JOIN `syrios_usuario` su 
  ON su.`cpf` = LPAD(p.`id_user_p`, 11, '0')
INNER JOIN `syrios_professor` sp 
  ON sp.`usuario_id` = su.`id`;

--escola ubiratan
-------------------------------------------------------------
INSERT IGNORE INTO `syrios_enturmacao`
(`school_id`, `aluno_id`, `turma_id`, `created_at`, `updated_at`)
SELECT
  1 AS `school_id`,
  sa.`id` AS `aluno_id`,
  st.`id` AS `turma_id`,
  NOW(),
  NOW()
FROM `323966`.`enturmacao` e
INNER JOIN `323966`.`aluno` a 
  ON CONVERT(a.`id_user_a` USING utf8mb4) = CONVERT(e.`id_user_a` USING utf8mb4)
INNER JOIN `syrios_aluno` sa 
  ON CONVERT(sa.`matricula` USING utf8mb4) = CONVERT(a.`id_user_a` USING utf8mb4)
INNER JOIN `323966`.`turno` t 
  ON CONVERT(t.`oferta_t` USING utf8mb4) = CONVERT(e.`oferta` USING utf8mb4)
INNER JOIN `syrios_turma` st 
  ON CONVERT(st.`serie_turma` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4);


--escola fcm
-------------------------------------------------------------
INSERT IGNORE INTO `syrios_enturmacao`
(`school_id`, `aluno_id`, `turma_id`, `created_at`, `updated_at`)
SELECT
  6 AS `school_id`,
  sa.`id` AS `aluno_id`,
  st.`id` AS `turma_id`,
  NOW(),
  NOW()
FROM `323966fcm`.`enturmacao` e
INNER JOIN `323966fcm`.`aluno` a 
  ON CONVERT(a.`id_user_a` USING utf8mb4) = CONVERT(e.`id_user_a` USING utf8mb4)
INNER JOIN `syrios_aluno` sa 
  ON CONVERT(sa.`matricula` USING utf8mb4) = CONVERT(a.`id_user_a` USING utf8mb4)
INNER JOIN `323966fcm`.`turno` t 
  ON CONVERT(t.`oferta_t` USING utf8mb4) = CONVERT(e.`oferta` USING utf8mb4)
INNER JOIN `syrios_turma` st 
  ON CONVERT(st.`serie_turma` USING utf8mb4) = CONVERT(t.`oferta_t` USING utf8mb4);



  exemplo de consulta para ver resultados nos dois bancos
  SELECT 
    u.nome_u AS professor,
    d.descr_d AS disciplina,
    t.serie_turma AS turma,
    o.status
FROM syrios_oferta o
INNER JOIN syrios_professor p 
    ON p.id = o.professor_id
INNER JOIN syrios_usuario u 
    ON u.id = p.usuario_id
INNER JOIN syrios_disciplina d 
    ON d.id = o.disciplina_id
INNER JOIN syrios_turma t 
    ON t.id = o.turma_id
WHERE u.cpf LIKE '%000000dimas%'
ORDER BY d.descr_d ASC;


SELECT 
    p.nome_p AS professor,
    d.descr_d AS disciplina,
    o.oferta AS turma_textual,
    o.status
FROM `323966`.`oferta` o
INNER JOIN `323966`.`professor` p 
    ON p.id_user_p = o.id_user_p
INNER JOIN `323966`.`disciplina` d 
    ON d.id = o.id_disc
WHERE p.nome_p LIKE '%Dimas%'
ORDER BY d.descr_d ASC;

--escola ubiratan
-----------------------------------------------------------
INSERT INTO `syrios`.syrios_diretor_turma (
    professor_id,
    turma_id,
    school_id,
    ano_letivo,
    vigente,
    created_at,
    updated_at
)
SELECT 
    p.id AS professor_id,
    t.id AS turma_id,
    p.school_id,
    2025 AS ano_letivo,
    1 AS vigente,
    NOW(),
    NOW()
FROM `323966`.dturma AS dt
JOIN `syrios`.syrios_usuario AS u
    ON u.cpf = LPAD(dt.id_user_p, 11, '0')
JOIN `syrios`.syrios_professor AS p
    ON p.usuario_id = u.id AND p.school_id = u.school_id
JOIN `syrios`.syrios_turma AS t
    ON t.serie_turma = dt.oferta
LEFT JOIN `syrios`.syrios_diretor_turma AS d
    ON d.professor_id = p.id
   AND d.turma_id = t.id
   AND d.school_id = p.school_id
WHERE d.id IS NULL;

--escola fcm
-----------------------------------------------------------
INSERT INTO `syrios`.syrios_diretor_turma (
    professor_id,
    turma_id,
    school_id,
    ano_letivo,
    vigente,
    created_at,
    updated_at
)
SELECT 
    p.id AS professor_id,
    t.id AS turma_id,
    p.school_id,
    2025 AS ano_letivo,
    1 AS vigente,
    NOW(),
    NOW()
FROM `323966fcm`.dturma AS dt
JOIN `syrios`.syrios_usuario AS u
    ON u.cpf = LPAD(dt.id_user_p, 11, '0')
JOIN `syrios`.syrios_professor AS p
    ON p.usuario_id = u.id AND p.school_id = u.school_id
JOIN `syrios`.syrios_turma AS t
    ON t.serie_turma = dt.oferta
LEFT JOIN `syrios`.syrios_diretor_turma AS d
    ON d.professor_id = p.id
   AND d.turma_id = t.id
   AND d.school_id = p.school_id
WHERE d.id IS NULL;
