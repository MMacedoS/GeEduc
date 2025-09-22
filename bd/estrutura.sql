-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Tempo de geração: 11/08/2025 às 17:01
-- Versão do servidor: 8.0.41
-- Versão do PHP: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `escola_ist`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `arquivos`
--

CREATE TABLE `arquivos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome_original` varchar(100) NOT NULL,
  `ext_arquivo` varchar(5) NOT NULL,
  `path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `atividade`
--

CREATE TABLE `atividade` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `turma_disciplina_id` int NOT NULL,
  `tipo` enum('A-1','A-2','A-3','A-4','prova','portifolio','projeto','participacao') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'A-1',
  `valor` decimal(3,2) NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `aula`
--

CREATE TABLE `aula` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `turma_disciplina_id` int DEFAULT NULL,
  `dia_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `boletos`
--

CREATE TABLE `boletos` (
  `id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `mensalidade_id` int NOT NULL,
  `conta_bancaria_id` int NOT NULL,
  `codigo_barras` varchar(100) DEFAULT NULL,
  `pix` text,
  `data` date DEFAULT NULL,
  `boleto` text,
  `nosso_numero` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `data_pagamento` date DEFAULT NULL,
  `valor_pago` float DEFAULT NULL,
  `webhook` text,
  `valor` decimal(10,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carga_horaria`
--

CREATE TABLE `carga_horaria` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `carga` varchar(45) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contas_bancarias`
--

CREATE TABLE `contas_bancarias` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `convenio` varchar(45) NOT NULL,
  `agencia` varchar(10) NOT NULL,
  `conta` varchar(20) NOT NULL,
  `banco` varchar(100) NOT NULL,
  `codigo_banco` varchar(45) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `conteudos`
--

CREATE TABLE `conteudos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `disciplina_professor_id` int NOT NULL,
  `conteudo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos`
--

CREATE TABLE `contratos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `estudante_id` int NOT NULL,
  `document_id` varchar(100) DEFAULT NULL,
  `ano_letivo` varchar(4) DEFAULT NULL,
  `quantidade_assinaturas` int DEFAULT '0',
  `url_contrato` text,
  `conteudo` text,
  `url_contrato_assinado` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `coordenadores`
--

CREATE TABLE `coordenadores` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `pessoa_fisica_id` int NOT NULL,
  `graduacao` varchar(45) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `coordenador_as_turma`
--

CREATE TABLE `coordenador_as_turma` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `coordenador_id` int NOT NULL,
  `turma_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dias_da_semana`
--

CREATE TABLE `dias_da_semana` (
  `id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dia` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `horario` int NOT NULL,
  `turno` enum('matutino','vespertino','noturno','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `dias_letivos`
--

CREATE TABLE `dias_letivos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `data` date NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `evento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estudantes`
--

CREATE TABLE `estudantes` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `pessoa_fisica_id` int NOT NULL,
  `pessoa_contato_id` int DEFAULT NULL,
  `matricula` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estudante_mensalidade`
--

CREATE TABLE `estudante_mensalidade` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `plano_id` int DEFAULT NULL,
  `estudante_id` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `dia_mensalidade` int NOT NULL,
  `desconto` decimal(3,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `estudante_turma`
--

CREATE TABLE `estudante_turma` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `turma_id` int NOT NULL,
  `estudante_id` int NOT NULL,
  `ativo` tinyint DEFAULT '1',
  `ano_letivo` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `frequencias`
--

CREATE TABLE `frequencias` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `turma_disciplina_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `estudante_turma_id` int NOT NULL,
  `faltas` tinyint DEFAULT '1',
  `data` date DEFAULT NULL,
  `justificativa` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensalidades`
--

CREATE TABLE `mensalidades` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `estudante_mensalidade_id` int NOT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  `gerou_boleto` tinyint DEFAULT '0',
  `nosso_numero` varchar(20) DEFAULT NULL,
  `data_vencimento` date DEFAULT NULL,
  `dia_vencimento` int NOT NULL,
  `situacao` enum('pendente','pago','atrasado','cancelado') DEFAULT 'pendente',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `notas`
--

CREATE TABLE `notas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `atividade_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `estudante_turma_id` int NOT NULL,
  `nota` decimal(3,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nota_final`
--

CREATE TABLE `nota_final` (
  `id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `turma_disciplina_id` int NOT NULL,
  `estudante_turma_id` int NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `situacao` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ano_letivo` varchar(4) NOT NULL,
  `created_at` date DEFAULT NULL,
  `updated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `paralela`
--

CREATE TABLE `paralela` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `estudante_turma_id` int NOT NULL,
  `turma_disciplina_id` int NOT NULL,
  `periodo_id` int NOT NULL,
  `nota` decimal(3,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `periodo`
--

CREATE TABLE `periodo` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `periodo` int NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao`
--

CREATE TABLE `permissao` (
  `id` int NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(30) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `permissao_as_usuario`
--

CREATE TABLE `permissao_as_usuario` (
  `id` int NOT NULL,
  `permissao_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_contato`
--

CREATE TABLE `pessoa_contato` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `pessoa_fisica_id` int NOT NULL,
  `ativo` tinyint DEFAULT '1',
  `responsavel_legal` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pessoa_fisica`
--

CREATE TABLE `pessoa_fisica` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `usuario_id` int NOT NULL,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `endereco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `email` varchar(100) NOT NULL,
  `doc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tipo_doc` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'CPF',
  `nome_mae` varchar(45) DEFAULT NULL,
  `nome_pai` varchar(45) DEFAULT NULL,
  `genero` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `valor` decimal(7,2) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professores`
--

CREATE TABLE `professores` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `graduacao` varchar(100) NOT NULL,
  `pessoa_fisica_id` int NOT NULL,
  `matricula` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `professor_disciplina`
--

CREATE TABLE `professor_disciplina` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `professor_id` int NOT NULL,
  `disciplina_id` int NOT NULL,
  `ano_letivo` int NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `recuperacao`
--

CREATE TABLE `recuperacao` (
  `id` int NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `ano_letivo` int DEFAULT NULL,
  `nota` decimal(3,2) DEFAULT NULL,
  `turma_disciplina_id` int NOT NULL,
  `periodo` enum('I Semestre','II Semestre','Exames Finais') DEFAULT 'I Semestre',
  `estudante_turma_id` int NOT NULL,
  `obs` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `turmas`
--

CREATE TABLE `turmas` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `nome` varchar(45) NOT NULL,
  `turno` enum('matutino','vespertino','noturno','integral') NOT NULL DEFAULT 'matutino',
  `ordem` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `visivel` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `turma_disciplina`
--

CREATE TABLE `turma_disciplina` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `turma_id` int NOT NULL,
  `carga_horaria_id` int NOT NULL,
  `professor_disciplina_id` int NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `ano_letivo` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `arquivo_id` int DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `painel` enum('administrativo','secretaria','professor','estudante','comunicacao','coordenador','responsavel_legal') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'estudante',
  `ativo` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario_recuperar_senha`
--

CREATE TABLE `usuario_recuperar_senha` (
  `id` int NOT NULL,
  `uuid` char(36) NOT NULL,
  `antiga` varchar(100) NOT NULL,
  `nova` varchar(100) DEFAULT NULL,
  `usuario_id` int NOT NULL,
  `ativo` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `arquivos`
--
ALTER TABLE `arquivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`);

--
-- Índices de tabela `atividade`
--
ALTER TABLE `atividade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD UNIQUE KEY `turma_disciplina_tipo` (`tipo`,`turma_disciplina_id`),
  ADD KEY `fk_atividade_turma_disciplina1_idx` (`turma_disciplina_id`),
  ADD KEY `tipo` (`tipo`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `turma_disciplina_id` (`turma_disciplina_id`,`dia_id`),
  ADD KEY `dia_id` (`dia_id`);

--
-- Índices de tabela `boletos`
--
ALTER TABLE `boletos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_2` (`uuid`),
  ADD KEY `fk_boletos_mensalidades1_idx` (`mensalidade_id`),
  ADD KEY `fk_boletos_contas_bancarias1_idx` (`conta_bancaria_id`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `nosso_numero` (`nosso_numero`),
  ADD KEY `data_pagamento` (`data_pagamento`);

--
-- Índices de tabela `carga_horaria`
--
ALTER TABLE `carga_horaria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`);

--
-- Índices de tabela `contas_bancarias`
--
ALTER TABLE `contas_bancarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `conteudos`
--
ALTER TABLE `conteudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conteudo_to_disciplina_professor` (`disciplina_professor_id`);

--
-- Índices de tabela `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_contratos_estudantes1_idx` (`estudante_id`);

--
-- Índices de tabela `coordenadores`
--
ALTER TABLE `coordenadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_coordenacao_pessoa_fisica1_idx` (`pessoa_fisica_id`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `coordenador_as_turma`
--
ALTER TABLE `coordenador_as_turma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `fk_coodenador_as_turmas` (`coordenador_id`),
  ADD KEY `fk_turma_as_coordenador` (`turma_id`);

--
-- Índices de tabela `dias_da_semana`
--
ALTER TABLE `dias_da_semana`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `dia` (`dia`,`horario`,`turno`) USING BTREE;

--
-- Índices de tabela `dias_letivos`
--
ALTER TABLE `dias_letivos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data` (`data`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Índices de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `disciplina` (`ativo`,`nome`),
  ADD KEY `uuid_2` (`uuid`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `estudantes`
--
ALTER TABLE `estudantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `fk_estudante_pessoa_fisica` (`pessoa_fisica_id`),
  ADD KEY `matricula` (`matricula`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `fk_estudante_pessoa_contato` (`pessoa_contato_id`);

--
-- Índices de tabela `estudante_mensalidade`
--
ALTER TABLE `estudante_mensalidade`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_estudante_mensalidade_planos1_idx` (`plano_id`),
  ADD KEY `fk_estudante_mensalidade_estudantes1_idx` (`estudante_id`);

--
-- Índices de tabela `estudante_turma`
--
ALTER TABLE `estudante_turma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `turma_estudante_ano_letivo` (`turma_id`,`estudante_id`,`ano_letivo`,`ativo`),
  ADD UNIQUE KEY `unicos` (`ano_letivo`,`estudante_id`,`ativo`),
  ADD KEY `fk_turma_estudante_turmas1_idx` (`turma_id`),
  ADD KEY `fk_turma_estudante_estudantes1_idx` (`estudante_id`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `ano_letivo` (`ano_letivo`);

--
-- Índices de tabela `frequencias`
--
ALTER TABLE `frequencias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_frequencias_turma_estudante1_idx` (`estudante_turma_id`),
  ADD KEY `fk_frequencias_periodo1_idx` (`periodo_id`),
  ADD KEY `fk_frequencias_turma_disciplina_idx` (`turma_disciplina_id`) USING BTREE;

--
-- Índices de tabela `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mensalidade_uniq` (`estudante_mensalidade_id`,`data_vencimento`,`situacao`,`valor`),
  ADD KEY `fk_mensalidades_estudante_mensalidade1_idx` (`estudante_mensalidade_id`),
  ADD KEY `nosso_numero` (`nosso_numero`);

--
-- Índices de tabela `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD UNIQUE KEY `atividade_periodo_estudante_turma_nota` (`atividade_id`,`periodo_id`,`estudante_turma_id`,`nota`),
  ADD KEY `fk_notas_periodo1_idx` (`periodo_id`),
  ADD KEY `fk_notas_turma_estudante1_idx` (`estudante_turma_id`),
  ADD KEY `fk_notas_atividade1_idx` (`atividade_id`),
  ADD KEY `periodo_id` (`periodo_id`);

--
-- Índices de tabela `nota_final`
--
ALTER TABLE `nota_final`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_nota_final_turma_disciplina1_idx` (`turma_disciplina_id`),
  ADD KEY `fk_nota_final_turma_estudante1_idx` (`estudante_turma_id`);

--
-- Índices de tabela `paralela`
--
ALTER TABLE `paralela`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `turma_periodo_estudante` (`estudante_turma_id`,`turma_disciplina_id`,`periodo_id`),
  ADD KEY `fk_paralela_to_periodo` (`periodo_id`),
  ADD KEY `fk_tuma_disciplina_to_paralela` (`turma_disciplina_id`);

--
-- Índices de tabela `periodo`
--
ALTER TABLE `periodo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD UNIQUE KEY `periodo` (`periodo`);

--
-- Índices de tabela `permissao`
--
ALTER TABLE `permissao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_2` (`name`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `name` (`name`);

--
-- Índices de tabela `permissao_as_usuario`
--
ALTER TABLE `permissao_as_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_permissao` (`permissao_id`),
  ADD KEY `fk_ussuario` (`usuario_id`);

--
-- Índices de tabela `pessoa_contato`
--
ALTER TABLE `pessoa_contato`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pessoa_contato_pessoa_fisica1_idx` (`pessoa_fisica_id`);

--
-- Índices de tabela `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `dados_unicos` (`usuario_id`,`nome`,`email`,`doc`),
  ADD KEY `fk_pessoa_fisica_usuario` (`usuario_id`),
  ADD KEY `ativo` (`ativo`) USING BTREE,
  ADD KEY `data_nascimento` (`data_nascimento`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `nome` (`nome`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `professores`
--
ALTER TABLE `professores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `fk_professores_pessoa_fisica` (`pessoa_fisica_id`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `professor_disciplina`
--
ALTER TABLE `professor_disciplina`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `disciplina_professor` (`professor_id`,`disciplina_id`),
  ADD UNIQUE KEY `professor_disciplina_ano_letivo` (`ano_letivo`,`ativo`,`disciplina_id`,`professor_id`),
  ADD KEY `fk_disciplina_professor_to_professores` (`professor_id`),
  ADD KEY `fk_disciplina_professor_to_disciplina` (`disciplina_id`),
  ADD KEY `ano_letivo` (`ano_letivo`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `recuperacao`
--
ALTER TABLE `recuperacao`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unicos` (`ano_letivo`,`turma_disciplina_id`,`periodo`,`estudante_turma_id`),
  ADD KEY `fk_recuperacao_estudante_turma1_idx` (`estudante_turma_id`),
  ADD KEY `fk_recuperacao_turma_disciplina1_idx` (`turma_disciplina_id`);

--
-- Índices de tabela `turmas`
--
ALTER TABLE `turmas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `turma` (`nome`,`ordem`,`turno`),
  ADD KEY `ordem` (`ordem`),
  ADD KEY `ativo` (`ativo`);

--
-- Índices de tabela `turma_disciplina`
--
ALTER TABLE `turma_disciplina`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD UNIQUE KEY `professor_disciplina` (`turma_id`,`ano_letivo`,`professor_disciplina_id`,`ativo`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `fk_turma_disciplina_disciplina_professor` (`professor_disciplina_id`),
  ADD KEY `fk_turma_disciplina_to_carga_horaria` (`carga_horaria_id`),
  ADD KEY `fk_turma_disciplina_to_turma` (`turma_id`),
  ADD KEY `ano_letivo` (`ano_letivo`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_2` (`uuid`),
  ADD KEY `arquivo_id` (`arquivo_id`),
  ADD KEY `uuid` (`uuid`),
  ADD KEY `painel` (`painel`),
  ADD KEY `ativo` (`ativo`),
  ADD KEY `email` (`email`);

--
-- Índices de tabela `usuario_recuperar_senha`
--
ALTER TABLE `usuario_recuperar_senha`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid_UNIQUE` (`uuid`),
  ADD KEY `fk_recover_senha_usuarios1_idx` (`usuario_id`),
  ADD KEY `ativo` (`ativo`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `arquivos`
--
ALTER TABLE `arquivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `atividade`
--
ALTER TABLE `atividade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aula`
--
ALTER TABLE `aula`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `boletos`
--
ALTER TABLE `boletos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carga_horaria`
--
ALTER TABLE `carga_horaria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contas_bancarias`
--
ALTER TABLE `contas_bancarias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `conteudos`
--
ALTER TABLE `conteudos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coordenadores`
--
ALTER TABLE `coordenadores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `coordenador_as_turma`
--
ALTER TABLE `coordenador_as_turma`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dias_da_semana`
--
ALTER TABLE `dias_da_semana`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `dias_letivos`
--
ALTER TABLE `dias_letivos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estudantes`
--
ALTER TABLE `estudantes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estudante_mensalidade`
--
ALTER TABLE `estudante_mensalidade`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `estudante_turma`
--
ALTER TABLE `estudante_turma`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `frequencias`
--
ALTER TABLE `frequencias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mensalidades`
--
ALTER TABLE `mensalidades`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nota_final`
--
ALTER TABLE `nota_final`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `paralela`
--
ALTER TABLE `paralela`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `periodo`
--
ALTER TABLE `periodo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissao`
--
ALTER TABLE `permissao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `permissao_as_usuario`
--
ALTER TABLE `permissao_as_usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_contato`
--
ALTER TABLE `pessoa_contato`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professores`
--
ALTER TABLE `professores`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `professor_disciplina`
--
ALTER TABLE `professor_disciplina`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `recuperacao`
--
ALTER TABLE `recuperacao`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turmas`
--
ALTER TABLE `turmas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turma_disciplina`
--
ALTER TABLE `turma_disciplina`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario_recuperar_senha`
--
ALTER TABLE `usuario_recuperar_senha`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `atividade`
--
ALTER TABLE `atividade`
  ADD CONSTRAINT `fk_atividade_turma_disciplina1` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`);

--
-- Restrições para tabelas `aula`
--
ALTER TABLE `aula`
  ADD CONSTRAINT `aula_ibfk_1` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `aula_ibfk_2` FOREIGN KEY (`dia_id`) REFERENCES `dias_da_semana` (`id`);

--
-- Restrições para tabelas `boletos`
--
ALTER TABLE `boletos`
  ADD CONSTRAINT `fk_boletos_contas_bancarias1` FOREIGN KEY (`conta_bancaria_id`) REFERENCES `contas_bancarias` (`id`),
  ADD CONSTRAINT `fk_boletos_mensalidades1` FOREIGN KEY (`mensalidade_id`) REFERENCES `mensalidades` (`id`);

--
-- Restrições para tabelas `conteudos`
--
ALTER TABLE `conteudos`
  ADD CONSTRAINT `fk_conteudo_to_disciplina_professor` FOREIGN KEY (`disciplina_professor_id`) REFERENCES `professor_disciplina` (`id`);

--
-- Restrições para tabelas `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `fk_contratos_estudantes1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`);

--
-- Restrições para tabelas `coordenadores`
--
ALTER TABLE `coordenadores`
  ADD CONSTRAINT `fk_coordenadores_to_pessoa_fisica` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `pessoa_fisica` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `coordenador_as_turma`
--
ALTER TABLE `coordenador_as_turma`
  ADD CONSTRAINT `fk_coodenador_as_turmas` FOREIGN KEY (`coordenador_id`) REFERENCES `coordenadores` (`id`),
  ADD CONSTRAINT `fk_turma_as_coordenador` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `estudantes`
--
ALTER TABLE `estudantes`
  ADD CONSTRAINT `fk_estudante_pessoa_contato` FOREIGN KEY (`pessoa_contato_id`) REFERENCES `pessoa_contato` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_estudante_pessoa_fisica` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `pessoa_fisica` (`id`);

--
-- Restrições para tabelas `estudante_mensalidade`
--
ALTER TABLE `estudante_mensalidade`
  ADD CONSTRAINT `fk_estudante_mensalidade_estudantes1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`),
  ADD CONSTRAINT `fk_estudante_mensalidade_planos1` FOREIGN KEY (`plano_id`) REFERENCES `planos` (`id`);

--
-- Restrições para tabelas `estudante_turma`
--
ALTER TABLE `estudante_turma`
  ADD CONSTRAINT `fk_turma_estudante_estudantes1` FOREIGN KEY (`estudante_id`) REFERENCES `estudantes` (`id`),
  ADD CONSTRAINT `fk_turma_estudante_turmas1` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`);

--
-- Restrições para tabelas `frequencias`
--
ALTER TABLE `frequencias`
  ADD CONSTRAINT `fk_frequencia_turma_disciplina` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_frequencias_bimestres1` FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`),
  ADD CONSTRAINT `fk_frequencias_turma_estudante1` FOREIGN KEY (`estudante_turma_id`) REFERENCES `estudante_turma` (`id`);

--
-- Restrições para tabelas `mensalidades`
--
ALTER TABLE `mensalidades`
  ADD CONSTRAINT `fk_estudante_mensalidades_to_estudantes` FOREIGN KEY (`estudante_mensalidade_id`) REFERENCES `estudante_mensalidade` (`id`);

--
-- Restrições para tabelas `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `fk_notas_atividade1` FOREIGN KEY (`atividade_id`) REFERENCES `atividade` (`id`),
  ADD CONSTRAINT `fk_notas_bimestres1` FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`),
  ADD CONSTRAINT `fk_notas_turma_estudante1` FOREIGN KEY (`estudante_turma_id`) REFERENCES `estudante_turma` (`id`);

--
-- Restrições para tabelas `nota_final`
--
ALTER TABLE `nota_final`
  ADD CONSTRAINT `fk_nota_final_to_turma_disciplina` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_nota_final_to_turma_estudante` FOREIGN KEY (`estudante_turma_id`) REFERENCES `estudante_turma` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `paralela`
--
ALTER TABLE `paralela`
  ADD CONSTRAINT `fk_paralela_to_estudante_turma` FOREIGN KEY (`estudante_turma_id`) REFERENCES `estudante_turma` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_paralela_to_period` FOREIGN KEY (`periodo_id`) REFERENCES `periodo` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_paralela_to_turma_disciplina` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `pessoa_contato`
--
ALTER TABLE `pessoa_contato`
  ADD CONSTRAINT `fk_pessoa_contato_pessoa_fisica` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `pessoa_fisica` (`id`);

--
-- Restrições para tabelas `pessoa_fisica`
--
ALTER TABLE `pessoa_fisica`
  ADD CONSTRAINT `fk_pessoa_to_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `professores`
--
ALTER TABLE `professores`
  ADD CONSTRAINT `fk_professor_pessoa_fisica` FOREIGN KEY (`pessoa_fisica_id`) REFERENCES `pessoa_fisica` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `professor_disciplina`
--
ALTER TABLE `professor_disciplina`
  ADD CONSTRAINT `fk_professor_disciplina_to_disciplina` FOREIGN KEY (`disciplina_id`) REFERENCES `disciplinas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_professor_disciplina_to_professor` FOREIGN KEY (`professor_id`) REFERENCES `professores` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Restrições para tabelas `recuperacao`
--
ALTER TABLE `recuperacao`
  ADD CONSTRAINT `fk_recuperacao_estudante_turma1` FOREIGN KEY (`estudante_turma_id`) REFERENCES `estudante_turma` (`id`),
  ADD CONSTRAINT `fk_recuperacao_turma_disciplina1` FOREIGN KEY (`turma_disciplina_id`) REFERENCES `turma_disciplina` (`id`);

--
-- Restrições para tabelas `turma_disciplina`
--
ALTER TABLE `turma_disciplina`
  ADD CONSTRAINT `fk_turma_disciplina_to_carga_horaria` FOREIGN KEY (`carga_horaria_id`) REFERENCES `carga_horaria` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_turma_disciplina_to_professor_disciplina` FOREIGN KEY (`professor_disciplina_id`) REFERENCES `professor_disciplina` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_turma_disciplina_to_turma` FOREIGN KEY (`turma_id`) REFERENCES `turmas` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
