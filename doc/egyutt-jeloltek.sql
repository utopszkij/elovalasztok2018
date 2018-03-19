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
/*Table structure for table `egyutt` */

DROP TABLE IF EXISTS `egyutt`;

CREATE TABLE `egyutt` (
  `oevk` varchar(40) COLLATE utf8_hungarian_ci DEFAULT NULL,
  `nev` varchar(120) COLLATE utf8_hungarian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_hungarian_ci;

/*Data for the table `egyutt` */

insert  into `egyutt`(`oevk`,`nev`) values ('Bács-Kiskun megye 01. OEVK','Kacsó Balázs'),('Bács-Kiskun megye 02. OEVK','Somodi Klára'),('Bács-Kiskun megye 03. OEVK','Szombathelyi Péter'),('Bács-Kiskun megye 04. OEVK','Bükkösi Zoltán'),('Bács-Kiskun megye 05. OEVK','Tóth Zoltán'),('Bács-Kiskun megye 06. OEVK','?'),('Baranya megye 01. OEVK','Berkecz Balázs'),('Baranya megye 02. OEVK','Rajnai Attila'),('Baranya megye 03. OEVK','Jenei Zsanett'),('Baranya megye 04. OEVK','Vassné Ágnes'),('Békés megye 01. OEVK','Körömi János'),('Békés megye 02. OEVK','Németh Judit'),('Békés megye 03. OEVK','Bod Tamás'),('Békés megye 04. OEVK','Kovács István'),('Borsod-Abaúj-Zemplén megye 01. OEVK','Juhász Jánosné Pandák Margit'),('Borsod-Abaúj-Zemplén megye 02. OEVK','Tóth László'),('Borsod-Abaúj-Zemplén megye 03. OEVK','Báder József'),('Borsod-Abaúj-Zemplén megye 04. OEVK','Vajsz Eleonóra'),('Borsod-Abaúj-Zemplén megye 05. OEVK','Szabó Mihály'),('Borsod-Abaúj-Zemplén megye 06. OEVK','Borbély Péter'),('Borsod-Abaúj-Zemplén megye 07. OEVK','Huzsvári Erzsébet'),('Budapest 01. OEVK','Juhász Péter'),('Budapest 02. OEVK','Szíjj Zsuzsanna dr.'),('Budapest 03. OEVK','Komáromi Zoltán dr.'),('Budapest 04. OEVK','?'),('Budapest 05. OEVK','Nyíri Gábor'),('Budapest 06. OEVK','Baranyi Krisztina'),('Budapest 07. OEVK','Spät Judit'),('Budapest 08. OEVK','?'),('Budapest 09. OEVK','Tábi Attila'),('Budapest 10. OEVK','Pataki Márton'),('Budapest 11. OEVK','Légrádi Péter'),('Budapest 12. OEVK','Szigetvári Viktor'),('Budapest 13. OEVK','Vajda Zoltán'),('Budapest 14. OEVK','Kóber György Márk'),('Budapest 15. OEVK','Mérő Péter'),('Budapest 16. OEVK','Lázár Róbert'),('Budapest 17. OEVK','Szabó Szabolcs dr.'),('Budapest 18. OEVK','Hajdu Nóra dr.'),('Csongrád megye 01. OEVK','Nagy Sándor'),('Csongrád megye 02. OEVK','Györgyey János'),('Csongrád megye 03. OEVK','Dombi Gábor'),('Csongrád megye 04. OEVK','Wéber Katalin'),('Fejér megye 01. OEVK','Buth Sándor'),('Fejér megye 02. OEVK','Bucher Fanni'),('Fejér megye 03. OEVK','Kulcsár Miklós'),('Fejér megye 04. OEVK','Mihalik Zoltán'),('Fejér megye 05. OEVK','Hegedűs Norbert'),('Győr-Moson Sopron megye 01. OEVK','Varga Márk'),('Győr-Moson-Sopron megye 02. OEVK','Kecskeméty – Rády Magdolna'),('Győr-Moson-Sopron megye 03. OEVK','Csornai Károly'),('Győr-Moson-Sopron megye 04. OEVK','Nádas Ágnes'),('Győr-Moson-Sopron megye 05. OEVK','Deschelák Károly'),('Hajdú-Bihar megye 01. OEVK','Szegedi István dr.'),('Hajdú-Bihar megye 02. OEVK','Orosz Tamás dr.'),('Hajdú-Bihar megye 03. OEVK','Kosztin Mihály'),('Hajdú-Bihar megye 04. OEVK','Oláh Lajos'),('Hajdú-Bihar megye 05. OEVK','Rutz Tamás'),('Hajdú-Bihar megye 06. OEVK','Szemők Zoltán'),('Heves megye 01. OEVK','Derda Ádám'),('Heves megye 02. OEVK','Réz Ágnes'),('Heves megye 03. OEVK','Kalcsó Tünde'),('Jász-Nagykun-Szolnok megye 01. OEVK','Nagy Attila'),('Jász-Nagykun-Szolnok megye 02. OEVK','Bényi Hajnal'),('Jász-Nagykun-Szolnok megye 03. OEVK','Kammerer Gábor'),('Jász-Nagykun-Szolnok megye 04. OEVK','Lőrincz Gábor'),('Komárom-Esztergom megye 01. OEVK','Balogh József'),('Komárom-Esztergom megye 02. OEVK','Babiczky László'),('Komárom-Esztergom megye 03. OEVK','Kiss Imre'),('Nógrád megye 01. OEVK','Árvai Zsóka'),('Nógrád megye 02. OEVK','Sütő Dezső'),('Pest megye 01. OEVK','Csornainé Romhányi Judit'),('Pest megye 02. OEVK','Serény József'),('Pest megye 03. OEVK','Elek Zsófia'),('Pest megye 04. OEVK','Jakab Zoltán'),('Pest megye 05. OEVK','Vargha Nóra'),('Pest megye 06. OEVK','Gombkötő Róbert'),('Pest megye 07. OEVK','Tóth Judit'),('Pest megye 08. OEVK','Orosz László'),('Pest megye 09. OEVK','Törökné Fejes Györgyi'),('Pest megye 10. OEVK','Papp Roland'),('Pest megye 11. OEVK','Benkovits Cecília'),('Pest megye 12. OEVK','Szabó László'),('Somogy megye 01. OEVK','Kerepesi Tibor'),('Somogy megye 02. OEVK','Németh Imre'),('Somogy megye 03. OEVK','Pintér Lóránt'),('Somogy megye 04. OEVK','Zsiga Csaba'),('Szabolcs-Szatmár-Bereg megye 01. OEVK','Lővei Csaba'),('Szabolcs-Szatmár-Bereg megye 02. OEVK','Ambrus Magdolna'),('Szabolcs-Szatmár-Bereg megye 03. OEVK','Herczku Tímea'),('Szabolcs-Szatmár-Bereg megye 04. OEVK','Ritter Ottó'),('Szabolcs-Szatmár-Bereg megye 05. OEVK','Máté Miklós'),('Szabolcs-Szatmár-Bereg megye 06. OEVK','Hegyháti Szilvia'),('Tolna megye 01. OEVK','Misli Balázs'),('Tolna megye 02. OEVK','Ádám Csaba'),('Tolna megye 03. OEVK','Schuckert Viktória'),('Vas megye 01. OEVK','Varga Tamás'),('Vas megye 02. OEVK','Herege Mónika'),('Vas megye 03. OEVK','Szabó Judit'),('Veszprém megye 01. OEVK','Erdei Rita'),('Veszprém megye 02. OEVK','Nógrádi Nóra'),('Veszprém megye 03. OEVK','Molnár Tibor'),('Veszprém megye 04. OEVK','Dr. Bányai Katalin'),('Zala megye 01. OEVK','Sági Tamás'),('Zala megye 02. OEVK','Kárász Márton'),('Zala megye 03. OEVK','Jellinek János');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
