# API RESTful com PHP Puro 8.2

## Sobre o Projeto

Este projeto consiste no desenvolvimento de uma **API RESTful** utilizando **PHP puro 8.2**, sem frameworks externos, aplicando conceitos de **Clean Architecture** e **Clean Code**.

## Objetivos

- Construir uma API robusta e escalável com PHP puro
- Implementar autenticação e autorização com **JWT (JSON Web Tokens)**
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

## Funcionalidades Implementadas

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
- [x] Controller base com método `respondJson()` e trait `Validators`
- [x] Injeção automática do objeto Request nos controladores

### Autenticação e Segurança

- [x] Sistema JWT completo (geração, validação e invalidação de tokens)
- [x] Middleware de autenticação (`Auth`)
- [x] Gestão de tokens em banco de dados
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
- [x] Resposta 422 para erros de validação

### Utilitários

- [x] Funções helpers (`dd()`, `dump()`)
- [x] Variáveis de ambiente com DotEnv
- [x] Docker Compose com PHP 8.2, Nginx, MySQL e phpMyAdmin

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

```bash
# Instale as dependências
cd api
composer install

# Inicie o servidor PHP
php -S localhost:8000
```

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
```

- **Resposta Sucesso (200)**:

```json
{
  "message": "Login efetuado com sucesso",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "uuid": "550e8400-e29b-41d4-a716-446655440000",
      "name": "John Doe",
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

#### Health Check

**GET** `/api/v1/health`

- **Descrição**: Verificar status da API
- **Resposta**:

```json
{
  "message": "This API is healthy"
}
```

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

```json
{
  "message": "Perfil do usuário",
  "data": {
    "id": 1,
    "uuid": "550e8400-e29b-41d4-a716-446655440000",
    "name": "John Doe",
    "email": "john@example.com",
    "access": "admin"
  }
}
```

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
```

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
