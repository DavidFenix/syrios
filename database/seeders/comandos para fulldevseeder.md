comandos para fulldevseeder

1) Escola Master
- 1 escola com is_master = 1
- Nome: "Secretaria do Administrador Master"
- Sem escola mãe

2) Usuário Super Master
- 1 usuário com is_super_master = 1
- CPF: "master"
- Vinculado à escola master
- Role: master

3) Outros usuários master
- 2 usuários com role master e is_super_master = 0
- Ambos vinculados à escola master

4) Secretarias
- Criar 3 secretarias (ex: "Secretaria Crede 08", "Secretaria SME Capistrano", "Secretaria SME Aratuba")
- Cada uma tem 1 usuário com role secretaria

5) Escolas regulares
- Criar 15 escolas
- Distribuir entre as secretarias(escola mãe) na proporção:
  - Secretaria Crede 08 → 4 escolas
  - Secretaria SME Capistrano → 5 escolas
  - Secretaria SME Aratuba → 6 escolas
- Criar 10 usuarios com Role escola, um pra cada escola e
- Escolher 5 desses usuarios para repetir, ou seja, serem vinculados nas escolas que ainda não tem usuarios vinculado


6) Turmas
- Criar 4 turmas ("1ª Série A", "1ª Série B", "1ª Série C", "1ª Série D") para cada escola diferente com turno integral ou noturno
- Criar 20 alunos para cada turma de cada escola diferente e fazer a Enturmacao

7) Disciplinas
- Criar 5 disciplinas ("Português", "Matemática", "História", "Geografia", "Ciências") para cada turma de cada escola diferente

7) Professores
- Criar 20 usuario, vincular a qualquer escola(criador) regular e atribuir a Role 'professor' nele caso ele seja escolhido para lecionar alguma disciplina naquela escola

9) Oferta 
- Criar 5 ofertas para cada turma combinando as 5 disciplinas e os 20 professores nas 15 escolas
- cada escola deve ofertar as 5 disciplinas em todas as turmas
- não pode repetir a mesma disciplina na mesma turma da mesma escola
- professores podem lecionar em mais de uma disciplina, em mais de uma turma e em mais de uma escola

10) Roles automáticas
- Preencher a tabela syrios_role caso vazia
  (admin, escola, gestor, master, pais, professor, secretaria)

11) Diretor de turma
- cada turma de cada escola deve ter uma professor diretor de turma, mas ele deve ser professor da turma em alguma outra disciplina desta turma

---

Se você me confirmar **nessa estrutura acima** (ou mandar sua própria versão parecida),
eu posso gerar um **único Seeder chamado `FullDevSeeder`**, que:

* Cria todas as escolas, usuários, secretarias, turmas, professores e alunos automaticamente
* Atribui as relações (escola mãe ↔ filha, usuário ↔ escola ↔ role)
* Usa o prefixo dinâmico do seu config
* É compatível com seu banco (campos, nomes, e tipos)
* Pode ser rodado com um único comando:

  ```bash
  php artisan db:seed --class=FullDevSeeder
  ```

---

💡 **Resumo:**
👉 Me envie o roteiro exatamente nesse formato (ou confirme o exemplo acima),
e eu te devolvo o **Seeder completo**, com tudo configurado e comentado.

Quer que eu use esse modelo mesmo, ou quer ajustar (ex: número de secretarias, escolas, usuários, etc.) antes de gerar?
