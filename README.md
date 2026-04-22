# 🎓 Sistema Escolar

Sistema de gestão escolar desenvolvido em **PHP Puro**, seguindo boas práticas de programação e arquitetura.  
O projeto aplica **POO**, **MVC**, **SOLID**, **Singleton**, **JWT** e **Clean Code**, garantindo organização, escalabilidade e manutenção simplificada.  
A interface utiliza **Bootstrap 5**, garantindo um layout moderno e responsivo.

## 📌 Sobre o projeto

Sistema de gestão escolar desenvolvido para digitalizar e organizar processos acadêmicos, substituindo controles manuais e centralizando informações de alunos, professores e turmas.

O sistema permite gerenciamento completo da rotina escolar, com foco em organização, segurança e escalabilidade.

## 💡 Problema resolvido

Muitas instituições realizam o controle acadêmico de forma manual ou com sistemas pouco integrados, gerando inconsistências e dificuldade de acesso às informações.

Este sistema resolve esse problema ao centralizar dados e automatizar processos como controle de notas, frequência e gestão de turmas.
---

## 🚀 Tecnologias e Padrões Utilizados

- **PHP Puro** (sem frameworks externos)
- **Arquitetura MVC**
- **Princípios SOLID**
- **Padrão Singleton**
- **Autenticação JWT**
- **Bootstrap 5** (Front-end)
- **Clean Code**
- **Roteamento personalizado**

---

## 🚀 Diferenciais

- Arquitetura baseada em MVC com separação clara de responsabilidades
- Aplicação de princípios SOLID para maior manutenção e escalabilidade
- Sistema de autenticação stateless utilizando JWT
- Organização em camadas (Services, Repositories, Controllers)
- Estrutura preparada para crescimento do sistema

## 📂 Estrutura do Projeto

```plaintext
Sistema-Escolar/
│
├── Public/                  # Pasta pública para assets e index inicial
├── logs/                    # Arquivos de log do sistema
├── scripts/                 # Scripts auxiliares (migrações, seeds, etc.)
├── src/                     # Código-fonte principal
│   ├── Config/              # Configurações globais
│   ├── Controllers/         # Controladores (lógica entre View e Model)
│   ├── Interfaces/          # Interfaces para contratos de classes
│   ├── Jobs/                # Tarefas agendadas/processos em segundo plano
│   ├── Models/              # Modelos de dados
│   ├── Repositories/        # Classes de acesso ao banco de dados
│   ├── Request/             # Validação e tratamento de requisições
│   ├── Resources/Views/     # Arquivos de visualização (HTML/PHP)
│   ├── Routers/             # Definições de rotas
│   ├── Services/            # Regras de negócio e serviços externos
│   ├── Storage/             # Armazenamento de arquivos temporários
│   ├── Utils/               # Funções utilitárias
│   └── env/                 # Variáveis de ambiente
│
├── .gitignore               # Ignora arquivos desnecessários no Git
├── README.md                # Documentação do projeto
├── client.http              # Arquivos de teste HTTP
├── composer.json            # Dependências do PHP (Composer)
├── crontab                  # Tarefas agendadas
└── index.php                # Ponto de entrada do sistema
```

## 🛠 Funcionalidades

- Cadastro e gerenciamento de alunos, professores e turmas
- Controle de notas e frequência
- Módulo de autenticação com **JWT**
- Painel administrativo responsivo com **Bootstrap**
- Rotas dinâmicas e organizadas
- Logs para auditoria e depuração
- Configuração modular via `Config/` e `.env`

---

## ⚡ Como Executar o Projeto

### 1️⃣ Clonar o repositório

```bash
git clone https://github.com/mmacedos/sistema-escolar.git

DB_HOST=localhost
DB_NAME=sistema_escolar
DB_USER=root
DB_PASS=secret
JWT_SECRET=suachavesecreta

docker-compose up -d --build

Sistema:
👉 http://localhost:8000
phpMyAdmin:
👉 http://localhost:8080

📌 Rotas
Exemplo de algumas rotas disponíveis:

GET /alunos → Lista alunos

POST /alunos → Cadastra novo aluno

POST /login → Gera token JWT

GET /turmas → Lista turmas
```
