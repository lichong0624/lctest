/*
SQLyog Ultimate v12.3.2 (64 bit)
MySQL - 8.0.13-3 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `news` (
	`id` int (11),
	`news_name` varchar (765),
	`news_content` text ,
	`news_author` varchar (765),
	`news_time` date ,
	`class_id` int (11),
	`state` tinyint (2),
	`user_id` int (11)
); 
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('1','因能源问题与普京吵架 卢卡申科道歉：讲错话了','就在刚刚举行的欧亚经济联盟峰会上，白俄罗斯和俄罗斯这对兄弟国家又因为能源价格问题“吵了一架”。但白俄罗斯总统卢卡申科马上在会后表示：好吧，是我讲话不好听，我向普京道歉！\r\n　　据俄罗斯卫星通讯社12月7日报道，白俄罗斯总统亚历山大•卢卡申科称，在与俄罗斯总统普京就俄罗斯向白俄罗斯的供气价格进行讨论时，因自己表达不准确而引起双方争议。他表示，已向普京道歉\r\n白俄罗斯国家通讯社（BelTA）12月6日报道称，在欧亚经济联盟峰会上，卢卡申科和普京公开就俄罗斯对白俄罗斯的供气价格起了争论。\r\n　　卢卡申科认为，俄罗斯对白俄罗斯的供气税率太高，俄罗斯天然气工业股份公司将天然气从俄罗斯亚马尔-涅涅茨自治区运送至白俄罗斯边境，每100公里就要收取近3美元/千立方米的税，而俄罗斯内部关税约为1美元/千立方米。\r\n　　而正是由于高昂的税率，白俄罗斯与俄罗斯斯摩棱斯克州接壤地区的天然气价格接近每130美元/千立方米，而斯摩棱斯克州消费者的批发价格为70美元/千立方米，几乎是白俄罗斯的一半。\r\n　　“一个简单的问题，”卢卡申科说，“在这种情况下我们如何竞争？”ddddddd\r\n普京对此则称，重要的是价格而不是税率。他将白俄罗斯的天然气价格与邻国德国比较。他称，今年俄罗斯对白俄罗斯的天然气收费标准是129美元/千立方米，明年则是127美元/千立方米，而对德国，届时将会是250美元/千立方米。\r\n　　对于普京的回应，卢卡申科似乎并不满意：“您说得很对，然而，幸或不幸的是，我们的关键合作伙伴不是德国而是俄罗斯，而我们的主要合作伙伴和竞争对手是俄罗斯人。”\r\n　　他强调，俄罗斯和白俄罗斯是兄弟国家，在这个问题上，两国的消费者应该被平等对待。\r\n　　俄罗斯卫星通讯社在报道中称，会后，卢卡申科在回答记者的问题时说：“我在会上的表述措辞不当。所以必须要对今天会议的东道主致歉。此事有很多种解读，但是你们最好还是不要知道这些。”\r\n　　另外，白俄罗斯国家通讯社在同天的另一则报道中提到，卢卡申科和普京计划在下周一或周二举行会面，届时将对两国的税收政策做进一步讨论。','观察者','2018-12-12','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('2','美军舰在俄海军驻地附近“示威” 却被俄方嘲笑了','55555','海外网','2018-12-07','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('12','1111111111111111111111111','3333s','aa','2018-12-13','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('13','鱼的记忆','额e问题他啊','一天一夜','2018-12-18','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('14','嘎嘎嘎','嗯嗯特尔AV通过','二塔','2018-12-05','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('15','烦烦烦','等发达的的','阿道夫','2018-12-04','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('16','太突然他','45455456456 ','3ff ','2018-12-26','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('17','二二二','4regebbjk,u','gghr','2019-01-16','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('18','wwww','wwwwww','www','2019-01-15','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('19','四四四','444','hj','2019-01-20','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('20','国际媒体头条速览：特朗普一炸向退役陆军上将','国际媒体头条速览：特朗普一炸向退役陆军上将','会突然突然好','2019-01-23','1','1',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('21','ddd','ddd','',NULL,'7','0',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('22','ll','lll','llll',NULL,'7','0',NULL);
insert into `news` (`id`, `news_name`, `news_content`, `news_author`, `news_time`, `class_id`, `state`, `user_id`) values('23','ll','lll','llll',NULL,'7','0',NULL);
