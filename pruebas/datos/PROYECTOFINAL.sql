-- MySQL dump 10.13  Distrib 8.0.46, for Linux (x86_64)
--
-- Host: localhost    Database: PROYECTOFINAL
-- ------------------------------------------------------
-- Server version	8.0.46-0ubuntu0.24.04.4

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Clases_particulares`
--

DROP TABLE IF EXISTS `Clases_particulares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Clases_particulares` (
  `idClase` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Descripcion` text,
  `Area` varchar(100) DEFAULT NULL,
  `idCoach` int NOT NULL,
  `idSucursal` int NOT NULL,
  PRIMARY KEY (`idClase`),
  KEY `fk_clase_coach` (`idCoach`),
  KEY `idx_clase_sucursal` (`idSucursal`),
  CONSTRAINT `fk_clase_coach` FOREIGN KEY (`idCoach`) REFERENCES `Coach` (`idCoach`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_clase_sucursal` FOREIGN KEY (`idSucursal`) REFERENCES `Sucursal` (`idSucursal`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Clases_particulares`
--

LOCK TABLES `Clases_particulares` WRITE;
/*!40000 ALTER TABLE `Clases_particulares` DISABLE KEYS */;
INSERT INTO `Clases_particulares` VALUES (1,'Yoga','Yoga para estirar músculos y entrenar flexibilidad.','Tatami',1,1),(2,'CrossFit','Entrenamiento deportivo basado en una combinación de ejercicios aeróbicos y anaeróbicos extremos.','Suelo de caucho',1,1),(3,'Judo','El judo es un arte marcial y deporte de combate que se basa en técnicas de agarre y proyección para desarrollar fuerza, coordinación y disciplina física.','Suelo de caucho',4,2),(4,'Judo Avanzado','El judo es un arte marcial y deporte de combate que se basa en técnicas de agarre y proyección para desarrollar fuerza, coordinación y disciplina física.','Suelo de caucho',9,1),(5,'Judo Intermedio','Arte marcial basado en técnicas de agarre y proyección para fuerza y disciplina.','Suelo de caucho',14,3),(6,'Box','Deporte de combate con golpes y técnicas de defensa para resistencia y coordinación.','Zona de Sacos',7,1),(7,'Karate','Arte marcial con técnicas de golpeo y bloqueos para fuerza y control corporal.','Suelo de caucho',5,2),(8,'Karate (Estilo Tijuana)','Arte marcial con técnicas de golpeo y bloqueos para fuerza y control corporal.','Suelo de caucho',15,3),(9,'Artes Marciales Mixtas','Combina técnicas de boxeo, jiu-jitsu y lucha para fuerza y defensa.','Suelo de caucho',11,1),(10,'Artes Marciales Mixtas (TJ)','Combina técnicas de boxeo, jiu-jitsu y lucha para fuerza y defensa.','Suelo de caucho',3,3);
/*!40000 ALTER TABLE `Clases_particulares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Cliente`
--

DROP TABLE IF EXISTS `Cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Cliente` (
  `idCliente` int NOT NULL AUTO_INCREMENT,
  `Nombres` varchar(100) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `Correo` varchar(150) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `fechaNacimiento` date DEFAULT NULL,
  `estadoMembresia` enum('DIA_PRUEBA','SEMANAL','MENSUALIDAD','ANUALIDAD','PROVISIONAL','VENCIDA') DEFAULT NULL,
  `fechaVencimiento` date DEFAULT NULL,
  `idCoach` int DEFAULT NULL,
  `fechaAsignacionCoach` date DEFAULT NULL,
  PRIMARY KEY (`idCliente`),
  UNIQUE KEY `Correo` (`Correo`),
  KEY `idx_cliente_coach` (`idCoach`),
  CONSTRAINT `fk_cliente_coach` FOREIGN KEY (`idCoach`) REFERENCES `Coach` (`idCoach`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10225 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Cliente`
--

LOCK TABLES `Cliente` WRITE;
/*!40000 ALTER TABLE `Cliente` DISABLE KEYS */;
INSERT INTO `Cliente` VALUES (1,'Romell Ivan','Salcedo Longoria','riv.salcedo@uabc','686645879','2004-02-24','ANUALIDAD','2027-10-06',1,'2025-12-05'),(2,'Guillermo','Encinas Rolon','gencinas@memos.com','6867771337','2001-07-05','SEMANAL','2026-10-13',1,'2025-12-05'),(10213,'Ana Valeria','Torrojas Vizcaino','ana.torrojas@uabc.edu.mx','6862341960','2006-06-23','MENSUALIDAD','2026-03-04',1,'2025-12-03'),(10214,'Felipe','Camarena Solorsano','felipon68@gmail.com','6866431744','2001-12-06','MENSUALIDAD','2026-01-04',3,'2025-12-04'),(10215,'Ana Fernanda','Rosales Perez','anafer@gmail.com','6866584503','2003-02-16','SEMANAL','2025-12-18',3,'2025-12-01'),(10216,'Alejandro','Sánchez Pizarro','alejsanz@gmail.com','6868942398','1968-12-18','DIA_PRUEBA','2026-12-06',3,'2025-12-05'),(10217,'José','Madero Vizcaino','josemade@uabc.edu.mx','6865581245','1980-11-01','ANUALIDAD','2026-12-04',12,'2025-12-04'),(10218,'Arturo','Perez Reverte','reverteartu@hotmail.com','6647362348','1963-03-24','DIA_PRUEBA','2026-01-13',1,'2025-12-05'),(10219,'Melissa','Robles Jauregui','melisanta@gmail.com','6867732371','1989-09-17','ANUALIDAD','2026-12-04',12,'2025-12-01'),(10220,'Rodolfo','Paez Sainz','fitopaez@gmail.com','6864491264','1962-05-04','MENSUALIDAD','2026-01-04',1,'2025-12-02'),(10221,'Pedro','Páramo Calles','pedritopara@gmail.com','6868327526','1956-01-09','ANUALIDAD','2026-12-04',12,'2025-12-03'),(10222,'Fausto Ignacio','Ramirez de Alba','faustopro@gmail.com','6867236458','1991-10-06','MENSUALIDAD','2026-01-04',1,'2025-12-04'),(10223,'Ivan','Raskolnikov','rasiivan@gmail.com','6641953571','2000-03-19','SEMANAL','2026-01-11',3,'2025-12-02'),(10224,'Walter','White','ibrokebad@gmail.com','6867135724','1969-07-12','PROVISIONAL',NULL,NULL,'2025-12-05');
/*!40000 ALTER TABLE `Cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Coach`
--

DROP TABLE IF EXISTS `Coach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Coach` (
  `idCoach` int NOT NULL AUTO_INCREMENT,
  `Nombres` varchar(100) NOT NULL,
  `Apellidos` varchar(100) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `areadeTrabajo` varchar(100) DEFAULT NULL,
  `Salario` decimal(10,2) DEFAULT NULL,
  `FechadeInicio` date NOT NULL,
  PRIMARY KEY (`idCoach`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Coach`
--

LOCK TABLES `Coach` WRITE;
/*!40000 ALTER TABLE `Coach` DISABLE KEYS */;
INSERT INTO `Coach` VALUES (1,'Vicente Hao','Chen Li','6861112233','Coach de Rutinas',12000.00,'2023-11-22'),(2,'Mario','Postigo Navejas','6861938516','Coach de Yoga',1300.00,'2023-06-24'),(3,'Rodrigo','Torres Mesa','6868648223','Coach de Rutinas',1200.00,'2025-09-13'),(4,'Juan','Rulfo Perez','6861436097','Coach de Judo',1400.00,'2019-07-15'),(5,'Maria Clara','Juarez Toledano','6863657522','Coach de Karate',1200.00,'2022-12-06'),(6,'Alicia','Olmeda Corrales','6868735672','Coach de Crossfit',1100.00,'2020-11-18'),(7,'Teresa','Fernandez Leon','6869024785','Coach de Box',1400.00,'2025-10-04'),(8,'Noelia','Bravo Quintanilla','6869457932','Coach de Yoga',1200.00,'2021-11-29'),(9,'Antonio','Moltisanti Guevara','6868945657','Coach de Judo',1500.00,'2023-07-27'),(10,'Anatoli','Karpov Quezada','6868945623','Coach de Karate',1100.00,'2020-12-04'),(11,'Roberto','Ruteaga Parra','6864536789','Coach de Artes Marciales Mixtas',1400.00,'2024-01-28'),(12,'Alfredo','Bañuelos Cuevas','6860976373','Coach de Rutinas',1300.00,'2022-06-27'),(13,'Porfirio','Lopez Castañeda','6869134556','Coach de Yoga',1800.00,'2024-02-15'),(14,'Carlos','Montes Villanueva','6647823451','Coach de Judo',1400.00,'2024-03-15'),(15,'Diana','Mercado Soto','6648934562','Coach de Karate',1300.00,'2023-08-20');
/*!40000 ALTER TABLE `Coach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Horario_clase`
--

DROP TABLE IF EXISTS `Horario_clase`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Horario_clase` (
  `idHorario` int NOT NULL AUTO_INCREMENT,
  `idClase` int NOT NULL,
  `diaSemana` enum('Lunes','Martes','Miércoles','Jueves','Viernes') NOT NULL,
  `horaInicio` time NOT NULL,
  `horaFin` time NOT NULL,
  PRIMARY KEY (`idHorario`),
  UNIQUE KEY `uk_clase_dia_hora` (`idClase`,`diaSemana`,`horaInicio`),
  KEY `idx_horario_dia` (`diaSemana`),
  CONSTRAINT `fk_horario_clase` FOREIGN KEY (`idClase`) REFERENCES `Clases_particulares` (`idClase`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `chk_horario_valido` CHECK ((`horaInicio` < `horaFin`))
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Horario_clase`
--

LOCK TABLES `Horario_clase` WRITE;
/*!40000 ALTER TABLE `Horario_clase` DISABLE KEYS */;
INSERT INTO `Horario_clase` VALUES (1,1,'Lunes','06:00:00','07:00:00'),(2,2,'Lunes','06:00:00','07:00:00'),(3,6,'Lunes','07:00:00','08:00:00'),(4,9,'Lunes','08:00:00','09:00:00'),(5,6,'Lunes','09:00:00','10:00:00'),(6,6,'Miércoles','07:00:00','08:00:00'),(7,9,'Miércoles','08:00:00','09:00:00'),(8,6,'Miércoles','09:00:00','10:00:00'),(9,6,'Viernes','07:00:00','08:00:00'),(10,9,'Viernes','08:00:00','09:00:00'),(11,6,'Viernes','09:00:00','10:00:00'),(12,9,'Lunes','12:00:00','13:00:00'),(13,6,'Lunes','13:00:00','14:00:00'),(14,9,'Lunes','14:00:00','15:00:00'),(15,6,'Lunes','15:00:00','16:00:00'),(16,9,'Miércoles','12:00:00','13:00:00'),(17,6,'Miércoles','13:00:00','14:00:00'),(18,9,'Miércoles','14:00:00','15:00:00'),(19,6,'Miércoles','15:00:00','16:00:00'),(20,9,'Viernes','12:00:00','13:00:00'),(21,6,'Viernes','13:00:00','14:00:00'),(22,9,'Viernes','14:00:00','15:00:00'),(23,6,'Viernes','15:00:00','16:00:00'),(24,6,'Lunes','18:00:00','19:00:00'),(25,9,'Lunes','19:00:00','20:00:00'),(26,6,'Lunes','20:00:00','21:00:00'),(27,9,'Lunes','21:00:00','22:00:00'),(28,6,'Miércoles','18:00:00','19:00:00'),(29,9,'Miércoles','19:00:00','20:00:00'),(30,6,'Miércoles','20:00:00','21:00:00'),(31,9,'Miércoles','21:00:00','22:00:00'),(32,6,'Viernes','18:00:00','19:00:00'),(33,9,'Viernes','19:00:00','20:00:00'),(34,6,'Viernes','20:00:00','21:00:00'),(35,9,'Viernes','21:00:00','22:00:00'),(63,5,'Lunes','06:00:00','07:00:00'),(64,8,'Lunes','07:00:00','08:00:00'),(65,10,'Lunes','08:00:00','09:00:00'),(66,5,'Lunes','09:00:00','10:00:00'),(67,5,'Miércoles','06:00:00','07:00:00'),(68,8,'Miércoles','07:00:00','08:00:00'),(69,10,'Miércoles','08:00:00','09:00:00'),(70,5,'Miércoles','09:00:00','10:00:00'),(71,5,'Viernes','06:00:00','07:00:00'),(72,8,'Viernes','07:00:00','08:00:00'),(73,10,'Viernes','08:00:00','09:00:00'),(74,5,'Viernes','09:00:00','10:00:00'),(75,8,'Lunes','13:00:00','14:00:00'),(76,10,'Lunes','14:00:00','15:00:00'),(77,5,'Lunes','15:00:00','16:00:00'),(78,8,'Lunes','16:00:00','17:00:00'),(79,8,'Miércoles','13:00:00','14:00:00'),(80,10,'Miércoles','14:00:00','15:00:00'),(81,5,'Miércoles','15:00:00','16:00:00'),(82,8,'Miércoles','16:00:00','17:00:00'),(83,8,'Viernes','13:00:00','14:00:00'),(84,10,'Viernes','14:00:00','15:00:00'),(85,5,'Viernes','15:00:00','16:00:00'),(86,8,'Viernes','16:00:00','17:00:00'),(87,10,'Lunes','19:00:00','20:00:00'),(88,5,'Lunes','20:00:00','21:00:00'),(89,8,'Lunes','21:00:00','22:00:00'),(90,10,'Miércoles','19:00:00','20:00:00'),(91,5,'Miércoles','20:00:00','21:00:00'),(92,8,'Miércoles','21:00:00','22:00:00'),(93,10,'Viernes','19:00:00','20:00:00'),(94,5,'Viernes','20:00:00','21:00:00'),(95,8,'Viernes','21:00:00','22:00:00');
/*!40000 ALTER TABLE `Horario_clase` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Sucursal`
--

DROP TABLE IF EXISTS `Sucursal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Sucursal` (
  `idSucursal` int NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(100) NOT NULL,
  `Telefono` varchar(20) DEFAULT NULL,
  `Direccion` varchar(200) DEFAULT NULL,
  `Ciudad` varchar(100) DEFAULT NULL,
  `hrApertura` time NOT NULL,
  `hrCierre` time NOT NULL,
  PRIMARY KEY (`idSucursal`),
  CONSTRAINT `chk_horario_sucursal` CHECK ((`hrApertura` < `hrCierre`))
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Sucursal`
--

LOCK TABLES `Sucursal` WRITE;
/*!40000 ALTER TABLE `Sucursal` DISABLE KEYS */;
INSERT INTO `Sucursal` VALUES (1,'Sucursal Principal','6861231234','Calle Principal #213','Mexicali','06:00:00','23:00:00'),(2,' Sucursal Zona Dorada','6866120102','Calzada Cetys, Plaza San Pedro','Mexicali','05:30:00','23:30:00'),(3,'Sucursal Tijuana','6642029685','Boulevard Lazaro Cardenas, Plaza Alameda Otay','Tijuana','05:00:00','23:00:00');
/*!40000 ALTER TABLE `Sucursal` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Toma`
--

DROP TABLE IF EXISTS `Toma`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `Toma` (
  `idCliente` int NOT NULL,
  `idClase` int NOT NULL,
  PRIMARY KEY (`idCliente`,`idClase`),
  KEY `fk_toma_clase` (`idClase`),
  CONSTRAINT `fk_toma_clase` FOREIGN KEY (`idClase`) REFERENCES `Clases_particulares` (`idClase`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_toma_cliente` FOREIGN KEY (`idCliente`) REFERENCES `Cliente` (`idCliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Toma`
--

LOCK TABLES `Toma` WRITE;
/*!40000 ALTER TABLE `Toma` DISABLE KEYS */;
INSERT INTO `Toma` VALUES (1,1),(1,2),(2,2),(10216,2),(10216,4),(10218,5),(10221,5),(10223,5),(1,6),(2,6),(10214,6),(10216,6),(10220,6),(10224,6),(10213,7),(10215,7),(10216,7),(10217,7),(10219,7),(10218,8),(10221,8),(1,9),(10214,9),(10216,9),(10222,9),(10216,10),(10221,10),(10223,10);
/*!40000 ALTER TABLE `Toma` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `checkIn`
--

DROP TABLE IF EXISTS `checkIn`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `checkIn` (
  `idCliente` int NOT NULL,
  `diaEntrada` date NOT NULL,
  `horaEntrada` time NOT NULL,
  PRIMARY KEY (`idCliente`,`diaEntrada`,`horaEntrada`),
  CONSTRAINT `fk_checkin_cliente` FOREIGN KEY (`idCliente`) REFERENCES `Cliente` (`idCliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `checkIn`
--

LOCK TABLES `checkIn` WRITE;
/*!40000 ALTER TABLE `checkIn` DISABLE KEYS */;
INSERT INTO `checkIn` VALUES (1,'2025-12-03','18:35:14'),(1,'2025-12-03','18:35:18'),(1,'2025-12-03','18:35:21'),(1,'2025-12-03','19:21:20'),(1,'2025-12-03','19:33:59'),(1,'2025-12-05','10:40:12'),(1,'2025-12-05','11:32:04'),(2,'2025-12-03','18:35:24'),(2,'2025-12-03','19:21:17'),(2,'2025-12-04','03:18:13'),(2,'2025-12-05','10:40:06'),(2,'2025-12-05','11:32:31'),(10213,'2025-12-04','11:47:04'),(10215,'2025-12-05','10:40:32'),(10218,'2025-12-05','10:40:22'),(10221,'2025-12-04','11:47:14');
/*!40000 ALTER TABLE `checkIn` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-07  1:56:51
