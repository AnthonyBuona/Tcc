-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 14-Nov-2025 às 15:47
-- Versão do servidor: 10.4.21-MariaDB
-- versão do PHP: 7.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `tcc`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `alocacao_prof`
--

CREATE TABLE `alocacao_prof` (
  `id_aloc` int(11) NOT NULL,
  `id_comp` int(11) NOT NULL,
  `id_prof` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `aluno`
--

CREATE TABLE `aluno` (
  `id_aluno` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(14) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `status_aprovacao` varchar(15) NOT NULL DEFAULT 'PENDENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `aluno`
--

INSERT INTO `aluno` (`id_aluno`, `nome`, `cpf`, `senha`, `id_serie`, `id_turma`, `status_aprovacao`) VALUES
(26, 'Aluno_Linguagens_1_1', '10000001', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(27, 'Aluno_Linguagens_1_2', '10000002', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(28, 'Aluno_Linguagens_1_3', '10000003', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(29, 'Aluno_Linguagens_1_4', '10000004', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(30, 'Aluno_Linguagens_1_5', '10000005', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(31, 'Aluno_Linguagens_1_6', '10000006', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(32, 'Aluno_Linguagens_1_7', '10000007', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(33, 'Aluno_Linguagens_1_8', '10000008', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(34, 'Aluno_Linguagens_1_9', '10000009', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(35, 'Aluno_Linguagens_1_10', '10000010', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(36, 'Aluno_Linguagens_1_11', '10000011', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(37, 'Aluno_Linguagens_1_12', '10000012', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(38, 'Aluno_Linguagens_1_13', '10000013', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(39, 'Aluno_Linguagens_1_14', '10000014', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(40, 'Aluno_Linguagens_1_15', '10000015', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(41, 'Aluno_Linguagens_1_16', '10000016', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(42, 'Aluno_Linguagens_1_17', '10000017', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(43, 'Aluno_Linguagens_1_18', '10000018', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(44, 'Aluno_Linguagens_1_19', '10000019', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(45, 'Aluno_Linguagens_1_20', '10000020', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(46, 'Aluno_Linguagens_1_21', '10000021', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(47, 'Aluno_Linguagens_1_22', '10000022', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(48, 'Aluno_Linguagens_1_23', '10000023', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(49, 'Aluno_Linguagens_1_24', '10000024', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(50, 'Aluno_Linguagens_1_25', '10000025', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(51, 'Aluno_Linguagens_1_26', '10000026', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(52, 'Aluno_Linguagens_1_27', '10000027', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(53, 'Aluno_Linguagens_1_28', '10000028', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(54, 'Aluno_Linguagens_1_29', '10000029', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(55, 'Aluno_Linguagens_1_30', '10000030', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(56, 'Aluno_Linguagens_1_31', '10000031', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(57, 'Aluno_Linguagens_1_32', '10000032', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(58, 'Aluno_Linguagens_1_33', '10000033', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(59, 'Aluno_Linguagens_1_34', '10000034', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(60, 'Aluno_Linguagens_1_35', '10000035', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(61, 'Aluno_Linguagens_1_36', '10000036', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(62, 'Aluno_Linguagens_1_37', '10000037', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(63, 'Aluno_Linguagens_1_38', '10000038', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(64, 'Aluno_Linguagens_1_39', '10000039', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(65, 'Aluno_Linguagens_1_40', '10000040', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 1, 'APROVADO'),
(66, 'Aluno_Linguagens_2_1', '10000041', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(67, 'Aluno_Linguagens_2_2', '10000042', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(68, 'Aluno_Linguagens_2_3', '10000043', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(69, 'Aluno_Linguagens_2_4', '10000044', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(70, 'Aluno_Linguagens_2_5', '10000045', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(71, 'Aluno_Linguagens_2_6', '10000046', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(72, 'Aluno_Linguagens_2_7', '10000047', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(73, 'Aluno_Linguagens_2_8', '10000048', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(74, 'Aluno_Linguagens_2_9', '10000049', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(75, 'Aluno_Linguagens_2_10', '10000050', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(76, 'Aluno_Linguagens_2_11', '10000051', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(77, 'Aluno_Linguagens_2_12', '10000052', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(78, 'Aluno_Linguagens_2_13', '10000053', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(79, 'Aluno_Linguagens_2_14', '10000054', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(80, 'Aluno_Linguagens_2_15', '10000055', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(81, 'Aluno_Linguagens_2_16', '10000056', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(82, 'Aluno_Linguagens_2_17', '10000057', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(83, 'Aluno_Linguagens_2_18', '10000058', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(84, 'Aluno_Linguagens_2_19', '10000059', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(85, 'Aluno_Linguagens_2_20', '10000060', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(86, 'Aluno_Linguagens_2_21', '10000061', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(87, 'Aluno_Linguagens_2_22', '10000062', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(88, 'Aluno_Linguagens_2_23', '10000063', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(89, 'Aluno_Linguagens_2_24', '10000064', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(90, 'Aluno_Linguagens_2_25', '10000065', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(91, 'Aluno_Linguagens_2_26', '10000066', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(92, 'Aluno_Linguagens_2_27', '10000067', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(93, 'Aluno_Linguagens_2_28', '10000068', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(94, 'Aluno_Linguagens_2_29', '10000069', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(95, 'Aluno_Linguagens_2_30', '10000070', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(96, 'Aluno_Linguagens_2_31', '10000071', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(97, 'Aluno_Linguagens_2_32', '10000072', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(98, 'Aluno_Linguagens_2_33', '10000073', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(99, 'Aluno_Linguagens_2_34', '10000074', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(100, 'Aluno_Linguagens_2_35', '10000075', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(101, 'Aluno_Linguagens_2_36', '10000076', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(102, 'Aluno_Linguagens_2_37', '10000077', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(103, 'Aluno_Linguagens_2_38', '10000078', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(104, 'Aluno_Linguagens_2_39', '10000079', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(105, 'Aluno_Linguagens_2_40', '10000080', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 2, 'APROVADO'),
(106, 'Aluno_Linguagens_3_1', '10000081', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(107, 'Aluno_Linguagens_3_2', '10000082', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(108, 'Aluno_Linguagens_3_3', '10000083', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(109, 'Aluno_Linguagens_3_4', '10000084', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(110, 'Aluno_Linguagens_3_5', '10000085', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(111, 'Aluno_Linguagens_3_6', '10000086', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(112, 'Aluno_Linguagens_3_7', '10000087', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(113, 'Aluno_Linguagens_3_8', '10000088', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(114, 'Aluno_Linguagens_3_9', '10000089', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(115, 'Aluno_Linguagens_3_10', '10000090', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(116, 'Aluno_Linguagens_3_11', '10000091', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(117, 'Aluno_Linguagens_3_12', '10000092', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(118, 'Aluno_Linguagens_3_13', '10000093', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(119, 'Aluno_Linguagens_3_14', '10000094', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(120, 'Aluno_Linguagens_3_15', '10000095', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(121, 'Aluno_Linguagens_3_16', '10000096', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(122, 'Aluno_Linguagens_3_17', '10000097', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(123, 'Aluno_Linguagens_3_18', '10000098', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(124, 'Aluno_Linguagens_3_19', '10000099', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(125, 'Aluno_Linguagens_3_20', '10000100', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(126, 'Aluno_Linguagens_3_21', '10000101', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(127, 'Aluno_Linguagens_3_22', '10000102', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(128, 'Aluno_Linguagens_3_23', '10000103', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(129, 'Aluno_Linguagens_3_24', '10000104', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(130, 'Aluno_Linguagens_3_25', '10000105', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(131, 'Aluno_Linguagens_3_26', '10000106', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(132, 'Aluno_Linguagens_3_27', '10000107', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(133, 'Aluno_Linguagens_3_28', '10000108', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(134, 'Aluno_Linguagens_3_29', '10000109', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(135, 'Aluno_Linguagens_3_30', '10000110', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(136, 'Aluno_Linguagens_3_31', '10000111', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(137, 'Aluno_Linguagens_3_32', '10000112', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(138, 'Aluno_Linguagens_3_33', '10000113', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(139, 'Aluno_Linguagens_3_34', '10000114', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(140, 'Aluno_Linguagens_3_35', '10000115', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(141, 'Aluno_Linguagens_3_36', '10000116', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(142, 'Aluno_Linguagens_3_37', '10000117', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(143, 'Aluno_Linguagens_3_38', '10000118', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(144, 'Aluno_Linguagens_3_39', '10000119', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(145, 'Aluno_Linguagens_3_40', '10000120', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 3, 'APROVADO'),
(146, 'Aluno_Mecatronica_1_1', '10000121', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(147, 'Aluno_Mecatronica_1_2', '10000122', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(148, 'Aluno_Mecatronica_1_3', '10000123', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(149, 'Aluno_Mecatronica_1_4', '10000124', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(150, 'Aluno_Mecatronica_1_5', '10000125', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(151, 'Aluno_Mecatronica_1_6', '10000126', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(152, 'Aluno_Mecatronica_1_7', '10000127', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(153, 'Aluno_Mecatronica_1_8', '10000128', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(154, 'Aluno_Mecatronica_1_9', '10000129', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(155, 'Aluno_Mecatronica_1_10', '10000130', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(156, 'Aluno_Mecatronica_1_11', '10000131', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(157, 'Aluno_Mecatronica_1_12', '10000132', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(158, 'Aluno_Mecatronica_1_13', '10000133', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(159, 'Aluno_Mecatronica_1_14', '10000134', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(160, 'Aluno_Mecatronica_1_15', '10000135', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(161, 'Aluno_Mecatronica_1_16', '10000136', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(162, 'Aluno_Mecatronica_1_17', '10000137', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(163, 'Aluno_Mecatronica_1_18', '10000138', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(164, 'Aluno_Mecatronica_1_19', '10000139', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(165, 'Aluno_Mecatronica_1_20', '10000140', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(166, 'Aluno_Mecatronica_1_21', '10000141', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(167, 'Aluno_Mecatronica_1_22', '10000142', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(168, 'Aluno_Mecatronica_1_23', '10000143', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(169, 'Aluno_Mecatronica_1_24', '10000144', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(170, 'Aluno_Mecatronica_1_25', '10000145', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(171, 'Aluno_Mecatronica_1_26', '10000146', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(172, 'Aluno_Mecatronica_1_27', '10000147', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(173, 'Aluno_Mecatronica_1_28', '10000148', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(174, 'Aluno_Mecatronica_1_29', '10000149', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(175, 'Aluno_Mecatronica_1_30', '10000150', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(176, 'Aluno_Mecatronica_1_31', '10000151', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(177, 'Aluno_Mecatronica_1_32', '10000152', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(178, 'Aluno_Mecatronica_1_33', '10000153', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(179, 'Aluno_Mecatronica_1_34', '10000154', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(180, 'Aluno_Mecatronica_1_35', '10000155', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(181, 'Aluno_Mecatronica_1_36', '10000156', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(182, 'Aluno_Mecatronica_1_37', '10000157', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(183, 'Aluno_Mecatronica_1_38', '10000158', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(184, 'Aluno_Mecatronica_1_39', '10000159', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(185, 'Aluno_Mecatronica_1_40', '10000160', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 4, 'APROVADO'),
(186, 'Aluno_Mecatronica_2_1', '10000161', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(187, 'Aluno_Mecatronica_2_2', '10000162', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(188, 'Aluno_Mecatronica_2_3', '10000163', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(189, 'Aluno_Mecatronica_2_4', '10000164', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(190, 'Aluno_Mecatronica_2_5', '10000165', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(191, 'Aluno_Mecatronica_2_6', '10000166', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(192, 'Aluno_Mecatronica_2_7', '10000167', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(193, 'Aluno_Mecatronica_2_8', '10000168', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(194, 'Aluno_Mecatronica_2_9', '10000169', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(195, 'Aluno_Mecatronica_2_10', '10000170', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(196, 'Aluno_Mecatronica_2_11', '10000171', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(197, 'Aluno_Mecatronica_2_12', '10000172', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(198, 'Aluno_Mecatronica_2_13', '10000173', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(199, 'Aluno_Mecatronica_2_14', '10000174', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(200, 'Aluno_Mecatronica_2_15', '10000175', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(201, 'Aluno_Mecatronica_2_16', '10000176', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(202, 'Aluno_Mecatronica_2_17', '10000177', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(203, 'Aluno_Mecatronica_2_18', '10000178', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(204, 'Aluno_Mecatronica_2_19', '10000179', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(205, 'Aluno_Mecatronica_2_20', '10000180', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(206, 'Aluno_Mecatronica_2_21', '10000181', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(207, 'Aluno_Mecatronica_2_22', '10000182', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(208, 'Aluno_Mecatronica_2_23', '10000183', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(209, 'Aluno_Mecatronica_2_24', '10000184', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(210, 'Aluno_Mecatronica_2_25', '10000185', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(211, 'Aluno_Mecatronica_2_26', '10000186', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(212, 'Aluno_Mecatronica_2_27', '10000187', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(213, 'Aluno_Mecatronica_2_28', '10000188', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(214, 'Aluno_Mecatronica_2_29', '10000189', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(215, 'Aluno_Mecatronica_2_30', '10000190', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(216, 'Aluno_Mecatronica_2_31', '10000191', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(217, 'Aluno_Mecatronica_2_32', '10000192', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(218, 'Aluno_Mecatronica_2_33', '10000193', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(219, 'Aluno_Mecatronica_2_34', '10000194', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(220, 'Aluno_Mecatronica_2_35', '10000195', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(221, 'Aluno_Mecatronica_2_36', '10000196', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(222, 'Aluno_Mecatronica_2_37', '10000197', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(223, 'Aluno_Mecatronica_2_38', '10000198', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(224, 'Aluno_Mecatronica_2_39', '10000199', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(225, 'Aluno_Mecatronica_2_40', '10000200', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 5, 'APROVADO'),
(226, 'Aluno_Mecatronica_3_1', '10000201', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(227, 'Aluno_Mecatronica_3_2', '10000202', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(228, 'Aluno_Mecatronica_3_3', '10000203', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(229, 'Aluno_Mecatronica_3_4', '10000204', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(230, 'Aluno_Mecatronica_3_5', '10000205', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(231, 'Aluno_Mecatronica_3_6', '10000206', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(232, 'Aluno_Mecatronica_3_7', '10000207', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(233, 'Aluno_Mecatronica_3_8', '10000208', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(234, 'Aluno_Mecatronica_3_9', '10000209', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(235, 'Aluno_Mecatronica_3_10', '10000210', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(236, 'Aluno_Mecatronica_3_11', '10000211', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(237, 'Aluno_Mecatronica_3_12', '10000212', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(238, 'Aluno_Mecatronica_3_13', '10000213', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(239, 'Aluno_Mecatronica_3_14', '10000214', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(240, 'Aluno_Mecatronica_3_15', '10000215', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(241, 'Aluno_Mecatronica_3_16', '10000216', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(242, 'Aluno_Mecatronica_3_17', '10000217', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(243, 'Aluno_Mecatronica_3_18', '10000218', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(244, 'Aluno_Mecatronica_3_19', '10000219', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(245, 'Aluno_Mecatronica_3_20', '10000220', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(246, 'Aluno_Mecatronica_3_21', '10000221', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(247, 'Aluno_Mecatronica_3_22', '10000222', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(248, 'Aluno_Mecatronica_3_23', '10000223', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(249, 'Aluno_Mecatronica_3_24', '10000224', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(250, 'Aluno_Mecatronica_3_25', '10000225', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(251, 'Aluno_Mecatronica_3_26', '10000226', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(252, 'Aluno_Mecatronica_3_27', '10000227', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(253, 'Aluno_Mecatronica_3_28', '10000228', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(254, 'Aluno_Mecatronica_3_29', '10000229', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(255, 'Aluno_Mecatronica_3_30', '10000230', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(256, 'Aluno_Mecatronica_3_31', '10000231', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(257, 'Aluno_Mecatronica_3_32', '10000232', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(258, 'Aluno_Mecatronica_3_33', '10000233', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(259, 'Aluno_Mecatronica_3_34', '10000234', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(260, 'Aluno_Mecatronica_3_35', '10000235', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(261, 'Aluno_Mecatronica_3_36', '10000236', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(262, 'Aluno_Mecatronica_3_37', '10000237', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(263, 'Aluno_Mecatronica_3_38', '10000238', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(264, 'Aluno_Mecatronica_3_39', '10000239', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(265, 'Aluno_Mecatronica_3_40', '10000240', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 6, 'APROVADO'),
(266, 'Aluno_DS_1_1', '10000241', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(267, 'Aluno_DS_1_2', '10000242', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(268, 'Aluno_DS_1_3', '10000243', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(269, 'Aluno_DS_1_4', '10000244', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(270, 'Aluno_DS_1_5', '10000245', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(271, 'Aluno_DS_1_6', '10000246', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(272, 'Aluno_DS_1_7', '10000247', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(273, 'Aluno_DS_1_8', '10000248', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(274, 'Aluno_DS_1_9', '10000249', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(275, 'Aluno_DS_1_10', '10000250', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(276, 'Aluno_DS_1_11', '10000251', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(277, 'Aluno_DS_1_12', '10000252', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(278, 'Aluno_DS_1_13', '10000253', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(279, 'Aluno_DS_1_14', '10000254', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(280, 'Aluno_DS_1_15', '10000255', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(281, 'Aluno_DS_1_16', '10000256', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(282, 'Aluno_DS_1_17', '10000257', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(283, 'Aluno_DS_1_18', '10000258', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(284, 'Aluno_DS_1_19', '10000259', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(285, 'Aluno_DS_1_20', '10000260', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(286, 'Aluno_DS_1_21', '10000261', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(287, 'Aluno_DS_1_22', '10000262', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(288, 'Aluno_DS_1_23', '10000263', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(289, 'Aluno_DS_1_24', '10000264', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(290, 'Aluno_DS_1_25', '10000265', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(291, 'Aluno_DS_1_26', '10000266', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(292, 'Aluno_DS_1_27', '10000267', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(293, 'Aluno_DS_1_28', '10000268', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(294, 'Aluno_DS_1_29', '10000269', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(295, 'Aluno_DS_1_30', '10000270', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(296, 'Aluno_DS_1_31', '10000271', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(297, 'Aluno_DS_1_32', '10000272', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(298, 'Aluno_DS_1_33', '10000273', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(299, 'Aluno_DS_1_34', '10000274', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(300, 'Aluno_DS_1_35', '10000275', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(301, 'Aluno_DS_1_36', '10000276', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(302, 'Aluno_DS_1_37', '10000277', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(303, 'Aluno_DS_1_38', '10000278', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(304, 'Aluno_DS_1_39', '10000279', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(305, 'Aluno_DS_1_40', '10000280', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 4, 10, 'APROVADO'),
(306, 'Aluno_DS_2_1', '10000281', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(307, 'Aluno_DS_2_2', '10000282', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(308, 'Aluno_DS_2_3', '10000283', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(309, 'Aluno_DS_2_4', '10000284', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(310, 'Aluno_DS_2_5', '10000285', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(311, 'Aluno_DS_2_6', '10000286', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(312, 'Aluno_DS_2_7', '10000287', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(313, 'Aluno_DS_2_8', '10000288', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(314, 'Aluno_DS_2_9', '10000289', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(315, 'Aluno_DS_2_10', '10000290', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(316, 'Aluno_DS_2_11', '10000291', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(317, 'Aluno_DS_2_12', '10000292', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(318, 'Aluno_DS_2_13', '10000293', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(319, 'Aluno_DS_2_14', '10000294', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(320, 'Aluno_DS_2_15', '10000295', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(321, 'Aluno_DS_2_16', '10000296', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(322, 'Aluno_DS_2_17', '10000297', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(323, 'Aluno_DS_2_18', '10000298', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(324, 'Aluno_DS_2_19', '10000299', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(325, 'Aluno_DS_2_20', '10000300', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(326, 'Aluno_DS_2_21', '10000301', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(327, 'Aluno_DS_2_22', '10000302', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(328, 'Aluno_DS_2_23', '10000303', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(329, 'Aluno_DS_2_24', '10000304', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(330, 'Aluno_DS_2_25', '10000305', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(331, 'Aluno_DS_2_26', '10000306', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(332, 'Aluno_DS_2_27', '10000307', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(333, 'Aluno_DS_2_28', '10000308', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(334, 'Aluno_DS_2_29', '10000309', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(335, 'Aluno_DS_2_30', '10000310', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(336, 'Aluno_DS_2_31', '10000311', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(337, 'Aluno_DS_2_32', '10000312', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(338, 'Aluno_DS_2_33', '10000313', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(339, 'Aluno_DS_2_34', '10000314', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(340, 'Aluno_DS_2_35', '10000315', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(341, 'Aluno_DS_2_36', '10000316', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(342, 'Aluno_DS_2_37', '10000317', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(343, 'Aluno_DS_2_38', '10000318', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(344, 'Aluno_DS_2_39', '10000319', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(345, 'Aluno_DS_2_40', '10000320', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 5, 11, 'APROVADO'),
(346, 'Aluno_DS_3_1', '10000321', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(347, 'Aluno_DS_3_2', '10000322', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(348, 'Aluno_DS_3_3', '10000323', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(349, 'Aluno_DS_3_4', '10000324', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(350, 'Aluno_DS_3_5', '10000325', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(351, 'Aluno_DS_3_6', '10000326', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(352, 'Aluno_DS_3_7', '10000327', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(353, 'Aluno_DS_3_8', '10000328', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(354, 'Aluno_DS_3_9', '10000329', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(355, 'Aluno_DS_3_10', '10000330', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(356, 'Aluno_DS_3_11', '10000331', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(357, 'Aluno_DS_3_12', '10000332', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(358, 'Aluno_DS_3_13', '10000333', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(359, 'Aluno_DS_3_14', '10000334', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(360, 'Aluno_DS_3_15', '10000335', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(361, 'Aluno_DS_3_16', '10000336', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(362, 'Aluno_DS_3_17', '10000337', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(363, 'Aluno_DS_3_18', '10000338', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(364, 'Aluno_DS_3_19', '10000339', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(365, 'Aluno_DS_3_20', '10000340', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(366, 'Aluno_DS_3_21', '10000341', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(367, 'Aluno_DS_3_22', '10000342', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(368, 'Aluno_DS_3_23', '10000343', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(369, 'Aluno_DS_3_24', '10000344', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(370, 'Aluno_DS_3_25', '10000345', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(371, 'Aluno_DS_3_26', '10000346', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(372, 'Aluno_DS_3_27', '10000347', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(373, 'Aluno_DS_3_28', '10000348', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(374, 'Aluno_DS_3_29', '10000349', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(375, 'Aluno_DS_3_30', '10000350', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(376, 'Aluno_DS_3_31', '10000351', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(377, 'Aluno_DS_3_32', '10000352', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(378, 'Aluno_DS_3_33', '10000353', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(379, 'Aluno_DS_3_34', '10000354', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(380, 'Aluno_DS_3_35', '10000355', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(381, 'Aluno_DS_3_36', '10000356', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(382, 'Aluno_DS_3_37', '10000357', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(383, 'Aluno_DS_3_38', '10000358', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(384, 'Aluno_DS_3_39', '10000359', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(385, 'Aluno_DS_3_40', '10000360', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', 6, 12, 'APROVADO'),
(386, 'teste filtro', '99999999999', '$2y$10$kWrXmYRNwF07NaH.dhXyOeDMi7urT9b9Tw04bVRaX79dxTMn.UAje', 0, 4, 'APROVADO'),
(387, 'teste firlto', '10110101010', '$2y$10$ct3rIo19gg54ZQBfNgCD9.Q5hO90QXqQGNBrHePGQijP/Kv9a3Seu', 0, 4, 'APROVADO'),
(391, 'amanda', '12345678900', '$2y$10$dYqTZWtVUiu2lotTKUgFzuXvLVbnoEZsaduTqKkmJtQs6TW0cPei.', 0, 4, 'APROVADO');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ano_letivo`
--

CREATE TABLE `ano_letivo` (
  `id_ano` int(11) NOT NULL,
  `ano` int(4) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `ano_letivo`
--

INSERT INTO `ano_letivo` (`id_ano`, `ano`, `data_inicio`, `data_fim`) VALUES
(1, 2025, '2025-02-02', '2025-12-12');

-- --------------------------------------------------------

--
-- Estrutura da tabela `carga_curricular`
--

CREATE TABLE `carga_curricular` (
  `id_carga` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL,
  `id_disc` int(11) NOT NULL,
  `aulas_seman` int(11) NOT NULL COMMENT 'quantidade min'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `componente_curricular`
--

CREATE TABLE `componente_curricular` (
  `id_comp` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `id_disc` int(11) NOT NULL,
  `aulas_seman` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `disciplina`
--

CREATE TABLE `disciplina` (
  `id_disc` int(11) NOT NULL,
  `nome_disc` varchar(60) NOT NULL,
  `area` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `disciplina`
--

INSERT INTO `disciplina` (`id_disc`, `nome_disc`, `area`) VALUES
(13, 'Língua Portuguesa, Literatura e Comunicação Profissional', 'Linguagens e Comunicação'),
(14, 'Língua Estrangeira Moderna – Inglês e Comunicação Profission', 'Linguagens e Comunicação'),
(15, 'Matemática', 'Ciências Exatas'),
(16, 'Educação Física', 'Linguagens'),
(17, 'Biologia', 'Ciências Biológicas'),
(18, 'Física', 'Ciências Exatas'),
(19, 'Química', 'Ciências Exatas'),
(20, 'História', 'Ciências Humanas e Sociais'),
(21, 'Geografia', 'Ciências Humanas e Sociais'),
(22, 'Filosofia', 'Ciências Humanas e Sociais'),
(23, 'Sociologia', 'Ciências Humanas e Sociais'),
(24, 'Língua Estrangeira Moderna – Espanhol', 'Linguagens e Comunicação'),
(25, 'Arte', 'Linguagens e Comunicação'),
(26, 'Itinerário Formativo (Linguagens, CHS)', 'Linguagens'),
(27, 'Aplicativos Informatizados', 'Engenharia e Tecnologia'),
(28, 'Automação I: Hidráulica e Pneumática', 'Engenharia e Tecnologia'),
(29, 'Desenho Auxiliado por Computador', 'Engenharia e Tecnologia'),
(30, 'Eletricidade e Instalações Elétricas', 'Engenharia e Tecnologia'),
(31, 'Programação Web I, II e III', 'Informática / TI'),
(32, 'Análise e Projeto de Sistemas', 'Informática / TI'),
(33, 'Design Digital', 'Informática / TI'),
(34, 'Fundamentos da Informática', 'Informática / TI'),
(35, 'Técnicas de Programação e Algoritmos', 'Informática / TI'),
(36, 'Banco de Dados I e II', 'Informática / TI'),
(37, 'Desenvolvimento de Sistemas', 'Informática / TI'),
(38, 'Ética e Cidadania Organizacional', 'Ciências Humanas e Sociais'),
(39, 'Sistemas Embarcados', 'Informática / TI'),
(40, 'Programação de Aplicativos Mobile I e II', 'Informatica'),
(41, 'Internet, Protocolos e Segurança de Sistemas da Informação', 'Informática / TI'),
(42, 'Planejamento e Desenvolvimento do TCC em D.S', 'Informática / TI'),
(43, 'Qualidade e Teste de Software', 'Informática / TI'),
(45, 'Planejamento e Desenvolvimento do TCC em Meca', 'Engenharia e Tecnologia');

-- --------------------------------------------------------

--
-- Estrutura da tabela `disponibilidade_prof`
--

CREATE TABLE `disponibilidade_prof` (
  `id_prof` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `status` varchar(15) NOT NULL COMMENT 'disponivel, indisponivel',
  `dia` varchar(10) NOT NULL,
  `horario` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `disponibilidade_prof`
--

INSERT INTO `disponibilidade_prof` (`id_prof`, `id_periodo`, `status`, `dia`, `horario`) VALUES
(47, 0, '', 'Quarta', '10:20 - 11:10'),
(47, 0, '', 'Quinta', '16:20 - 17:10'),
(47, 0, '', 'Terça', '08:20 - 09:10');

-- --------------------------------------------------------

--
-- Estrutura da tabela `horario`
--

CREATE TABLE `horario` (
  `id_horario` int(11) NOT NULL,
  `id_turma` int(11) NOT NULL,
  `id_periodo` int(11) NOT NULL,
  `id_disc` int(11) NOT NULL,
  `id_prof` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `dia` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `lista_aprovacao_previa`
--

CREATE TABLE `lista_aprovacao_previa` (
  `cpf` varchar(11) NOT NULL,
  `tipo_usuario` varchar(15) NOT NULL,
  `data_inclusao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `notificacoes`
--

CREATE TABLE `notificacoes` (
  `id_notificacao` int(11) NOT NULL,
  `tipo_usuario` enum('aluno','professor') NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `mensagem` varchar(255) NOT NULL,
  `url_destino` varchar(255) DEFAULT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `notificacoes`
--

INSERT INTO `notificacoes` (`id_notificacao`, `tipo_usuario`, `id_usuario`, `mensagem`, `url_destino`, `lida`, `data_criacao`) VALUES
(1, 'aluno', 388, 'Seu cadastro no PlanIt foi aprovado! Clique aqui para ir para a tela de login e acessar o sistema.', '/login.php', 0, '2025-10-13 11:03:02'),
(2, 'aluno', 390, 'Seu cadastro foi aprovado! Clique aqui para acessar o sistema.', 'http://localhost/proj_site/login.php', 0, '2025-10-13 11:25:46'),
(3, 'aluno', 391, 'Seu cadastro no PlanIt foi aprovado! Clique aqui para acessar o sistema.', 'http://localhost/proj_site/login.php', 0, '2025-10-13 11:34:21'),
(4, 'professor', 76, 'Seu cadastro no PlanIt foi aprovado! Clique aqui para acessar o sistema.', 'http://localhost/proj_site/login.php', 0, '2025-10-13 17:43:06');

-- --------------------------------------------------------

--
-- Estrutura da tabela `periodo_aula`
--

CREATE TABLE `periodo_aula` (
  `id_periodo` int(11) NOT NULL,
  `dia_semana` tinyint(4) NOT NULL COMMENT 'segunda',
  `ordem` tinyint(4) NOT NULL COMMENT '1°',
  `hora_inicio` time NOT NULL COMMENT '7:30',
  `hora_fim` time NOT NULL COMMENT '8:20',
  `turno_periodo` varchar(10) NOT NULL COMMENT 'manhã',
  `dia` varchar(20) NOT NULL,
  `horario` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `periodo_aula`
--

INSERT INTO `periodo_aula` (`id_periodo`, `dia_semana`, `ordem`, `hora_inicio`, `hora_fim`, `turno_periodo`, `dia`, `horario`) VALUES
(1, 0, 0, '00:00:00', '00:00:00', '', '', '07:30 - 08:20'),
(2, 0, 0, '00:00:00', '00:00:00', '', '', '08:20 - 09:10'),
(3, 0, 0, '00:00:00', '00:00:00', '', '', '09:10 - 10:00'),
(4, 0, 0, '00:00:00', '00:00:00', '', '', '10:20 - 11:10'),
(5, 0, 0, '00:00:00', '00:00:00', '', '', '11:10 - 12:00'),
(6, 0, 0, '00:00:00', '00:00:00', '', '', '12:00 - 12:50'),
(7, 0, 0, '00:00:00', '00:00:00', '', '', '13:30 - 14:20'),
(8, 0, 0, '00:00:00', '00:00:00', '', '', '14:20 - 15:10'),
(9, 0, 0, '00:00:00', '00:00:00', '', '', '15:10 - 16:00'),
(10, 0, 0, '00:00:00', '00:00:00', '', '', '16:10 - 17:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `professor`
--

CREATE TABLE `professor` (
  `id_prof` int(11) NOT NULL,
  `nome` varchar(80) NOT NULL,
  `trabalha_outro_lugar` tinyint(1) DEFAULT 0,
  `horario_saida_outro_lugar` time DEFAULT NULL,
  `cpf` varchar(80) NOT NULL,
  `areas` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `id_serie` int(11) DEFAULT NULL,
  `status_aprovacao` varchar(15) NOT NULL DEFAULT 'PENDENTE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `professor`
--

INSERT INTO `professor` (`id_prof`, `nome`, `trabalha_outro_lugar`, `horario_saida_outro_lugar`, `cpf`, `areas`, `senha`, `id_serie`, `status_aprovacao`) VALUES
(33, 'Professor_LC_1', 0, NULL, '40000001', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(34, 'Professor_LC_2', 0, NULL, '40000002', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(35, 'Professor_LC_3', 0, NULL, '40000003', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(36, 'Professor_LC_4', 0, NULL, '40000004', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(37, 'Professor_LC_5', 0, NULL, '40000005', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(38, 'Professor_LC_6', 0, NULL, '40000006', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(39, 'Professor_LC_7', 0, NULL, '40000007', 'Linguagens e Comunicação', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(40, 'Professor_Exatas_1', 0, NULL, '40000008', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(41, 'Professor_Exatas_2', 0, NULL, '40000009', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(42, 'Professor_Exatas_3', 0, NULL, '40000010', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(43, 'Professor_Exatas_4', 0, NULL, '40000011', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(44, 'Professor_Exatas_5', 0, NULL, '40000012', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(45, 'Professor_Exatas_6', 0, NULL, '40000013', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(46, 'Professor_Exatas_7', 0, NULL, '40000014', 'Ciências Exatas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(47, 'Professor_Bio_1', 1, '15:30:00', '40000015', 'Ciências Biológicas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(48, 'Professor_Bio_2', 0, NULL, '40000016', 'Ciências Biológicas', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(49, 'Professor_CHS_1', 0, NULL, '40000017', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(50, 'Professor_CHS_2', 0, NULL, '40000018', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(51, 'Professor_CHS_3', 0, NULL, '40000019', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(52, 'Professor_CHS_4', 0, NULL, '40000020', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(53, 'Professor_CHS_5', 0, NULL, '40000021', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(54, 'Professor_CHS_6', 0, NULL, '40000022', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(55, 'Professor_CHS_7', 0, NULL, '40000023', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(56, 'Professor_CHS_8', 0, NULL, '40000024', 'Ciências Humanas e Sociais', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(57, 'Professor_Design_1', 0, NULL, '40000025', 'Design e Comunicação Visual', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(58, 'Professor_Design_2', 0, NULL, '40000026', 'Design e Comunicação Visual', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(59, 'Professor_Design_3', 0, NULL, '40000027', 'Design e Comunicação Visual', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(60, 'Professor_Design_4', 0, NULL, '40000028', 'Design e Comunicação Visual', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(61, 'Professor_EngTec_1', 0, NULL, '40000029', 'Engenharia e Tecnologia', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(62, 'Professor_EngTec_2', 0, NULL, '40000030', 'Engenharia e Tecnologia', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(63, 'Professor_EngTec_3', 0, NULL, '40000031', 'Engenharia e Tecnologia', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(64, 'Professor_EngTec_4', 0, NULL, '40000032', 'Engenharia e Tecnologia', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(65, 'Professor_Saude_1', 0, NULL, '40000033', 'Saúde', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(66, 'Professor_Saude_2', 0, NULL, '40000034', 'Saúde', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(67, 'Professor_Itinerario_1', 0, NULL, '40000035', 'Itinerário Formativo', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(68, 'Professor_Itinerario_2', 0, NULL, '40000036', 'Itinerário Formativo', '$2b$12$3l8xYMFMlDqRZtkSJ7S3COrzJqeXCriqTozOxu12.jZ3gh3GqRFlW', NULL, 'APROVADO'),
(77, 'Prof_Info_1', 0, NULL, '40000037', 'Informática / TI; Programação Web e Mobile', '123456', NULL, 'APROVADO'),
(78, 'Prof_Info_2', 0, NULL, '40000038', 'Informática / TI; Banco de Dados e Análise de Sistemas', '123456', NULL, 'APROVADO'),
(79, 'Prof_Info_3', 0, NULL, '40000039', 'Informática / TI; Redes, Segurança e TCC', '123456', NULL, 'APROVADO'),
(80, 'Prof_Info_4', 1, '18:00:00', '40000040', 'Informática / TI; Design Digital e Front-End', '123456', NULL, 'APROVADO');

-- --------------------------------------------------------

--
-- Estrutura da tabela `relacionamento_disciplina`
--

CREATE TABLE `relacionamento_disciplina` (
  `id_relacionamento` int(11) NOT NULL,
  `id_disc` int(11) NOT NULL COMMENT 'Chave estrangeira para a tabela disciplina',
  `id_turma` int(11) NOT NULL COMMENT 'Chave estrangeira para a tabela turma',
  `id_prof_padrao` int(11) NOT NULL COMMENT 'Chave estrangeira para a tabela professor (Professor Padrao)',
  `aulas_semanais` int(3) NOT NULL COMMENT 'Numero de aulas por semana para essa disciplina/turma',
  `carga_horaria_total` int(5) NOT NULL COMMENT 'Carga horaria total (anual ou semestral)',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabela para relacionar Disciplinas com Turmas, Professor e Cargas Horarias';

--
-- Extraindo dados da tabela `relacionamento_disciplina`
--

INSERT INTO `relacionamento_disciplina` (`id_relacionamento`, `id_disc`, `id_turma`, `id_prof_padrao`, `aulas_semanais`, `carga_horaria_total`, `data_criacao`) VALUES
(3, 13, 1, 34, 3, 120, '2025-11-13 15:14:14'),
(4, 13, 2, 34, 3, 120, '2025-11-13 15:14:14'),
(5, 13, 3, 34, 3, 120, '2025-11-13 15:14:14'),
(6, 16, 1, 44, 2, 80, '2025-11-13 15:14:14'),
(7, 16, 2, 44, 2, 80, '2025-11-13 15:14:14'),
(8, 17, 2, 43, 2, 80, '2025-11-13 15:14:14'),
(9, 17, 3, 43, 2, 80, '2025-11-13 15:14:14'),
(10, 18, 2, 41, 2, 80, '2025-11-13 15:14:14'),
(11, 18, 3, 41, 2, 80, '2025-11-13 15:14:14'),
(12, 19, 2, 42, 2, 80, '2025-11-13 15:14:14'),
(13, 19, 3, 42, 2, 80, '2025-11-13 15:14:14'),
(14, 20, 1, 48, 2, 80, '2025-11-13 15:14:14'),
(15, 20, 2, 48, 2, 80, '2025-11-13 15:14:14'),
(16, 21, 1, 47, 2, 80, '2025-11-13 15:14:14'),
(17, 21, 2, 47, 2, 80, '2025-11-13 15:14:14'),
(18, 22, 1, 45, 1, 40, '2025-11-13 15:14:14'),
(19, 22, 3, 45, 1, 40, '2025-11-13 15:14:14'),
(20, 23, 1, 46, 1, 40, '2025-11-13 15:14:14'),
(21, 24, 1, 35, 2, 80, '2025-11-13 15:14:14'),
(22, 25, 1, 36, 2, 80, '2025-11-13 15:14:14'),
(23, 26, 1, 37, 6, 200, '2025-11-13 15:14:14'),
(24, 26, 2, 37, 6, 200, '2025-11-13 15:14:14'),
(25, 26, 3, 37, 12, 440, '2025-11-13 15:14:14'),
(26, 13, 4, 33, 4, 160, '2025-11-13 15:14:14'),
(27, 13, 5, 33, 4, 160, '2025-11-13 15:14:14'),
(28, 13, 6, 33, 4, 160, '2025-11-13 15:14:14'),
(29, 16, 4, 44, 2, 80, '2025-11-13 15:14:14'),
(30, 16, 5, 44, 2, 80, '2025-11-13 15:14:14'),
(31, 16, 6, 44, 2, 80, '2025-11-13 15:14:14'),
(32, 17, 4, 43, 2, 80, '2025-11-13 15:14:14'),
(33, 17, 5, 43, 2, 80, '2025-11-13 15:14:14'),
(34, 17, 6, 43, 2, 80, '2025-11-13 15:14:14'),
(35, 18, 4, 41, 2, 80, '2025-11-13 15:14:14'),
(36, 18, 5, 41, 2, 80, '2025-11-13 15:14:14'),
(37, 18, 6, 41, 2, 80, '2025-11-13 15:14:14'),
(38, 19, 4, 42, 2, 80, '2025-11-13 15:14:14'),
(39, 19, 5, 42, 2, 80, '2025-11-13 15:14:14'),
(40, 19, 6, 42, 2, 80, '2025-11-13 15:14:14'),
(41, 20, 4, 45, 2, 80, '2025-11-13 15:14:14'),
(42, 20, 5, 45, 2, 80, '2025-11-13 15:14:14'),
(43, 20, 6, 45, 2, 80, '2025-11-13 15:14:14'),
(44, 21, 4, 46, 2, 80, '2025-11-13 15:14:14'),
(45, 21, 5, 46, 2, 80, '2025-11-13 15:14:14'),
(46, 21, 6, 46, 2, 80, '2025-11-13 15:14:14'),
(47, 22, 4, 47, 2, 80, '2025-11-13 15:14:14'),
(48, 23, 4, 48, 2, 80, '2025-11-13 15:14:14'),
(49, 27, 4, 50, 2, 80, '2025-11-13 15:14:14'),
(50, 28, 4, 51, 3, 120, '2025-11-13 15:14:14'),
(51, 29, 4, 52, 3, 120, '2025-11-13 15:14:14'),
(52, 30, 4, 53, 2, 80, '2025-11-13 15:14:14'),
(53, 13, 10, 34, 4, 160, '2025-11-13 15:14:14'),
(54, 13, 11, 34, 4, 160, '2025-11-13 15:14:14'),
(55, 13, 12, 34, 4, 160, '2025-11-13 15:14:14'),
(56, 16, 10, 44, 2, 80, '2025-11-13 15:14:14'),
(57, 16, 11, 44, 2, 80, '2025-11-13 15:14:14'),
(58, 16, 12, 44, 2, 80, '2025-11-13 15:14:14'),
(59, 17, 10, 43, 2, 80, '2025-11-13 15:14:14'),
(60, 17, 11, 43, 2, 80, '2025-11-13 15:14:14'),
(61, 17, 12, 43, 2, 80, '2025-11-13 15:14:14'),
(62, 18, 10, 40, 2, 80, '2025-11-13 15:14:14'),
(63, 18, 12, 40, 2, 80, '2025-11-13 15:14:14'),
(64, 19, 10, 42, 2, 80, '2025-11-13 15:14:14'),
(65, 19, 12, 42, 2, 80, '2025-11-13 15:14:14'),
(66, 20, 10, 47, 2, 80, '2025-11-13 15:14:14'),
(67, 20, 12, 47, 2, 80, '2025-11-13 15:14:14'),
(68, 21, 10, 46, 2, 80, '2025-11-13 15:14:14'),
(69, 21, 12, 46, 2, 80, '2025-11-13 15:14:14'),
(70, 22, 10, 45, 2, 80, '2025-11-13 15:14:14'),
(71, 22, 11, 45, 1, 40, '2025-11-13 15:14:14'),
(72, 22, 12, 45, 1, 40, '2025-11-13 15:14:14'),
(73, 23, 11, 48, 1, 40, '2025-11-13 15:14:14'),
(74, 23, 12, 48, 1, 40, '2025-11-13 15:14:14'),
(75, 24, 11, 36, 2, 80, '2025-11-13 15:14:14'),
(76, 24, 12, 36, 2, 80, '2025-11-13 15:14:14'),
(77, 25, 10, 35, 3, 120, '2025-11-13 15:14:14'),
(78, 31, 10, 50, 2, 80, '2025-11-13 15:14:14'),
(79, 31, 11, 50, 2, 80, '2025-11-13 15:14:14'),
(80, 31, 12, 50, 2, 80, '2025-11-13 15:14:14'),
(81, 32, 10, 51, 2, 80, '2025-11-13 15:14:14'),
(82, 33, 10, 51, 2, 80, '2025-11-13 15:14:14'),
(83, 34, 10, 51, 2, 80, '2025-11-13 15:14:14'),
(84, 35, 10, 52, 3, 120, '2025-11-13 15:14:14'),
(85, 36, 10, 52, 2, 80, '2025-11-13 15:14:14'),
(86, 36, 11, 52, 2, 80, '2025-11-13 15:14:14'),
(87, 37, 11, 53, 3, 120, '2025-11-13 15:14:14'),
(88, 38, 11, 49, 1, 40, '2025-11-13 15:14:14'),
(89, 39, 11, 53, 2, 80, '2025-11-13 15:14:14'),
(90, 40, 11, 50, 2, 80, '2025-11-13 15:14:14'),
(91, 40, 12, 50, 2, 80, '2025-11-13 15:14:14'),
(92, 41, 12, 51, 2, 80, '2025-11-13 15:14:14'),
(93, 43, 12, 53, 2, 80, '2025-11-13 15:14:14'),
(113, 15, 1, 40, 3, 120, '2025-11-13 15:32:43'),
(114, 15, 2, 40, 3, 120, '2025-11-13 15:32:43'),
(115, 15, 3, 40, 3, 120, '2025-11-13 15:32:43'),
(116, 16, 3, 44, 2, 80, '2025-11-13 15:32:43'),
(117, 15, 4, 40, 4, 160, '2025-11-13 15:32:43'),
(118, 15, 5, 40, 4, 160, '2025-11-13 15:32:43'),
(119, 15, 6, 40, 4, 160, '2025-11-13 15:32:43'),
(120, 15, 10, 41, 4, 160, '2025-11-13 15:32:43'),
(121, 15, 11, 41, 4, 160, '2025-11-13 15:32:43'),
(122, 15, 12, 41, 4, 160, '2025-11-13 15:32:43');

-- --------------------------------------------------------

--
-- Estrutura da tabela `sala`
--

CREATE TABLE `sala` (
  `id_sala` int(11) NOT NULL,
  `nome_sala` varchar(30) NOT NULL,
  `capacid_sala` int(11) NOT NULL,
  `recursos` varchar(100) NOT NULL COMMENT 'computador, quadra...'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura da tabela `serie`
--

CREATE TABLE `serie` (
  `id_serie` int(11) NOT NULL,
  `nome_serie` varchar(30) NOT NULL COMMENT '1° ano...',
  `id_ano` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `serie`
--

INSERT INTO `serie` (`id_serie`, `nome_serie`, `id_ano`) VALUES
(4, '1° ano', 1),
(5, '2° ano', 1),
(6, '3° ano', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `turma`
--

CREATE TABLE `turma` (
  `id_turma` int(11) NOT NULL,
  `nome_turma` varchar(100) NOT NULL COMMENT 'mecatronica, linguagens...',
  `turno` varchar(60) NOT NULL,
  `capacidade` int(11) NOT NULL,
  `id_serie` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Extraindo dados da tabela `turma`
--

INSERT INTO `turma` (`id_turma`, `nome_turma`, `turno`, `capacidade`, `id_serie`) VALUES
(1, '1° Linguaguagens', 'manhã', 40, 4),
(2, '2° Linguaguagens', 'manhã', 40, 5),
(3, '3° Linguaguagens', 'manhã', 40, 6),
(4, '1° Mecatrônica', 'Integral', 40, 4),
(5, '2° Mecatrônica', 'Integral', 40, 5),
(6, '3° Mecatrônica', 'Integral', 40, 6),
(10, '1° Desenvolvimento de Sistemas', 'Integral', 40, 4),
(11, '2° Desenvolvimento de Sistemas', 'Integral', 40, 5),
(12, '3° Desenvolvimento de Sistemas', 'Integral', 40, 6);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `alocacao_prof`
--
ALTER TABLE `alocacao_prof`
  ADD PRIMARY KEY (`id_aloc`),
  ADD KEY `aloc_comp` (`id_comp`),
  ADD KEY `aloc_prof` (`id_prof`);

--
-- Índices para tabela `aluno`
--
ALTER TABLE `aluno`
  ADD PRIMARY KEY (`id_aluno`),
  ADD UNIQUE KEY `cpf` (`cpf`),
  ADD KEY `aluno_ibfk_2` (`id_turma`);

--
-- Índices para tabela `ano_letivo`
--
ALTER TABLE `ano_letivo`
  ADD PRIMARY KEY (`id_ano`),
  ADD UNIQUE KEY `ano` (`ano`);

--
-- Índices para tabela `carga_curricular`
--
ALTER TABLE `carga_curricular`
  ADD PRIMARY KEY (`id_carga`),
  ADD KEY `id_disc` (`id_disc`);

--
-- Índices para tabela `componente_curricular`
--
ALTER TABLE `componente_curricular`
  ADD PRIMARY KEY (`id_comp`),
  ADD KEY `componente_turma` (`id_turma`);

--
-- Índices para tabela `disciplina`
--
ALTER TABLE `disciplina`
  ADD PRIMARY KEY (`id_disc`);

--
-- Índices para tabela `disponibilidade_prof`
--
ALTER TABLE `disponibilidade_prof`
  ADD PRIMARY KEY (`id_prof`,`dia`,`horario`),
  ADD UNIQUE KEY `unique_restricao` (`id_prof`,`dia`,`horario`),
  ADD KEY `periodo_disp` (`id_periodo`);

--
-- Índices para tabela `horario`
--
ALTER TABLE `horario`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `horario_turma` (`id_turma`),
  ADD KEY `horario_periodo` (`id_periodo`),
  ADD KEY `horario_disc` (`id_disc`),
  ADD KEY `horario_professor` (`id_prof`),
  ADD KEY `horario_sala` (`id_sala`);

--
-- Índices para tabela `lista_aprovacao_previa`
--
ALTER TABLE `lista_aprovacao_previa`
  ADD PRIMARY KEY (`cpf`);

--
-- Índices para tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  ADD PRIMARY KEY (`id_notificacao`),
  ADD KEY `id_usuario` (`id_usuario`,`tipo_usuario`,`lida`);

--
-- Índices para tabela `periodo_aula`
--
ALTER TABLE `periodo_aula`
  ADD PRIMARY KEY (`id_periodo`);

--
-- Índices para tabela `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`id_prof`),
  ADD UNIQUE KEY `capacidade` (`cpf`);

--
-- Índices para tabela `relacionamento_disciplina`
--
ALTER TABLE `relacionamento_disciplina`
  ADD PRIMARY KEY (`id_relacionamento`),
  ADD UNIQUE KEY `uk_disciplina_turma` (`id_disc`,`id_turma`),
  ADD KEY `fk_relacionamento_turma` (`id_turma`),
  ADD KEY `fk_relacionamento_professor` (`id_prof_padrao`);

--
-- Índices para tabela `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`);

--
-- Índices para tabela `serie`
--
ALTER TABLE `serie`
  ADD KEY `serie_ano` (`id_ano`);

--
-- Índices para tabela `turma`
--
ALTER TABLE `turma`
  ADD PRIMARY KEY (`id_turma`),
  ADD KEY `serie_turma` (`id_serie`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `alocacao_prof`
--
ALTER TABLE `alocacao_prof`
  MODIFY `id_aloc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `aluno`
--
ALTER TABLE `aluno`
  MODIFY `id_aluno` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=392;

--
-- AUTO_INCREMENT de tabela `ano_letivo`
--
ALTER TABLE `ano_letivo`
  MODIFY `id_ano` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `carga_curricular`
--
ALTER TABLE `carga_curricular`
  MODIFY `id_carga` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `componente_curricular`
--
ALTER TABLE `componente_curricular`
  MODIFY `id_comp` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `disciplina`
--
ALTER TABLE `disciplina`
  MODIFY `id_disc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `horario`
--
ALTER TABLE `horario`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `notificacoes`
--
ALTER TABLE `notificacoes`
  MODIFY `id_notificacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `periodo_aula`
--
ALTER TABLE `periodo_aula`
  MODIFY `id_periodo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `professor`
--
ALTER TABLE `professor`
  MODIFY `id_prof` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de tabela `relacionamento_disciplina`
--
ALTER TABLE `relacionamento_disciplina`
  MODIFY `id_relacionamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=224;

--
-- AUTO_INCREMENT de tabela `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `turma`
--
ALTER TABLE `turma`
  MODIFY `id_turma` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `alocacao_prof`
--
ALTER TABLE `alocacao_prof`
  ADD CONSTRAINT `aloc_comp` FOREIGN KEY (`id_comp`) REFERENCES `componente_curricular` (`id_comp`),
  ADD CONSTRAINT `aloc_prof` FOREIGN KEY (`id_prof`) REFERENCES `professor` (`id_prof`);

--
-- Limitadores para a tabela `aluno`
--
ALTER TABLE `aluno`
  ADD CONSTRAINT `aluno_ibfk_2` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`);

--
-- Limitadores para a tabela `carga_curricular`
--
ALTER TABLE `carga_curricular`
  ADD CONSTRAINT `carga_curricular_ibfk_1` FOREIGN KEY (`id_disc`) REFERENCES `disciplina` (`id_disc`);

--
-- Limitadores para a tabela `componente_curricular`
--
ALTER TABLE `componente_curricular`
  ADD CONSTRAINT `componente_turma` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`);

--
-- Limitadores para a tabela `disponibilidade_prof`
--
ALTER TABLE `disponibilidade_prof`
  ADD CONSTRAINT `prof_disp` FOREIGN KEY (`id_prof`) REFERENCES `professor` (`id_prof`);

--
-- Limitadores para a tabela `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `horario_disc` FOREIGN KEY (`id_disc`) REFERENCES `disciplina` (`id_disc`),
  ADD CONSTRAINT `horario_periodo` FOREIGN KEY (`id_periodo`) REFERENCES `periodo_aula` (`id_periodo`),
  ADD CONSTRAINT `horario_professor` FOREIGN KEY (`id_prof`) REFERENCES `professor` (`id_prof`),
  ADD CONSTRAINT `horario_sala` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`),
  ADD CONSTRAINT `horario_turma` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`);

--
-- Limitadores para a tabela `relacionamento_disciplina`
--
ALTER TABLE `relacionamento_disciplina`
  ADD CONSTRAINT `fk_relacionamento_disciplina` FOREIGN KEY (`id_disc`) REFERENCES `disciplina` (`id_disc`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_relacionamento_professor` FOREIGN KEY (`id_prof_padrao`) REFERENCES `professor` (`id_prof`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_relacionamento_turma` FOREIGN KEY (`id_turma`) REFERENCES `turma` (`id_turma`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limitadores para a tabela `serie`
--
ALTER TABLE `serie`
  ADD CONSTRAINT `serie_ano` FOREIGN KEY (`id_ano`) REFERENCES `ano_letivo` (`id_ano`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
