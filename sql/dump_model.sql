CREATE DATABASE IF NOT EXISTS `cms_bd` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `cms_bd`;
-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cms_bd
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `password_reset`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'уникальный идентификатор',
  `user_id` int NOT NULL,
  `token` varchar(255) NOT NULL COMMENT 'токен хранимый для подтверждения сессии',
  `expires_at` datetime NOT NULL COMMENT 'срок годности токена (час, два)',
  `used_at` datetime DEFAULT NULL COMMENT 'был ли токен использован',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'ip для безопасности',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  KEY `fk_password_reset_user_idx` (`user_id`),
  CONSTRAINT `fk_password_reset_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset`
--

LOCK TABLES `password_reset` WRITE;
/*!40000 ALTER TABLE `password_reset` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'уникальный идентификатор',
  `name` varchar(50) NOT NULL COMMENT 'название роли ',
  `description` varchar(255) DEFAULT NULL COMMENT 'описание роли',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'admin','Полный доступ к системе'),(2,'moderator','Модерация контента, но без настроек системы'),(3,'user','Обычный зарегистрированный пользователь');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `session` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'Уникальный номер записи в таблице',
  `user_id` int NOT NULL COMMENT 'ID пользователя (связь с таблицей user)',
  `session_token` varchar(255) NOT NULL COMMENT 'Уникальный токен текущей сессии (генерируется PHP при входе)',
  `refresh_token` varchar(255) DEFAULT NULL COMMENT 'Токен для продления сессии (генерируется PHP)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP-адрес пользователя ($_SERVER["REMOTE_ADDR"])',
  `user_agent` text COMMENT 'Браузер и ОС пользователя ($_SERVER["HTTP_USER_AGENT"])',
  `device_name` varchar(100) DEFAULT NULL COMMENT 'Тип устройства: iPhone/Android/Windows (определяется PHP по user-agent)',
  `location` varchar(100) DEFAULT NULL COMMENT 'Геопозиция по IP: Москва, СПб и т.д. (определяется через API)',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Когда вошли',
  `last_activity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Последнее действие (обновляется PHP)',
  `expires_at` datetime NOT NULL COMMENT 'Когда сессия истечет (например, +7 дней)',
  `is_active` tinyint NOT NULL DEFAULT '1' COMMENT '1 - активна, 0 - завершена (при выходе)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_token_UNIQUE` (`session_token`),
  UNIQUE KEY `refresh_token_UNIQUE` (`refresh_token`),
  KEY `fk_session_user_idx` (`user_id`),
  KEY `idx_session_expires` (`expires_at`),
  KEY `idx_session_last_activity` (`last_activity`),
  KEY `idx_session_active` (`is_active`),
  CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Таблица для хранения активных сессий пользователей';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'уникальный идентификатор',
  `username` varchar(45) NOT NULL COMMENT 'никнейм',
  `email` varchar(100) NOT NULL COMMENT 'почта',
  `last_name` varchar(50) NOT NULL COMMENT 'имя',
  `first_name` varchar(50) NOT NULL COMMENT 'фамилия',
  `middle_name` varchar(50) DEFAULT NULL COMMENT 'отчество (необязательно)',
  `password_hash` varchar(255) NOT NULL COMMENT 'хэш для хранения пароля',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата создания пользователя',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `assigned_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'когда роль была создана',
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_user_role_role_idx` (`role_id`),
  KEY `fk_user_role_user_idx` (`user_id`),
  CONSTRAINT `fk_user_role_role` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_role_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'cms_bd'
--

--
-- Dumping routines for database 'cms_bd'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-24 13:30:51