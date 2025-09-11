# Cupcake Store - Loja Online

Uma loja online completa para venda de cupcakes artesanais, desenvolvida em PHP com MySQL e design responsivo.

## 🧁 Características

- **Catálogo de Produtos**: Vitrine com cupcakes, preços, descrições e controle de estoque
- **Sistema de Carrinho**: Adicionar, remover e alterar quantidades
- **Autenticação**: Sistema completo de login/registro para clientes
- **Checkout**: Finalização de compra com múltiplos métodos de pagamento
- **Painel Admin**: Gerenciamento completo de produtos, pedidos e clientes
- **Relatórios**: Sistema de relatórios de vendas e estatísticas
- **Design Responsivo**: Paleta de cores verde, bege e cinza prateado

## 🎨 Design

- **Cores**: Verde (#6B8E6B), Bege (#F5F1E8), Cinza Prateado (#C0C0C0)
- **Tipografia**: Google Fonts (Poppins)  
- **Ícones**: Font Awesome 6
- **Logo**: Logo personalizada integrada
- **Layout**: Responsivo com CSS Grid e Flexbox

## 👥 Tipos de Usuário

### Cliente
- Navegar no catálogo de produtos
- Adicionar itens ao carrinho
- Cadastro completo (nome, CPF, endereço, telefone, email)
- Finalizar compras
- Visualizar histórico de pedidos

### Administrador
- Gerenciar produtos (adicionar, editar, remover, controle de estoque)
- Gerenciar pedidos (alterar status, visualizar detalhes)
- Gerenciar clientes
- Gerar relatórios de vendas
- Dashboard com estatísticas

## 🛠️ Tecnologias

- **Backend**: PHP 7.4+
- **Banco de Dados**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6)
- **Servidor**: Apache/Nginx
- **Dependências**: PDO para banco de dados

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Servidor web (Apache/Nginx)
- Extensões PHP: PDO, PDO_MySQL

## 🚀 Instalação

### 1. Preparar o Ambiente

```bash
# Clone ou extraia os arquivos para seu servidor web
# Exemplo para XAMPP: C:\xampp\htdocs\cupcake-store
```

### 2. Configurar Banco de Dados

```bash
# Acesse: http://localhost/cupcake-store/install/setup_database.php
# O script criará automaticamente:
# - Banco de dados 'cupcake_store'
# - Todas as tabelas necessárias
# - Usuário admin padrão
# - Produtos de exemplo
```

### 3. Configuração (Opcional)

Edite `config/database.php` se necessário:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cupcake_store');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## 🔑 Acesso Inicial

### Administrador
- **Usuário**: admin
- **Senha**: admin123
- **URL**: http://localhost/cupcake-store/admin/dashboard.php

### Cliente
- Cadastre-se através da página de registro
- **URL**: http://localhost/cupcake-store/pages/register.php

## 📁 Estrutura do Projeto

```
cupcake-store/
├── index.php                 # Página principal
├── config/
│   └── database.php          # Configuração do banco
├── assets/
│   ├── css/
│   │   └── style.css         # Estilos principais
│   └── js/
│       └── main.js           # JavaScript principal
├── pages/
│   ├── login.php             # Página de login
│   ├── register.php          # Página de cadastro
│   ├── cart.php              # Carrinho de compras
│   ├── checkout.php          # Finalização de compra
│   └── order_success.php     # Confirmação de pedido
├── admin/
│   ├── dashboard.php         # Dashboard administrativo
│   ├── products.php          # Gerenciar produtos
│   ├── orders.php            # Gerenciar pedidos
│   └── reports.php           # Relatórios
├── api/
│   ├── get_products.php      # API para listar produtos
│   ├── add_to_cart.php       # API para adicionar ao carrinho
│   ├── get_cart.php          # API para obter carrinho
│   └── ...                   # Outras APIs
├── actions/
│   ├── login_process.php     # Processar login
│   ├── register_process.php  # Processar cadastro
│   ├── process_order.php     # Processar pedido
│   └── logout.php            # Logout
├── database/
│   └── create_database.sql   # Script SQL
├── install/
│   └── setup_database.php    # Instalador
└── README.md
```

## 🗄️ Banco de Dados

### Tabelas Principais

- **users**: Usuários (clientes e admins)
- **products**: Catálogo de produtos
- **cart**: Carrinho de compras
- **orders**: Pedidos realizados
- **order_items**: Itens dos pedidos

## 🛒 Funcionalidades Detalhadas

### Catálogo de Produtos
- Exibição em grid responsivo
- Filtros e busca
- Controle de estoque em tempo real
- Seleção de quantidade antes de adicionar ao carrinho

### Carrinho de Compras
- Adicionar/remover itens
- Alterar quantidades
- Cálculo automático de totais
- Persistência por sessão

### Sistema de Pedidos
- Múltiplos métodos de pagamento:
  - Cartão de Crédito
  - Cartão de Débito
  - PIX
  - Dinheiro na Entrega
- Confirmação de endereço
- Atualizações de status em tempo real

### Painel Administrativo
- Dashboard com estatísticas
- CRUD completo de produtos
- Gerenciamento de estoque
- Controle de status de pedidos
- Relatórios detalhados de vendas

## 🔧 Personalização

### Cores
Edite as variáveis CSS em `assets/css/style.css`:

```css
:root {
    --primary-green: #6B8E6B;
    --light-green: #A4C3A4;
    --beige: #F5F1E8;
    --silver-gray: #C0C0C0;
    /* ... */
}
```

### Produtos
Adicione novos produtos através do painel admin ou diretamente no banco:

```sql
INSERT INTO products (name, description, price, stock_quantity) 
VALUES ('Novo Cupcake', 'Descrição...', 10.50, 20);
```

## 📱 Responsividade

O sistema é totalmente responsivo e funciona em:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (até 767px)

## 🔒 Segurança

- Senhas com hash bcrypt
- Proteção contra SQL Injection (PDO Prepared Statements)
- Validação de dados no frontend e backend
- Sessões seguras para autenticação

## 🐛 Solução de Problemas

### Erro de Conexão com Banco
1. Verifique se o MySQL está rodando
2. Confirme as credenciais em `config/database.php`
3. Execute o instalador novamente

### Problemas de Permissão
1. Verifique permissões de escrita nas pastas
2. Configure o servidor web adequadamente

### Erro 404
1. Verifique se o mod_rewrite está habilitado
2. Confirme a estrutura de pastas

## 📈 Próximas Funcionalidades

- Sistema de avaliações e comentários
- Cupons de desconto
- Integração com gateways de pagamento reais
- Sistema de fidelidade
- Notificações por email
- API REST completa

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique este README
2. Consulte os comentários no código
3. Teste com dados de exemplo

## 📄 Licença

Este projeto é um exemplo educacional. Sinta-se livre para usar e modificar conforme necessário.

---

**Cupcake Store** - Os melhores cupcakes artesanais da cidade! 🧁
