# Guia de Deploy - LC Soluções em Móveis

## Estrutura do Pacote

```
deploy/
├── database_dumps/          ← SQL para importar no banco
│   ├── meusite_db.sql       (Site principal - 74 KB)
│   ├── crm_schema.sql       (Estrutura CRM - 10 KB)
│   ├── crm_data.sql         (Dados CRM: clientes, projetos, usuarios - 1.4 MB)
│   └── crm_segmentos.sql    (Segmentos de parceiros - 4.2 MB)
│
└── public_html/             ← Enviar para a HostGator
    ├── .env                 (EDITAR com credenciais de produção)
    ├── .htaccess
    ├── index.php
    ├── app/
    ├── assets/
    ├── config/
    ├── includes/
    ├── pages/
    ├── public/
    ├── routes/
    ├── vendor/              (GERAR com composer install)
    │
    └── crm/                 ← Subdomínio crm.lcsolucoesemmoveis.com.br
        ├── .htaccess
        ├── index.php
        ├── login.php
        ├── api/
        ├── assets/
        ├── includes/
        │   ├── db_config.php  (EDITAR credenciais do CRM)
        │   ├── db.php
        │   ├── auth.php
        │   └── ...
        └── pages/
```

---

## PASSO 1 — Criar Bancos de Dados no cPanel

1. Acesse o cPanel da HostGator
2. Vá em **"MySQL Databases"** (ou "Banco de Dados MySQL")
3. Crie **dois** bancos:

| Banco | Nome sugerido | Finalidade |
|-------|--------------|------------|
| `luizca93_meusite_db` | Site principal | Tabelas: contatos, posts, projetos, usuarios |
| `luizca93_crm_cnpj` | CRM | Tabelas: clientes, projetos, usuarios, segmentos |

4. Crie **um usuário** (ex: `luizca93_joao`) com senha forte
5. Adicione o usuário aos **DOIS** bancos com **"Todos os Privilégios"**

---

## PASSO 2 — Importar os SQLs

### Via phpMyAdmin:
1. Acesse phpMyAdmin no cPanel
2. Selecione `luizca93_meusite_db` → Importar → escolha `meusite_db.sql`
3. Selecione `luizca93_crm_cnpj` → Importar → escolha na ordem:
   - `crm_schema.sql` (estrutura primeiro)
   - `crm_data.sql` (dados)
   - `crm_segmentos.sql` (segmentos — pode levar alguns minutos)

### Via linha de comando (SSH - se disponível):
```bash
mysql -u luizca93_joao -p luizca93_meusite_db < meusite_db.sql
mysql -u luizca93_joao -p luizca93_crm_cnpj < crm_schema.sql
mysql -u luizca93_joao -p luizca93_crm_cnpj < crm_data.sql
mysql -u luizca93_joao -p luizca93_crm_cnpj < crm_segmentos.sql
```

---

## PASSO 3 — Configurar Domínio e Subdomínio

1. No cPanel, vá em **"Subdomains"** (ou "Subdomínios")
2. Crie:
   - **Subdomínio:** `crm`
   - **Domínio:** `lcsolucoesemmoveis.com.br`
   - **Diretório:** `public_html/crm`

3. Acesse **"SSL/TLS"** e ative SSL **para ambos**:
   - `lcsolucoesemmoveis.com.br` (AutoSSL)
   - `crm.lcsolucoesemmoveis.com.br` (AutoSSL)

---

## PASSO 4 — Fazer Upload dos Arquivos

### Via FTP (FileZilla):
1. Conecte ao FTP da HostGator
2. Envie **todo o conteúdo** de `deploy/public_html/` para a raiz
3. Estrutura final no servidor:

```
/public_html/
├── .env              ← EDITAR
├── index.php
├── app/
├── assets/
├── config/
├── includes/
├── pages/
├── public/
├── routes/
├── crm/              ← Subdomínio
│   ├── .htaccess
│   ├── index.php
│   ├── login.php
│   ├── includes/
│   │   ├── db_config.php  ← EDITAR
│   │   └── ...
│   └── ...
```

