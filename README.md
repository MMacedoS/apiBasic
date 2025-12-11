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

## ✨ Funcionalidades Planejadas

- [x] Sistema de roteamento customizado
- [x] Container de injeção de dependências (DI)
- [ ] Autenticação JWT
- [ ] Middleware de autenticação
- [ ] CRUD de recursos
- [ ] Validação de dados
- [ ] Tratamento de erros
- [ ] Documentação da API

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
