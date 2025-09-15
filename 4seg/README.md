# API de Autenticação Segura com Laravel

Este projeto é uma API de autenticação desenvolvida em Laravel seguindo boas práticas de segurança, arquitetura DDD e com suporte a autenticação JWT e verificação em dois fatores (2FA).

---

## 🚀 Funcionalidades

-   Registro de usuários com validação forte de senha
-   Login com JWT
-   Verificação de código 2FA
-   Logout
-   Middleware de throttle para prevenir ataques de força bruta
-   Armazenamento do IP do usuário
-   Arquitetura baseada em serviços e repositórios

---

## 🛠️ Tecnologias Utilizadas

-   PHP 8+
-   Laravel 10
-   JWT via [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
-   Arquitetura DDD
-   Validação com Laravel Validator
-   2FA manual via código (básico)
-   Throttle (proteção contra força bruta)

---

## 📁 Estrutura do Projeto

app/
├── Application/
│ └── Services/
│ └── AuthService.php
├── Domain/
│ └── User/
│ └── Repositories/
│ └── UserRepositoryInterface.php
├── Infrastructure/
│ └── Repositories/
│ └── UserRepository.php
├── Http/
│ └── Controllers/
│ └── API/
│ └── AuthController.php

---

## 📌 Requisitos

-   PHP >= 8.1
-   Composer
-   MySQL ou PostgreSQL
-   Laravel 10

---

## ⚙️ Instalação

```bash
git clone https://github.com/seu-usuario/nome-do-repo.git
cd nome-do-repo
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate

🔐 Rotas da API

| Método | Rota           | Descrição                            | Middleware     |
| ------ | -------------- | ------------------------------------ | -------------- |
| POST   | `/register`    | Registra novo usuário                | `throttle:5,1` |
| POST   | `/login`       | Login com email e senha              | `throttle:5,1` |
| POST   | `/verify-code` | Verifica código 2FA                  | -              |
| GET    | `/user`        | Retorna dados do usuário autenticado | `auth:api`     |
| POST   | `/logout`      | Logout do usuário                    | `auth:api`     |



✅ Validação do Registro
name: obrigatório, texto, máximo 255 caracteres

email: obrigatório, válido e único

password: obrigatório, mínimo 8 caracteres e deve conter:

Uma letra maiúscula

Uma letra minúscula

Um número

Um caractere especial

password_confirmation: deve coincidir com a senha


🛡️ Segurança
SQL Injection: protegido por Eloquent e validação

Força Bruta: protegido com middleware throttle

JWT Token: usado para autenticação de rotas protegidas

2FA: verificação adicional por código manual

IP Tracking: IP do usuário salvo no cadastro

🧪 Testes
Você pode usar ferramentas como Postman ou Insomnia para testar a API.

Exemplo de Payload para /register

{
  "name": "Andrey Barros",
  "email": "4seg12@tav5.com",
  "password": "SenhaSegura123!",
  "password_confirmation": "SenhaSegura123!"
}


👨‍💻 Autor
Andrey Barros
https://www.linkedin.com/in/andrey-barros-243114201/
Desenvolvedor Backend | Estudante de ADS | Apaixonado por segurança e boas práticas

```
