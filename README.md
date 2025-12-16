# API RESTful - Sistema de Ordem de Serviço

## Sobre o Projeto

Este projeto consiste no desenvolvimento de uma **API RESTful para Sistema de Ordem de Serviço** utilizando **PHP puro 8.2**, sem frameworks externos, aplicando conceitos de **Clean Architecture** e **Clean Code**.

O sistema permite o gerenciamento completo de ordens de serviço, incluindo cadastro de clientes, funcionários, produtos, serviços e o controle de todas as etapas de uma ordem de serviço, desde a abertura até o fechamento.

## Objetivos

- Construir uma API robusta e escalável com PHP puro para gestão de ordens de serviço
- Implementar autenticação e autorização com **JWT (JSON Web Tokens)**
- Gerenciar clientes, funcionários, produtos, serviços e ordens de serviço
- Aplicar princípios de **Clean Architecture**
- Seguir boas práticas de **Clean Code**
- Demonstrar domínio de PHP moderno e padrões de projeto

## Tecnologias

- **PHP 8.2** - Linguagem principal
- **JWT** - Autenticação e autorização
- **Docker** - Containerização
- **Nginx** - Servidor web
- **Composer** - Gerenciamento de dependências

## Arquitetura

O projeto segue os princípios da **Clean Architecture**, com separação clara de responsabilidades:

```
api/
├── src/
│   ├── Config/           # Configurações (Container, Routes, Database, etc)
│   ├── Core/             # Núcleo do framework (Router, Dispatcher)
│   ├── Http/
│   │   ├── Controllers/  # Controladores da aplicação
│   │   │   └── Middleware/ # Middlewares (Auth, etc)
│   │   ├── JWT/          # Sistema de autenticação JWT
│   │   └── Request/      # Request e Response
│   ├── Models/           # Modelos de domínio
│   ├── Repositories/     # Camada de dados (Repository Pattern)
│   │   ├── Contracts/    # Interfaces dos repositórios
│   │   ├── Entities/     # Implementações concretas
│   │   └── Traits/       # Traits reutilizáveis (Find, Create, etc)
│   ├── Routes/           # Definição de rotas
│   ├── Services/         # Regras de negócio
│   ├── Transformers/     # Transformação de dados
│   └── Utils/            # Utilitários e Validators
├── composer.json
└── index.php
```

## Módulos do Sistema

### 👥 Gestão de Pessoas

- **Clientes**: Cadastro e gerenciamento de clientes
- **Funcionários**: Controle de funcionários e suas informações

### 📋 Gestão de Ordens de Serviço

- **Ordens de Serviço**: Criação, atualização e acompanhamento de ordens
- **Produtos**: Associação de produtos às ordens de serviço
- **Serviços**: Vinculação de serviços prestados às ordens

### 🔐 Autenticação e Autorização

- **Usuários**: Gerenciamento de usuários do sistema
- **JWT**: Sistema completo de autenticação por token
- **Permissões**: Controle de acesso baseado em níveis (admin, padrão, cliente)

### 📦 Catálogo

- **Produtos**: Cadastro de produtos e peças
- **Serviços**: Registro de serviços oferecidos

## Funcionalidades Técnicas Implementadas

### Core

- [x] Sistema de roteamento customizado com suporte a parâmetros dinâmicos
- [x] Container de injeção de dependências (DI) com Singleton pattern
- [x] Normalização automática de URLs (remoção de trailing slashes)
- [x] Dispatcher para resolução de rotas e controladores
- [x] Sistema de agrupamento de rotas com middleware

### HTTP

- [x] Classe `Request` (Singleton) para manipulação de requisições
  - Métodos: `method()`, `url()`, `getRequestData()`, `header()`, `setUser()`
- [x] Classe `Response` para padronização de respostas JSON
  - Método: `json($data, $statusCode)`

### Banco de Dados

- [x] Conexão PDO com MySQL via Singleton
- [x] Repository Pattern com interfaces
- [x] Traits para operações comuns (FindTrait, StandartTrait, etc)
- [x] Estrutura de tabelas completa
  - Tabela `users` (gerenciamento de usuários)
  - Tabela `tokens` (controle de JWT)
  - Tabela `persons` (dados pessoais base)
  - Tabela `customers` (clientes)
  - Tabela `employees` (funcionários)
  - Tabela `products` (catálogo de produtos)
  - Tabela `services` (catálogo de serviços)
  - Tabela `service_orders` (ordens de serviço)
  - Tabela `service_order_services` (serviços da ordem)
  - Tabela `service_order_products` (produtos da ordem)dos
