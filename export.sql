-- mysqldump-php https://github.com/ifsnop/mysqldump-php
--
-- Host: localhost	Database: meb
-- ------------------------------------------------------
-- Server version 	5.7.33
-- Date: Fri, 09 Dec 2022 14:08:56 +0000

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `client_games`
--

LOCK TABLES `client_games` WRITE;
/*!40000 ALTER TABLE `client_games` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `client_games` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `client_games` with 0 row(s)
--

--
-- Dumping data for table `games`
--

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
SET autocommit=0;
INSERT INTO `games` VALUES (100101,'Clap de vin dans le Golfe de Saint-Tropez','Golfe de Saint-Tropez','/images/games/Cover-jeu-BTW.png','Bienvenue dans le Golfe de Saint Tropez, lieu de tournage de tous les temps ! ?\n\n? Les derniers réalisateurs à être venus dans les années 60, Mr Vadam (ou Mr Angelvam) et Lalouche, ont oublié une mallette dans la voiture qu’ils utilisaient pour se déplacer dans le Golfe de Saint-Tropez lors de leur dernier tournage.\n\nOn dit que les réalisateurs cachaient dans cette mallette leur prochain lieu de tournage dans la région et qu’ils donneraient cher pour retrouver ce lieu si cher à leur coeur. \nEt oui Mr Vadam et Lalouche ont la mémoire un peu courte…\n\nCette information étant considérée comme TOP SECRÈTE, ils avaient scellé la mallette avec un cadenas ?...et ne savent plus où ils doivent se rendre...\n\nPercez le mystère du prochain lieu de tournage pour eux en vous rendant sur les 3 lieux que vous allez vous même identifier afin de récolter des indices.\n\nCes 3 indices vous permettront de retrouver le code de la mallette et de l’ouvrir. Mais l’aventure ne s’arrête pas là...\n\nUn dernier défi vous attendra une fois que vous aurez ouvert la mallette : rejoindre le lieu de tournage et faire une photo de votre équipe avec le matériel présent dans la mallette. \n\nCette photo, si elle est rendue à son propriétaire au plus tôt, vous donnera accès à une récompense Tropézéique ! ?');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `games` with 1 row(s)
--

--
-- Dumping data for table `poi`
--

LOCK TABLES `poi` WRITE;
/*!40000 ALTER TABLE `poi` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `poi` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `poi` with 0 row(s)
--

--
-- Dumping data for table `quest`
--

LOCK TABLES `quest` WRITE;
/*!40000 ALTER TABLE `quest` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `quest` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `quest` with 0 row(s)
--

--
-- Dumping data for table `slide`
--

LOCK TABLES `slide` WRITE;
/*!40000 ALTER TABLE `slide` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `slide` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `slide` with 0 row(s)
--

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
SET autocommit=0;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;

-- Dumped table `user` with 0 row(s)
--

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on: Fri, 09 Dec 2022 14:08:56 +0000
