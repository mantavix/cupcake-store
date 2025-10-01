# 🚀 Guia de Deploy - Cupcake Store

Este guia te ajudará a colocar sua aplicação Cupcake Store online gratuitamente.

## 📋 Pré-requisitos

- Conta no GitHub (para versionamento)
- Aplicação funcionando localmente
- Banco de dados MySQL configurado

## 🌐 Opções de Hospedagem Gratuita

### 1. 🥇 InfinityFree (Recomendado)

**Vantagens:**
- ✅ PHP 8.1+ suportado
- ✅ MySQL gratuito (400MB)
- ✅ 5GB de espaço em disco
- ✅ SSL gratuito
- ✅ Sem anúncios forçados
- ✅ Subdomínio gratuito

**Passos:**

1. **Criar conta:** Acesse [infinityfree.net](https://infinityfree.net)
2. **Criar novo site:** Escolha um subdomínio
3. **Configurar banco de dados:**
   - Acesse o painel de controle
   - Vá em "MySQL Databases"
   - Crie um novo banco de dados
   - Anote: host, nome do banco, usuário e senha

4. **Upload dos arquivos:**
   - Use o File Manager ou FTP
   - Faça upload de todos os arquivos para a pasta `htdocs`

5. **Configurar variáveis de ambiente:**
   - Crie arquivo `.env` na raiz com:
   ```
   DB_HOST=seu_host_mysql
   DB_NAME=seu_nome_banco
   DB_USER=seu_usuario
   DB_PASS=sua_senha
   ```

6. **Importar banco de dados:**
   - Acesse phpMyAdmin
   - Importe o arquivo `database/create_database.sql`

### 2. 🥈 000WebHost

**Vantagens:**
- ✅ PHP 8.0+ suportado
- ✅ MySQL gratuito
- ✅ 1GB de espaço
- ✅ SSL gratuito

**Passos:**

1. **Criar conta:** Acesse [000webhost.com](https://000webhost.com)
2. **Criar website:** Escolha "Build Website"
3. **Configurar banco de dados:**
   - Vá em "Manage Database"
   - Crie novo banco MySQL
   - Anote as credenciais

4. **Upload via File Manager:**
   - Acesse File Manager
   - Upload todos os arquivos para `public_html`

5. **Configurar .env e importar banco**

### 3. 🥉 Railway

**Vantagens:**
- ✅ Deploy via Git
- ✅ MySQL gratuito (limitado)
- ✅ Muito fácil de usar
- ✅ HTTPS automático

**Passos:**

1. **Preparar repositório Git:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/seu-usuario/cupcake-store.git
   git push -u origin main
   ```

2. **Criar conta:** Acesse [railway.app](https://railway.app)

3. **Conectar GitHub:** Autorize acesso ao seu repositório

4. **Deploy:**
   - Clique em "New Project"
   - Selecione "Deploy from GitHub repo"
   - Escolha seu repositório

5. **Adicionar banco MySQL:**
   - Clique em "Add Service"
   - Selecione "MySQL"
   - Anote as credenciais geradas

6. **Configurar variáveis de ambiente:**
   - Vá em "Variables"
   - Adicione as variáveis do banco de dados

### 4. 🎯 Heroku

**Vantagens:**
- ✅ Muito confiável
- ✅ Git deploy
- ✅ Add-ons gratuitos

**Passos:**

1. **Instalar Heroku CLI:** [devcenter.heroku.com/articles/heroku-cli](https://devcenter.heroku.com/articles/heroku-cli)

2. **Login e criar app:**
   ```bash
   heroku login
   heroku create nome-do-seu-app
   ```

3. **Adicionar MySQL (ClearDB):**
   ```bash
   heroku addons:create cleardb:ignite
   heroku config:get CLEARDB_DATABASE_URL
   ```

4. **Configurar variáveis de ambiente:**
   ```bash
   heroku config:set DB_HOST=seu_host
   heroku config:set DB_NAME=seu_banco
   heroku config:set DB_USER=seu_usuario
   heroku config:set DB_PASS=sua_senha
   ```

5. **Deploy:**
   ```bash
   git add .
   git commit -m "Deploy to Heroku"
   git push heroku main
   ```

## 🔧 Configuração Pós-Deploy

### 1. Importar Banco de Dados

Execute o script SQL em `database/create_database.sql` no seu banco de produção.

### 2. Configurar Admin

Acesse: `seu-dominio.com/install/fix_admin_password.php` para criar usuário admin.

### 3. Testar Funcionalidades

- ✅ Registro de usuário
- ✅ Login/Logout
- ✅ Adicionar produtos ao carrinho
- ✅ Finalizar pedido
- ✅ Painel administrativo

## 🛡️ Segurança

### Arquivos Importantes:

- `.htaccess` - Configurações de segurança
- `.env` - Variáveis de ambiente (NUNCA commitar)
- `config/database.php` - Configuração automática de ambiente

### Checklist de Segurança:

- ✅ Senhas fortes no banco de dados
- ✅ SSL/HTTPS habilitado
- ✅ Arquivos sensíveis protegidos
- ✅ Logs de erro configurados
- ✅ Backup regular do banco

## 🐛 Solução de Problemas

### Erro de Conexão com Banco:
1. Verifique as credenciais no `.env`
2. Confirme se o banco foi criado
3. Teste conexão via phpMyAdmin

### Erro 500:
1. Verifique logs de erro do servidor
2. Confirme permissões de arquivos
3. Verifique sintaxe PHP

### Imagens não carregam:
1. Verifique caminhos das imagens
2. Confirme upload das imagens
3. Verifique permissões da pasta `assets/img`

## 📞 Suporte

Se encontrar problemas:

1. Verifique os logs de erro
2. Teste localmente primeiro
3. Confirme todas as configurações
4. Consulte documentação da plataforma escolhida

## 🎉 Pronto!

Sua aplicação Cupcake Store agora está online e acessível para o mundo todo! 

**URLs de exemplo:**
- InfinityFree: `http://seusite.epizy.com`
- 000WebHost: `http://seusite.000webhostapp.com`
- Railway: `https://seuapp.up.railway.app`
- Heroku: `https://seuapp.herokuapp.com`

Compartilhe o link com seus amigos e familiares para testarem sua loja online! 🧁