- [x] Sistema de login/logout
- [x] Proteção de rotas sensíveis com middleware

### Validação de Dados

- [x] Trait `Validators` com regras de validação
  - `required`, `min`, `max`, `email`, `integer`, `string`
  - `unique`, `exists`, `confirmed`, `sometimes`
  - `uuid`, `in`, `date`, `boolean`, `regex`
- [x] Validação integrada nos controllers
- [x] Mensagens de erro personalizadas

### Banco de Dados

- [x] Conexão PDO com MySQL via Singleton
- [x] Repository Pattern com interfaces
- [x] Traits para operações comuns (FindTrait, CreateTrait, etc)
- [x] Migrations e estrutura de tabelas
  - Tabela `users` (gerenciamento de usuários)
  - Tabela `tokens` (controle de JWT)

### Transformers

- [x] Sistema de transformação de dados
- [x] Conversão de nomenclatura (camelCase ↔ snake_case)
- [x] Formatação de respostas padronizadas

### Arquitetura

- [x] Clean Architecture com separação de camadas
- [x] Autoloading PSR-4 via Composer
- [x] Padrão Singleton para classes compartilhadas
- [x] Reflection API para injeção de dependências automática
- [x] Repository Pattern com contratos (interfaces)
- [x] Service Provider para registro de dependências

### Tratamento de Erros

- [x] Resposta 404 para rotas não encontradas
- [x] Resposta 405 para métodos HTTP não permitidos
- [x] Resposta 500 para handlers inválidos ou métodos não encontrados
- [x] Resposta 401 para autenticação falha

## Próximas Funcionalidades

### Melhorias no Sistema de Ordens de Serviço

- [ ] Dashboard com estatísticas de ordens
- [ ] Sistema de notificações (email/SMS)
- [ ] Histórico de alterações nas ordens
- [ ] Anexo de fotos e documentos
- [ ] Assinatura digital do cliente
- [ ] Geração de PDF das ordens
- [ ] Sistema de orçamento prévio

### Recursos Técnicos

- [ ] Rate limiting
- [ ] Documentação da API (Swagger/OpenAPI)
- [ ] Sistema de permissões e roles mais granular
- [ ] Recuperação de senha
- [ ] Verificação de email
- [ ] Logs de auditoria
- [ ] Cache de consultas frequentes
- [ ] Backup automático de dados

## Próximas Funcionalidades

- [ ] Rate limiting
- [ ] Documentação da API (Swagger/OpenAPI)
- [ ] Sistema de permissões e roles
- [ ] Recuperação de senha
- [ ] Verificação de email
- [ ] Logs de auditoria

## Como Executar

### Pré-requisitos

- Docker e Docker Compose instalados
- PHP 8.2+ (se executar localmente)
- Composer

### Com Docker

```bash
# Clone o repositório
git clone https://github.com/MMacedoS/apiBasic.git

# Entre no diretório
cd apiBasic

# Copie o arquivo de ambiente (se necessário)
cp api/.env.example api/.env

# Inicie os containers
sudo docker compose up -d

# Instale as dependências do Composer
sudo docker compose exec php composer install

# Acesse a API
curl http://localhost:8080/api/v1/health
```

### Acessos

- **API**: http://localhost:8080/api/v1/
- **phpMyAdmin**: http://localhost:8082
  - Servidor: `db`
  - Usuário: `root`
  - Senha: `secret`

### Localmente

````bash
# Instale as dependências
cd api
composer install
## Documentação da API

A API está organizada em módulos para facilitar a manutenção e escalabilidade:

- **Autenticação**: Login/Logout com JWT
- **Usuários**: Gerenciamento de usuários do sistema
- **Pessoas**: Dados pessoais base
- **Clientes**: Cadastro e gestão de clientes
- **Funcionários**: Controle de funcionários
- **Produtos**: Catálogo de produtos e peças
- **Serviços**: Registro de serviços oferecidos
- **Ordens de Serviço**: Gestão completa de ordens

### Autenticação

Todas as rotas protegidas requerem um token JWT no header `Authorization`.
## Princípios Aplicados

### Clean Architecture

- **Separação de camadas**: Controllers, Services, Models
- **Injeção de dependências**: Container personalizado
- **Single Responsibility**: Cada classe tem uma única responsabilidade

### Clean Code

- Código legível e autoexplicativo
- Funções pequenas e focadas
- Nomenclatura significativa
- Comentários apenas quando necessário

## Documentação da API

### Autenticação

Todas as rotas protegidas requerem um token JWT no header `Authorization`.

#### Login

**POST** `/api/v1/login`

