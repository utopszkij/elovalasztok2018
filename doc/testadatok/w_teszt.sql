/*
SQLyog Community v9.02 
MySQL - 5.6.12 : Database - elovalasztok
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `w_teszt` */

DROP TABLE IF EXISTS `w_teszt`;

CREATE TABLE `w_teszt` (
  `i` int(11) NOT NULL AUTO_INCREMENT,
  `a` int(11) DEFAULT NULL,
  `b` int(11) DEFAULT NULL,
  `c` int(11) DEFAULT NULL,
  `d` int(11) DEFAULT NULL,
  `e` int(11) DEFAULT NULL,
  `filler` char(1) DEFAULT NULL,
  PRIMARY KEY (`i`)
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=latin1;

/*Data for the table `w_teszt` */

insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (1,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (2,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (3,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (4,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (5,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (6,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (7,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (8,3,1,2,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (9,3,2,5,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (10,3,2,4,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (11,5,3,2,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (12,5,4,3,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (13,5,1,2,3,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (14,5,2,3,4,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (15,5,3,4,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (16,4,1,2,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (17,4,2,1,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (18,4,3,5,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (19,4,3,2,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (20,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (21,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (22,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (23,3,1,2,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (24,3,2,5,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (25,3,2,4,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (26,5,3,2,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (27,5,4,3,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (28,5,1,2,3,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (29,5,3,4,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (30,4,1,2,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (31,4,2,1,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (32,4,3,5,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (33,4,3,2,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (34,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (35,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (36,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (37,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (38,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (39,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (40,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (41,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (42,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (43,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (44,3,1,2,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (45,3,2,5,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (46,3,2,4,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (47,5,3,2,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (48,5,4,3,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (49,5,1,2,3,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (50,5,2,3,4,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (51,5,3,4,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (52,4,1,2,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (53,4,2,1,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (54,4,3,5,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (55,5,4,3,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (56,5,1,2,3,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (57,5,2,3,4,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (58,5,3,4,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (59,4,1,2,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (60,4,2,1,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (61,4,3,5,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (62,4,3,2,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (63,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (64,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (65,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (66,3,1,2,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (67,3,2,5,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (68,3,2,4,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (69,5,3,2,1,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (70,5,4,3,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (71,5,1,2,3,4,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (72,5,3,4,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (73,4,1,2,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (74,4,2,1,3,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (75,4,3,5,2,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (76,4,3,2,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (77,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (78,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (79,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (80,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (81,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (82,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (83,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (84,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (85,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (86,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (87,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (88,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (89,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (90,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (91,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (92,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (93,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (94,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (95,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (96,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (97,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (98,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (99,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (100,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (101,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (102,1,2,3,4,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (103,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (104,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (105,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (106,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (107,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (108,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (109,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (110,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (111,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (112,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (113,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (114,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (115,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (116,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (117,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (118,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (119,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (120,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (121,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (122,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (123,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (124,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (125,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (126,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (127,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (128,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (129,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (130,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (131,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (132,2,4,3,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (133,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (134,2,5,4,3,2,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (135,2,4,3,1,5,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (136,2,3,4,5,1,'');
insert  into `w_teszt`(`i`,`a`,`b`,`c`,`d`,`e`,`filler`) values (137,2,5,4,3,2,'');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
