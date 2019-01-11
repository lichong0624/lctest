/*
SQLyog Ultimate v12.3.2 (64 bit)
MySQL - 8.0.13-3 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `admin` (
	`id` int (11),
	`admin` varchar (765),
	`password` varchar (765),
	`nickname` varchar (765),
	`sex` tinyint (1),
	`mobile` char (33),
	`desc` varchar (765)
); 
insert into `admin` (`id`, `admin`, `password`, `nickname`, `sex`, `mobile`, `desc`) values('55','admin','60d6c1b19643e2af7327a173d446420a','ttt恶6u','2','18706716068','tt');
insert into `admin` (`id`, `admin`, `password`, `nickname`, `sex`, `mobile`, `desc`) values('56','test','709af0f95a92359c314f8a069fc91ec0',NULL,'0',NULL,NULL);
insert into `admin` (`id`, `admin`, `password`, `nickname`, `sex`, `mobile`, `desc`) values('57','qqqq','709af0f95a92359c314f8a069fc91ec0',NULL,'0',NULL,NULL);