- **Descrição**: Autenticar usuário e obter token JWT
- **Body**:

```json
{
  "email": "user@example.com",
  "password": "senha123"
}
````

- **Resposta Sucesso (200)**:

```json
{
  "message": "Login efetuado com sucesso",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
### Endpoints Públicos

#### Health Checkhn Doe",
      "email": "user@example.com"
    }
  }
}
```

#### Logout

**POST** `/api/v1/logout`

- **Descrição**: Invalidar token JWT atual
- **Headers**: `Authorization: Bearer {token}`
- **Resposta (200)**:

```json
{
  "message": "Logout efetuado com sucesso"
}
```

### Endpoints Disponíveis

}

````

---

## Endpoints Protegidos

Todas as rotas abaixo requerem autenticação via JWT no header: `Authorization: Bearer {token}`

### 👤 Usuários

```json
{
  "message": "This API is healthy"
}
````

#### Home

**GET** `/api/v1/`

- **Descrição**: Endpoint de boas-vindas
- **Resposta**:

```json
{
  "message": "Welcome to the Home Controller!"
}
```

### Usuários (Rotas Protegidas)

Todas as rotas abaixo requerem autenticação via JWT.

#### Listar Usuários

**GET** `/api/v1/users`

- **Headers**: `Authorization: Bearer {token}`
- **Resposta**:

```json
{
  "message": "Lista de usuários",
  "data": [
    {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
      "email": "john@example.com",
      "access": "admin",
      "status": "active"
    }
  ]
}
```

#### Criar Usuário

**POST** `/api/v1/users`

- **Headers**: `Authorization: Bearer {token}`
- **Body**:

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "access": "padrao"
}
```

- **Validações**:
  - `name`: obrigatório, mín. 3, máx. 100 caracteres
  - `email`: obrigatório, formato válido, único
  - `password`: obrigatório, mín. 6 caracteres, confirmação obrigatória
  - `access`: opcional, valores: admin, padrao, cliente

#### Atualizar Usuário

**PUT** `/api/v1/users/{id}`

- **Headers**: `Authorization: Bearer {token}`
- **Parâmetros**: `id` - ID do usuário
- **Body**:

```json
{
  "name": "John Doe Updated",
  "email": "john.updated@example.com"
}
```

#### Remover Usuário

**DELETE** `/api/v1/users/{id}`

- **Headers**: `Authorization: Bearer {token}`
- **Parâmetros**: `id` - ID do usuário
- **Resposta**:

```json
{
  "message": "Usuário removido com sucesso"
}
```

#### Perfil do Usuário Autenticado

**GET** `/api/v1/profile`

- **Headers**: `Authorization: Bearer {token}`
- **Resposta**:
  }

````

---

### 👥 Clientes

#### Listar Clientes

**GET** `/api/v1/customers`

#### Criar Cliente

**POST** `/api/v1/customers`

**Body**:
```json
{
  "name": "João Silva",
  "email": "joao@email.com",
  "phone": "11999999999",
  "cpf_cnpj": "12345678900",
  "address": "Rua Exemplo, 123",
  "city": "São Paulo",
  "state": "SP"
}
````

#### Atualizar Cliente

**PUT** `/api/v1/customers/{uuid}`

#### Remover Cliente

**DELETE** `/api/v1/customers/{uuid}`

---

### Exemplos de Uso

#### cURL - Fluxo Completo de Ordem de Serviço

```bash
# 1. Login
TOKEN=$(curl -s -X POST http://localhost:8080/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@sistema.com","password":"senha123"}' \
  | jq -r '.data.token')

# 2. Criar Cliente
CUSTOMER=$(curl -s -X POST http://localhost:8080/api/v1/customers \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"João Silva",
    "email":"joao@email.com",
    "phone":"11999999999"
  }')

# 3. Criar Serviço
SERVICE=$(curl -s -X POST http://localhost:8080/api/v1/services \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"Manutenção",
    "price":200.00
  }')

# 4. Criar Ordem de Serviço
ORDER=$(curl -s -X POST http://localhost:8080/api/v1/orders \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "customer_id":1,
    "descricao":"Equipamento com defeito",
    "situacao":"aberta",
    "servicos":["uuid-do-servico"]
  }')

# 5. Atualizar Status
curl -X PATCH http://localhost:8080/api/v1/orders/{uuid}/status \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"status":"em_andamento"}'

# 6. Fechar Ordem
curl -X POST http://localhost:8080/api/v1/orders/{uuid}/close \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "laudo_tecnico":"Serviço concluído",
    "observacoes":"Cliente satisfeito"
  }'
```

