# API RESTful com PHP Puro 8.2

## 📋 Sobre o Projeto

Este projeto consiste no desenvolvimento de uma **API RESTful** utilizando **PHP puro 8.2**, sem frameworks externos, aplicando conceitos de **Clean Architecture** e **Clean Code**.

## 🎯 Objetivos

- Construir uma API robusta e escalável com PHP puro
- Implementar autenticação e autorização com **JWT (JSON Web Tokens)**
- Aplicar princípios de **Clean Architecture**
- Seguir boas práticas de **Clean Code**
- Demonstrar domínio de PHP moderno e padrões de projeto

## 🛠️ Tecnologias

- **PHP 8.2** - Linguagem principal
- **JWT** - Autenticação e autorização
- **Docker** - Containerização
- **Nginx** - Servidor web
- **Composer** - Gerenciamento de dependências

## 🏗️ Arquitetura

O projeto segue os princípios da **Clean Architecture**, com separação clara de responsabilidades:

```
api/
├── src/
│   ├── Config/         # Configurações (Container, Routes, etc)
│   ├── Core/           # Núcleo do framework (Router, Dispatcher)
│   ├── Http/
│   │   └── Controllers/ # Controladores da aplicação
│   ├── Models/         # Modelos de domínio
│   ├── Routes/         # Definição de rotas
│   ├── Services/       # Regras de negócio
│   └── Utils/          # Utilitários
├── composer.json
└── index.php
```

## ✨ Funcionalidades Implementadas

### Core

- [x] Sistema de roteamento customizado com suporte a parâmetros dinâmicos
- [x] Container de injeção de dependências (DI) com Singleton pattern
- [x] Normalização automática de URLs (remoção de trailing slashes)
- [x] Dispatcher para resolução de rotas e controladores

### HTTP

- [x] Classe `Request` (Singleton) para manipulação de requisições
  - Métodos: `method()`, `url()`, `getRequestData()`
- [x] Classe `Response` para padronização de respostas JSON
  - Método: `json($data, $statusCode)`
- [x] Controller base com método `respondJson()`
- [x] Injeção automática do objeto Request nos controladores

### Arquitetura

- [x] Clean Architecture com separação de camadas
- [x] Autoloading PSR-4 via Composer
- [x] Padrão Singleton para classes compartilhadas
- [x] Reflection API para injeção de dependências automática

### Tratamento de Erros

- [x] Resposta 404 para rotas não encontradas
- [x] Resposta 405 para métodos HTTP não permitidos
- [x] Resposta 500 para handlers inválidos ou métodos não encontrados

## 📋 Próximas Funcionalidades

- [ ] Autenticação JWT
- [ ] Middleware de autenticação
- [ ] CRUD de recursos
- [ ] Validação de dados
- [ ] Rate limiting
- [ ] Documentação da API (Swagger/OpenAPI)

## 🚀 Como Executar

### Pré-requisitos

- Docker e Docker Compose instalados
- PHP 8.2+ (se executar localmente)
- Composer

### Com Docker

```bash
# Clone o repositório
git clone <seu-repositorio>

# Entre no diretório
cd apiBasic

# Inicie os containers
docker-compose up -d

# Acesse a API
curl http://localhost/api/v1/
```

### Localmente

```bash
# Instale as dependências
cd api
composer install

# Inicie o servidor PHP
php -S localhost:8000
```

## 📚 Princípios Aplicados

### Clean Architecture

- **Separação de camadas**: Controllers, Services, Models
- **Injeção de dependências**: Container personalizado
- **Single Responsibility**: Cada classe tem uma única responsabilidade

### Clean Code

- Código legível e autoexplicativo
- Funções pequenas e focadas
- Nomenclatura significativa
- Comentários apenas quando necessário

## 📖 Documentação da API

### Endpoints Disponíveis

#### Home

**GET** `/api/v1/`
- **Descrição**: Endpoint de boas-vindas
- **Resposta**: 
```json
{
  "message": "Welcome to the Home Controller!"
}
```

**GET** `/api/v1/ss`
- **Descrição**: Endpoint alternativo de teste
- **Resposta**: 
```json
{
  "message": "Welcome to the Home Controller!"
}
```

#### Usuários (Em desenvolvimento)

**POST** `/api/v1/users`
- **Descrição**: Criar novo usuário
- **Body**: JSON com dados do usuário
- **Status**: 🚧 Em desenvolvimento

**PUT** `/api/v1/users/{id}`
- **Descrição**: Atualizar usuário existente
- **Parâmetros**: `id` - ID do usuário
- **Body**: JSON com dados a serem atualizados
- **Status**: 🚧 Em desenvolvimento

**DELETE** `/api/v1/users/{id}`
- **Descrição**: Remover usuário
- **Parâmetros**: `id` - ID do usuário
- **Status**: 🚧 Em desenvolvimento

### Formato de Resposta Padrão

Todas as respostas seguem o formato JSON:

```json
{
  "data": {},
  "message": "string",
  "status": 200
}
```

### Códigos de Status HTTP

- `200` - Sucesso
- `404` - Rota não encontrada
- `405` - Método HTTP não permitido
- `500` - Erro interno do servidor

### Exemplos de Uso

#### cURL

```bash
# GET request
curl -X GET http://localhost/api/v1/

# POST request
curl -X POST http://localhost/api/v1/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John Doe","email":"john@example.com"}'
```

#### PHP

```php
$ch = curl_init('http://localhost/api/v1/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
```

#### JavaScript (Fetch)

```javascript
fetch('http://localhost/api/v1/')
  .then(response => response.json())
  .then(data => console.log(data));
```

## 🔐 Autenticação JWT

A API utilizará JWT para autenticação stateless:

- Geração de tokens após login
- Validação de tokens em rotas protegidas
- Refresh tokens para renovação
- Expiração configurável

## 📝 Licença

Este projeto está sob a licença MIT.

## 👨‍💻 Autor

Desenvolvido como projeto de estudo de PHP moderno e arquitetura de software.
