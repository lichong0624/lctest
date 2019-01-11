/*
SQLyog Ultimate v12.3.2 (64 bit)
MySQL - 8.0.13-3 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `new_collect` (
	`id` int (11),
	`new_id` int (11),
	`user_id` int (11),
	`state` tinyint (1),
	`time` date 
); 
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('6','1','1','1','2019-01-09');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('7','17','1','1','2019-01-09');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('10','20','2','1','2019-01-09');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('11','19','2','1','2019-01-09');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('12','18','2','1','2019-01-09');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('17','19','1','1','2019-01-10');
insert into `new_collect` (`id`, `new_id`, `user_id`, `state`, `time`) values('18','16','1','1','2019-01-10');
