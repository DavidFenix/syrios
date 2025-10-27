# 🧾 Relatório de Compatibilidade — Projeto **Syrios**

## 📌 Resumo Técnico

| Item | Valor Detectado |
|------|------------------|
| **Laravel Framework** | 8.83.29 |
| **PHP** | 8.1.31 (ZTS x64, com OPcache e Xdebug) |
| **Banco de Dados** | MySQL 9.1.0 Community Server |
| **Sistema Local** | Windows 10 (WAMP64) |
| **Timezone** | America/Sao_Paulo |
| **Idioma / Locale** | pt_BR |
| **APP_ENV** | local |
| **APP_DEBUG** | false |
| **APP_URL** | http://localhost |
| **DB Connection** | mysql |
| **DB Database** | syrios |
| **Cache / Session** | file |
| **Storage driver** | local |
| **Fila / Queue** | sync |
| **Gerador de PDF** | barryvdh/laravel-dompdf |

---

## ⚙️ Extensões PHP necessárias

| Extensão | Obrigatória | Finalidade |
|-----------|-------------|-------------|
| `pdo_mysql` | ✅ | Conexão com banco MySQL |
| `mbstring` | ✅ | Manipulação de strings (acentos, UTF-8) |
| `openssl` | ✅ | Criptografia e APP_KEY |
| `tokenizer` | ✅ | Segurança em sessões |
| `ctype` | ✅ | Validação de caracteres |
| `json` | ✅ | Respostas de API e serialização |
| `fileinfo` | ✅ | Uploads e PDFs |
| `dom`, `gd`, `xml` | 🔹 | Necessárias para DomPDF |
| `zip` | 🔹 | Compactação em backups |

---

## 🧱 Requisitos mínimos de hospedagem

| Recurso | Valor mínimo exigido |
|----------|----------------------|
| PHP | ≥ 8.1 |
| MySQL / MariaDB | ≥ 5.7 |
| Extensões | conforme tabela acima |
| Acesso SSH ou Composer | preferencial |
| Suporte a `.htaccess` / reescrita de URL | obrigatório |
| Permissão para `public/` ser a raiz do site | obrigatório |

---

## 🌐 Hospedagens gratuitas compatíveis

| Provedor | Compatibilidade | Observações |
|-----------|------------------|-------------|
| **InfinityFree** | ⚠️ Parcial | PHP 8.1 ok, mas **não permite composer nem artisan**. Ideal apenas para sites Blade já compilados. |
| **Render.com (Free Plan)** | ✅ Completa | Suporta Composer, artisan e MySQL externo. Deploy via GitHub. |
| **Railway.app (Free Tier)** | ✅ Completa | Banco MySQL embutido e PHP até 8.2. |
| **Cyclic.sh** | ✅ Boa | Laravel + MySQL remoto, integração GitHub simples. |
| **Vercel / Netlify** | ❌ Limitado | Voltados a Node.js. |
| **000WebHost / ByetHost** | ⚠️ Limitado | PHP 8.0 apenas, sem artisan. |

✅ **Recomendado:** **Render** ou **Railway**, por suportarem Composer e Artisan.

---

## ⚡ Passos para Deploy em Render

1. Crie conta em [https://render.com](https://render.com)
2. Conecte o repositório GitHub (ou envie .zip)
3. Configure:
   ```bash
   Build Command: composer install --optimize-autoloader --no-dev
   Start Command: php artisan serve --host 0.0.0.0 --port 10000
   ```
4. Defina variáveis de ambiente:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   DB_CONNECTION=mysql
   DB_HOST=sql.<seu_servidor>
   DB_DATABASE=syrios
   DB_USERNAME=******
   DB_PASSWORD=******
   ```
5. Render gera uma URL pública (`https://syrios.onrender.com`)

---

## 🧮 Observações sobre o Banco

Seu MySQL 9.1 é mais novo que o suportado pela maioria das hospedagens.  
Exporte assim para compatibilidade:
```bash
mysqldump --compatible=mysql57 --no-create-db --skip-lock-tables syrios > syrios.sql
```

---

## 🔐 Segurança e performance sugeridas

| Item | Sugestão |
|------|-----------|
| `.env` | nunca subir no GitHub |
| `APP_DEBUG=false` | obrigatório em produção |
| `php artisan optimize` | antes do deploy |
| `php artisan config:cache` | acelera carregamento |
| `php artisan route:cache` | acelera rotas |
| `php artisan storage:link` | garante acesso a imagens |
| `chmod -R 775 storage bootstrap/cache` | permissões corretas |

---

## 🧩 Conclusão

✅ Ambiente compatível com **Render.com** e **Railway.app**  
⚠️ InfinityFree funcional apenas em modo simplificado (sem Artisan/Composer)  
🎯 Pronto para deploy com `php artisan optimize` e `.env` de produção.