#### cURL - Básicoonários

**GET** `/api/v1/employees`

#### Criar Funcionário

**POST** `/api/v1/employees`

**Body**:

```json
{
  "name": "Maria Santos",
  "email": "maria@empresa.com",
  "phone": "11988888888",
  "cpf": "98765432100",
  "position": "Técnico",
  "salary": 3500.0
}
```

#### Atualizar Funcionário

**PUT** `/api/v1/employees/{uuid}`

#### Remover Funcionário

**DELETE** `/api/v1/employees/{uuid}`

---

### 📦 Produtos

#### Listar Produtos

**GET** `/api/v1/products`

#### Criar Produto

**POST** `/api/v1/products`

**Body**:

```json
{
  "name": "Peça XYZ",
  "description": "Descrição do produto",
  "price": 150.0,
  "stock": 50,
  "code": "PROD-001"
}
```

#### Atualizar Produto

**PUT** `/api/v1/products/{uuid}`

#### Remover Produto

**DELETE** `/api/v1/products/{uuid}`

---

### 🔧 Serviços

#### Listar Serviços

**GET** `/api/v1/services`

#### Criar Serviço

**POST** `/api/v1/services`

**Body**:

```json
{
  "name": "Manutenção Preventiva",
  "description": "Serviço completo de manutenção",
  "price": 200.0,
  "estimated_time": "2 horas"
}
```

#### Atualizar Serviço

**PUT** `/api/v1/services/{uuid}`

#### Remover Serviço

**DELETE** `/api/v1/services/{uuid}`

---

### 📋 Ordens de Serviço

#### Listar Ordens de Serviço

**GET** `/api/v1/orders`

#### Criar Ordem de Serviço

**POST** `/api/v1/orders`

**Body**:

```json
{
  "customer_id": 1,
  "descricao": "Equipamento com defeito",
  "observacoes": "Cliente relatou problema intermitente",
  "situacao": "aberta",
  "servicos": ["uuid-servico-1", "uuid-servico-2"],
  "produtos": ["uuid-produto-1"]
}
```

#### Buscar Ordem por UUID

**GET** `/api/v1/orders/{uuid}`

**Resposta**:

```json
{
  "message": "Ordem de serviço encontrada",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "customer_id": 1,
    "descricao": "Equipamento com defeito",
    "situacao": "aberta",
    "data_abertura": "2025-12-16 10:30:00",
    "servicos": [],
    "produtos": []
  }
}
```

#### Atualizar Ordem de Serviço

**PUT** `/api/v1/orders/{uuid}`

**Body**:

```json
{
  "descricao": "Descrição atualizada",
  "situacao": "em_andamento",
  "laudo_tecnico": "Diagnóstico realizado"
}
```

#### Atualizar Status da Ordem

**PATCH** `/api/v1/orders/{uuid}/status`

**Body**:

```json
{
  "status": "concluida"
}
```

**Status possíveis**: `aberta`, `em_andamento`, `aguardando_pecas`, `concluida`, `cancelada`

#### Fechar Ordem de Serviço

**POST** `/api/v1/orders/{uuid}/close`

**Body**:

```json
{
  "laudo_tecnico": "Serviço concluído com sucesso",
  "observacoes": "Cliente satisfeito"
}
```

#### Listar Ordens por Cliente

**GET** `/api/v1/orders/customer/{customerId}`

#### Remover Ordem de Serviço

**DELETE** `/api/v1/orders/{uuid}`

---

### Formato de Resposta Padrãoo",

"data": {
"id": 1,
"uuid": "550e8400-e29b-41d4-a716-446655440000",
"name": "John Doe",
"email": "john@example.com",
"access": "admin"
}
}

````

#### Atualizar Perfil

**PUT** `/api/v1/profile`

- **Headers**: `Authorization: Bearer {token}`
- **Body**:

```json
{
  "name": "John Doe Updated",
  "email": "john.updated@example.com",
  "password": "novaSenha123",
  "password_confirmation": "novaSenha123"
}
````

### Formato de Resposta Padrão

Todas as respostas seguem o formato JSON:

```json
{
  "message": "string",
  "data": {}
}
```

### Códigos de Status HTTP

- `200` - Sucesso
- `201` - Criado com sucesso
- `401` - Não autenticado
- `404` - Recurso não encontrado
- `405` - Método HTTP não permitido
- `422` - Erro de validação
- `500` - Erro interno do servidor

### Erros de Validação

Quando há erros de validação, a resposta será:

```json
{
  "message": "Erros de validação",
  "errors": {
    "email": ["O campo email é obrigatório.", "O email já está em uso."],
    "password": ["O campo password deve ter no mínimo 6 caracteres."]
  }
}
```

### Exemplos de Uso

#### cURL - Login

```bash
# Login
curl -X POST http://localhost:8080/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"senha123"}'

