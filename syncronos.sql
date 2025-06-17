-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 17/06/2025 às 18:29
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `syncronos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

CREATE TABLE `agenda` (
  `idAgenda` int(11) NOT NULL,
  `Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `idAgendamento` int(11) NOT NULL,
  `idUsuario` int(11) DEFAULT NULL,
  `idServico` int(11) DEFAULT NULL,
  `idProfissional` int(11) DEFAULT NULL,
  `Titulo` varchar(220) NOT NULL,
  `Cor` varchar(45) NOT NULL,
  `dataInicio` datetime NOT NULL,
  `dataFim` datetime NOT NULL,
  `Status` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`idAgendamento`, `idUsuario`, `idServico`, `idProfissional`, `Titulo`, `Cor`, `dataInicio`, `dataFim`, `Status`) VALUES
(22, NULL, NULL, NULL, 'Cabelo', '#3a8d60', '2025-06-18 10:00:00', '2025-06-18 14:00:00', 'confirmado'),
(23, 5, 17, 20, 'Corte', '#3a8d60', '2025-06-17 14:30:00', '2025-06-17 15:00:00', 'confirmado'),
(24, 5, 18, 20, 'Corte de Cabelo e Sobrancelha', '#3a8d60', '2025-06-19 22:00:00', '2025-06-19 22:30:00', 'confirmado'),
(25, 5, 17, 20, 'Corte', '#3a8d60', '2025-06-21 21:00:00', '2025-06-21 21:30:00', 'confirmado'),
(27, 7, 18, 20, 'Corte de Cabelo e Sobrancelha', '#3a8d60', '2025-06-23 21:30:00', '2025-06-23 22:00:00', 'confirmado'),
(32, 7, 17, 20, 'Corte', '#3a8d60', '2025-06-19 18:30:00', '2025-06-19 19:00:00', 'Confirmado'),
(33, 7, 17, 20, 'Corte', '#3a8d60', '2025-06-20 19:00:00', '2025-06-20 19:30:00', 'Confirmado'),
(34, 5, 20, 21, 'Barboterapia', '#3a8d60', '2025-06-18 15:30:00', '2025-06-18 16:00:00', 'Confirmado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `expediente`
--

CREATE TABLE `expediente` (
  `idExpediente` int(11) NOT NULL,
  `idProfissional` int(11) DEFAULT NULL,
  `diaSemana` varchar(20) DEFAULT NULL,
  `inicioExpediente` time DEFAULT NULL,
  `inicioIntervalo` time DEFAULT NULL,
  `fimIntervalo` time DEFAULT NULL,
  `fimExpediente` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `expediente`
--

INSERT INTO `expediente` (`idExpediente`, `idProfissional`, `diaSemana`, `inicioExpediente`, `inicioIntervalo`, `fimIntervalo`, `fimExpediente`) VALUES
(11, 20, 'segunda', '08:00:00', '12:00:00', '13:00:00', '22:00:00'),
(12, 20, 'terca', '08:00:00', '12:00:00', '13:00:00', '22:00:00'),
(13, 20, 'quarta', '08:00:00', '12:00:00', '13:00:00', '22:00:00'),
(14, 20, 'quinta', '08:00:00', '12:00:00', '13:00:00', '22:00:00'),
(15, 20, 'sexta', '08:00:00', '12:00:00', '13:00:00', '22:00:00'),
(16, 20, 'sabado', '08:00:00', '12:00:00', '13:00:00', '17:00:00'),
(17, 21, 'terca', '10:00:00', '12:00:00', '13:00:00', '17:00:00'),
(18, 21, 'quarta', '10:00:00', '12:00:00', '13:00:00', '17:00:00'),
(19, 21, 'quinta', '10:00:00', '12:00:00', '13:00:00', '17:00:00'),
(20, 21, 'sexta', '10:00:00', '12:00:00', '13:00:00', '17:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios`
--

CREATE TABLE `horarios` (
  `idHorario` int(11) NOT NULL,
  `idAgenda` int(11) DEFAULT NULL,
  `horaInicio` time DEFAULT NULL,
  `horaFim` time DEFAULT NULL,
  `diaSemana` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horarios`
--

INSERT INTO `horarios` (`idHorario`, `idAgenda`, `horaInicio`, `horaFim`, `diaSemana`) VALUES
(31, NULL, '12:00:00', '19:00:00', 'segunda');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentos`
--

CREATE TABLE `pagamentos` (
  `idPagamento` int(11) NOT NULL,
  `idAgendamento` int(11) DEFAULT NULL,
  `valorTotal` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `profissional`
--

CREATE TABLE `profissional` (
  `idProfissional` int(11) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `Especialidade` varchar(30) DEFAULT NULL,
  `Celular` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `profissional`
--

INSERT INTO `profissional` (`idProfissional`, `Nome`, `Especialidade`, `Celular`) VALUES
(20, 'Jobber', 'Barbeiro', ''),
(21, 'Rodrigo', 'Barbeiro', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `idServico` int(11) NOT NULL,
  `Nome` varchar(30) NOT NULL,
  `Descricao` varchar(255) DEFAULT NULL,
  `Preco` decimal(10,2) DEFAULT NULL,
  `Duracao` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`idServico`, `Nome`, `Descricao`, `Preco`, `Duracao`) VALUES
(17, 'Corte Tradicional', 'Corte normal', 30.00, '30 Min'),
(18, 'Corte de Cabelo e Sobrancelha', 'Corte seu cabelo, alinhe e limpe sua sobrancelha na navalha.', 55.00, '40 Min'),
(19, 'Corte Navalhado', 'Corte navalhado é aquele corte degradê na zero + navalha', 45.00, '30 Min'),
(20, 'Barboterapia', 'É um procedimento que tem como objetivo promover o relaxamento através da toalha quente', 40.00, '30 Min');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idUsuario` int(11) NOT NULL,
  `Nome` varchar(20) NOT NULL,
  `CPF` varchar(14) NOT NULL,
  `Telefone` varchar(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Senha` varchar(30) DEFAULT NULL,
  `Tipo` enum('cliente','administrador','','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`idUsuario`, `Nome`, `CPF`, `Telefone`, `Email`, `Senha`, `Tipo`) VALUES
(5, 'Teste', '12345678911', '11996028465', 'teste@teste.com', '123456', 'cliente'),
(6, 'Admin', '11111111111', '11111111111', 'admin@admin.com', 'admin', 'administrador'),
(7, 'Rodolfo Pereira', '12345678567', '18998543245', 'rodolfo@gmail.com', 'rodolfo', 'cliente');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`idAgenda`);

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`idAgendamento`),
  ADD KEY `idUsuario` (`idUsuario`),
  ADD KEY `idServico` (`idServico`),
  ADD KEY `idProfissional` (`idProfissional`);

--
-- Índices de tabela `expediente`
--
ALTER TABLE `expediente`
  ADD PRIMARY KEY (`idExpediente`),
  ADD KEY `idProfissional` (`idProfissional`);

--
-- Índices de tabela `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`idHorario`),
  ADD KEY `idAgenda` (`idAgenda`);

--
-- Índices de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD PRIMARY KEY (`idPagamento`),
  ADD KEY `idAgendamento` (`idAgendamento`);

--
-- Índices de tabela `profissional`
--
ALTER TABLE `profissional`
  ADD PRIMARY KEY (`idProfissional`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`idServico`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idUsuario`),
  ADD UNIQUE KEY `CPF` (`CPF`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agenda`
--
ALTER TABLE `agenda`
  MODIFY `idAgenda` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `idAgendamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de tabela `expediente`
--
ALTER TABLE `expediente`
  MODIFY `idExpediente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `horarios`
--
ALTER TABLE `horarios`
  MODIFY `idHorario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT de tabela `pagamentos`
--
ALTER TABLE `pagamentos`
  MODIFY `idPagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `profissional`
--
ALTER TABLE `profissional`
  MODIFY `idProfissional` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `idServico` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idUsuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`idUsuario`) REFERENCES `usuarios` (`idUsuario`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`idServico`) REFERENCES `servicos` (`idServico`),
  ADD CONSTRAINT `agendamentos_ibfk_3` FOREIGN KEY (`idProfissional`) REFERENCES `profissional` (`idProfissional`);

--
-- Restrições para tabelas `expediente`
--
ALTER TABLE `expediente`
  ADD CONSTRAINT `expediente_ibfk_1` FOREIGN KEY (`idProfissional`) REFERENCES `profissional` (`idProfissional`);

--
-- Restrições para tabelas `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`idAgenda`) REFERENCES `agenda` (`idAgenda`);

--
-- Restrições para tabelas `pagamentos`
--
ALTER TABLE `pagamentos`
  ADD CONSTRAINT `pagamentos_ibfk_1` FOREIGN KEY (`idAgendamento`) REFERENCES `agendamentos` (`idAgendamento`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
