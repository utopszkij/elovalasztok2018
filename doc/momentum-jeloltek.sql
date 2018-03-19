/*
SQLyog Community v9.02 
MySQL - 5.7.20-0ubuntu0.16.04.1 : Database - elovalasztok
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `momentum` */

DROP TABLE IF EXISTS `momentum`;

CREATE TABLE `momentum` (
  `id` int(11) DEFAULT NULL,
  `oevk` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `nev` varchar(120) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Data for the table `momentum` */

insert  into `momentum`(`id`,`oevk`,`nev`) values (1,'Bács-Kiskun 01','Bodrozsán Alexandra'),(2,'Bács-Kiskun 02','Csontos Gábor'),(3,'Bács-Kiskun 03','Környei Balázs'),(4,'Bács-Kiskun 04','Hegedűs Barnabás'),(5,'Bács-Kiskun 05','Takács Zoltán'),(6,'Bács-Kiskun 06','Béni Kornél'),(7,'Baranya 01','Nemes Balázs'),(8,'Baranya 02','Körömi Attila'),(9,'Baranya 03','Pokorádi Gábor'),(10,'Baranya 04','Gergely Attila'),(11,'Békés 01','Almási István'),(12,'Békés 02','Szatmári Péter'),(13,'Békés 03','Nagy Zoltán'),(14,'Békés 04','Sebők Éva'),(15,'Borsod-Abaúj-Zemplén 01','Szopkó Tibor'),(16,'Borsod-Abaúj-Zemplén 02','Prokaj Tamás'),(17,'Borsod-Abaúj-Zemplén 03','Kovács József'),(18,'Borsod-Abaúj-Zemplén 04','Győri Gyula'),(19,'Borsod-Abaúj-Zemplén 05','Pencz András'),(20,'Borsod-Abaúj-Zemplén 06','Budai János'),(21,'Borsod-Abaúj-Zemplén 07','Lengyel Ádám'),(22,'Budapest 01','Fekete-Győr András'),(23,'Budapest 02','Bedő Dávid'),(24,'Budapest 03','Kádár Barnabás'),(25,'Budapest 04','Benedek Márton'),(26,'Budapest 05','Soproni Tamás'),(27,'Budapest 06','Cseh Katalin'),(28,'Budapest 07','Hajnal Miklós'),(29,'Budapest 08','Szücs Attila'),(30,'Budapest 09','Tölcsér Borbála'),(31,'Budapest 10','Donáth Anna'),(32,'Budapest 11','Molnár Tibor'),(33,'Budapest 12','Krisztics Bianka'),(34,'Budapest 13','Hollai Gábor'),(35,'Budapest 14','Tompos Márton'),(36,'Budapest 15','Szalóky Réka'),(37,'Budapest 16','Teveli Dalma'),(38,'Budapest 17','Dukán András Ferenc'),(39,'Budapest 18','Orosz Anna'),(40,'Csongrád 01','Mihálik Edvin'),(41,'Csongrád 02','Boros-Gyevi Gergely'),(42,'Csongrád 03','Csányi Balázs'),(43,'Csongrád 04','Jakab Tamás'),(44,'Fejér 01','Pintér András Gábor'),(45,'Fejér 02','Tóth Péter'),(46,'Fejér 03','Molnár Tamás'),(47,'Fejér 04','Kaszó Róbert'),(48,'Fejér 05','Bálint Zoltán'),(49,'Győr-Moson-Sopron 01','Molnár József'),(50,'Győr-Moson-Sopron 02','Liszi Norbert'),(51,'Győr-Moson-Sopron 03','Havasi Ádám'),(52,'Győr-Moson-Sopron 04','Supka-Kovácsné Holzhofer Tünde'),(53,'Győr-Moson-Sopron 05','Maróti Csanád'),(54,'Hajdú-Bihar 01','Horváth Zoltán'),(55,'Hajdú-Bihar 02','Mándi László'),(56,'Hajdú-Bihar 03','Lakatos Árpád'),(57,'Hajdú-Bihar 04','Buzinkay György'),(58,'Hajdú-Bihar 05','Gyuris Dóra'),(59,'Hajdú-Bihar 06','Kovács Roland'),(60,'Heves 01','Tóth Zoltán'),(61,'Heves 02','Bakó Béla'),(62,'Heves 03','Déri Tibor'),(63,'Jász-Nagykun-Szolnok 01','Szekeres Éva'),(64,'Jász-Nagykun-Szolnok 02','Pálffy István'),(65,'Jász-Nagykun-Szolnok 03','Szincsák Gergő Bendegúz'),(66,'Jász-Nagykun-Szolnok 04','Papp Gergő'),(67,'Komárom-Esztergom 01','Novák László'),(68,'Komárom-Esztergom 02','Cserép János'),(69,'Komárom-Esztergom 03','Lakatos Béla'),(70,'Nógrád 01','Barta László'),(71,'Nógrád 02','Benkó Tamás'),(72,'Pest 01','Gál Alex'),(73,'Pest 02','Szemző Áron'),(74,'Pest 03','Vásárhelyi Judit'),(75,'Pest 04','Juhász Béla'),(76,'Pest 05','Kohut Ákos'),(77,'Pest 06','Rehó Sándor'),(78,'Pest 07','Kalasovszky Bernadett'),(79,'Pest 08','Tótok József'),(80,'Pest 09','Kiss Zoltán'),(81,'Pest 10','Janzsó Miklós'),(82,'Pest 11','Galgóczi Péter'),(83,'Pest 12','Mucsinyi Levente'),(84,'Somogy 01','Berg Dániel'),(85,'Somogy 02','Rózsa András'),(86,'Somogy 03','Fábián László'),(87,'Somogy 04','Tóth Péter'),(88,'Szabolcs-Szatmár-Bereg 01','Babosi György'),(89,'Szabolcs-Szatmár-Bereg 02','Szántó Gábor'),(90,'Szabolcs-Szatmár-Bereg 03','Papp Csaba'),(91,'Szabolcs-Szatmár-Bereg 04','Áncsán Márton'),(92,'Szabolcs-Szatmár-Bereg 05','Magyar Péter'),(93,'Szabolcs-Szatmár-Bereg 06','Bankus Tibor János'),(94,'Tolna 01','Rácz Norbert'),(95,'Tolna 02','Zaránd Péter'),(96,'Tolna 03','Dobosi Norbert'),(97,'Vas 01','Taoufik Roland'),(98,'Vas 02','Kovács Attila'),(99,'Vas 03','Gerencsér Mária'),(100,'Veszprém 01','Dr.Meződi János'),(101,'Veszprém 02','Baán Barnabás'),(102,'Veszprém 03','Csernák Éva'),(103,'Veszprém 04','Benedek Szilveszter'),(104,'Zala 01','Sencz András'),(105,'Zala 02','Elekes István'),(106,'Zala 03','Dr.Polónyi Tamás');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