---

## PASSO 5 — Editar Configurações

### 5.1 — Site (.env)

Edite `/public_html/.env` com as credenciais da HostGator:

```ini
DB_HOST='localhost'
DB_NAME='luizca93_meusite_db'
DB_USER='luizca93_joao'
DB_PASS='SUA_SENHA_AQUI'

CRM_DB_NAME='luizca93_crm_cnpj'
CRM_DB_HOST='localhost'
CRM_DB_USER='luizca93_joao'
CRM_DB_PASS='SUA_SENHA_AQUI'

RECAPTCHA_SITE_KEY='SUA_CHAVE'
RECAPTCHA_SECRET_KEY='SUA_CHAVE'
GEMINI_API_KEY='SUA_CHAVE'
```

### 5.2 — CRM (db_config.php)

Edite `/public_html/crm/includes/db_config.php`:

```php
define('CRM_DB_HOST', 'localhost');
define('CRM_DB_NAME', 'luizca93_crm_cnpj');
define('CRM_DB_USER', 'luizca93_joao');
define('CRM_DB_PASS', 'SUA_SENHA_AQUI');
define('CRM_DB_PORT', '3306');
```

---

## PASSO 6 — Instalar Dependências (Composer)

No servidor (via SSH ou Terminal do cPanel):
```bash
cd /home/luizca93/public_html
php composer.phar install --no-dev --optimize-autoloader
```

Se não tiver acesso SSH, **adicione a pasta `vendor/` manualmente**:
- Execute `composer install` localmente com a mesma versão PHP
- Faça upload da pasta `vendor/` gerada

---

## PASSO 7 — Configurar PHP (se necessário)

Se a HostGator usar PHP < 7.4, solicite a mudança via cPanel:
- **MultiPHP Manager** → Selecione PHP 8.0 ou 8.1
- Extensões necessárias: `mysqli`, `pdo_mysql`, `mbstring`, `json`, `openssl`

---

## PASSO 8 — Verificar Funcionamento

### Site principal:
- ✅ https://lcsolucoesemmoveis.com.br/ — carrega homepage
- ✅ https://lcsolucoesemmoveis.com.br/portfolio — portfólio
- ✅ https://lcsolucoesemmoveis.com.br/contato — formulário de contato
- ✅ CSS, JS e imagens carregando

### CRM:
- ✅ https://crm.lcsolucoesemmoveis.com.br/ — redireciona para login
- ✅ Login com email e senha do admin
- ✅ Dashboard com dados carregando
- ✅ Listagem de segmentos e clientes

### Criar usuário admin do CRM:
Acesse: `https://crm.lcsolucoesemmoveis.com.br/setup_usuario.php`
(Remova este arquivo após criar o usuário!)

---

## PASSO 9 — Pós-Deploy (Segurança)

1. **Remova** `setup_usuario.php` do servidor
2. **Remova** a pasta `deploy/` se foi enviada
3. **Proteja** `.env` — já está bloqueado pelo `.htaccess`
4. **Ative HTTPS** — verificar se SSL está funcionando
5. **Teste o formulário** de contato (reCAPTCHA + sync CRM)

---

## Alterações Realizadas no Código

| Arquivo | O que mudou |
|---------|-------------|
| `includes/db_config.php` (NOVO) | Config centralizada de banco do CRM |
| `includes/db.php` | Agora usa `db_config.php` |
| `api/*.php` (18 arquivos) | Substituído `new mysqli(...)` por `db_config.php` |
| `app/Models/Contato.php` | Sync CRM agora usa env vars `CRM_DB_*` |
| `pages/email.php` | Logo URL corrigida para produção |
| `.env` | Nova versão com placeholders + credenciais CRM |
| `config/app.php` | URL ajustada para produção |
| `includes/config.php` | Error reporting desabilitado |
| `crm/.htaccess` (NOVO) | Proteção de diretórios sensíveis |
| `.htaccess` | Regras de segurança atualizadas |
