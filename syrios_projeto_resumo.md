# 🧩 Projeto Syrios — Resumo Técnico Atual

## 📘 Estrutura e Conceito Central
O **Syrios** é um sistema Laravel para **gestão escolar multiusuário**, com **papéis (roles)** e **vínculos a escolas (school context)**.  
Cada usuário pode ter múltiplos papéis (por exemplo, *professor* e *escola*) em diferentes escolas, e o sistema gerencia o **contexto ativo (role + escola)** em sessão.

---

## ⚙️ Autenticação e Contexto
**Arquivo:** `app/Http/Controllers/Auth/LoginController.php`
Funções principais:
- **login():** valida CPF e senha, autentica o usuário e determina o contexto (`school_id` e `role_name`).
- **setContext():** grava `current_school_id` e `current_role` na sessão.
- **chooseSchool() / chooseRole():** exibem telas para o usuário escolher contexto quando há múltiplos vínculos.
- **dashboardRoute():** redireciona o usuário ao painel correto conforme o papel ativo.

**Middleware:** `EnsureContextSelected`
- Garante que toda requisição autenticada tenha contexto ativo.
- Se o contexto estiver ausente ou inválido, redireciona o usuário à tela de escolha.

---

## 🧭 Helpers
**Arquivo:** `app/helpers.php`
```php
if (!function_exists('dashboard_route')) {
    function dashboard_route() {
        $user = auth()->user();
        if (!$user) return route('login');
        if ($user->hasRole('master')) return route('master.dashboard');
        if ($user->hasRole('secretaria')) return route('secretaria.dashboard');
        return route('escola.usuarios.index');
    }
}
```

---

## 🛣️ Rotas (web.php)
- **Master:** `/master/*` → Gerencia escolas, usuários e roles.
- **Secretaria:** `/secretaria/*` → Controla escolas filhas e usuários administrativos.
- **Escola:** `/escola/*` → CRUDs para professores, alunos, turmas, disciplinas, usuários.

Rotas públicas: `/login`, `/logout`, `/choose-school`, `/choose-role`, `/set-context`.

---

## 👑 Painel Master
### ✅ `Master/UsuarioController.php`
- Cria ou vincula usuários existentes à escola selecionada.
- Atualiza dados e papéis (roles) com detecção de duplicatas.
- Remove vínculos antes de excluir usuários.

### 🧩 Views
- `master/usuarios/create.blade.php`: criação com detecção de CPF existente.
- `master/usuarios/edit.blade.php`: edição completa com escola, status e papéis.
- Planejada: `master/usuarios/roles.blade.php` (edição segura de roles por escola).

---

## 🏫 Painel Escola
### ✅ `Escola/UsuarioController.php`
Gerencia usuários e faz vinculação com roles e professores.  
Detecta duplicidade via CPF antes de criar.

### ✅ `Escola/ProfessorController.php`
Lista professores com:
- Nome do usuário
- Escola de origem
- Apenas os da escola atual (`session('current_school_id')`).

---

## 🔐 Estrutura de Dados
| Tabela | Descrição |
|--------|------------|
| `syrios_usuario` | Usuários com nome, CPF, senha hash, status, school_id |
| `syrios_role` | Papéis do sistema |
| `syrios_usuario_role` | Pivot com `usuario_id`, `role_id`, `school_id` |
| `syrios_escola` | Escolas |
| `syrios_professor` | Professores vinculados (`usuario_id`, `school_id`) |

---

## 💬 Feedbacks e Mensagens
Mensagens: `success`, `error`, `info`, e validações (`$errors->any()`).  
Plano: adicionar mensagens detalhadas para exceções SQL e operações críticas.

---

## ⚠️ Problemas Detectados e Próximas Ações
| Área | Problema | Próxima ação |
|------|-----------|--------------|
| Edição de roles | Violação de chave primária | Tela separada para roles |
| Banco de dados | Falta de cascade controlado | Revisar FKs |
| Mensagens | Pouco feedback SQL | Envolver `try/catch` |
| Desempenho | Chat pesado | Novo chat com base limpa |
| Permissões | Roles sobrepostas | Melhorar middleware `role` |

---

## 💾 Recomendação
Salve este arquivo como `docs/syrios_projeto_resumo.md`.  
No novo chat, diga: “Continuar o desenvolvimento do sistema Syrios a partir do resumo técnico salvo.”