# Listar usuários (com autenticação)
TOKEN="seu_token_jwt_aqui"
curl -X GET http://localhost:8080/api/v1/users \
  -H "Authorization: Bearer $TOKEN"

# Criar usuário
curl -X POST http://localhost:8080/api/v1/users \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name":"John Doe",
    "email":"john@example.com",
    "password":"senha123",
    "password_confirmation":"senha123"
  }'
```

#### PHP

```php
// Login
$ch = curl_init('http://localhost:8080/api/v1/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'email' => 'user@example.com',
    'password' => 'senha123'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$data = json_decode($response, true);
$token = $data['data']['token'];
curl_close($ch);

// Listar usuários
$ch = curl_init('http://localhost:8080/api/v1/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token
]);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
```

#### JavaScript (Fetch)

```javascript
// Login
fetch("http://localhost:8080/api/v1/login", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({
    email: "user@example.com",
    password: "senha123",
  }),
})
  .then((response) => response.json())
  .then((data) => {
    const token = data.data.token;

    // Listar usuários
    return fetch("http://localhost:8080/api/v1/users", {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });
  })
  .then((response) => response.json())
  .then((data) => console.log(data));
```

## Autenticação JWT

A API utiliza JWT para autenticação stateless com as seguintes funcionalidades implementadas:

### Funcionalidades

- **Geração de tokens**: Criação de JWT com payload customizado e expiração configurável
- **Validação de tokens**: Verificação de assinatura e expiração
- **Invalidação de tokens**: Sistema de blacklist para logout seguro
- **Armazenamento**: Tokens são persistidos no banco de dados
- **Middleware de proteção**: Rotas protegidas automaticamente via middleware `auth`

### Fluxo de Autenticação

1. **Login**: Usuário envia email e senha
2. **Geração**: Sistema gera JWT com dados do usuário
3. **Armazenamento**: Token é salvo no banco de dados
4. **Resposta**: Token é enviado ao cliente
5. **Uso**: Cliente envia token no header `Authorization: Bearer {token}`
6. **Validação**: Middleware valida token em cada requisição
7. **Logout**: Token é invalidado e removido do banco

### Configuração

As seguintes variáveis de ambiente controlam o JWT:

```env
JWT_SECRET=sua_chave_secreta_aqui
JWT_EXPIRATION=3600  # Tempo em segundos (1 hora)
```

### Segurança

- Algoritmo: HS256 (HMAC SHA-256)
- Tokens expirados são automaticamente invalidados
- Sistema de blacklist para tokens revogados
- Validação de assinatura em todas as requisições

## Sistema de Validação

A API possui um sistema robusto de validação de dados através do trait `Validators`:

### Regras Disponíveis

- `required` - Campo obrigatório
- `min:n` - Tamanho mínimo (caracteres)
- `max:n` - Tamanho máximo (caracteres)
- `email` - Formato de email válido
- `integer` - Valor inteiro
- `string` - Valor texto
- `unique:table,column` - Valor único no banco
- `exists:table,column` - Valor deve existir no banco
- `confirmed` - Confirmação de campo (ex: password_confirmation)
- `sometimes` - Validação opcional
- `uuid` - Formato UUID válido
- `in:value1,value2` - Valor deve estar na lista
- `date` - Formato de data válido
- `boolean` - Valor booleano
- `regex:pattern` - Validação com expressão regular

### Exemplo de Uso

```php
$validatedData = $this->validate($data, [
    'name' => 'required|min:3|max:100',
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:6|confirmed',
    'access' => 'sometimes|in:admin,padrao,cliente'
]);
```

## Middleware

Sistema de middleware para interceptar requisições:

### Middleware Auth

Protege rotas que requerem autenticação:

```php
Route::group(['middleware' => ['auth']], function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/logout', [UserController::class, 'logout']);
});
```

### Criando Middlewares

Middlewares devem estar em `src/Http/Controllers/Middleware/` e implementar o método `handle()`:

```php
class CustomMiddleware
{
    public static function handle(Request $request, $next)
    {
        // Lógica do middleware

        if ($condicao) {
            return [
                'status' => 403,
                'body' => ['message' => 'Acesso negado']
            ];
        }

        return $next($request);
    }
}
```

## Licença

Este projeto está sob a licença MIT.

## Autor

Desenvolvido como projeto de estudo de PHP moderno e arquitetura de software.
