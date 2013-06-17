DROP TABLE IF EXISTS aphs_accounts;

CREATE TABLE `aphs_accounts` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email` varchar(70) CHARACTER SET latin1 NOT NULL,
  `account_type` enum('owner','mainadmin','admin','hotelowner') CHARACTER SET latin1 NOT NULL DEFAULT 'mainadmin',
  `hotels` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `preferred_language` varchar(2) CHARACTER SET latin1 NOT NULL,
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_lastlogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_accounts VALUES("1","Barock","Roy","admin","¸ài­ê2mìª¬Íñ","barock@astamanavilla.com","owner","","en","0000-00-00 00:00:00","2013-06-09 00:32:13","1");



DROP TABLE IF EXISTS aphs_banlist;

CREATE TABLE `aphs_banlist` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ban_item` varchar(70) CHARACTER SET latin1 NOT NULL,
  `ban_item_type` enum('IP','Email') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'IP',
  `ban_reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ban_ip` (`ban_item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_banners;

CREATE TABLE `aphs_banners` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `image_file` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `image_file_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` tinyint(1) NOT NULL DEFAULT '0',
  `link_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `priority_order` (`priority_order`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_banners VALUES("1","jdo9o4ut7zl4qnakungx.jpg","jdo9o4ut7zl4qnakungx_thumb.jpg","1","","1");
INSERT INTO aphs_banners VALUES("2","jxupensxh5q8li1n33zn.jpg","jxupensxh5q8li1n33zn_thumb.jpg","2","","1");
INSERT INTO aphs_banners VALUES("3","l63zfwtsr2tmrespc8x2.jpg","l63zfwtsr2tmrespc8x2_thumb.jpg","3","","1");
INSERT INTO aphs_banners VALUES("4","ti0z69fsn7f5u9o07wfk.jpg","ti0z69fsn7f5u9o07wfk_thumb.jpg","4","","1");
INSERT INTO aphs_banners VALUES("5","y6lqa0a1zje87pe7q7uo.jpg","y6lqa0a1zje87pe7q7uo_thumb.jpg","5","","1");



DROP TABLE IF EXISTS aphs_banners_description;

CREATE TABLE `aphs_banners_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `banner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `image_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_banners_description VALUES("1","1","en","Marutha at Astamana Villa\'s compromises Three-bed room, with private swimming pools, private balcony, free internet access, living rooms, kitchen and dine places, TV Satellite");
INSERT INTO aphs_banners_description VALUES("4","2","en","");
INSERT INTO aphs_banners_description VALUES("7","3","en","");
INSERT INTO aphs_banners_description VALUES("10","4","en","");
INSERT INTO aphs_banners_description VALUES("13","5","en","");



DROP TABLE IF EXISTS aphs_bookings;

CREATE TABLE `aphs_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(20) CHARACTER SET latin1 NOT NULL,
  `hotel_reservation_id` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `booking_description` varchar(255) CHARACTER SET latin1 NOT NULL,
  `discount_percent` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `discount_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `order_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pre_payment_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `pre_payment_value` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `vat_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `vat_percent` decimal(5,3) unsigned NOT NULL DEFAULT '0.000',
  `initial_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `payment_sum` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `additional_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'USD',
  `rooms_amount` tinyint(4) NOT NULL DEFAULT '0',
  `customer_id` int(11) NOT NULL DEFAULT '0',
  `is_admin_reservation` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `transaction_number` varchar(30) CHARACTER SET latin1 NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `payment_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - POA, 1 - Online Order, 2 - PayPal, 3 - 2CO, 4 - Authorize.Net, 5 - Bank Transfer',
  `payment_method` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - Payment Company Account, 1 - Credit Card, 2 - E-Check',
  `coupon_code` varchar(20) CHARACTER SET latin1 DEFAULT NULL,
  `discount_campaign_id` int(10) DEFAULT '0',
  `additional_info` text COLLATE utf8_unicode_ci NOT NULL,
  `extras` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `extras_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `cc_type` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_holder_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `cc_number` varchar(50) CHARACTER SET latin1 NOT NULL,
  `cc_expires_month` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_expires_year` varchar(4) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `cc_cvv_code` varchar(4) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - preparing, 1 - reserved, 2 - completed, 3 - refunded, 4 - payment error, 5 - canceled',
  `status_changed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status_description` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email_sent` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `payment_type` (`payment_type`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_bookings VALUES("1","CT5R2ZMY4Y","","Rooms Reservation","0.00","0.00","55.00","full price","0.00","0.00","0.000","0.00","55.00","0.00","USD","1","1","1","","2013-06-02 11:39:10","2013-06-02 11:41:09","0","0","","0","","a:0:{}","0.00","","","´h30jýQ^GÏÐK;%","","","","2","2013-06-02 11:39:15","","1");
INSERT INTO aphs_bookings VALUES("2","2YF5AMB9U5","","Rooms Reservation","0.00","0.00","55.00","full price","0.00","0.00","0.000","0.00","55.00","0.00","USD","1","1","1","","2013-06-02 11:45:51","0000-00-00 00:00:00","0","0","","0","","a:0:{}","0.00","","","´h30jýQ^GÏÐK;%","","","","1","2013-06-02 11:45:55","","0");
INSERT INTO aphs_bookings VALUES("4","4X9WECO6AA","","Rooms Reservation","0.00","0.00","55.00","full price","0.00","0.00","0.000","0.00","55.00","0.00","USD","1","2","0","","2013-06-05 20:07:29","0000-00-00 00:00:00","0","0","","0","","a:0:{}","0.00","","","´h30jýQ^GÏÐK;%","","","","1","2013-06-05 20:07:37","","0");
INSERT INTO aphs_bookings VALUES("5","3I4LBMQY66","","Rooms Reservation","0.00","0.00","80.00","full price","0.00","0.00","0.000","0.00","80.00","0.00","USD","1","2","0","","2013-06-05 20:19:16","0000-00-00 00:00:00","0","0","","0","","a:0:{}","0.00","","","´h30jýQ^GÏÐK;%","","","","5","2013-06-05 20:19:56","This booking was canceled by customer.","0");



DROP TABLE IF EXISTS aphs_bookings_rooms;

CREATE TABLE `aphs_bookings_rooms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `hotel_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `room_id` int(11) NOT NULL DEFAULT '0',
  `room_numbers` varchar(12) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `checkin` date DEFAULT NULL,
  `checkout` date DEFAULT NULL,
  `adults` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `children` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `rooms` tinyint(1) NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `guests` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `guests_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `meal_plan_id` int(11) unsigned NOT NULL DEFAULT '0',
  `meal_plan_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `booking_number` (`booking_number`),
  KEY `room_id` (`room_id`),
  KEY `hotel_id` (`hotel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_bookings_rooms VALUES("1","CT5R2ZMY4Y","1","1","","2013-06-02","2013-06-03","1","0","1","55.00","0","0.00","1","0.00");
INSERT INTO aphs_bookings_rooms VALUES("3","2YF5AMB9U5","1","1","","2013-06-02","2013-06-03","1","0","1","55.00","0","0.00","1","0.00");
INSERT INTO aphs_bookings_rooms VALUES("5","QO27CZB1GY","1","1","","2013-06-02","2013-06-03","1","0","1","55.00","0","0.00","1","0.00");
INSERT INTO aphs_bookings_rooms VALUES("6","4X9WECO6AA","1","1","","2013-06-05","2013-06-06","1","0","1","55.00","0","0.00","1","0.00");
INSERT INTO aphs_bookings_rooms VALUES("7","3I4LBMQY66","1","2","","2013-06-25","2013-06-26","1","0","1","80.00","0","0.00","1","0.00");



DROP TABLE IF EXISTS aphs_campaigns;

CREATE TABLE `aphs_campaigns` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `campaign_type` enum('global','standard') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'global',
  `campaign_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date NOT NULL,
  `finish_date` date NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `target_group_id` (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_campaigns VALUES("1","0","standard","Year End Campign","2013-12-01","2014-01-07","5.00","1");



DROP TABLE IF EXISTS aphs_comments;

CREATE TABLE `aphs_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(10) NOT NULL DEFAULT '0',
  `user_id` int(10) NOT NULL DEFAULT '0',
  `user_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(70) CHARACTER SET latin1 NOT NULL,
  `comment_text` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `date_published` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`,`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_countries;

CREATE TABLE `aphs_countries` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `abbrv` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vat_value` decimal(5,3) NOT NULL DEFAULT '0.000',
  `priority_order` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `abbrv` (`abbrv`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_countries VALUES("1","AF","Afghanistan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("2","AL","Albania","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("3","DZ","Algeria","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("4","AS","American Samoa","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("5","AD","Andorra","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("6","AO","Angola","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("7","AI","Anguilla","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("8","AQ","Antarctica","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("9","AG","Antigua and Barbuda","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("10","AR","Argentina","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("11","AM","Armenia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("12","AW","Aruba","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("13","AU","Australia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("14","AT","Austria","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("15","AZ","Azerbaijan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("16","BS","Bahamas","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("17","BH","Bahrain","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("18","BD","Bangladesh","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("19","BB","Barbados","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("20","BY","Belarus","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("21","BE","Belgium","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("22","BZ","Belize","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("23","BJ","Benin","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("24","BM","Bermuda","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("25","BT","Bhutan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("26","BO","Bolivia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("27","BA","Bosnia and Herzegowina","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("28","BW","Botswana","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("29","BV","Bouvet Island","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("30","BR","Brazil","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("31","IO","British Indian Ocean Territory","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("32","VG","British Virgin Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("33","BN","Brunei Darussalam","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("34","BG","Bulgaria","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("35","BF","Burkina Faso","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("36","BI","Burundi","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("37","KH","Cambodia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("38","CM","Cameroon","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("39","CA","Canada","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("40","CV","Cape Verde","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("41","KY","Cayman Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("42","CF","Central African Republic","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("43","TD","Chad","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("44","CL","Chile","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("45","CN","China","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("46","CX","Christmas Island","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("47","CC","Cocos (Keeling) Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("48","CO","Colombia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("49","KM","Comoros","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("50","CG","Congo","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("51","CK","Cook Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("52","CR","Costa Rica","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("53","CI","Cote D\'ivoire","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("54","HR","Croatia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("55","CU","Cuba","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("56","CY","Cyprus","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("57","CZ","Czech Republic","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("58","DK","Denmark","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("59","DJ","Djibouti","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("60","DM","Dominica","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("61","DO","Dominican Republic","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("62","TP","East Timor","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("63","EC","Ecuador","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("64","EG","Egypt","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("65","SV","El Salvador","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("66","GQ","Equatorial Guinea","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("67","ER","Eritrea","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("68","EE","Estonia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("69","ET","Ethiopia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("70","FK","Falkland Islands (Malvinas)","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("71","FO","Faroe Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("72","FJ","Fiji","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("73","FI","Finland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("74","FR","France","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("75","GF","French Guiana","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("76","PF","French Polynesia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("77","TF","French Southern Territories","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("78","GA","Gabon","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("79","GM","Gambia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("80","GE","Georgia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("81","DE","Germany","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("82","GH","Ghana","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("83","GI","Gibraltar","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("84","GR","Greece","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("85","GL","Greenland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("86","GD","Grenada","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("87","GP","Guadeloupe","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("88","GU","Guam","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("89","GT","Guatemala","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("90","GN","Guinea","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("91","GW","Guinea-Bissau","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("92","GY","Guyana","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("93","HT","Haiti","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("94","HM","Heard and McDonald Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("95","HN","Honduras","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("96","HK","Hong Kong","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("97","HU","Hungary","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("98","IS","Iceland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("99","IN","India","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("100","ID","Indonesia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("101","IQ","Iraq","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("102","IE","Ireland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("103","IR","Islamic Republic of Iran","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("104","IL","Israel","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("105","IT","Italy","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("106","JM","Jamaica","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("107","JP","Japan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("108","JO","Jordan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("109","KZ","Kazakhstan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("110","KE","Kenya","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("111","KI","Kiribati","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("112","KP","Korea, Dem. Peoples Rep of","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("113","KR","Korea, Republic of","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("114","KW","Kuwait","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("115","KG","Kyrgyzstan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("116","LA","Laos","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("117","LV","Latvia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("118","LB","Lebanon","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("119","LS","Lesotho","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("120","LR","Liberia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("121","LY","Libyan Arab Jamahiriya","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("122","LI","Liechtenstein","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("123","LT","Lithuania","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("124","LU","Luxembourg","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("125","MO","Macau","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("126","MK","Macedonia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("127","MG","Madagascar","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("128","MW","Malawi","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("129","MY","Malaysia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("130","MV","Maldives","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("131","ML","Mali","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("132","MT","Malta","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("133","MH","Marshall Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("134","MQ","Martinique","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("135","MR","Mauritania","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("136","MU","Mauritius","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("137","YT","Mayotte","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("138","MX","Mexico","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("139","FM","Micronesia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("140","MD","Moldova, Republic of","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("141","MC","Monaco","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("142","MN","Mongolia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("143","MS","Montserrat","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("144","MA","Morocco","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("145","MZ","Mozambique","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("146","MM","Myanmar","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("147","NA","Namibia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("148","NR","Nauru","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("149","NP","Nepal","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("150","NL","Netherlands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("151","AN","Netherlands Antilles","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("152","NC","New Caledonia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("153","NZ","New Zealand","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("154","NI","Nicaragua","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("155","NE","Niger","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("156","NG","Nigeria","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("157","NU","Niue","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("158","NF","Norfolk Island","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("159","MP","Northern Mariana Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("160","NO","Norway","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("161","OM","Oman","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("162","PK","Pakistan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("163","PW","Palau","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("164","PA","Panama","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("165","PG","Papua New Guinea","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("166","PY","Paraguay","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("167","PE","Peru","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("168","PH","Philippines","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("169","PN","Pitcairn","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("170","PL","Poland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("171","PT","Portugal","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("172","PR","Puerto Rico","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("173","QA","Qatar","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("174","RE","Reunion","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("175","RO","Romania","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("176","RU","Russian Federation","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("177","RW","Rwanda","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("178","LC","Saint Lucia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("179","WS","Samoa","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("180","SM","San Marino","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("181","ST","Sao Tome and Principe","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("182","SA","Saudi Arabia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("183","SN","Senegal","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("184","YU","Serbia and Montenegro","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("185","SC","Seychelles","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("186","SL","Sierra Leone","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("187","SG","Singapore","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("188","SK","Slovakia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("189","SI","Slovenia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("190","SB","Solomon Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("191","SO","Somalia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("192","ZA","South Africa","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("193","ES","Spain","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("194","LK","Sri Lanka","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("195","SH","St. Helena","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("196","KN","St. Kitts and Nevis","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("197","PM","St. Pierre and Miquelon","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("198","VC","St. Vincent and the Grenadines","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("199","SD","Sudan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("200","SR","Suriname","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("201","SJ","Svalbard and Jan Mayen Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("202","SZ","Swaziland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("203","SE","Sweden","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("204","CH","Switzerland","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("205","SY","Syrian Arab Republic","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("206","TW","Taiwan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("207","TJ","Tajikistan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("208","TZ","Tanzania, United Republic of","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("209","TH","Thailand","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("210","TG","Togo","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("211","TK","Tokelau","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("212","TO","Tonga","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("213","TT","Trinidad and Tobago","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("214","TN","Tunisia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("215","TR","Turkey","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("216","TM","Turkmenistan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("217","TC","Turks and Caicos Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("218","TV","Tuvalu","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("219","UG","Uganda","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("220","UA","Ukraine","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("221","AE","United Arab Emirates","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("222","GB","United Kingdom (GB)","1","0","0.000","999");
INSERT INTO aphs_countries VALUES("224","US","United States","1","1","0.000","1000");
INSERT INTO aphs_countries VALUES("225","VI","United States Virgin Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("226","UY","Uruguay","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("227","UZ","Uzbekistan","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("228","VU","Vanuatu","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("229","VA","Vatican City State","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("230","VE","Venezuela","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("231","VN","Vietnam","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("232","WF","Wallis And Futuna Islands","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("233","EH","Western Sahara","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("234","YE","Yemen","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("235","ZR","Zaire","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("236","ZM","Zambia","1","0","0.000","0");
INSERT INTO aphs_countries VALUES("237","ZW","Zimbabwe","1","0","0.000","0");



DROP TABLE IF EXISTS aphs_coupons;

CREATE TABLE `aphs_coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `coupon_code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `date_started` date NOT NULL,
  `date_finished` date NOT NULL,
  `discount_percent` tinyint(2) NOT NULL,
  `comments` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_currencies;

CREATE TABLE `aphs_currencies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `symbol` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(3) CHARACTER SET latin1 NOT NULL,
  `rate` double(10,4) NOT NULL DEFAULT '1.0000',
  `symbol_placement` enum('left','right') CHARACTER SET latin1 NOT NULL DEFAULT 'right',
  `primary_order` tinyint(1) NOT NULL DEFAULT '0',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_currencies VALUES("1","US Dollar","$","USD","1.0000","left","1","1","1");
INSERT INTO aphs_currencies VALUES("2","Euro","€","EUR","0.7691","left","2","0","1");
INSERT INTO aphs_currencies VALUES("3","GB Pound","£","GBP","0.6555","left","3","0","1");
INSERT INTO aphs_currencies VALUES("4","AU Dollar","AU$","AUD","0.9742","left","4","0","1");



DROP TABLE IF EXISTS aphs_customer_groups;

CREATE TABLE `aphs_customer_groups` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_customer_groups VALUES("1","General","General purpose only");



DROP TABLE IF EXISTS aphs_customers;

CREATE TABLE `aphs_customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `birth_date` date NOT NULL DEFAULT '0000-00-00',
  `company` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `b_address` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `b_address_2` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `b_city` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `b_state` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `b_country` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `b_zipcode` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(32) CHARACTER SET latin1 NOT NULL,
  `user_password` varchar(50) CHARACTER SET latin1 NOT NULL,
  `preferred_language` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `date_created` datetime NOT NULL,
  `date_lastlogin` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registered_from_ip` varchar(15) CHARACTER SET latin1 NOT NULL,
  `last_logged_ip` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '000.000.000.000',
  `email_notifications` tinyint(1) NOT NULL DEFAULT '0',
  `notification_status_changed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `orders_count` smallint(6) NOT NULL DEFAULT '0',
  `rooms_count` smallint(6) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 - registration pending, 1 - active customer',
  `is_removed` tinyint(4) NOT NULL DEFAULT '0',
  `comments` text COLLATE utf8_unicode_ci NOT NULL,
  `registration_code` varchar(20) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `b_country` (`b_country`),
  KEY `status` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_customers VALUES("1","0","Dave","Harry","0000-00-00","","medan","","medan","","ID","20122","081317686969","","dave@daveharry.net","","","","en","2013-06-02 12:25:25","0000-00-00 00:00:00","139.0.47.237","","1","0000-00-00 00:00:00","0","0","1","0","","");
INSERT INTO aphs_customers VALUES("2","0","sdf","dfdf","1931-01-01","","dfd","","fd","","IS","fd","dfdf","","daveharry.ios@gmail.com","","daveharry.ios@gmail.com","Q#e‘ˆó´ã;àÍ¶:Ô","en","2013-06-05 20:07:23","2013-06-05 20:18:15","202.72.221.190","202.72.221.190","1","0000-00-00 00:00:00","2","2","1","0","","");



DROP TABLE IF EXISTS aphs_email_templates;

CREATE TABLE `aphs_email_templates` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL,
  `template_code` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `template_name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `template_subject` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `template_content` text COLLATE utf8_unicode_ci NOT NULL,
  `is_system_template` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_email_templates VALUES("1","en","new_account_created","Email for new customer","Your account has been created","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nCongratulations on creating your new account.\n\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\n\nYou login: {USER NAME}\nYou password: {USER PASSWORD}\n\nYou may follow the link below to log into your account:\n<a href=\"{BASE URL}index.php?customer=login\">Login</a>\n\nP.S. Remember, we will never sell your name or email address.\n\nEnjoy!\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("4","en","new_account_created_confirm_by_admin","Email for new user (admin approval required)","Your account has been created (approval required)","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nCongratulations on creating your new account.\n\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\n\nYour login: {USER NAME}\nYour password: {USER PASSWORD}\n\nAfter your registration will be approved by administrator,  you could log into your account with a following link:\n<a href=\"{BASE URL}index.php?customer=login\">Login</a>\n\nP.S. Remember, we will never sell your name or email address.\n\nEnjoy!\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("7","en","new_account_created_confirm_by_email","Email for new user (email confirmation required)","Your account has been created (confirmation required)","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nCongratulations on creating your new account.\n\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\n\nYour login: {USER NAME}\nYour password: {USER PASSWORD}\n\nIn order to become authorized member, you will need to confirm your registration. You may follow the link below to access the confirmation page:\n<a href=\"{BASE URL}index.php?customer=confirm_registration&c={REGISTRATION CODE}\">Confirm Registration</a>\n\nP.S. Remember, we will never sell your personal information or email address.\n\nEnjoy!\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("10","en","new_account_created_by_admin","Email for new user (account created by admin)","Your account has been created by admin","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nOur administrator just created a new account for you.\n\nPlease keep this email for your records, as it contains an important information that you may need, should you ever encounter problems or forget your password.\n\nYou login: {USER NAME}\nYou password: {USER PASSWORD}\n\nYou may follow the link below to log into your account:\n<a href=\"{BASE URL}index.php?customer=login\">Login</a>\n\nP.S. Remember, we will never sell your name or email address.\n\nEnjoy!\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("14","en","new_account_created_notify_admin","New account has been created (notify admin)","New account has been created","Hello Admin!\n\nA new user has been registered at your site.\nThis email contains a user account details:\n\nName: {FIRST NAME} {LAST NAME}\nEmail: {USER EMAIL}\nUsername: {USER NAME}\n\nP.S. Please check if it doesn\'t require your approval for activation","1");
INSERT INTO aphs_email_templates VALUES("16","en","password_forgotten","Email for customer or admin forgotten password","Forgotten Password","Hello <b>{USER NAME}</b>!\n\nYou or someone else asked for your login info on our site:\n{WEB SITE}\n\nYour Login Info:\n\nUsername: {USER NAME}\nPassword: {USER PASSWORD}\n\n\nBest regards,\n{WEB SITE}","1");
INSERT INTO aphs_email_templates VALUES("19","en","password_changed_by_admin","Password changed by admin","Your password has been changed","Hello <b>{FIRST NAME} {LAST NAME}</b>!\n\nYour password was changed by administrator of the site:\n{WEB SITE}\n\nHere your new login info:\n-\nUsername: {USER NAME} \nPassword: {USER PASSWORD}\n\n-\nBest regards,\nAdministration","1");
INSERT INTO aphs_email_templates VALUES("22","en","registration_approved_by_admin","Email for new customer (registration was approved by admin)","Your registration has been approved","Dear <b>{FIRST NAME} {LAST NAME}!</b>\n\nCongratulations! This e-mail is to confirm that your registration at {WEB SITE} has been approved.\n\nYou can now login in to your account now.\n\nThank you for choosing {WEB SITE}.\n-\nSincerely,\nAdministration","1");
INSERT INTO aphs_email_templates VALUES("25","en","account_deleted_by_user","Account removed email (by customer)","Your account has been removed","Dear {USER NAME}!\n\nYour account was removed.\n\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("30","en","new_account_created_without","Email for new/returned customer (without account)","Your contact information has been accepted","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nThank you for sending us your contact information. You may now complete your booking - just follow the instructions on the checkout page.\n\nPlease remember that even you don\'t have account on our site, you may always create it with easily. To do it simply follow this link and enter all needed information to create a new account: <a href=\"{BASE URL}index.php?customer=create_account\">Create Account</a>\n\nP.S. Remember, we will never sell your name or email address.\n\nEnjoy!\n-\nSincerely,\nCustomer Support","1");
INSERT INTO aphs_email_templates VALUES("31","en","order_placed_online","Email for online placed orders (not paid yet)","Your order has been placed in our system!","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nThank you for reservation request!\n\nYour order {BOOKING NUMBER} has been placed in our system and will be processed shortly.\n\n{BOOKING DETAILS}\n\nP.S. Please keep this email for your records, as it contains an important information that you may\nneed.\n\n-\nSincerely,\nCustomer Support\n\n{HOTEL INFO}","1");
INSERT INTO aphs_email_templates VALUES("34","en","order_paid","Email for orders paid via payment processing systems","Your order {BOOKING NUMBER} has been paid and received by the system!","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nThank you for reservation!\n\nYour order {BOOKING NUMBER} has been completed!\n\n{BOOKING DETAILS}\n\n{PERSONAL INFORMATION}\n\n{BILLING INFORMATION}\n\nP.S. Please keep this email for your records, as it contains an important information that you may need.\nP.P.S You may always check your booking status here:\n<a href=\"{BASE URL}index.php?page=check_status\">Check Status</a>\n\n-\nSincerely,\nCustomer Support\n\n{HOTEL INFO}","1");
INSERT INTO aphs_email_templates VALUES("37","en","events_new_registration","Events - new member has registered (member copy)","You have been successfully registered to the event!","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nCongratulations on registering to {EVENT}.\n\nPlease keep this email for your records, as it contains an important information that you may need.\n\n-\nBest Regards,\nAdministration","1");
INSERT INTO aphs_email_templates VALUES("40","en","order_canceled","Reservation has been canceled by Customer/Administrator","Your order has been canceled!","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nYour order {BOOKING NUMBER} has been canceled.\n\n{BOOKING DETAILS}\n\nP.S. Please feel free to contact us if you have any questions.\n\n-\nSincerely,\nCustomer Support\n\n{HOTEL INFO}","1");
INSERT INTO aphs_email_templates VALUES("43","en","payment_error","Customer payment has been failed for some reason","Your payment has been failed","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nThe payment for your booking {BOOKING NUMBER} has been failed. The reason was: {STATUS DESCRIPTION}\n\n{BOOKING DETAILS}\n\nP.S. Please feel free to contact us if you have any questions.\n\n-\nSincerely,\nCustomer Support\n\n{HOTEL INFO}","1");
INSERT INTO aphs_email_templates VALUES("46","en","subscription_to_newsletter","Newsletter - new member has subscribed (member copy)","You have been subscribed to the Newsletter","Hello!\n\nYou are receiving this email because you, or someone using this email address, subscribed to the Newsletter of {WEB SITE}\n\nIf you do not wish to receive such emails in the future, please click this link: <a href=\"{BASE URL}index.php?page=newsletter&task=pre_unsubscribe&email={USER EMAIL}\">Unsubscribe</a>\n\n-\nBest Regards,\nAdministration","1");
INSERT INTO aphs_email_templates VALUES("49","en","unsubscription_from_newsletter","Newsletter - member has unsubscribed (member copy)","You have been unsubscribed from the Newsletter","Hello!\n\nYou are receiving this email because you, or someone using this email address, unsubscribed from the Newsletter of {WEB SITE}\n\nYou can always restore your subscription, using the link below: <a href=\"{BASE URL}index.php?page=newsletter&task=pre_subscribe&email={USER EMAIL}\">Subscribe</a>\n\n-\nBest Regards,\nAdministration","1");
INSERT INTO aphs_email_templates VALUES("52","en","reservation_expired","Reservation has been expired","Your reservation has been expired!","Dear <b>{FIRST NAME} {LAST NAME}</b>!\n\nYour order reservation has been expired.\n\n{BOOKING DETAILS}\n\nP.S. Please feel free to contact us if you have any questions.\n\n-\nSincerely,\nCustomer Support\n\n{HOTEL INFO}","1");
INSERT INTO aphs_email_templates VALUES("55","en","test_template","Testing Email","Testing Email","Hello <b>{USER NAME}</b>!\n\nThis a testing email.\n\nBest regards,\n{WEB SITE}","0");



DROP TABLE IF EXISTS aphs_events_registered;

CREATE TABLE `aphs_events_registered` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned NOT NULL DEFAULT '0',
  `first_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `date_registered` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_extras;

CREATE TABLE `aphs_extras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `maximum_count` smallint(6) unsigned NOT NULL DEFAULT '0',
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_extras VALUES("1","10.00","1","3","1");
INSERT INTO aphs_extras VALUES("2","30.00","5","4","1");



DROP TABLE IF EXISTS aphs_extras_description;

CREATE TABLE `aphs_extras_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extra_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_extras_description VALUES("1","1","en","Wireless Internet Access","Wireless Internet Access (24 hour period)	");
INSERT INTO aphs_extras_description VALUES("2","1","es","Acceso inalámbrico a Internet","Acceso inalámbrico a Internet (período de 24 horas)");
INSERT INTO aphs_extras_description VALUES("3","1","de","WLAN","WLAN (24 Stunden)");
INSERT INTO aphs_extras_description VALUES("4","2","en","Airport Pickup","Airport Pickup (1 car with 5 seater)");
INSERT INTO aphs_extras_description VALUES("5","2","es","Recogida en el aeropuerto","Recogida en el aeropuerto (1 coche con 5 plazas)");
INSERT INTO aphs_extras_description VALUES("6","2","de","Abholung vom Flughafen","Abholung vom Flughafen (1 Fahrzeug mit 5-Sitzer)");



DROP TABLE IF EXISTS aphs_faq_categories;

CREATE TABLE `aphs_faq_categories` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_faq_category_items;

CREATE TABLE `aphs_faq_category_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL DEFAULT '0',
  `faq_question` text COLLATE utf8_unicode_ci NOT NULL,
  `faq_answer` text COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_gallery_album_items;

CREATE TABLE `aphs_gallery_album_items` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `album_code` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `item_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `item_file_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` smallint(6) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `album_code` (`album_code`),
  KEY `priority_order` (`priority_order`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_gallery_album_items VALUES("1","dkw3vvot","http://www.youtube.com/watch?v=5VIV8nt2KkU","","5","1");
INSERT INTO aphs_gallery_album_items VALUES("2","afbirxww","home.jpg","home_thumb.jpg","1","1");
INSERT INTO aphs_gallery_album_items VALUES("3","7u9sfhaz","img1_1.jpg","img1_1_thumb.jpg","1","1");
INSERT INTO aphs_gallery_album_items VALUES("4","7u9sfhaz","img1_2.jpg","img1_2_thumb.jpg","2","1");
INSERT INTO aphs_gallery_album_items VALUES("5","7u9sfhaz","img1_3.jpg","img1_3_thumb.jpg","3","1");
INSERT INTO aphs_gallery_album_items VALUES("6","0bxbqgps","IMG_8042.jpg","img_8042_thumb.jpg","1","1");
INSERT INTO aphs_gallery_album_items VALUES("7","0bxbqgps","IMG_8050.jpg","img_8050_thumb.jpg","2","1");
INSERT INTO aphs_gallery_album_items VALUES("9","6z5i5ikr","img3_1.jpg","img3_1_thumb.jpg","1","1");
INSERT INTO aphs_gallery_album_items VALUES("10","6z5i5ikr","img3_2.jpg","img3_2_thumb.jpg","2","1");
INSERT INTO aphs_gallery_album_items VALUES("11","6z5i5ikr","img3_3.jpg","img3_3_thumb.jpg","3","1");
INSERT INTO aphs_gallery_album_items VALUES("12","gvgbrtmc","img4_1.jpg","img4_1_thumb.jpg","1","1");
INSERT INTO aphs_gallery_album_items VALUES("13","gvgbrtmc","img4_2.jpg","img4_2_thumb.jpg","2","1");
INSERT INTO aphs_gallery_album_items VALUES("14","gvgbrtmc","img4_3.jpg","img4_3_thumb.jpg","3","1");



DROP TABLE IF EXISTS aphs_gallery_album_items_description;

CREATE TABLE `aphs_gallery_album_items_description` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gallery_album_item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `album_code` (`gallery_album_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_gallery_album_items_description VALUES("2","1","en","My Hotel Video","");
INSERT INTO aphs_gallery_album_items_description VALUES("5","2","en","Picture #1","");
INSERT INTO aphs_gallery_album_items_description VALUES("7","3","en","Picture #2","");
INSERT INTO aphs_gallery_album_items_description VALUES("10","4","en","Picture #3","");
INSERT INTO aphs_gallery_album_items_description VALUES("13","5","en","Picture #1","");
INSERT INTO aphs_gallery_album_items_description VALUES("16","6","en","Picture #1","");
INSERT INTO aphs_gallery_album_items_description VALUES("19","7","en","Picture #2","");
INSERT INTO aphs_gallery_album_items_description VALUES("25","9","en","Picture #2","");
INSERT INTO aphs_gallery_album_items_description VALUES("28","10","en","Picture #3","");
INSERT INTO aphs_gallery_album_items_description VALUES("31","11","en","Picture #1","");
INSERT INTO aphs_gallery_album_items_description VALUES("35","12","en","Picture #2","");
INSERT INTO aphs_gallery_album_items_description VALUES("37","13","en","Picture #3","");
INSERT INTO aphs_gallery_album_items_description VALUES("40","14","en","","");



DROP TABLE IF EXISTS aphs_gallery_albums;

CREATE TABLE `aphs_gallery_albums` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `album_code` varchar(8) COLLATE utf8_unicode_ci NOT NULL,
  `album_type` enum('images','video') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'images',
  `priority_order` smallint(6) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_active` (`is_active`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_gallery_albums VALUES("1","afbirxww","images","1","1");
INSERT INTO aphs_gallery_albums VALUES("2","dkw3vvot","video","11","0");
INSERT INTO aphs_gallery_albums VALUES("3","7u9sfhaz","images","3","1");
INSERT INTO aphs_gallery_albums VALUES("4","0bxbqgps","images","5","1");
INSERT INTO aphs_gallery_albums VALUES("5","6z5i5ikr","images","7","0");
INSERT INTO aphs_gallery_albums VALUES("6","gvgbrtmc","images","9","0");



DROP TABLE IF EXISTS aphs_gallery_albums_description;

CREATE TABLE `aphs_gallery_albums_description` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gallery_album_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_gallery_albums_description VALUES("2","1","en","Marutha Villa","Marutha Villa");
INSERT INTO aphs_gallery_albums_description VALUES("4","2","en","General Video","General Video");
INSERT INTO aphs_gallery_albums_description VALUES("7","3","en","Matha Villa","Matha Villa");
INSERT INTO aphs_gallery_albums_description VALUES("10","4","en","Ayusya Villa","Ayusya Villa");
INSERT INTO aphs_gallery_albums_description VALUES("13","5","en","Superior Rooms","Superior Rooms");
INSERT INTO aphs_gallery_albums_description VALUES("16","6","en","Luxury Rooms","Luxury Rooms");



DROP TABLE IF EXISTS aphs_hotels;

CREATE TABLE `aphs_hotels` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `hotel_location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `phone` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `fax` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time_zone` varchar(5) CHARACTER SET latin1 NOT NULL,
  `map_code` text COLLATE utf8_unicode_ci NOT NULL,
  `hotel_image` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `hotel_image_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `stars` tinyint(1) unsigned NOT NULL DEFAULT '3',
  `priority_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `hotel_location_id` (`hotel_location_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_hotels VALUES("1","2","(0361) - 8484307","(0361) - 8484309","info@astamanavilla.com","8","<iframe width=\"560\" height=\"350\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"http://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=Hotel+Circle+South,+San+Diego,+California,+United+States&aq=1&sll=33.981232,-84.173813&sspn=0.12868,0.307274&ie=UTF8&hq=&hnear=Hotel+Cir+S,+San+Diego,+California&ll=32.759,-117.177036&spn=0.017215,0.038409&z=14&output=embed\"></iframe>","hotel_1_xc7wzkl2w7bm0y4rgu7p.jpg","hotel_1_xc7wzkl2w7bm0y4rgu7p_thumb.jpg","2","0","1","1");



DROP TABLE IF EXISTS aphs_hotels_description;

CREATE TABLE `aphs_hotels_description` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` smallint(6) unsigned NOT NULL DEFAULT '1',
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hotel_id` (`hotel_id`,`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_hotels_description VALUES("1","1","en","Astamana Villa","Jl.Veteran No.84, Pererenan, Canggu , Buduk, Bali, Indonesia","<p>Step into a world of ease, luxury and Swiss hospitality at The Stamford, our 5-star deluxe hotel. Located in the heart of the city amidst world-class shopping, entertainment and the CBD. The hotel is seated at Turn 9 of the F1 race and 20 minutes away from the Airport.</p>");



DROP TABLE IF EXISTS aphs_hotels_locations;

CREATE TABLE `aphs_hotels_locations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `country_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_hotels_locations VALUES("1","US","0","1");
INSERT INTO aphs_hotels_locations VALUES("2","ID","0","1");



DROP TABLE IF EXISTS aphs_hotels_locations_description;

CREATE TABLE `aphs_hotels_locations_description` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `hotel_location_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `hotel_location_id` (`hotel_location_id`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_hotels_locations_description VALUES("3","1","en","Ronston");
INSERT INTO aphs_hotels_locations_description VALUES("4","2","en","Bali");



DROP TABLE IF EXISTS aphs_languages;

CREATE TABLE `aphs_languages` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `lang_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lang_name_en` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `abbreviation` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `lc_time_name` varchar(5) CHARACTER SET latin1 NOT NULL DEFAULT 'en_US',
  `lang_dir` varchar(3) CHARACTER SET latin1 NOT NULL DEFAULT 'ltr',
  `icon_image` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `priority_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `used_on` enum('front-end','back-end','global') CHARACTER SET latin1 NOT NULL DEFAULT 'global',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_languages VALUES("1","English","English","en","en_US","ltr","en.gif","1","global","1","1");



DROP TABLE IF EXISTS aphs_meal_plans;

CREATE TABLE `aphs_meal_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hotel_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `charge_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Per person per night',
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_default` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_meal_plans VALUES("1","1","0.00","0","0","1","1");
INSERT INTO aphs_meal_plans VALUES("2","1","10.00","0","1","1","0");
INSERT INTO aphs_meal_plans VALUES("3","1","22.00","0","2","1","0");



DROP TABLE IF EXISTS aphs_meal_plans_description;

CREATE TABLE `aphs_meal_plans_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `meal_plan_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_meal_plans_description VALUES("1","1","en","Breakfast (Included)","One meal supplied");
INSERT INTO aphs_meal_plans_description VALUES("4","2","en","Half Board","Two meals (no lunch) supplied");
INSERT INTO aphs_meal_plans_description VALUES("7","3","en","Full Board","Three meals supplied");



DROP TABLE IF EXISTS aphs_menus;

CREATE TABLE `aphs_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_code` varchar(10) CHARACTER SET latin1 NOT NULL,
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL,
  `menu_name` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `menu_placement` enum('','left','top','right','bottom','hidden') CHARACTER SET latin1 NOT NULL,
  `menu_order` tinyint(3) DEFAULT '1',
  `access_level` enum('public','registered') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_menus VALUES("6","W7GHW72XM2","en","Bottom","bottom","1","public");



DROP TABLE IF EXISTS aphs_modules;

CREATE TABLE `aphs_modules` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name_const` varchar(20) CHARACTER SET latin1 NOT NULL,
  `description_const` varchar(30) CHARACTER SET latin1 NOT NULL,
  `icon_file` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `module_tables` varchar(255) CHARACTER SET latin1 NOT NULL,
  `dependent_modules` varchar(20) CHARACTER SET latin1 NOT NULL,
  `settings_page` varchar(30) CHARACTER SET latin1 NOT NULL,
  `settings_const` varchar(30) CHARACTER SET latin1 NOT NULL,
  `settings_access_by` varchar(50) CHARACTER SET latin1 NOT NULL,
  `management_page` varchar(125) CHARACTER SET latin1 NOT NULL,
  `management_const` varchar(125) CHARACTER SET latin1 NOT NULL,
  `management_access_by` varchar(50) CHARACTER SET latin1 NOT NULL,
  `is_installed` tinyint(1) NOT NULL DEFAULT '0',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `priority_order` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_modules VALUES("1","backup","_BACKUP_AND_RESTORE","_MD_BACKUP_AND_RESTORE","backup.png","","","mod_backup_installation","_BACKUP_INSTALLATION","owner","mod_backup_restore","_BACKUP_RESTORE","owner,mainadmin","1","0","10");
INSERT INTO aphs_modules VALUES("2","news","_NEWS","_MD_NEWS","news.png","news,events_registered,news_subscribed","","mod_news_settings","_NEWS_SETTINGS","owner,mainadmin","mod_news_management,mod_news_subscribed","_NEWS_MANAGEMENT,_SUBSCRIPTION_MANAGEMENT","owner,mainadmin","1","0","6");
INSERT INTO aphs_modules VALUES("3","customers","_CUSTOMERS","_MD_CUSTOMERS","customers.png","customers","","mod_customers_settings","_CUSTOMERS_SETTINGS","owner,mainadmin","","","","1","0","2");
INSERT INTO aphs_modules VALUES("4","gallery","_GALLERY","_MD_GALLERY","gallery.png","gallery_albums,gallery_images","","mod_gallery_settings","_GALLERY_SETTINGS","owner,mainadmin","mod_gallery_management","_GALLERY_MANAGEMENT","owner,mainadmin","1","0","7");
INSERT INTO aphs_modules VALUES("5","contact_us","_CONTACT_US","_MD_CONTACT_US","contact_us.png","","","mod_contact_us_settings","_CONTACT_US_SETTINGS","owner,mainadmin","","","","1","0","3");
INSERT INTO aphs_modules VALUES("6","comments","_COMMENTS","_MD_COMMENTS","comments.png","comments","","mod_comments_settings","_COMMENTS_SETTINGS","owner,mainadmin","mod_comments_management","_COMMENTS_MANAGEMENT","owner,mainadmin","1","0","4");
INSERT INTO aphs_modules VALUES("7","banners","_BANNERS","_MD_BANNERS","banners.png","banners","","mod_banners_settings","_BANNERS_SETTINGS","owner,mainadmin","mod_banners_management","_BANNERS_MANAGEMENT","owner,mainadmin","1","0","8");
INSERT INTO aphs_modules VALUES("8","booking","_BOOKINGS","_MD_BOOKINGS","booking.png","bookings,bookings_rooms,extras","","mod_booking_settings","_BOOKINGS_SETTINGS","owner,mainadmin","","","","1","0","5");
INSERT INTO aphs_modules VALUES("9","rooms","_ROOMS","_MD_ROOMS","rooms.png","rooms,rooms_availabilities,rooms_description,rooms_prices,room_facilities,room_facilities_description","","mod_rooms_settings","_ROOMS_SETTINGS","owner,mainadmin","mod_rooms_management","_ROOMS_MANAGEMENT","owner,mainadmin","1","1","1");
INSERT INTO aphs_modules VALUES("10","pages","_PAGES","_MD_PAGES","pages.png","pages,menus","","","","owner,mainadmin","pages","_PAGE_EDIT_PAGES","owner,mainadmin","1","1","0");
INSERT INTO aphs_modules VALUES("11","testimonials","_TESTIMONIALS","_MD_TESTIMONIALS","testimonials.png","testimonials","","mod_testimonials_settings","_TESTIMONIALS_SETTINGS","owner,mainadmin","mod_testimonials_management","_TESTIMONIALS_MANAGEMENT","owner,mainadmin","1","0","9");
INSERT INTO aphs_modules VALUES("12","faq","_FAQ","_MD_FAQ","faq.png","faq_categories,faq_category_items","","mod_faq_settings","_FAQ_SETTINGS","owner,mainadmin","mod_faq_management","_FAQ_MANAGEMENT","owner,mainadmin","1","0","10");



DROP TABLE IF EXISTS aphs_modules_settings;

CREATE TABLE `aphs_modules_settings` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(20) CHARACTER SET latin1 NOT NULL,
  `settings_key` varchar(40) CHARACTER SET latin1 NOT NULL,
  `settings_value` text COLLATE utf8_unicode_ci NOT NULL,
  `settings_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `settings_description_const` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `key_display_type` enum('string','email','numeric','unsigned float','integer','positive integer','unsigned integer','enum','yes/no','html size','text') CHARACTER SET latin1 NOT NULL,
  `key_is_required` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `key_display_source` varchar(255) CHARACTER SET latin1 NOT NULL COMMENT 'for ''enum'' field type',
  PRIMARY KEY (`id`),
  KEY `module_name` (`module_name`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_modules_settings VALUES("1","banners","is_active","yes","Activate Banners","_MS_BANNERS_IS_ACTIVE","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("2","banners","rotation_type","slide show","Rotation Type","_MS_ROTATION_TYPE","enum","1","random image,slide show");
INSERT INTO aphs_modules_settings VALUES("3","banners","rotate_delay","9","Rotation Delay","_MS_ROTATE_DELAY","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("4","banners","slideshow_caption_html","no","HTML in Slideshow Caption","_MS_BANNERS_CAPTION_HTML","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("5","booking","is_active","global","Activate Bookings","_MS_ACTIVATE_BOOKINGS","enum","1","front-end,back-end,global,no");
INSERT INTO aphs_modules_settings VALUES("6","booking","payment_type_poa","yes","&#8226; \'POA\' Payment Type","_MS_PAYMENT_TYPE_POA","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("7","booking","payment_type_online","yes","&#8226; \'On-line Order\' Payment Type","_MS_PAYMENT_TYPE_ONLINE","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("8","booking","online_credit_card_required","yes","&nbsp; Credit Cards for \'On-line Orders\'","_MS_ONLINE_CREDIT_CARD_REQUIRED","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("9","booking","payment_type_bank_transfer","yes","&#8226; \'Bank Transfer\' Payment Type","_MS_PAYMENT_TYPE_BANK_TRANSFER","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("10","booking","bank_transfer_info","Bank name: {BANK NAME HERE}\nSwift code: {CODE HERE}\nRouting in Transit# or ABA#: {ROUTING HERE}\nAccount number *: {ACCOUNT NUMBER HERE}\n\n*The account number must be in the IBAN format which may be obtained from the branch handling the customer\'s account or may be seen at the top the customer\'s bank statement\n","&nbsp; Bank Transfer Info","_MS_BANK_TRANSFER_INFO","text","0","");
INSERT INTO aphs_modules_settings VALUES("11","booking","payment_type_paypal","yes","&#8226; PayPal Payment Type","_MS_PAYMENT_TYPE_PAYPAL","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("12","booking","paypal_email","info@astamanavilla.com","&nbsp; PayPal Email","_MS_PAYPAL_EMAIL","email","1","");
INSERT INTO aphs_modules_settings VALUES("13","booking","payment_type_2co","yes","&#8226; 2CO Payment Type","_MS_PAYMENT_TYPE_2CO","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("14","booking","two_checkout_vendor","Your 2CO Vendor ID here","&nbsp; 2CO Vendor ID","_MS_TWO_CHECKOUT_VENDOR","string","1","");
INSERT INTO aphs_modules_settings VALUES("15","booking","payment_type_authorize","yes","&#8226; Authorize.Net Payment Type","_MS_PAYMENT_TYPE_AUTHORIZE","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("16","booking","authorize_login_id","Your API Login ID here","&nbsp; Authorize.Net Login ID","_MS_AUTHORIZE_LOGIN_ID","string","1","");
INSERT INTO aphs_modules_settings VALUES("17","booking","authorize_transaction_key","Your Transaction Key here","&nbsp; Authorize.Net Transaction Key","_MS_AUTHORIZE_TRANSACTION_KEY","string","1","");
INSERT INTO aphs_modules_settings VALUES("18","booking","default_payment_system","paypal","Default Payment System","_MS_DEFAULT_PAYMENT_SYSTEM","enum","1","poa,online,bank transfer,paypal,2co,authorize.net");
INSERT INTO aphs_modules_settings VALUES("19","booking","send_order_copy_to_admin","yes","Admin Copy of Order","_MS_SEND_ORDER_COPY_TO_ADMIN","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("20","booking","allow_booking_without_account","yes","Allow Booking Without Account","_MS_ALLOW_BOOKING_WITHOUT_ACCOUNT","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("21","booking","pre_payment_type","first night","Pre-Payment Type","_MS_PRE_PAYMENT_TYPE","enum","1","full price,first night,fixed sum,percentage");
INSERT INTO aphs_modules_settings VALUES("22","booking","pre_payment_value","10","Pre-Payment Value","_MS_PRE_PAYMENT_VALUE","enum","0","1,2,3,4,5,6,7,8,9,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,99");
INSERT INTO aphs_modules_settings VALUES("23","booking","vat_value","0","VAT Default Value","_MS_VAT_VALUE","unsigned float","0","");
INSERT INTO aphs_modules_settings VALUES("24","booking","minimum_nights","1","Minimum Nights Stay","_MS_MINIMUM_NIGHTS","enum","1","1,2,3,4,5,6,7,8,9,10,14,21,28,30,45,60,90");
INSERT INTO aphs_modules_settings VALUES("25","booking","maximum_nights","90","Maximum Nights Stay","_MS_MAXIMUM_NIGHTS","enum","1","1,2,3,4,5,6,7,8,9,10,14,21,28,30,45,60,90,120,150,180,240,360");
INSERT INTO aphs_modules_settings VALUES("26","booking","mode","REAL MODE","Payment Mode","_MS_BOOKING_MODE","enum","1","TEST MODE,REAL MODE");
INSERT INTO aphs_modules_settings VALUES("27","booking","show_fully_booked_rooms","yes","Show Fully Booked Rooms","_MS_SHOW_FULLY_BOOKED_ROOMS","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("28","booking","preparing_orders_timeout","2","\'Preparing\' Orders Timeout","_MS_PREPARING_ORDERS_TIMEOUT","enum","1","0,1,2,3,4,5,6,7,8,9,10,12,14,16,18,20,22,24,36,48,72");
INSERT INTO aphs_modules_settings VALUES("29","booking","customers_cancel_reservation","7","Customers May Cancel Reservation","_MS_CUSTOMERS_CANCEL_RESERVATION","enum","1","0,1,2,3,4,5,6,7,10,14,21,30,45,60");
INSERT INTO aphs_modules_settings VALUES("30","booking","show_reservation_form","yes","Show Reservation Form","_MS_SHOW_RESERVATION_FORM","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("31","booking","booking_initial_fee","0","Booking Initial Fee","_MS_RESERVATION_INITIAL_FEE","unsigned float","1","");
INSERT INTO aphs_modules_settings VALUES("32","booking","booking_number_type","random","Type of Booking Numbers","_MS_BOOKING_NUMBER_TYPE","enum","1","random,sequential");
INSERT INTO aphs_modules_settings VALUES("33","booking","vat_included_in_price","no","Include VAT in Price","_MS_VAT_INCLUDED_IN_PRICE","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("34","booking","show_booking_status_form","yes","Show Booking Status Form","_MS_SHOW_BOOKING_STATUS_FORM","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("35","booking","maximum_allowed_reservations","10","Maximum Allowed Reservations","_MS_MAXIMUM_ALLOWED_RESERVATIONS","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("36","booking","first_night_calculating_type","real","First Night Calculating Type","_MS_FIRST_NIGHT_CALCULATING_TYPE","enum","1","real,average");
INSERT INTO aphs_modules_settings VALUES("37","booking","available_until_approval","no","Available Until Approval","_MS_AVAILABLE_UNTIL_APPROVAL","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("38","booking","reservation_expired_alert","no","Reservation Expired Alert","_MS_RESERVATION EXPIRED_ALERT","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("39","booking","allow_booking_in_past","no","Allow Booking in the Past","_MS_ADMIN_BOOKING_IN_PAST","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("40","customers","allow_adding_by_admin","yes","Admin Creates Customers","_MS_ALLOW_ADDING_BY_ADMIN","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("41","customers","reg_confirmation","by email","Confirmation Type","_MS_REG_CONFIRMATION","enum","0","automatic,by email,by admin");
INSERT INTO aphs_modules_settings VALUES("42","customers","image_verification_allow","yes","Image Verification","_MS_CUSTOMERS_IMAGE_VERIFICATION","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("43","customers","allow_login","yes","Allow Customers to Login","_MS_ALLOW_CUSTOMERS_LOGIN","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("44","customers","allow_registration","yes","Allow New Customers Registration","_MS_ALLOW_CUSTOMERS_REGISTRATION","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("45","customers","password_changing_by_admin","yes","Admin Changes Customer Password","_MS_ADMIN_CHANGE_CUSTOMER_PASSWORD","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("46","customers","allow_reset_passwords","yes","Allow Reset Passwords","_MS_ALLOW_CUST_RESET_PASSWORDS","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("47","customers","admin_alert_new_registration","yes","Alert Admin On New  Registration","_MS_ALERT_ADMIN_NEW_REGISTRATION","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("48","customers","remember_me_allow","yes","Remember Me","_MS_REMEMBER_ME","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("49","comments","comments_allow","yes","Allow Comments","_MS_COMMENTS_ALLOW","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("50","comments","user_type","registered","User Type","_MS_USER_TYPE","enum","1","all,registered");
INSERT INTO aphs_modules_settings VALUES("51","comments","comment_length","500","Comments Length","_MS_COMMENTS_LENGTH","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("52","comments","image_verification_allow","yes","Image Verification","_MS_IMAGE_VERIFICATION_ALLOW","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("53","comments","page_size","20","Comments per Page","_MS_COMMENTS_PAGE_SIZE","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("54","comments","pre_moderation_allow","yes","Comments Pre-moderation","_MS_PRE_MODERATION_ALLOW","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("55","comments","delete_pending_time","2","Pending Time","_MS_DELETE_PENDING_TIME","enum","1","0,1,2,3,4,5,6,7,8,9,10,15,20,30,45,60,120,180");
INSERT INTO aphs_modules_settings VALUES("56","contact_us","key","{module:contact_us}","Contact Key","_MS_CONTACT_US_KEY","enum","1","{module:contact_us}");
INSERT INTO aphs_modules_settings VALUES("57","contact_us","email","info@astamanavilla.com","Contact Email","_MS_EMAIL","email","1","");
INSERT INTO aphs_modules_settings VALUES("58","contact_us","is_send_delay","yes","Sending Delay","_MS_IS_SEND_DELAY","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("59","contact_us","delay_length","20","Delay Length","_MS_DELAY_LENGTH","positive integer","0","");
INSERT INTO aphs_modules_settings VALUES("60","contact_us","image_verification_allow","yes","Image Verification","_MS_IMAGE_VERIFICATION_ALLOW","yes/no","0","");
INSERT INTO aphs_modules_settings VALUES("61","faq","is_active","yes","Activate FAQ","_MS_FAQ_IS_ACTIVE","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("62","gallery","key","{module:gallery}","Gallery Key","_MS_GALLERY_KEY","enum","1","{module:gallery}");
INSERT INTO aphs_modules_settings VALUES("63","gallery","album_key","{module:album=CODE}","Album Key","_MS_ALBUM_KEY","enum","1","{module:album=CODE}");
INSERT INTO aphs_modules_settings VALUES("64","gallery","image_gallery_type","lytebox","Image Gallery Type","_MS_IMAGE_GALLERY_TYPE","enum","1","lytebox,rokbox");
INSERT INTO aphs_modules_settings VALUES("65","gallery","album_icon_width","140px","Album Icon Width","_MS_ALBUM_ICON_WIDTH","html size","1","");
INSERT INTO aphs_modules_settings VALUES("66","gallery","album_icon_height","105px","Album Icon Height","_MS_ALBUM_ICON_HEIGHT","html size","1","");
INSERT INTO aphs_modules_settings VALUES("67","gallery","albums_per_line","4","Albums per Line","_MS_ALBUMS_PER_LINE","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("68","gallery","video_gallery_type","rokbox","Video Gallery Type","_MS_VIDEO_GALLERY_TYPE","enum","1","rokbox,videobox");
INSERT INTO aphs_modules_settings VALUES("69","gallery","wrapper","table","HTML Wrapping Tag","_MS_GALLERY_WRAPPER","enum","1","table,div");
INSERT INTO aphs_modules_settings VALUES("70","gallery","show_items_count_in_album","yes","Show Items Count in Album","_MS_ITEMS_COUNT_IN_ALBUM","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("71","news","news_count","5","News Count","_MS_NEWS_COUNT","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("72","news","news_header_length","80","News Header Length","_MS_NEWS_HEADER_LENGTH","positive integer","1","");
INSERT INTO aphs_modules_settings VALUES("73","news","news_rss","yes","News RSS","_MS_NEWS_RSS","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("74","news","show_news_block","left side","News Block","_MS_SHOW_NEWS_BLOCK","enum","1","no,left side,right side");
INSERT INTO aphs_modules_settings VALUES("75","news","show_newsletter_subscribe_block","no","Newsletter Subscription","_MS_SHOW_NEWSLETTER_SUBSCRIBE_BLOCK","enum","1","no,left side,right side");
INSERT INTO aphs_modules_settings VALUES("76","rooms","search_availability_page_size","20","Search Availability Page Size","_MS_SEARCH_AVAILABILITY_PAGE_SIZE","enum","1","1,2,3,4,5,6,7,8,9,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,250,500,1000");
INSERT INTO aphs_modules_settings VALUES("77","rooms","show_room_types_in_search","all","Show Rooms In Search","_MS_ROOMS_IN_SEARCH","enum","1","all,available only");
INSERT INTO aphs_modules_settings VALUES("78","rooms","allow_children","yes","Allow Children in Room","_MS_ALLOW_CHILDREN_IN_ROOM","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("79","rooms","allow_system_suggestion","yes","Allow System Suggestion","_MS_ALLOW_SYSTEM_SUGGESTION","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("80","rooms","allow_guests","yes","Allow Guests in Room","_MS_ALLOW_GUESTS_IN_ROOM","yes/no","1","");
INSERT INTO aphs_modules_settings VALUES("81","testimonials","key","{module:testimonials}","Testimonials Key","_MS_TESTIMONIALS_KEY","enum","1","{module:testimonials}");



DROP TABLE IF EXISTS aphs_news;

CREATE TABLE `aphs_news` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `news_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL,
  `type` enum('news','events') CHARACTER SET latin1 NOT NULL DEFAULT 'news',
  `header_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body_text` text COLLATE utf8_unicode_ci NOT NULL,
  `date_created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_news VALUES("1","txj17hkwau","en","news","Astamana Villa Soft Launching..","<p>We wil be soft launching in July 2013!</p>","2012-11-12 18:47:33");



DROP TABLE IF EXISTS aphs_news_subscribed;

CREATE TABLE `aphs_news_subscribed` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `date_subscribed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_packages;

CREATE TABLE `aphs_packages` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `package_name` varchar(50) CHARACTER SET latin1 NOT NULL,
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `finish_date` date NOT NULL DEFAULT '0000-00-00',
  `minimum_nights` tinyint(1) NOT NULL DEFAULT '0',
  `maximum_nights` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_pages;

CREATE TABLE `aphs_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_code` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL,
  `content_type` enum('article','link','') CHARACTER SET latin1 NOT NULL DEFAULT 'article',
  `link_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link_target` enum('','_self','_blank') COLLATE utf8_unicode_ci NOT NULL,
  `page_key` varchar(125) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_text` text COLLATE utf8_unicode_ci,
  `menu_id` int(11) DEFAULT '0',
  `menu_link` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tag_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  `tag_description` text COLLATE utf8_unicode_ci NOT NULL,
  `comments_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `date_updated` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `finish_publishing` date NOT NULL DEFAULT '0000-00-00',
  `is_home` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_removed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_system_page` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `system_page` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `show_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `status_changed` datetime NOT NULL,
  `access_level` enum('public','registered') CHARACTER SET latin1 NOT NULL DEFAULT 'public',
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `is_published` (`is_published`),
  KEY `is_removed` (`is_removed`),
  KEY `language_id` (`language_id`),
  KEY `comments_allowed` (`comments_allowed`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_pages VALUES("1","rpo5bahloy","en","article","","_self","Astamana-Villa-is-happy-to-welcome","Astamana Villa is happy to welcome you!","<img class=\"img-indent\" alt=\"\" src=\"images/uploads/img1.png\" align=\"left\" border=\"0\" hspace=\"\" vspace=\"\"><p class=\"alt-top\">Come alone or bring your family with you, stay here for a night or for weeks, stay here while on business trip or at some kind of conference - either way our hotel is the best possible variant.</p>\n<p>Feel free to contact us anytime in case you have any questions or concerns. We\'re always glad to see you in our hotel.</p>\n<div class=\"clear\"></div>\n<div class=\"line-hor\"></div>\n<div class=\"wrapper\" line-ver=\"\">\n<div class=\"col-1\">\n<h3>Special Offers</h3>\n<ul>\n<li>FREE wide-screen TV \n</li><li>50% Discount for Restaraunt service \n</li><li>30% Discount for 3 days+ orders \n</li><li>FREE drinks and beverages in rooms \n</li><li>Exclusive souvenirs</li></ul></div>\n<div class=\"col-2\">\n<h3>Location</h3>\n<p>Jl.Veteran No.84, Pererenan,Canggu , Buduk, Bali, Indonesia</p></div></div>","0","","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2013-06-02 10:39:27","0000-00-00","1","0","1","0","","1","2010-04-24 16:55:05","public","0");
INSERT INTO aphs_pages VALUES("4","99fnhie8in","en","article","","_self","Installation","Installation","<p>Software requirements: PHP 5.0 or later version.</p>\n<p>A new installation of ApPHP Hotel Site is a very straight forward process:</p>\n<p><strong>Step 1</strong>. Uncompressing downloaded file.<br>-<br>&nbsp;&nbsp; Uncompress the ApPHP Hotel Site version 3.x.x script archive. The archive will create<br>&nbsp;&nbsp; a directory called \"PHPHS_3xx\"<br><br><br><strong>Step 2</strong>. Uploading files.<br>-<br>&nbsp;&nbsp; Upload content of this folder (all files and directories it includes) to your <br>&nbsp;&nbsp; document root (public_html, www, httpdocs etc.) or your booking script directory using FTP.<br>&nbsp;&nbsp; Pay attention to DON\\\'T use the capital letters in the name of the folder (for Linux users).</p>\n<p>&nbsp;&nbsp; public_html/<br>&nbsp;&nbsp; or<br>&nbsp;&nbsp; public_html/{hotel-site directory}/<br>&nbsp;&nbsp; <br>&nbsp;&nbsp; Rename default.htaccess into .htaccess if you need to add PHP5 handler.</p>\n<p><br><strong>Step 3</strong>. Creating database.<br>-<br>&nbsp;&nbsp; Using your hosting Control Panel, phpMyAdmin or another tool, create your database<br>&nbsp;&nbsp; and user, and assign that user to the database. Write down the name of the<br>&nbsp;&nbsp; database, username, and password for the site installation procedure.</p>\n<p><br><strong>Step 4</strong>. Running install.php file.<br>-<br>&nbsp;&nbsp; Now you can run install.php file. To do this, open a browser and type in Address Bar</p>\n<p>&nbsp;&nbsp; http://{www.mydomain.com}/install.php<br>&nbsp;&nbsp; or<br>&nbsp;&nbsp; http://{www.mydomain.com}/{hotel-site directory}/install.php</p>\n<p>&nbsp;&nbsp; Follow instructions on the screen. You will be asked to enter: database host,<br>&nbsp;&nbsp; database name, username and password. Also you need to enter admin username and<br>&nbsp;&nbsp; admin password, that will be used to get access to administration area of the<br>&nbsp;&nbsp; site.</p>\n<p><br><strong>Step 5</strong>. Setting up access permissions.<br>-<br>&nbsp;&nbsp; Check access permissions to images/uploads/. You need to have 755 permissions <br>&nbsp;&nbsp; to this folder.</p>\n<p><br><strong>Step 6</strong>. Deleting install.php file.<br>-<br>&nbsp;&nbsp; After successful installation you will get an appropriate message and warning to<br>&nbsp;&nbsp; remove install.php file. For security reasons, please delete install file<br>&nbsp;&nbsp; immediately.</p>\n<p><br>Congratulations, you now have ApPHP Hotel Site v3.x.x. Installed!</p>","1","Installation","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-11-08 12:26:12","0000-00-00","0","1","0","0","","1","2013-06-02 11:02:30","public","0");
INSERT INTO aphs_pages VALUES("7","afd4vgf5yt","en","article","","_self","Gallery","Gallery","{module:gallery}","0","Gallery","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2013-06-08 23:31:20","0000-00-00","0","0","1","1","gallery","1","0000-00-00 00:00:00","public","1");
INSERT INTO aphs_pages VALUES("10","op8uy67ydd","en","article","","_self","Testimonials","Testimonials","{module:testimonials}","0","Testimonials","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-05-07 23:35:48","0000-00-00","0","0","1","1","testimonials","1","0000-00-00 00:00:00","public","3");
INSERT INTO aphs_pages VALUES("13","87ghtyfd5t","en","article","","_self","We-offer-several-kinds-of-rooms","We offer several kinds of rooms","<div>{module:rooms}</div>","0","Rooms","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-06-25 15:03:07","0000-00-00","0","0","1","1","rooms","1","0000-00-00 00:00:00","public","0");
INSERT INTO aphs_pages VALUES("16","45tfrtbfg8","en","article","","_self","Today’s-featured-menu-item","Today’s featured menu item","<img style=\"MARGIN-RIGHT: 7px\" border=\"0\" alt=\"\" vspace=\"5\" align=\"left\" src=\"images/uploads/restaurant_dishes.jpg\"> \n<h3 class=\"extra-wrap\">Foie gras!</h3>\n<div class=\"extra-wrap\">\n<ul class=\"list2\">\n<li>Nice and tasty! \n</li><li>Made from French ingredients! \n</li><li>Cooked by Italian chef! \n</li><li>Awarded by world’s assosiation of chef! \n</li><li>Proved to be good for your health!</li></ul></div>\n<div><strong class=\"txt2\">AS LOW AS €19!</strong></div><br><br><br><br>\n<h3><br>Menu/Specials</h3>\n<div class=\"extra-wrap\">\n<ul>\n<li>LYNAGH’S BEER CHEESE <br>Our own recipe, made with Guinness Stout served w/ carrots, celery &amp; crackers. -- $4.99 \n</li><li>SALSA <br>TAME OR FLAME Homemade salsa served with tortilla chips. The TAME is HOT!!! -- $2.99 \n</li><li>SPINACH ARTICHOKE DIP <br>Served with tortilla chips. -- $6.49 \n</li><li>DOC BILL\\\'S PUB PRETZELS <br>Two jumbo pretzels deep fried and served with hot homemade beer cheese. -- $5.49 \n</li><li>ULTIMATE IRISH <br>Take the Irish Nacho, add red onions and our famous chili. -- $9.99 \n</li><li>SPICY QUESO BEEF DIP <br>Ground beef, queso, Mexican spices, jalapenos, and sour cream. That’s gotta be good! -- $6.49 \n</li><li>DELUXE NACHOS <br>Tortillas smothered with chili, cheese, lettuce tomatoes, jalapenos &amp; sour cream.</li></ul></div>","0","Restaurant","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2013-06-08 23:37:22","0000-00-00","0","0","0","1","restaurant","1","0000-00-00 00:00:00","public","8");
INSERT INTO aphs_pages VALUES("19","s3d4fder56","en","article","","_self","About-Us","About Us","{module:about_us} ","0","About Us","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-05-03 12:52:10","0000-00-00","0","0","1","1","about_us","1","0000-00-00 00:00:00","public","5");
INSERT INTO aphs_pages VALUES("22","90jhtyu78y","en","article","","_self","Terms-and-Conditions","Terms and Conditions","<h4>Conditions of Purchase and Money Back Guarantee\n</h4><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed in enim sed arcu congue mollis. Mauris sed elementum nulla. Donec eleifend nunc dapibus turpis euismod at commodo mi pulvinar. Praesent vitae metus ligula. Maecenas commodo massa id arcu luctus posuere. Praesent adipiscing scelerisque nisi id accumsan.&nbsp;</p>\n<ul>\n<li>Sed posuere, sem mollis eleifend placerat, nisl magna dapibus nunc, in mattis augue urna ac dui. Nunc mollis venenatis mi. \n</li><li>A elementum nulla mollis in. Maecenas et mi augue. Nulla euismod mauris sit amet mauris ullamcorper lobortis. \n</li><li>Vivamus nec ligula nulla. Curabitur non sapien nec lectus euismod consectetur. Morbi ut vestibulum risus. </li></ul>\n<h4><br>Detailed Conditions</h4><br>Cras elit purus, dapibus et cursus vel, eleifend interdum neque. Aenean nec magna sit amet felis pellentesque sollicitudin. Praesent ut enim est, quis ornare massa: <br>\n<ul>\n<li>Sed ultrices turpis at dolor dictum eu sollicitudin leo gravida. Praesent leo leo, malesuada nec facilisis non, lobortis eget lacus. \n</li><li>Donec at orci odio. Aliquam eu nulla felis, eget volutpat enim. Vivamus ullamcorper ligula eu sapien rutrum et hendrerit neque convallis. Sed fringilla tristique arcu, a interdum erat fringilla non. Nunc sit amet sodales leo. \n</li><li>Quisque luctus lacus nulla. Duis iaculis porttitor velit et feugiat. Nam sed velit libero. Praesent metus mauris, fermentum nec consequat vel, bibendum vel sem. </li></ul><br>Etiam auctor est et leo tristique ut scelerisque sapien bibendum. Suspendisse tellus urna, pellentesque eget pellentesque a, dictum in massa. ","0","Terms and Conditions","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-09-26 21:47:52","0000-00-00","0","0","1","1","terms_and_conditions","1","0000-00-00 00:00:00","public","6");
INSERT INTO aphs_pages VALUES("25","zxcs3d4fd5","en","article","","_self","test-page","test-page","Test page with comments","1","Test Page","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","1","2011-05-01 11:16:22","2012-06-25 15:02:03","0000-00-00","0","1","0","0","","1","2013-06-02 11:02:36","public","1");
INSERT INTO aphs_pages VALUES("28","q8mv7zrzmo","en","article","","_self","Contact-Us","Contact Us","{module:contact_us}","0","Contact Us","Astamana Villa","astamana villa, bali villa, bali, bali hotel, canggu","hotel site bali","0","2011-05-01 11:16:22","2012-05-03 12:52:36","0000-00-00","0","0","1","1","contact_us","1","0000-00-00 00:00:00","public","4");



DROP TABLE IF EXISTS aphs_privileges;

CREATE TABLE `aphs_privileges` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_privileges VALUES("1","add_menus","Add Menus","Add Menus on the site");
INSERT INTO aphs_privileges VALUES("2","edit_menus","Edit Menus","Edit Menus on the site");
INSERT INTO aphs_privileges VALUES("3","delete_menus","Delete Menus","Delete Menus from the site");
INSERT INTO aphs_privileges VALUES("4","add_pages","Add Pages","Add Pages on the site");
INSERT INTO aphs_privileges VALUES("5","edit_pages","Edit Pages","Edit Pages on the site");
INSERT INTO aphs_privileges VALUES("6","delete_pages","Delete Pages","Delete Pages from the site");
INSERT INTO aphs_privileges VALUES("7","edit_hotel_info","Manage Hotels","See and modify the hotels info");
INSERT INTO aphs_privileges VALUES("8","edit_hotel_rooms","Manage Hotel Rooms","See and modify the hotel rooms info");
INSERT INTO aphs_privileges VALUES("9","view_hotel_reports","See Hotel Reports","See only reports related to assigned hotel");



DROP TABLE IF EXISTS aphs_role_privileges;

CREATE TABLE `aphs_role_privileges` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(5) NOT NULL,
  `privilege_id` int(5) NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_role_privileges VALUES("1","1","1","1");
INSERT INTO aphs_role_privileges VALUES("2","1","2","1");
INSERT INTO aphs_role_privileges VALUES("3","1","3","1");
INSERT INTO aphs_role_privileges VALUES("4","1","4","1");
INSERT INTO aphs_role_privileges VALUES("5","1","5","1");
INSERT INTO aphs_role_privileges VALUES("6","1","6","1");
INSERT INTO aphs_role_privileges VALUES("7","2","1","1");
INSERT INTO aphs_role_privileges VALUES("8","2","2","1");
INSERT INTO aphs_role_privileges VALUES("9","2","3","1");
INSERT INTO aphs_role_privileges VALUES("10","2","4","1");
INSERT INTO aphs_role_privileges VALUES("11","2","5","1");
INSERT INTO aphs_role_privileges VALUES("12","2","6","1");
INSERT INTO aphs_role_privileges VALUES("13","3","1","0");
INSERT INTO aphs_role_privileges VALUES("14","3","2","1");
INSERT INTO aphs_role_privileges VALUES("15","3","3","0");
INSERT INTO aphs_role_privileges VALUES("16","3","4","1");
INSERT INTO aphs_role_privileges VALUES("17","3","5","1");
INSERT INTO aphs_role_privileges VALUES("18","3","6","0");
INSERT INTO aphs_role_privileges VALUES("19","4","7","1");
INSERT INTO aphs_role_privileges VALUES("20","4","8","1");
INSERT INTO aphs_role_privileges VALUES("21","4","9","1");



DROP TABLE IF EXISTS aphs_roles;

CREATE TABLE `aphs_roles` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_roles VALUES("1","owner","Site Owner","Site Owner is the owner of the site, has all privileges and could not be removed.");
INSERT INTO aphs_roles VALUES("2","mainadmin","Main Admin","The \"Main Administrator\" user has top privileges like Site Owner and may be removed only by him.");
INSERT INTO aphs_roles VALUES("3","admin","Simple Admin","The \"Simple Admin\" is required to assist the Main Admins, has different privileges and may be created by Site Owner or Main Admins.");
INSERT INTO aphs_roles VALUES("4","hotelowner","Hotel Owner","The \"Hotel Owner\" is the owner of the hotel, has special privileges to the hotels/rooms he/she assigned to.");



DROP TABLE IF EXISTS aphs_room_facilities;

CREATE TABLE `aphs_room_facilities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_room_facilities VALUES("1","1","1");
INSERT INTO aphs_room_facilities VALUES("2","2","1");
INSERT INTO aphs_room_facilities VALUES("3","3","1");
INSERT INTO aphs_room_facilities VALUES("4","4","1");
INSERT INTO aphs_room_facilities VALUES("5","5","1");
INSERT INTO aphs_room_facilities VALUES("6","6","1");
INSERT INTO aphs_room_facilities VALUES("7","7","1");
INSERT INTO aphs_room_facilities VALUES("8","8","1");
INSERT INTO aphs_room_facilities VALUES("9","9","1");
INSERT INTO aphs_room_facilities VALUES("10","10","1");
INSERT INTO aphs_room_facilities VALUES("11","11","1");
INSERT INTO aphs_room_facilities VALUES("12","12","1");
INSERT INTO aphs_room_facilities VALUES("13","13","1");
INSERT INTO aphs_room_facilities VALUES("14","14","1");
INSERT INTO aphs_room_facilities VALUES("15","15","1");
INSERT INTO aphs_room_facilities VALUES("16","16","1");
INSERT INTO aphs_room_facilities VALUES("17","17","1");
INSERT INTO aphs_room_facilities VALUES("18","18","1");
INSERT INTO aphs_room_facilities VALUES("19","19","1");



DROP TABLE IF EXISTS aphs_room_facilities_description;

CREATE TABLE `aphs_room_facilities_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_facility_id` int(10) unsigned NOT NULL DEFAULT '0',
  `language_id` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(125) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_room_facilities_description VALUES("1","1","en","Smoking Allowed","");
INSERT INTO aphs_room_facilities_description VALUES("4","2","en","Elevator in Building","");
INSERT INTO aphs_room_facilities_description VALUES("8","3","en","Hot Tub","");
INSERT INTO aphs_room_facilities_description VALUES("10","4","en","Pets allowed","");
INSERT INTO aphs_room_facilities_description VALUES("13","5","en","Handicap Accessible","");
INSERT INTO aphs_room_facilities_description VALUES("16","6","en","Indoor Fireplace","");
INSERT INTO aphs_room_facilities_description VALUES("19","7","en","TV","");
INSERT INTO aphs_room_facilities_description VALUES("22","8","en","Pool","");
INSERT INTO aphs_room_facilities_description VALUES("25","9","en","Buzzer/Wireless Intercom","");
INSERT INTO aphs_room_facilities_description VALUES("29","10","en","Cable TV","");
INSERT INTO aphs_room_facilities_description VALUES("31","11","en","Kitchen","");
INSERT INTO aphs_room_facilities_description VALUES("34","12","en","Internet","");
INSERT INTO aphs_room_facilities_description VALUES("37","13","en","Parking Included","");
INSERT INTO aphs_room_facilities_description VALUES("40","14","en","Family/Kid Friendly","");
INSERT INTO aphs_room_facilities_description VALUES("43","15","en","Wireless Internet","");
INSERT INTO aphs_room_facilities_description VALUES("46","16","en","Washer/Dryer","");
INSERT INTO aphs_room_facilities_description VALUES("49","17","en","Suitable for Events","");
INSERT INTO aphs_room_facilities_description VALUES("52","18","en","Air Conditioning","");
INSERT INTO aphs_room_facilities_description VALUES("55","19","en","Heating","");



DROP TABLE IF EXISTS aphs_rooms;

CREATE TABLE `aphs_rooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `hotel_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `room_type` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `room_short_description` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `room_long_description` text COLLATE utf8_unicode_ci NOT NULL,
  `room_count` smallint(6) NOT NULL,
  `max_adults` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `max_guests` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `max_children` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `default_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `additional_guest_fee` decimal(10,2) unsigned NOT NULL,
  `default_availability` tinyint(1) NOT NULL DEFAULT '1',
  `beds` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `bathrooms` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `room_area` decimal(5,2) unsigned NOT NULL DEFAULT '0.00',
  `facilities` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_icon` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_icon_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_1` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_1_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_2` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_2_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_3` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_3_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_4` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_4_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_5` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `room_picture_5_thumb` varchar(70) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `priority_order` smallint(6) unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_rooms VALUES("1","1","Single","","Single","15","1","0","0","55.00","22.00","1","1","1","20.00","a:7:{i:0;s:1:\"4\";i:1;s:1:\"5\";i:2;s:1:\"7\";i:3;s:1:\"8\";i:4;s:1:\"9\";i:5;s:2:\"11\";i:6;s:2:\"15\";}","single_icon.png","single_icon_thumb.jpg","single_1.jpg","single_1_thumb.jpg","single_2.jpg","single_2_thumb.jpg","single_3.jpg","single_3_thumb.jpg","single_4.jpg","single_4_thumb.jpg","single_5.jpg","single_5_thumb.jpg","1","1");
INSERT INTO aphs_rooms VALUES("2","1","Double","","Double","10","2","1","1","80.00","30.00","1","2","2","25.00","a:8:{i:0;s:1:\"4\";i:1;s:1:\"5\";i:2;s:1:\"7\";i:3;s:1:\"8\";i:4;s:2:\"10\";i:5;s:2:\"13\";i:6;s:2:\"16\";i:7;s:2:\"17\";}","double_icon.png","double_icon_thumb.jpg","double_1.jpg","double_1_thumb.jpg","double_2.jpg","double_2_thumb.jpg","double_3.jpg","double_3_thumb.jpg","","","","","2","1");
INSERT INTO aphs_rooms VALUES("3","1","Superior","","Superior","5","3","1","1","140.00","50.00","1","2","1","35.00","a:11:{i:0;s:1:\"4\";i:1;s:1:\"5\";i:2;s:1:\"7\";i:3;s:1:\"8\";i:4;s:1:\"9\";i:5;s:2:\"12\";i:6;s:2:\"15\";i:7;s:2:\"16\";i:8;s:2:\"19\";i:9;s:2:\"22\";i:10;s:2:\"24\";}","superior_icon.png","superior_icon_thumb.jpg","superior_1.jpg","superior_1_thumb.jpg","superior_2.jpg","superior_2_thumb.jpg","superior_3.jpg","superior_3_thumb.jpg","","","","","3","1");
INSERT INTO aphs_rooms VALUES("4","1","Luxury","","Luxury","3","4","2","2","190.00","80.00","1","2","2","55.00","a:15:{i:0;s:1:\"4\";i:1;s:1:\"7\";i:2;s:1:\"9\";i:3;s:2:\"10\";i:4;s:2:\"11\";i:5;s:2:\"12\";i:6;s:2:\"13\";i:7;s:2:\"15\";i:8;s:2:\"16\";i:9;s:2:\"17\";i:10;s:2:\"18\";i:11;s:2:\"19\";i:12;s:2:\"20\";i:13;s:2:\"22\";i:14;s:2:\"23\";}","luxury_icon.png","luxury_icon_thumb.jpg","luxury_1.jpg","luxury_1_thumb.jpg","luxury_2.jpg","luxury_2_thumb.jpg","luxury_3.jpg","luxury_3_thumb.jpg","","","","","4","1");



DROP TABLE IF EXISTS aphs_rooms_availabilities;

CREATE TABLE `aphs_rooms_availabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL DEFAULT '0',
  `y` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - current year, 1 - next year',
  `m` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `d1` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d2` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d3` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d4` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d5` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d6` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d7` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d8` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d9` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d10` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d11` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d12` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d13` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d14` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d15` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d16` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d17` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d18` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d19` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d20` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d21` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d22` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d23` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d24` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d25` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d26` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d27` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d28` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d29` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d30` smallint(6) unsigned NOT NULL DEFAULT '0',
  `d31` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`),
  KEY `y` (`y`),
  KEY `m` (`m`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_rooms_availabilities VALUES("1","1","0","1","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("2","1","0","2","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("3","1","0","3","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("4","1","0","4","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("5","1","0","5","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("6","1","0","6","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("7","1","0","7","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("8","1","0","8","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("9","1","0","9","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("10","1","0","10","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("11","1","0","11","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("12","1","0","12","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("13","1","1","1","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("14","1","1","2","0","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("15","1","1","3","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("16","1","1","4","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("17","1","1","5","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("18","1","1","6","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("19","1","1","7","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("20","1","1","8","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("21","1","1","9","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("22","1","1","10","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("23","1","1","11","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","0");
INSERT INTO aphs_rooms_availabilities VALUES("24","1","1","12","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15","15");
INSERT INTO aphs_rooms_availabilities VALUES("25","2","0","1","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("26","2","0","2","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("27","2","0","3","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("28","2","0","4","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("29","2","0","5","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("30","2","0","6","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("31","2","0","7","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("32","2","0","8","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","2","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("33","2","0","9","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("34","2","0","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("35","2","0","11","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("36","2","0","12","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("37","2","1","1","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("38","2","1","2","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("39","2","1","3","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("40","2","1","4","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("41","2","1","5","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("42","2","1","6","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("43","2","1","7","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("44","2","1","8","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("45","2","1","9","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("46","2","1","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("47","2","1","11","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","0");
INSERT INTO aphs_rooms_availabilities VALUES("48","2","1","12","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10","10");
INSERT INTO aphs_rooms_availabilities VALUES("49","3","0","1","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("50","3","0","2","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("51","3","0","3","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("52","3","0","4","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("53","3","0","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("54","3","0","6","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("55","3","0","7","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("56","3","0","8","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("57","3","0","9","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("58","3","0","10","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("59","3","0","11","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("60","3","0","12","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("61","3","1","1","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("62","3","1","2","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("63","3","1","3","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("64","3","1","4","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("65","3","1","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("66","3","1","6","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("67","3","1","7","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("68","3","1","8","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("69","3","1","9","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("70","3","1","10","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("71","3","1","11","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","0");
INSERT INTO aphs_rooms_availabilities VALUES("72","3","1","12","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5","5");
INSERT INTO aphs_rooms_availabilities VALUES("73","4","0","1","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("74","4","0","2","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("75","4","0","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("76","4","0","4","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("77","4","0","5","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("78","4","0","6","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("79","4","0","7","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("80","4","0","8","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("81","4","0","9","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("82","4","0","10","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("83","4","0","11","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("84","4","0","12","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("85","4","1","1","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("86","4","1","2","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0","0","0");
INSERT INTO aphs_rooms_availabilities VALUES("87","4","1","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("88","4","1","4","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("89","4","1","5","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("90","4","1","6","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("91","4","1","7","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("92","4","1","8","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("93","4","1","9","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("94","4","1","10","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");
INSERT INTO aphs_rooms_availabilities VALUES("95","4","1","11","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","0");
INSERT INTO aphs_rooms_availabilities VALUES("96","4","1","12","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3","3");



DROP TABLE IF EXISTS aphs_rooms_description;

CREATE TABLE `aphs_rooms_description` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(10) NOT NULL DEFAULT '0',
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL DEFAULT 'en',
  `room_type` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `room_short_description` varchar(512) COLLATE utf8_unicode_ci NOT NULL,
  `room_long_description` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_rooms_description VALUES("1","1","en","Single","<p>Rooms measuring 15 m&sup2; equipped with all the details expected of a superior 4 star hotel. Services: Wake up call service, Customer service, Laundry service and express laundry, Concierge service, Pillow menu.</p>","<p><strong>Description</strong>:<br />Rooms measuring 15 m&sup2; equipped with all the details expected of a superior 4 star hotel.<br /><br /><strong>Services</strong>:<br />&middot; Wake up call service<br />&middot; Customer service<br />&middot; Laundry service and express laundry<br />&middot; Concierge service <br />&middot; Pillow menu <strong><br /></strong></p>");
INSERT INTO aphs_rooms_description VALUES("4","2","en","Double","<p>Modern and functional rooms measuring approximately 20-25 m&sup2; equipped with all the details expected of the hotel. The rooms have a king or queen size bed or two single beds, in addition to beds measuring 1 by 2.2 metres ideal for sports teams.</p>","<p><strong>Description</strong>:<br />Modern and functional rooms measuring approximately 20-25 m&sup2; equipped with all the details expected of the hotel. <br /><br />The rooms have a king or queen size bed or two single beds, in addition to beds measuring 1 by 2.2 metres ideal for sports teams and views of the streets of the quiet interior patios (request availability upon arrival at the hotel).<br /><br /><strong>Services</strong>:<br />&middot; Wake up call service<br />&middot; Customer service<br />&middot; Laundry service and express laundry<br />&middot; Concierge service <br />&middot; Pillow menu <strong><br /></strong></p>");
INSERT INTO aphs_rooms_description VALUES("7","3","en","Superior","<p>Spacious rooms with exquisite decor, measuring approximately 25-30 m&sup2; and equipped with all the details expected of the hotel hotel. The rooms have a king or queen size bed or two single beds, in addition to beds measuring 1 by 2.2 metres.</p>","<p><strong>Description</strong>:<br /> Spacious rooms with exquisite decor, measuring approximately 25-30 m&sup2; and equipped with all the details expected of the hotel hotel. <br /><br /> The rooms have a king or queen size bed or two single beds, in addition to beds measuring 1 by 2.2 metres ideal for sports teams and views of the streets of the quiet interior patios (request availability upon arrival at the hotel).  <br /><br /> <strong>Services</strong>:<br /> &middot; 24 hour room service <br /> &middot; Wake up call service<br /> &middot;&rdquo;Serviexpress&rdquo; customer service<br /> &middot; Laundry service and express laundry<br /> &middot; Concierge service <br /> &middot; Pillow menu <strong><br /></strong></p>");
INSERT INTO aphs_rooms_description VALUES("10","4","en","Luxury","<p>Spacious rooms with exquisite decor measuring approximately 25-30 m&sup2; and equipped with all the details expected of a superior 4 star Hotel. The rooms have a king or queen size bed or two single beds, and views of the streets.</p>","<p><strong>Description</strong>:<br />Spacious rooms with exquisite decor measuring approximately 25-30 m&sup2; and equipped with all the details expected of a superior 4 star Hotel.<br /><br />The rooms have a king or queen size bed or two single beds, and views of the streets of the quiet interior patios (request availability upon arrival at the hotel). <br /><br /><strong>Services</strong>:<br />&middot; Private reception located on the 6th floor<br />Welcome courtesy replaced daily <br />&middot; Buffet breakfast in private room between 7am and 11am<br />&middot; Free Open Bar available on the Luxury Service floor, from 16:30 to 22:30 <br />&middot; 24 hour room service<br />&middot; 10% discount on lunches and dinners at our Di&aacute;bolo restaurant, after consultation and booking at the Royal Service reception and subject to availability<br />&middot; Free 30 minute use per stay of computers in the Business Centre (hall), including printer and 25 photocopies<br />&middot; Free 8 minute Ultra Violet Ray session per stay<br />&middot; Wake up call service<br />&middot; Customer service<br />&middot; Laundry service and express laundry<br />&middot; Concierge service <br />&middot; Pillow menu <br />&middot; Turn down service <br /><br />* The Luxury Service is closed from Friday at 12:00 until Sunday at 15:00.<strong><br /></strong></p>");



DROP TABLE IF EXISTS aphs_rooms_prices;

CREATE TABLE `aphs_rooms_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date_from` date DEFAULT '0000-00-00',
  `date_to` date DEFAULT '0000-00-00',
  `adults` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `children` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `guest_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `mon` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tue` decimal(10,2) NOT NULL DEFAULT '0.00',
  `wed` decimal(10,2) NOT NULL DEFAULT '0.00',
  `thu` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fri` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sat` decimal(10,2) NOT NULL DEFAULT '0.00',
  `sun` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_rooms_prices VALUES("1","1","0000-00-00","0000-00-00","1","0","22.00","55.00","55.00","55.00","55.00","55.00","55.00","55.00","1");
INSERT INTO aphs_rooms_prices VALUES("2","2","0000-00-00","0000-00-00","2","1","30.00","80.00","80.00","80.00","80.00","80.00","90.00","90.00","1");
INSERT INTO aphs_rooms_prices VALUES("3","3","0000-00-00","0000-00-00","3","1","50.00","140.00","140.00","140.00","140.00","140.00","140.00","140.00","1");
INSERT INTO aphs_rooms_prices VALUES("4","4","0000-00-00","0000-00-00","4","2","90.00","190.00","190.00","190.00","190.00","190.00","190.00","190.00","1");



DROP TABLE IF EXISTS aphs_search_wordlist;

CREATE TABLE `aphs_search_wordlist` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `word_text` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `word_count` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word_text` (`word_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




DROP TABLE IF EXISTS aphs_settings;

CREATE TABLE `aphs_settings` (
  `id` smallint(6) NOT NULL,
  `template` varchar(32) CHARACTER SET latin1 NOT NULL,
  `ssl_mode` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0 - no, 1 - entire site, 2 - admin, 3 - customer & payment modules',
  `seo_urls` tinyint(1) NOT NULL DEFAULT '1',
  `date_format` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'dd/mm/yyyy',
  `time_zone` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `price_format` enum('european','american') CHARACTER SET latin1 NOT NULL,
  `week_start_day` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `admin_email` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `mailer` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT 'php_mail_standard',
  `mailer_type` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `mailer_wysiwyg_type` enum('none','tinymce') CHARACTER SET latin1 NOT NULL DEFAULT 'none',
  `smtp_secure` enum('ssl','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ssl',
  `smtp_host` varchar(70) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_port` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `smtp_password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `wysiwyg_type` enum('none','openwysiwyg','tinymce') CHARACTER SET latin1 NOT NULL DEFAULT 'openwysiwyg',
  `rss_feed` tinyint(1) NOT NULL DEFAULT '1',
  `rss_feed_type` enum('rss1','rss2','atom') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'rss1',
  `rss_last_ids` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `is_offline` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `caching_allowed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `cache_lifetime` tinyint(3) unsigned NOT NULL DEFAULT '5' COMMENT 'in minutes',
  `offline_message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `google_rank` varchar(2) CHARACTER SET latin1 NOT NULL,
  `alexa_rank` varchar(12) CHARACTER SET latin1 NOT NULL,
  `cron_type` enum('batch','non-batch','stop') CHARACTER SET latin1 NOT NULL DEFAULT 'non-batch',
  `cron_run_last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cron_run_period` enum('minute','hour') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'minute',
  `cron_run_period_value` smallint(6) unsigned NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_settings VALUES("0","x-brown","0","1","dd/mm/yyyy","8","american","1","info@astamanavilla.com","php","php_mail_standard","none","ssl","","","","","openwysiwyg","1","rss1","1","0","0","5","Our website is currently offline for maintenance. Please visit us later.","-1","0","non-batch","2013-06-08 22:42:56","hour","24");



DROP TABLE IF EXISTS aphs_site_description;

CREATE TABLE `aphs_site_description` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` varchar(2) CHARACTER SET latin1 NOT NULL,
  `header_text` text COLLATE utf8_unicode_ci NOT NULL,
  `slogan_text` text COLLATE utf8_unicode_ci NOT NULL,
  `footer_text` text COLLATE utf8_unicode_ci NOT NULL,
  `tag_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `tag_description` text COLLATE utf8_unicode_ci NOT NULL,
  `tag_keywords` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_site_description VALUES("1","en","Astamana Villa","Serene and Peaceful...","AstamanaVilla © <a class=\"footer_link\" href=\"http://www.astamanavilla.com/php-hotel-site/index.php\">Astamanavilla</a>","Astamana Villa","hotel site bali","astamana villa, bali villa, bali, bali hotel, canggu");



DROP TABLE IF EXISTS aphs_testimonials;

CREATE TABLE `aphs_testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_country` varchar(2) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_city` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `author_email` varchar(70) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `testimonial_text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `priority_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO aphs_testimonials VALUES("1","Roberto","IT","Rome","roberto@email.com","Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.","1","0");
INSERT INTO aphs_testimonials VALUES("2","Hantz","DE","Munich","hantz@email.com","Typi non habent claritatem insitam est usus legentis in iis qui facit eorum claritatem. Investigationes demonstraverunt lectores legere me lius quod ii legunt saepius.","1","1");
INSERT INTO aphs_testimonials VALUES("3","Lilian","GB","London","lilian@email.com","Nam liber tempor cum soluta nobis eleifend option congue nihil imperdiet doming id quod mazim placerat facer possim assum.","1","3");
INSERT INTO aphs_testimonials VALUES("4","Debora","US","","debora@email.com","Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.","1","2");



DROP TABLE IF EXISTS aphs_vocabulary;

CREATE TABLE `aphs_vocabulary` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `language_id` varchar(3) CHARACTER SET latin1 NOT NULL,
  `key_value` varchar(50) CHARACTER SET latin1 NOT NULL,
  `key_text` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voc_item` (`language_id`,`key_value`),
  KEY `language_id` (`language_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3427 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO aphs_vocabulary VALUES("2","en","_2CO_NOTICE","2CheckOut.com Inc. (Ohio, USA) is an authorized retailer for goods and services.");
INSERT INTO aphs_vocabulary VALUES("5","en","_2CO_ORDER","2CO Order");
INSERT INTO aphs_vocabulary VALUES("8","en","_ABBREVIATION","Abbreviation");
INSERT INTO aphs_vocabulary VALUES("11","en","_ABOUT_US","About Us");
INSERT INTO aphs_vocabulary VALUES("14","en","_ACCESS","Access");
INSERT INTO aphs_vocabulary VALUES("17","en","_ACCESSIBLE_BY","Accessible By");
INSERT INTO aphs_vocabulary VALUES("20","en","_ACCOUNTS","Accounts");
INSERT INTO aphs_vocabulary VALUES("23","en","_ACCOUNTS_MANAGEMENT","Accounts");
INSERT INTO aphs_vocabulary VALUES("26","en","_ACCOUNT_ALREADY_RESET","Your account was already reset! Please check your email inbox for more information.");
INSERT INTO aphs_vocabulary VALUES("29","en","_ACCOUNT_CREATED_CONF_BY_ADMIN_MSG","Your account has been successfully created! In a few minutes you should receive an email, containing the details of your account. <br><br> After approval your registration by administrator, you will be able to log into your account.");
INSERT INTO aphs_vocabulary VALUES("32","en","_ACCOUNT_CREATED_CONF_BY_EMAIL_MSG","Your account has been successfully created! In a few minutes you should receive an email, containing the details of your registration. <br><br> Complete this registration, using the confirmation code that was sent to the provided email address, and you will be able to log into your account.");
INSERT INTO aphs_vocabulary VALUES("35","en","_ACCOUNT_CREATED_CONF_MSG","Your account was successfully created. <b>You will receive now an email</b>, containing the details of your account (it may take a few minutes).<br><br>After approval by an administrator, you will be able to log into your account.");
INSERT INTO aphs_vocabulary VALUES("38","en","_ACCOUNT_CREATED_MSG","Your account was successfully created. <b>You will receive now a confirmation email</b>, containing the details of your account (it may take a few minutes). <br /><br />After completing the confirmation you will be able to log into your account.");
INSERT INTO aphs_vocabulary VALUES("41","en","_ACCOUNT_CREATED_NON_CONFIRM_LINK","Click <a href=index.php?customer=login>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("44","en","_ACCOUNT_CREATED_NON_CONFIRM_MSG","Your account has been successfully created! For your convenience in a few minutes you will receive an email, containing the details of your registration (no confirmation required). <br><br>You may log into your account now.");
INSERT INTO aphs_vocabulary VALUES("47","en","_ACCOUNT_CREATE_MSG","This registration process requires confirmation via email! <br />Please fill out the form below with correct information.");
INSERT INTO aphs_vocabulary VALUES("50","en","_ACCOUNT_DETAILS","Account Details");
INSERT INTO aphs_vocabulary VALUES("53","en","_ACCOUNT_SUCCESSFULLY_RESET","You have successfully reset your account and username with temporary password have been sent to your email.");
INSERT INTO aphs_vocabulary VALUES("56","en","_ACCOUNT_TYPE","Account type");
INSERT INTO aphs_vocabulary VALUES("59","en","_ACCOUNT_WAS_CREATED","Your account has been created");
INSERT INTO aphs_vocabulary VALUES("62","en","_ACCOUNT_WAS_DELETED","Your account was successfully removed! In seconds, you will be automatically redirected to the homepage.");
INSERT INTO aphs_vocabulary VALUES("65","en","_ACCOUNT_WAS_UPDATED","Your account was successfully updated!");
INSERT INTO aphs_vocabulary VALUES("68","en","_ACCOUT_CREATED_CONF_LINK","Already confirmed your registration? Click <a href=index.php?customer=login>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("71","en","_ACCOUT_CREATED_CONF_MSG","Already confirmed your registration? Click <a href=index.php?customer=login>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("74","en","_ACTIONS","Action");
INSERT INTO aphs_vocabulary VALUES("77","en","_ACTIONS_WORD","Action");
INSERT INTO aphs_vocabulary VALUES("80","en","_ACTION_REQUIRED","ACTION REQUIRED");
INSERT INTO aphs_vocabulary VALUES("83","en","_ACTIVATION_EMAIL_ALREADY_SENT","The activation email was already sent to your email. Please try again later.");
INSERT INTO aphs_vocabulary VALUES("86","en","_ACTIVATION_EMAIL_WAS_SENT","An email has been sent to _EMAIL_ with an activation key. Please check your mail to complete registration.");
INSERT INTO aphs_vocabulary VALUES("89","en","_ACTIVE","Active");
INSERT INTO aphs_vocabulary VALUES("92","en","_ADD","Add");
INSERT INTO aphs_vocabulary VALUES("95","en","_ADDING_OPERATION_COMPLETED","The adding operation completed successfully!");
INSERT INTO aphs_vocabulary VALUES("98","en","_ADDITIONAL_GUEST_FEE","Additional Guest Fee");
INSERT INTO aphs_vocabulary VALUES("101","en","_ADDITIONAL_INFO","Additional Info");
INSERT INTO aphs_vocabulary VALUES("104","en","_ADDITIONAL_MODULES","Additional Modules");
INSERT INTO aphs_vocabulary VALUES("107","en","_ADDITIONAL_PAYMENT","Additional Payment");
INSERT INTO aphs_vocabulary VALUES("110","en","_ADDITIONAL_PAYMENT_TOOLTIP","To apply an additional payment or admin discount enter into this field an appropriate value (positive or negative).");
INSERT INTO aphs_vocabulary VALUES("113","en","_ADDRESS","Address");
INSERT INTO aphs_vocabulary VALUES("116","en","_ADDRESS_2","Address (line 2)");
INSERT INTO aphs_vocabulary VALUES("119","en","_ADDRESS_EMPTY_ALERT","Address cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("122","en","_ADD_NEW","Add New");
INSERT INTO aphs_vocabulary VALUES("125","en","_ADD_NEW_MENU","Add New Menu");
INSERT INTO aphs_vocabulary VALUES("128","en","_ADD_TO_CART","Add to Cart");
INSERT INTO aphs_vocabulary VALUES("131","en","_ADD_TO_MENU","Add To Menu");
INSERT INTO aphs_vocabulary VALUES("134","en","_ADMIN","Admin");
INSERT INTO aphs_vocabulary VALUES("137","en","_ADMINISTRATOR_ONLY","Administrator Only");
INSERT INTO aphs_vocabulary VALUES("140","en","_ADMINS","Admins");
INSERT INTO aphs_vocabulary VALUES("143","en","_ADMINS_AND_CUSTOMERS","Customers & Admins");
INSERT INTO aphs_vocabulary VALUES("146","en","_ADMINS_MANAGEMENT","Admins Management");
INSERT INTO aphs_vocabulary VALUES("149","en","_ADMIN_EMAIL","Admin Email");
INSERT INTO aphs_vocabulary VALUES("152","en","_ADMIN_EMAIL_ALERT","This email is used as \"From\" address for the system email notifications. Make sure, that you write here a valid email address based on domain of your site");
INSERT INTO aphs_vocabulary VALUES("155","en","_ADMIN_EMAIL_EXISTS_ALERT","Administrator with such email already exists! Please choose another.");
INSERT INTO aphs_vocabulary VALUES("158","en","_ADMIN_EMAIL_IS_EMPTY","Admin email must not be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("161","en","_ADMIN_EMAIL_WRONG","Admin email in wrong format! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("164","en","_ADMIN_LOGIN","Admin Login");
INSERT INTO aphs_vocabulary VALUES("167","en","_ADMIN_MAILER_ALERT","Select which mailer you prefer to use for the delivery of site emails.");
INSERT INTO aphs_vocabulary VALUES("170","en","_ADMIN_PANEL","Admin Panel");
INSERT INTO aphs_vocabulary VALUES("173","en","_ADMIN_RESERVATION","Admin Reservation");
INSERT INTO aphs_vocabulary VALUES("176","en","_ADMIN_WELCOME_TEXT","<p>Welcome to Administrator Control Panel that allows you to add, edit or delete site content. With this Administrator Control Panel you can easy manage customers, reservations and perform a full hotel site management.</p><p><b>&#8226;</b> There are some modules for you: Backup & Restore, News. Installation or un-installation of them is possible from <a href=\'index.php?admin=modules\'>Modules Menu</a>.</p><p><b>&#8226;</b> In <a href=\'index.php?admin=languages\'>Languages Menu</a> you may add/remove language or change language settings and edit your vocabulary (the words and phrases, used by the system).</p><p><b>&#8226;</b> <a href=\'index.php?admin=settings\'>Settings Menu</a> allows you to define important settings for the site.</p><p><b>&#8226;</b> In <a href=\'index.php?admin=my_account\'>My Account</a> there is a possibility to change your info.</p><p><b>&#8226;</b> <a href=\'index.php?admin=menus\'>Menus</a> and <a href=\'index.php?admin=pages\'>Pages Management</a> are designed for creating and managing menus, links and pages.</p><p><b>&#8226;</b> To create and edit room types, seasons, prices, bookings and other hotel info, use <a href=\'index.php?admin=hotel_info\'>Hotel Management</a>, <a href=\'index.php?admin=rooms_management\'>Rooms Management</a> and <a href=\'index.php?admin=mod_booking_bookings\'>Bookings</a> menus.</p>");
INSERT INTO aphs_vocabulary VALUES("179","en","_ADULT","Adult");
INSERT INTO aphs_vocabulary VALUES("182","en","_ADULTS","Adults");
INSERT INTO aphs_vocabulary VALUES("185","en","_ADVANCED","Advanced");
INSERT INTO aphs_vocabulary VALUES("188","en","_AFTER_DISCOUNT","after discount");
INSERT INTO aphs_vocabulary VALUES("191","en","_AGREE_CONF_TEXT","I have read and AGREE with Terms & Conditions");
INSERT INTO aphs_vocabulary VALUES("194","en","_ALBUM","Album");
INSERT INTO aphs_vocabulary VALUES("197","en","_ALBUM_CODE","Album Code");
INSERT INTO aphs_vocabulary VALUES("200","en","_ALBUM_NAME","Album Name");
INSERT INTO aphs_vocabulary VALUES("203","en","_ALERT_CANCEL_BOOKING","Are you sure you want to cancel this booking?");
INSERT INTO aphs_vocabulary VALUES("206","en","_ALERT_REQUIRED_FILEDS","Items marked with an asterisk (*) are required");
INSERT INTO aphs_vocabulary VALUES("209","en","_ALL","All");
INSERT INTO aphs_vocabulary VALUES("212","en","_ALLOW","Allow");
INSERT INTO aphs_vocabulary VALUES("215","en","_ALLOW_COMMENTS","Allow comments");
INSERT INTO aphs_vocabulary VALUES("218","en","_ALL_AVAILABLE","All Available");
INSERT INTO aphs_vocabulary VALUES("221","en","_ALREADY_HAVE_ACCOUNT","Already have an account? <a href=\'index.php?customer=login\'>Login here</a>");
INSERT INTO aphs_vocabulary VALUES("224","en","_ALREADY_LOGGED","You are already logged in!");
INSERT INTO aphs_vocabulary VALUES("227","en","_AMOUNT","Amount");
INSERT INTO aphs_vocabulary VALUES("230","en","_ANSWER","Answer");
INSERT INTO aphs_vocabulary VALUES("233","en","_ANY","Any");
INSERT INTO aphs_vocabulary VALUES("236","en","_APPLY","Apply");
INSERT INTO aphs_vocabulary VALUES("239","en","_APPLY_TO_ALL_LANGUAGES","Apply to all languages");
INSERT INTO aphs_vocabulary VALUES("242","en","_APPLY_TO_ALL_PAGES","Apply changes to all pages");
INSERT INTO aphs_vocabulary VALUES("245","en","_APPROVE","Approve");
INSERT INTO aphs_vocabulary VALUES("248","en","_APPROVED","Approved");
INSERT INTO aphs_vocabulary VALUES("251","en","_APRIL","April");
INSERT INTO aphs_vocabulary VALUES("254","en","_ARTICLE","Article");
INSERT INTO aphs_vocabulary VALUES("257","en","_ARTICLE_ID","Article ID");
INSERT INTO aphs_vocabulary VALUES("260","en","_AUGUST","August");
INSERT INTO aphs_vocabulary VALUES("263","en","_AUTHENTICATION","Authentication");
INSERT INTO aphs_vocabulary VALUES("266","en","_AUTHORIZE_NET_NOTICE","The Authorize.Net payment gateway service provider.");
INSERT INTO aphs_vocabulary VALUES("269","en","_AUTHORIZE_NET_ORDER","Authorize.Net Order");
INSERT INTO aphs_vocabulary VALUES("272","en","_AVAILABILITY","Availability");
INSERT INTO aphs_vocabulary VALUES("275","en","_AVAILABILITY_ROOMS_NOTE","Define a maximum number of rooms available for booking for a specified day or date range (maximum availability _MAX_ rooms)<br>To edit room availability simply change the value in a day cell and then click \'Save Changes\' button");
INSERT INTO aphs_vocabulary VALUES("278","en","_AVAILABLE","available");
INSERT INTO aphs_vocabulary VALUES("281","en","_AVAILABLE_ROOMS","Available Rooms");
INSERT INTO aphs_vocabulary VALUES("284","en","_BACKUP","Backup");
INSERT INTO aphs_vocabulary VALUES("287","en","_BACKUPS_EXISTING","Existing Backups");
INSERT INTO aphs_vocabulary VALUES("290","en","_BACKUP_AND_RESTORE","Backup & Restore");
INSERT INTO aphs_vocabulary VALUES("293","en","_BACKUP_CHOOSE_MSG","Choose a backup from the list below");
INSERT INTO aphs_vocabulary VALUES("296","en","_BACKUP_DELETE_ALERT","Are you sure you want to delete this backup?");
INSERT INTO aphs_vocabulary VALUES("299","en","_BACKUP_EMPTY_MSG","No existing backups found.");
INSERT INTO aphs_vocabulary VALUES("302","en","_BACKUP_EMPTY_NAME_ALERT","Name of backup file cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("305","en","_BACKUP_EXECUTING_ERROR","An error occurred while backup the system! Please check write permissions to backup folder or try again later.");
INSERT INTO aphs_vocabulary VALUES("308","en","_BACKUP_INSTALLATION","Backup Installation");
INSERT INTO aphs_vocabulary VALUES("311","en","_BACKUP_RESTORE","Backup Restore");
INSERT INTO aphs_vocabulary VALUES("314","en","_BACKUP_RESTORE_ALERT","Are you sure you want to restore this backup");
INSERT INTO aphs_vocabulary VALUES("317","en","_BACKUP_RESTORE_NOTE","Remember: this action will rewrite all your current settings!");
INSERT INTO aphs_vocabulary VALUES("320","en","_BACKUP_RESTORING_ERROR","An error occurred while restoring file! Please try again later.");
INSERT INTO aphs_vocabulary VALUES("323","en","_BACKUP_WAS_CREATED","Backup _FILE_NAME_ was successfully created.");
INSERT INTO aphs_vocabulary VALUES("326","en","_BACKUP_WAS_DELETED","Backup _FILE_NAME_ was successfully deleted.");
INSERT INTO aphs_vocabulary VALUES("329","en","_BACKUP_WAS_RESTORED","Backup _FILE_NAME_ was successfully restored.");
INSERT INTO aphs_vocabulary VALUES("332","en","_BACKUP_YOUR_INSTALLATION","Backup your current Installation");
INSERT INTO aphs_vocabulary VALUES("335","en","_BACK_TO_ADMIN_PANEL","Back to Admin Panel");
INSERT INTO aphs_vocabulary VALUES("338","en","_BANK_PAYMENT_INFO","Bank Payment Information");
INSERT INTO aphs_vocabulary VALUES("341","en","_BANK_TRANSFER","Bank Transfer");
INSERT INTO aphs_vocabulary VALUES("344","en","_BANNERS","Banners");
INSERT INTO aphs_vocabulary VALUES("347","en","_BANNERS_MANAGEMENT","Banners Management");
INSERT INTO aphs_vocabulary VALUES("350","en","_BANNERS_SETTINGS","Banners Settings");
INSERT INTO aphs_vocabulary VALUES("353","en","_BANNER_IMAGE","Banner Image");
INSERT INTO aphs_vocabulary VALUES("356","en","_BAN_ITEM","Ban Item");
INSERT INTO aphs_vocabulary VALUES("359","en","_BAN_LIST","Ban List");
INSERT INTO aphs_vocabulary VALUES("362","en","_BATHROOMS","Bathrooms");
INSERT INTO aphs_vocabulary VALUES("365","en","_BEDS","Beds");
INSERT INTO aphs_vocabulary VALUES("368","en","_BILLING_ADDRESS","Billing Address");
INSERT INTO aphs_vocabulary VALUES("371","en","_BILLING_DETAILS","Billing Details");
INSERT INTO aphs_vocabulary VALUES("373","en","_BILLING_DETAILS_UPDATED","Your Billing Details has been updated.");
INSERT INTO aphs_vocabulary VALUES("375","en","_BIRTH_DATE","Birth Date");
INSERT INTO aphs_vocabulary VALUES("378","en","_BIRTH_DATE_VALID_ALERT","Birth date was entered in wrong format! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("381","en","_BOOK","Book");
INSERT INTO aphs_vocabulary VALUES("384","en","_BOOKING","Booking");
INSERT INTO aphs_vocabulary VALUES("387","en","_BOOKINGS","Bookings");
INSERT INTO aphs_vocabulary VALUES("390","en","_BOOKINGS_MANAGEMENT","Bookings Management");
INSERT INTO aphs_vocabulary VALUES("393","en","_BOOKINGS_SETTINGS","Booking Settings");
INSERT INTO aphs_vocabulary VALUES("396","en","_BOOKING_CANCELED","Booking Canceled");
INSERT INTO aphs_vocabulary VALUES("399","en","_BOOKING_CANCELED_SUCCESS","The booking _BOOKING_ has been successfully canceled from the system!");
INSERT INTO aphs_vocabulary VALUES("402","en","_BOOKING_COMPLETED","Booking Completed");
INSERT INTO aphs_vocabulary VALUES("405","en","_BOOKING_DATE","Booking Date");
INSERT INTO aphs_vocabulary VALUES("408","en","_BOOKING_DESCRIPTION","Booking Description");
INSERT INTO aphs_vocabulary VALUES("411","en","_BOOKING_DETAILS","Booking Details");
INSERT INTO aphs_vocabulary VALUES("414","en","_BOOKING_NUMBER","Booking Number");
INSERT INTO aphs_vocabulary VALUES("417","en","_BOOKING_PRICE","Booking Price");
INSERT INTO aphs_vocabulary VALUES("420","en","_BOOKING_SETTINGS","Booking Settings");
INSERT INTO aphs_vocabulary VALUES("423","en","_BOOKING_STATUS","Booking Status");
INSERT INTO aphs_vocabulary VALUES("426","en","_BOOKING_SUBTOTAL","Booking Subtotal");
INSERT INTO aphs_vocabulary VALUES("429","en","_BOOKING_WAS_CANCELED_MSG","Your booking has been canceled.");
INSERT INTO aphs_vocabulary VALUES("432","en","_BOOKING_WAS_COMPLETED_MSG","Thank you for reservation rooms in our hotel! Your booking has been completed.");
INSERT INTO aphs_vocabulary VALUES("435","en","_BOOK_NOW","Book Now");
INSERT INTO aphs_vocabulary VALUES("438","en","_BOOK_ONE_NIGHT_ALERT","Sorry, but you must book at least one night.");
INSERT INTO aphs_vocabulary VALUES("441","en","_BOTTOM","Bottom");
INSERT INTO aphs_vocabulary VALUES("444","en","_BUTTON_BACK","Back");
INSERT INTO aphs_vocabulary VALUES("447","en","_BUTTON_CANCEL","Cancel");
INSERT INTO aphs_vocabulary VALUES("450","en","_BUTTON_CHANGE","Change");
INSERT INTO aphs_vocabulary VALUES("453","en","_BUTTON_CHANGE_PASSWORD","Change Password");
INSERT INTO aphs_vocabulary VALUES("456","en","_BUTTON_CREATE","Create");
INSERT INTO aphs_vocabulary VALUES("459","en","_BUTTON_LOGIN","Login");
INSERT INTO aphs_vocabulary VALUES("462","en","_BUTTON_LOGOUT","Logout");
INSERT INTO aphs_vocabulary VALUES("465","en","_BUTTON_RESET","Reset");
INSERT INTO aphs_vocabulary VALUES("468","en","_BUTTON_REWRITE","Rewrite Vocabulary");
INSERT INTO aphs_vocabulary VALUES("471","en","_BUTTON_SAVE_CHANGES","Save Changes");
INSERT INTO aphs_vocabulary VALUES("474","en","_BUTTON_UPDATE","Update");
INSERT INTO aphs_vocabulary VALUES("477","en","_CACHE_LIFETIME","Cache Lifetime");
INSERT INTO aphs_vocabulary VALUES("480","en","_CACHING","Caching");
INSERT INTO aphs_vocabulary VALUES("483","en","_CAMPAIGNS","Campaigns");
INSERT INTO aphs_vocabulary VALUES("486","en","_CAMPAIGNS_MANAGEMENT","Campaigns Management");
INSERT INTO aphs_vocabulary VALUES("489","en","_CAMPAIGNS_TOOLTIP","Global - allows booking for any date and runs (visible) within a defined period of time only\n\nTargeted - allows booking in a specified period of time only and runs (visible) till the first date is beginning");
INSERT INTO aphs_vocabulary VALUES("492","en","_CANCELED","Canceled");
INSERT INTO aphs_vocabulary VALUES("495","en","_CANCELED_BY_ADMIN","This booking was canceled by administrator.");
INSERT INTO aphs_vocabulary VALUES("498","en","_CANCELED_BY_CUSTOMER","This booking was canceled by customer.");
INSERT INTO aphs_vocabulary VALUES("501","en","_CAN_USE_TAGS_MSG","You can use some HTML tags, such as");
INSERT INTO aphs_vocabulary VALUES("504","en","_CAPACITY","Capacity");
INSERT INTO aphs_vocabulary VALUES("507","en","_CART_WAS_UPDATED","Reservation cart was successfully updated!");
INSERT INTO aphs_vocabulary VALUES("510","en","_CATEGORIES","Categories");
INSERT INTO aphs_vocabulary VALUES("513","en","_CATEGORIES_MANAGEMENT","Categories Management");
INSERT INTO aphs_vocabulary VALUES("516","en","_CATEGORY","Category");
INSERT INTO aphs_vocabulary VALUES("519","en","_CATEGORY_DESCRIPTION","Category Description");
INSERT INTO aphs_vocabulary VALUES("522","en","_CC_CARD_HOLDER_NAME_EMPTY","No card holder\'s name provided! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("525","en","_CC_CARD_INVALID_FORMAT","Credit card number has invalid format! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("528","en","_CC_CARD_INVALID_NUMBER","Credit card number is invalid! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("531","en","_CC_CARD_NO_CVV_NUMBER","No CVV Code provided! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("534","en","_CC_CARD_WRONG_EXPIRE_DATE","Credit card expiry date is wrong! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("537","en","_CC_CARD_WRONG_LENGTH","Credit card number has a wrong length! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("540","en","_CC_NO_CARD_NUMBER_PROVIDED","No card number provided! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("543","en","_CC_NUMBER_INVALID","Credit card number is invalid! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("546","en","_CC_UNKNOWN_CARD_TYPE","Unknown card type! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("549","en","_CHANGES_SAVED","Changes were saved.");
INSERT INTO aphs_vocabulary VALUES("552","en","_CHANGES_WERE_SAVED","Changes were successfully saved! Please refresh the <a href=index.php>Home Page</a> to see the results.");
INSERT INTO aphs_vocabulary VALUES("555","en","_CHANGE_CUSTOMER","Change Customer");
INSERT INTO aphs_vocabulary VALUES("558","en","_CHANGE_ORDER","Change Order");
INSERT INTO aphs_vocabulary VALUES("561","en","_CHANGE_YOUR_PASSWORD","Change your password");
INSERT INTO aphs_vocabulary VALUES("564","en","_CHARGE_TYPE","Charge Type");
INSERT INTO aphs_vocabulary VALUES("567","en","_CHECKOUT","Checkout");
INSERT INTO aphs_vocabulary VALUES("570","en","_CHECK_AVAILABILITY","Check Availability");
INSERT INTO aphs_vocabulary VALUES("573","en","_CHECK_IN","Check In");
INSERT INTO aphs_vocabulary VALUES("576","en","_CHECK_NOW","Check Now");
INSERT INTO aphs_vocabulary VALUES("579","en","_CHECK_OUT","Check Out");
INSERT INTO aphs_vocabulary VALUES("582","en","_CHECK_STATUS","Check Status");
INSERT INTO aphs_vocabulary VALUES("585","en","_CHILD","Child");
INSERT INTO aphs_vocabulary VALUES("588","en","_CHILDREN","Children");
INSERT INTO aphs_vocabulary VALUES("591","en","_CITY","City");
INSERT INTO aphs_vocabulary VALUES("594","en","_CITY_EMPTY_ALERT","City cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("597","en","_CLEANED","Cleaned");
INSERT INTO aphs_vocabulary VALUES("600","en","_CLEANUP","Cleanup");
INSERT INTO aphs_vocabulary VALUES("603","en","_CLEANUP_TOOLTIP","The cleanup feature is used to remove pending (temporary) reservations from your web site. A pending reservation is one where the system is waiting for the payment gateway to callback with the transaction status.");
INSERT INTO aphs_vocabulary VALUES("606","en","_CLEAN_CACHE","Clean Cache");
INSERT INTO aphs_vocabulary VALUES("609","en","_CLICK_FOR_MORE_INFO","Click for more information");
INSERT INTO aphs_vocabulary VALUES("612","en","_CLICK_TO_EDIT","Click to edit");
INSERT INTO aphs_vocabulary VALUES("615","en","_CLICK_TO_INCREASE","Click to enlarge");
INSERT INTO aphs_vocabulary VALUES("618","en","_CLICK_TO_MANAGE","Click to manage");
INSERT INTO aphs_vocabulary VALUES("621","en","_CLICK_TO_SEE_DESCR","Click to see description");
INSERT INTO aphs_vocabulary VALUES("624","en","_CLICK_TO_SEE_PRICES","Click to see prices");
INSERT INTO aphs_vocabulary VALUES("627","en","_CLICK_TO_VIEW","Click to view");
INSERT INTO aphs_vocabulary VALUES("630","en","_CLOSE","Close");
INSERT INTO aphs_vocabulary VALUES("633","en","_CLOSE_META_TAGS","Close META tags");
INSERT INTO aphs_vocabulary VALUES("636","en","_CODE","Code");
INSERT INTO aphs_vocabulary VALUES("639","en","_COLLAPSE_PANEL","Collapse navigation panel");
INSERT INTO aphs_vocabulary VALUES("642","en","_COMMENTS","Comments");
INSERT INTO aphs_vocabulary VALUES("645","en","_COMMENTS_AWAITING_MODERATION_ALERT","There are _COUNT_ comment/s awaiting your moderation. Click <a href=\'index.php?admin=mod_comments_management\'>here</a> for review.");
INSERT INTO aphs_vocabulary VALUES("648","en","_COMMENTS_LINK","Comments (_COUNT_)");
INSERT INTO aphs_vocabulary VALUES("651","en","_COMMENTS_MANAGEMENT","Comments Management");
INSERT INTO aphs_vocabulary VALUES("654","en","_COMMENTS_SETTINGS","Comments Settings");
INSERT INTO aphs_vocabulary VALUES("657","en","_COMMENT_DELETED_SUCCESS","Your comment was successfully deleted.");
INSERT INTO aphs_vocabulary VALUES("660","en","_COMMENT_LENGTH_ALERT","The length of comment must be less than _LENGTH_ characters!");
INSERT INTO aphs_vocabulary VALUES("663","en","_COMMENT_POSTED_SUCCESS","Your comment has been successfully posted!");
INSERT INTO aphs_vocabulary VALUES("666","en","_COMMENT_SUBMITTED_SUCCESS","Your comment has been successfully submitted and will be posted after administrator\'s review!");
INSERT INTO aphs_vocabulary VALUES("669","en","_COMMENT_TEXT","Comment text");
INSERT INTO aphs_vocabulary VALUES("672","en","_COMPANY","Company");
INSERT INTO aphs_vocabulary VALUES("675","en","_COMPLETED","Completed");
INSERT INTO aphs_vocabulary VALUES("678","en","_CONFIRMATION","Confirmation");
INSERT INTO aphs_vocabulary VALUES("681","en","_CONFIRMATION_CODE","Confirmation Code");
INSERT INTO aphs_vocabulary VALUES("684","en","_CONFIRMED_ALREADY_MSG","Your account has already been confirmed! <br /><br />Click <a href=\'index.php?customer=login\'>here</a> to continue.");
INSERT INTO aphs_vocabulary VALUES("687","en","_CONFIRMED_SUCCESS_MSG","Thank you for confirming your registration! <br /><br />You may now log into your account. Click <a href=\'index.php?customer=login\'>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("690","en","_CONFIRM_PASSWORD","Confirm Password");
INSERT INTO aphs_vocabulary VALUES("693","en","_CONFIRM_TERMS_CONDITIONS","You must confirm you agree to our Terms & Conditions!");
INSERT INTO aphs_vocabulary VALUES("696","en","_CONF_PASSWORD_IS_EMPTY","Confirm Password cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("699","en","_CONF_PASSWORD_MATCH","Password must be match with Confirm Password");
INSERT INTO aphs_vocabulary VALUES("702","en","_CONTACTUS_DEFAULT_EMAIL_ALERT","You have to change default email address for Contact Us module. Click <a href=\'index.php?admin=mod_contact_us_settings\'>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("705","en","_CONTACT_INFORMATION","Contact Information");
INSERT INTO aphs_vocabulary VALUES("708","en","_CONTACT_US","Contact us");
INSERT INTO aphs_vocabulary VALUES("711","en","_CONTACT_US_ALREADY_SENT","Your message was already sent. Please try again later or wait _WAIT_ seconds.");
INSERT INTO aphs_vocabulary VALUES("714","en","_CONTACT_US_EMAIL_SENT","Thank you for contacting us! Your message has been successfully sent.");
INSERT INTO aphs_vocabulary VALUES("717","en","_CONTACT_US_SETTINGS","Contact Us Settings");
INSERT INTO aphs_vocabulary VALUES("720","en","_CONTENT_TYPE","Content Type");
INSERT INTO aphs_vocabulary VALUES("723","en","_CONTINUE_RESERVATION","Continue Reservation");
INSERT INTO aphs_vocabulary VALUES("726","en","_COPY_TO_OTHER_LANGS","Copy to other languages");
INSERT INTO aphs_vocabulary VALUES("729","en","_COUNT","Count");
INSERT INTO aphs_vocabulary VALUES("732","en","_COUNTRIES","Countries");
INSERT INTO aphs_vocabulary VALUES("735","en","_COUNTRIES_MANAGEMENT","Countries Management");
INSERT INTO aphs_vocabulary VALUES("738","en","_COUNTRY","Country");
INSERT INTO aphs_vocabulary VALUES("741","en","_COUNTRY_EMPTY_ALERT","Country cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("744","en","_COUPONS","Coupons");
INSERT INTO aphs_vocabulary VALUES("747","en","_COUPONS_MANAGEMENT","Coupons Management");
INSERT INTO aphs_vocabulary VALUES("750","en","_COUPON_CODE","Coupon Code");
INSERT INTO aphs_vocabulary VALUES("753","en","_COUPON_WAS_APPLIED","The coupon _COUPON_CODE_ has been successfully applied!");
INSERT INTO aphs_vocabulary VALUES("756","en","_COUPON_WAS_REMOVED","The coupon has been successfully removed!");
INSERT INTO aphs_vocabulary VALUES("759","en","_CREATED_DATE","Date Created");
INSERT INTO aphs_vocabulary VALUES("762","en","_CREATE_ACCOUNT","Create account");
INSERT INTO aphs_vocabulary VALUES("765","en","_CREATE_ACCOUNT_NOTE","NOTE: <br>We recommend that your password should be at least 6 characters long and should be different from your username.<br><br>Your e-mail address must be valid. We use e-mail for communication purposes (order notifications, etc). Therefore, it is essential to provide a valid e-mail address to be able to use our services correctly.<br><br>All your private data is confidential. We will never sell, exchange or market it in any way. For further information on the responsibilities of both parts, you may refer to us.");
INSERT INTO aphs_vocabulary VALUES("768","en","_CREATING_ACCOUNT_ERROR","An error occurred while creating your account! Please try again later or send information about this error to administration of the site.");
INSERT INTO aphs_vocabulary VALUES("771","en","_CREATING_NEW_ACCOUNT","Creating new account");
INSERT INTO aphs_vocabulary VALUES("774","en","_CREDIT_CARD","Credit Card");
INSERT INTO aphs_vocabulary VALUES("777","en","_CREDIT_CARD_EXPIRES","Expires");
INSERT INTO aphs_vocabulary VALUES("780","en","_CREDIT_CARD_HOLDER_NAME","Card Holder\'s Name");
INSERT INTO aphs_vocabulary VALUES("783","en","_CREDIT_CARD_NUMBER","Credit Card Number");
INSERT INTO aphs_vocabulary VALUES("786","en","_CREDIT_CARD_TYPE","Credit Card Type");
INSERT INTO aphs_vocabulary VALUES("789","en","_CRONJOB_HTACCESS_BLOCK","To block remote access to cron.php, in the server&#039;s .htaccess file or vhost configuration file add this section:");
INSERT INTO aphs_vocabulary VALUES("792","en","_CRONJOB_NOTICE","Cron jobs allow you to automate certain commands or scripts on your site.<br /><br />ApPHP Hotel Site needs to periodically run cron.php to close expired discount campaigns or perform another importans operations. The recommended way to run cron.php is to set up a cronjob if you run a Unix/Linux server. If for any reason you can&#039;t run a cronjob on your server, you can choose the Non-batch option below to have cron.php run by ApPHP Hotel Site itself: in this case cron.php will be run each time someone access your home page. <br /><br />Example of Batch Cron job command: <b>php &#36;HOME/public_html/cron.php >/dev/null 2>&1</b>");
INSERT INTO aphs_vocabulary VALUES("795","en","_CRON_JOBS","Cron Jobs");
INSERT INTO aphs_vocabulary VALUES("798","en","_CURRENCIES","Currencies");
INSERT INTO aphs_vocabulary VALUES("801","en","_CURRENCIES_DEFAULT_ALERT","Remember! After you change the default currency:<br>- Edit exchange rate to each currency manually (relatively to the new default currency)<br>- Redefine prices for all rooms in the new currency.");
INSERT INTO aphs_vocabulary VALUES("804","en","_CURRENCIES_MANAGEMENT","Currencies Management");
INSERT INTO aphs_vocabulary VALUES("807","en","_CURRENCY","Currency");
INSERT INTO aphs_vocabulary VALUES("810","en","_CURRENT_NEXT_YEARS","for current/next years");
INSERT INTO aphs_vocabulary VALUES("813","en","_CUSTOMER","Customer");
INSERT INTO aphs_vocabulary VALUES("816","en","_CUSTOMERS","Customers");
INSERT INTO aphs_vocabulary VALUES("819","en","_CUSTOMERS_AWAITING_MODERATION_ALERT","There are _COUNT_ customer/s awaiting your approval. Click <a href=\'index.php?admin=mod_customers_management\'>here</a> for review.");
INSERT INTO aphs_vocabulary VALUES("822","en","_CUSTOMERS_MANAGEMENT","Customers Management");
INSERT INTO aphs_vocabulary VALUES("825","en","_CUSTOMERS_SETTINGS","Customers Settings");
INSERT INTO aphs_vocabulary VALUES("828","en","_CUSTOMER_DETAILS","Customer Details");
INSERT INTO aphs_vocabulary VALUES("831","en","_CUSTOMER_GROUP","Customer Group");
INSERT INTO aphs_vocabulary VALUES("834","en","_CUSTOMER_GROUPS","Customer Groups");
INSERT INTO aphs_vocabulary VALUES("837","en","_CUSTOMER_LOGIN","Customer Login");
INSERT INTO aphs_vocabulary VALUES("840","en","_CUSTOMER_NAME","Customer Name");
INSERT INTO aphs_vocabulary VALUES("843","en","_CUSTOMER_PANEL","Customer Panel");
INSERT INTO aphs_vocabulary VALUES("846","en","_CUSTOMER_PAYMENT_MODULES","Customer & Payment Modules");
INSERT INTO aphs_vocabulary VALUES("849","en","_CVV_CODE","CVV Code");
INSERT INTO aphs_vocabulary VALUES("852","en","_DASHBOARD","Dashboard");
INSERT INTO aphs_vocabulary VALUES("855","en","_DATE","Date");
INSERT INTO aphs_vocabulary VALUES("858","en","_DATETIME_PRICE_FORMAT","Datetime & Price Settings");
INSERT INTO aphs_vocabulary VALUES("861","en","_DATE_AND_TIME_SETTINGS","Date & Time Settings");
INSERT INTO aphs_vocabulary VALUES("864","en","_DATE_CREATED","Date Created");
INSERT INTO aphs_vocabulary VALUES("867","en","_DATE_EMPTY_ALERT","Date fields cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("870","en","_DATE_FORMAT","Date Format");
INSERT INTO aphs_vocabulary VALUES("873","en","_DATE_MODIFIED","Date Modified");
INSERT INTO aphs_vocabulary VALUES("876","en","_DATE_PAYMENT","Date of Payment");
INSERT INTO aphs_vocabulary VALUES("879","en","_DATE_PUBLISHED","Date Published");
INSERT INTO aphs_vocabulary VALUES("882","en","_DATE_SUBSCRIBED","Date Subscribed");
INSERT INTO aphs_vocabulary VALUES("885","en","_DAY","Day");
INSERT INTO aphs_vocabulary VALUES("888","en","_DECEMBER","December");
INSERT INTO aphs_vocabulary VALUES("891","en","_DEFAULT","Default");
INSERT INTO aphs_vocabulary VALUES("894","en","_DEFAULT_AVAILABILITY","Default Availability");
INSERT INTO aphs_vocabulary VALUES("897","en","_DEFAULT_CURRENCY_DELETE_ALERT","You cannot delete default currency!");
INSERT INTO aphs_vocabulary VALUES("900","en","_DEFAULT_EMAIL_ALERT","You have to change default email address for site administrator. Click <a href=\'index.php?admin=settings&tabid=1_4\'>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("903","en","_DEFAULT_HOTEL_DELETE_ALERT","You cannot delete default hotel!");
INSERT INTO aphs_vocabulary VALUES("906","en","_DEFAULT_OWN_EMAIL_ALERT","You have to change your own email address. Click <a href=\'index.php?admin=my_account\'>here</a> to proceed.");
INSERT INTO aphs_vocabulary VALUES("909","en","_DEFAULT_PRICE","Default Price");
INSERT INTO aphs_vocabulary VALUES("912","en","_DEFAULT_TEMPLATE","Default Template");
INSERT INTO aphs_vocabulary VALUES("915","en","_DELETE_WARNING","Are you sure you want to delete this record?");
INSERT INTO aphs_vocabulary VALUES("918","en","_DELETE_WARNING_COMMON","Are you sure you want to delete this record?");
INSERT INTO aphs_vocabulary VALUES("921","en","_DELETE_WORD","Delete");
INSERT INTO aphs_vocabulary VALUES("924","en","_DELETING_ACCOUNT_ERROR","An error occurred while deleting your account! Please try again later or send email about this issue to administration of the site.");
INSERT INTO aphs_vocabulary VALUES("927","en","_DELETING_OPERATION_COMPLETED","Deleting operation was successfully completed!");
INSERT INTO aphs_vocabulary VALUES("930","en","_DESCRIPTION","Description");
INSERT INTO aphs_vocabulary VALUES("933","en","_DISCOUNT","Discount");
INSERT INTO aphs_vocabulary VALUES("936","en","_DISCOUNT_BY_ADMIN","Discount By Administrator");
INSERT INTO aphs_vocabulary VALUES("939","en","_DISCOUNT_CAMPAIGN","Discount Campaign");
INSERT INTO aphs_vocabulary VALUES("942","en","_DISCOUNT_CAMPAIGNS","Discount Campaigns");
INSERT INTO aphs_vocabulary VALUES("945","en","_DISCOUNT_CAMPAIGN_TEXT","<span class=\'campaign_header\'>Super discount campaign!</span><br /><br />\nEnjoy special price cuts <br />_FROM_ _TO_:<br /> \n<b>_PERCENT_</b> on every room reservation in our Hotel!");
INSERT INTO aphs_vocabulary VALUES("948","en","_DISCOUNT_STD_CAMPAIGN_TEXT","Super discount campaign!<br><br>Enjoy special price cuts in our Hotel at the specified periods of time below!");
INSERT INTO aphs_vocabulary VALUES("951","en","_DISPLAY_ON","Display on");
INSERT INTO aphs_vocabulary VALUES("954","en","_DOWN","Down");
INSERT INTO aphs_vocabulary VALUES("957","en","_DOWNLOAD","Download");
INSERT INTO aphs_vocabulary VALUES("960","en","_DOWNLOAD_INVOICE","Download Invoice");
INSERT INTO aphs_vocabulary VALUES("963","en","_ECHECK","E-Check");
INSERT INTO aphs_vocabulary VALUES("966","en","_EDIT_MENUS","Edit Menus");
INSERT INTO aphs_vocabulary VALUES("969","en","_EDIT_MY_ACCOUNT","Edit My Account");
INSERT INTO aphs_vocabulary VALUES("972","en","_EDIT_PAGE","Edit Page");
INSERT INTO aphs_vocabulary VALUES("975","en","_EDIT_WORD","Edit");
INSERT INTO aphs_vocabulary VALUES("978","en","_EMAIL","Email");
INSERT INTO aphs_vocabulary VALUES("981","en","_EMAILS_SENT_ERROR","An error occurred while sending emails or there are no emails to be sent! Please try again later.");
INSERT INTO aphs_vocabulary VALUES("984","en","_EMAILS_SUCCESSFULLY_SENT","Status: _SENT_ emails from _TOTAL_ were successfully sent!");
INSERT INTO aphs_vocabulary VALUES("987","en","_EMAIL_ADDRESS","E-mail address");
INSERT INTO aphs_vocabulary VALUES("990","en","_EMAIL_BLOCKED","Your email was blocked! To resolve this problem, please contact the site administrator.");
INSERT INTO aphs_vocabulary VALUES("993","en","_EMAIL_EMPTY_ALERT","Email cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("996","en","_EMAIL_FROM","Email Address (From)");
INSERT INTO aphs_vocabulary VALUES("999","en","_EMAIL_IS_EMPTY","Email must not be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1002","en","_EMAIL_IS_WRONG","Please enter a valid email address.");
INSERT INTO aphs_vocabulary VALUES("1005","en","_EMAIL_NOTIFICATIONS","Send email notifications");
INSERT INTO aphs_vocabulary VALUES("1008","en","_EMAIL_NOT_EXISTS","This e-mail account does not exist in the system! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1011","en","_EMAIL_SEND_ERROR","An error occurred while sending email. Please check your email settings and message recipients, then try again.");
INSERT INTO aphs_vocabulary VALUES("1014","en","_EMAIL_SETTINGS","Email Settings");
INSERT INTO aphs_vocabulary VALUES("1017","en","_EMAIL_SUCCESSFULLY_SENT","Email was successfully sent!");
INSERT INTO aphs_vocabulary VALUES("1020","en","_EMAIL_TEMPLATES","Email Templates");
INSERT INTO aphs_vocabulary VALUES("1023","en","_EMAIL_TEMPLATES_EDITOR","Email Templates Editor");
INSERT INTO aphs_vocabulary VALUES("1026","en","_EMAIL_TO","Email Address (To)");
INSERT INTO aphs_vocabulary VALUES("1029","en","_EMAIL_VALID_ALERT","Please enter a valid email address!");
INSERT INTO aphs_vocabulary VALUES("1032","en","_EMPTY","Empty");
INSERT INTO aphs_vocabulary VALUES("1035","en","_ENTER_BOOKING_NUMBER","Enter Your Booking Number");
INSERT INTO aphs_vocabulary VALUES("1038","en","_ENTER_CONFIRMATION_CODE","Enter Confirmation Code");
INSERT INTO aphs_vocabulary VALUES("1041","en","_ENTER_EMAIL_ADDRESS","(Please enter ONLY real email address)");
INSERT INTO aphs_vocabulary VALUES("1044","en","_ENTIRE_SITE","Entire Site");
INSERT INTO aphs_vocabulary VALUES("1047","en","_EVENTS","Events");
INSERT INTO aphs_vocabulary VALUES("1050","en","_EVENT_REGISTRATION_COMPLETED","Thank you for your interest! You have just successfully registered to this event.");
INSERT INTO aphs_vocabulary VALUES("1053","en","_EVENT_USER_ALREADY_REGISTERED","Member with such email was already registered to this event! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1056","en","_EXPAND_PANEL","Expand navigation panel");
INSERT INTO aphs_vocabulary VALUES("1059","en","_EXPIRED","Expired");
INSERT INTO aphs_vocabulary VALUES("1062","en","_EXPORT","Export");
INSERT INTO aphs_vocabulary VALUES("1065","en","_EXTRAS","Extras");
INSERT INTO aphs_vocabulary VALUES("1068","en","_EXTRAS_MANAGEMENT","Extras Management");
INSERT INTO aphs_vocabulary VALUES("1071","en","_EXTRAS_SUBTOTAL","Extras Subtotal");
INSERT INTO aphs_vocabulary VALUES("1074","en","_FACILITIES","Facilities");
INSERT INTO aphs_vocabulary VALUES("1077","en","_FAQ","FAQ");
INSERT INTO aphs_vocabulary VALUES("1080","en","_FAQ_MANAGEMENT","FAQ Management");
INSERT INTO aphs_vocabulary VALUES("1083","en","_FAQ_SETTINGS","FAQ Settings");
INSERT INTO aphs_vocabulary VALUES("1086","en","_FAX","Fax");
INSERT INTO aphs_vocabulary VALUES("1089","en","_FEBRUARY","February");
INSERT INTO aphs_vocabulary VALUES("1092","en","_FIELD_CANNOT_BE_EMPTY","Field _FIELD_ cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1095","en","_FIELD_LENGTH_ALERT","The length of the field _FIELD_ must be less than _LENGTH_ characters! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1098","en","_FIELD_LENGTH_EXCEEDED","_FIELD_ has exceeded the maximum allowed size: _LENGTH_ characters! Please re-enter. ");
INSERT INTO aphs_vocabulary VALUES("1101","en","_FIELD_MIN_LENGTH_ALERT","The length of the field _FIELD_ cannot  be less than _LENGTH_ characters! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1104","en","_FIELD_MUST_BE_ALPHA","_FIELD_ must be an alphabetic value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1107","en","_FIELD_MUST_BE_ALPHA_NUMERIC","_FIELD_ must be an alphanumeric value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1110","en","_FIELD_MUST_BE_BOOLEAN","Field _FIELD_ value must be \'yes\' or \'no\'! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1113","en","_FIELD_MUST_BE_EMAIL","_FIELD_ must be in valid email format! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1116","en","_FIELD_MUST_BE_FLOAT","Field _FIELD_ must be a float number value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1119","en","_FIELD_MUST_BE_FLOAT_POSITIVE","Field _FIELD_ must be a positive float number value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1123","en","_FIELD_MUST_BE_IP_ADDRESS","_FIELD_ must be a valid IP Address! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1126","en","_FIELD_MUST_BE_NUMERIC","Field _FIELD_ must be a numeric value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1129","en","_FIELD_MUST_BE_NUMERIC_POSITIVE","Field _FIELD_ must be a positive numeric value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1132","en","_FIELD_MUST_BE_PASSWORD","_FIELD_ must be 6 characters at least and consist of letters and digits! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1135","en","_FIELD_MUST_BE_POSITIVE_INT","Field _FIELD_ must be a positive integer value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1138","en","_FIELD_MUST_BE_POSITIVE_INTEGER","Field _FIELD_ must be a positive integer number!");
INSERT INTO aphs_vocabulary VALUES("1140","en","_FIELD_MUST_BE_SIZE_VALUE","Field _FIELD_ must be a valid HTML size property in \'px\', \'pt\', \'em\' or \'%\' units! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1143","en","_FIELD_MUST_BE_TEXT","_FIELD_ value must be a text! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1146","en","_FIELD_MUST_BE_UNSIGNED_FLOAT","Field _FIELD_ must be an unsigned float value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1149","en","_FIELD_MUST_BE_UNSIGNED_INT","Field _FIELD_ must be an unsigned integer value! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1152","en","_FIELD_VALUE_EXCEEDED","_FIELD_ has exceeded the maximum allowed value _MAX_! Please re-enter. ");
INSERT INTO aphs_vocabulary VALUES("1155","en","_FIELD_VALUE_MINIMUM","_FIELD_ value should not be less then _MIN_! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1158","en","_FILED_UNIQUE_VALUE_ALERT","The field _FIELD_ accepts only unique values - please re-enter!");
INSERT INTO aphs_vocabulary VALUES("1161","en","_FILE_DELETING_ERROR","An error occurred while deleting file! Please try again later.");
INSERT INTO aphs_vocabulary VALUES("1164","en","_FILTER_BY","Filter by");
INSERT INTO aphs_vocabulary VALUES("1167","en","_FINISH_DATE","Finish Date");
INSERT INTO aphs_vocabulary VALUES("1170","en","_FINISH_PUBLISHING","Finish Publishing");
INSERT INTO aphs_vocabulary VALUES("1173","en","_FIRST_NAME","First Name");
INSERT INTO aphs_vocabulary VALUES("1176","en","_FIRST_NAME_EMPTY_ALERT","First Name cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1179","en","_FIRST_NIGHT","First Night");
INSERT INTO aphs_vocabulary VALUES("1182","en","_FIXED_SUM","Fixed Sum");
INSERT INTO aphs_vocabulary VALUES("1185","en","_FOOTER_IS_EMPTY","Footer cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1188","en","_FORCE_SSL","Force SSL");
INSERT INTO aphs_vocabulary VALUES("1191","en","_FORCE_SSL_ALERT","Force site access to always occur under SSL (https) for selected areas. You or site visitors will not be able to access selected areas under non-ssl. Note, you must have SSL enabled on your server to make this option works.");
INSERT INTO aphs_vocabulary VALUES("1194","en","_FORGOT_PASSWORD","Forgot your password?");
INSERT INTO aphs_vocabulary VALUES("1197","en","_FORM","Form");
INSERT INTO aphs_vocabulary VALUES("1200","en","_FOUND_HOTELS","Found Hotels");
INSERT INTO aphs_vocabulary VALUES("1203","en","_FOUND_ROOMS","Found Rooms");
INSERT INTO aphs_vocabulary VALUES("1206","en","_FR","Fr");
INSERT INTO aphs_vocabulary VALUES("1209","en","_FRI","Fri");
INSERT INTO aphs_vocabulary VALUES("1212","en","_FRIDAY","Friday");
INSERT INTO aphs_vocabulary VALUES("1215","en","_FROM","From");
INSERT INTO aphs_vocabulary VALUES("1218","en","_FROM_TO_DATE_ALERT","Date \'To\' must be the same or later than date \'From\'! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1221","en","_FULLY_BOOKED","fully booked/unavailable");
INSERT INTO aphs_vocabulary VALUES("1224","en","_FULL_PRICE","Full Price");
INSERT INTO aphs_vocabulary VALUES("1227","en","_GALLERY","Gallery");
INSERT INTO aphs_vocabulary VALUES("1230","en","_GALLERY_MANAGEMENT","Gallery Management");
INSERT INTO aphs_vocabulary VALUES("1233","en","_GALLERY_SETTINGS","Gallery Settings");
INSERT INTO aphs_vocabulary VALUES("1236","en","_GENERAL","General");
INSERT INTO aphs_vocabulary VALUES("1239","en","_GENERAL_INFO","General Info");
INSERT INTO aphs_vocabulary VALUES("1242","en","_GENERAL_SETTINGS","General Settings");
INSERT INTO aphs_vocabulary VALUES("1245","en","_GENERATE","Generate");
INSERT INTO aphs_vocabulary VALUES("1248","en","_GLOBAL","Global");
INSERT INTO aphs_vocabulary VALUES("1251","en","_GLOBAL_CAMPAIGN","Global Campaign");
INSERT INTO aphs_vocabulary VALUES("1254","en","_GROUP","Group");
INSERT INTO aphs_vocabulary VALUES("1257","en","_GROUP_NAME","Group Name");
INSERT INTO aphs_vocabulary VALUES("1260","en","_GROUP_TIME_OVERLAPPING_ALERT","This period of time (fully or partially) was already chosen for selected group! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1263","en","_GUEST","Guest");
INSERT INTO aphs_vocabulary VALUES("1266","en","_GUESTS","Guests");
INSERT INTO aphs_vocabulary VALUES("1269","en","_GUESTS_FEE","Guests Fee");
INSERT INTO aphs_vocabulary VALUES("1272","en","_GUEST_FEE","Guest Fee");
INSERT INTO aphs_vocabulary VALUES("1275","en","_HDR_FOOTER_TEXT","Footer Text");
INSERT INTO aphs_vocabulary VALUES("1278","en","_HDR_HEADER_TEXT","Header Text");
INSERT INTO aphs_vocabulary VALUES("1281","en","_HDR_SLOGAN_TEXT","Slogan");
INSERT INTO aphs_vocabulary VALUES("1284","en","_HDR_TEMPLATE","Template");
INSERT INTO aphs_vocabulary VALUES("1287","en","_HDR_TEXT_DIRECTION","Text Direction");
INSERT INTO aphs_vocabulary VALUES("1290","en","_HEADER","Header");
INSERT INTO aphs_vocabulary VALUES("1293","en","_HEADERS_AND_FOOTERS","Headers & Footers");
INSERT INTO aphs_vocabulary VALUES("1296","en","_HEADER_IS_EMPTY","Header cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1299","en","_HIDDEN","Hidden");
INSERT INTO aphs_vocabulary VALUES("1302","en","_HIDE","Hide");
INSERT INTO aphs_vocabulary VALUES("1305","en","_HOME","Home");
INSERT INTO aphs_vocabulary VALUES("1308","en","_HOTEL","Hotel");
INSERT INTO aphs_vocabulary VALUES("1311","en","_HOTELOWNER_WELCOME_TEXT","Welcome to Hotel Owner Control Panel! With this Control Panel you can easily manage your hotels, customers, reservations and perform a full hotel site management.");
INSERT INTO aphs_vocabulary VALUES("1314","en","_HOTELS","Hotels");
INSERT INTO aphs_vocabulary VALUES("1317","en","_HOTELS_AND_ROMS","Hotels and Rooms");
INSERT INTO aphs_vocabulary VALUES("1320","en","_HOTELS_INFO","Hotels Info");
INSERT INTO aphs_vocabulary VALUES("1323","en","_HOTELS_MANAGEMENT","Hotels Management");
INSERT INTO aphs_vocabulary VALUES("1326","en","_HOTEL_DESCRIPTION","Hotel Description");
INSERT INTO aphs_vocabulary VALUES("1329","en","_HOTEL_INFO","Hotel Info");
INSERT INTO aphs_vocabulary VALUES("1332","en","_HOTEL_MANAGEMENT","Hotel Management");
INSERT INTO aphs_vocabulary VALUES("1335","en","_HOTEL_OWNER","Hotel Owner");
INSERT INTO aphs_vocabulary VALUES("1338","en","_HOTEL_RESERVATION_ID","Hotel Reservation ID");
INSERT INTO aphs_vocabulary VALUES("1341","en","_HOUR","Hour");
INSERT INTO aphs_vocabulary VALUES("1344","en","_HOURS","hours");
INSERT INTO aphs_vocabulary VALUES("1347","en","_ICON_IMAGE","Icon image");
INSERT INTO aphs_vocabulary VALUES("1350","en","_IMAGE","Image");
INSERT INTO aphs_vocabulary VALUES("1353","en","_IMAGES","Images");
INSERT INTO aphs_vocabulary VALUES("1356","en","_IMAGE_VERIFICATION","Image verification");
INSERT INTO aphs_vocabulary VALUES("1359","en","_IMAGE_VERIFY_EMPTY","You must enter image verification code!");
INSERT INTO aphs_vocabulary VALUES("1362","en","_INCOME","Income");
INSERT INTO aphs_vocabulary VALUES("1365","en","_INFO_AND_STATISTICS","Information and Statistics");
INSERT INTO aphs_vocabulary VALUES("1368","en","_INITIAL_FEE","Initial Fee");
INSERT INTO aphs_vocabulary VALUES("1371","en","_INSTALL","Install");
INSERT INTO aphs_vocabulary VALUES("1374","en","_INSTALLED","Installed");
INSERT INTO aphs_vocabulary VALUES("1377","en","_INSTALL_PHP_EXISTS","File <b>install.php</b> and/or directory <b>install/</b> still exists. For security reasons please remove them immediately!");
INSERT INTO aphs_vocabulary VALUES("1380","en","_INTEGRATION","Integration");
INSERT INTO aphs_vocabulary VALUES("1383","en","_INTEGRATION_MESSAGE","Copy the code below and put it in the appropriate place of your web site to get a <b>Search Availability</b> block.");
INSERT INTO aphs_vocabulary VALUES("1386","en","_INTERNAL_USE_TOOLTIP","For internal use only");
INSERT INTO aphs_vocabulary VALUES("1389","en","_INVALID_FILE_SIZE","Invalid file size: _FILE_SIZE_ (max. allowed: _MAX_ALLOWED_)");
INSERT INTO aphs_vocabulary VALUES("1392","en","_INVALID_IMAGE_FILE_TYPE","Uploaded file is not a valid image! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1395","en","_INVOICE","Invoice");
INSERT INTO aphs_vocabulary VALUES("1398","en","_INVOICE_SENT_SUCCESS","The invoice was successfully sent to the customer!");
INSERT INTO aphs_vocabulary VALUES("1401","en","_IN_PRODUCTS","In Products");
INSERT INTO aphs_vocabulary VALUES("1404","en","_IP_ADDRESS","IP Address");
INSERT INTO aphs_vocabulary VALUES("1407","en","_IP_ADDRESS_BLOCKED","Your IP Address is blocked! To resolve this problem, please contact the site administrator.");
INSERT INTO aphs_vocabulary VALUES("1410","en","_IS_DEFAULT","Is default");
INSERT INTO aphs_vocabulary VALUES("1413","en","_ITEMS","Items");
INSERT INTO aphs_vocabulary VALUES("1416","en","_ITEMS_LC","items");
INSERT INTO aphs_vocabulary VALUES("1419","en","_ITEM_NAME","Item Name");
INSERT INTO aphs_vocabulary VALUES("1422","en","_JANUARY","January");
INSERT INTO aphs_vocabulary VALUES("1425","en","_JULY","July");
INSERT INTO aphs_vocabulary VALUES("1428","en","_JUNE","June");
INSERT INTO aphs_vocabulary VALUES("1431","en","_KEY","Key");
INSERT INTO aphs_vocabulary VALUES("1434","en","_KEYWORDS","Keywords");
INSERT INTO aphs_vocabulary VALUES("1437","en","_KEY_DISPLAY_TYPE","Key display type");
INSERT INTO aphs_vocabulary VALUES("1440","en","_LANGUAGE","Language");
INSERT INTO aphs_vocabulary VALUES("1443","en","_LANGUAGES","Languages");
INSERT INTO aphs_vocabulary VALUES("1446","en","_LANGUAGES_SETTINGS","Languages Settings");
INSERT INTO aphs_vocabulary VALUES("1449","en","_LANGUAGE_ADDED","New language was successfully added!");
INSERT INTO aphs_vocabulary VALUES("1452","en","_LANGUAGE_ADD_NEW","Add New Language");
INSERT INTO aphs_vocabulary VALUES("1455","en","_LANGUAGE_EDIT","Edit Language");
INSERT INTO aphs_vocabulary VALUES("1458","en","_LANGUAGE_EDITED","Language data was successfully updated!");
INSERT INTO aphs_vocabulary VALUES("1461","en","_LANGUAGE_NAME","Language Name");
INSERT INTO aphs_vocabulary VALUES("1464","en","_LANG_ABBREV_EMPTY","Language abbreviation cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("1467","en","_LANG_DELETED","Language was successfully deleted!");
INSERT INTO aphs_vocabulary VALUES("1470","en","_LANG_DELETE_LAST_ERROR","You cannot delete last language!");
INSERT INTO aphs_vocabulary VALUES("1473","en","_LANG_DELETE_WARNING","Are you sure you want to remove this language? This operation will delete all language vocabulary!");
INSERT INTO aphs_vocabulary VALUES("1476","en","_LANG_MISSED","Missed language to update! Please, try again.");
INSERT INTO aphs_vocabulary VALUES("1479","en","_LANG_NAME_EMPTY","Language name cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("1482","en","_LANG_NAME_EXISTS","Language with such name already exists! Please choose another.");
INSERT INTO aphs_vocabulary VALUES("1485","en","_LANG_NOT_DELETED","Language was not deleted!");
INSERT INTO aphs_vocabulary VALUES("1488","en","_LANG_ORDER_CHANGED","Language order was successfully changed!");
INSERT INTO aphs_vocabulary VALUES("1491","en","_LAST_CURRENCY_ALERT","You cannot delete last active currency!");
INSERT INTO aphs_vocabulary VALUES("1494","en","_LAST_HOTEL_ALERT","You cannot delete last active hotel record!\n");
INSERT INTO aphs_vocabulary VALUES("1497","en","_LAST_LOGGED_IP","Last logged IP");
INSERT INTO aphs_vocabulary VALUES("1500","en","_LAST_LOGIN","Last Login");
INSERT INTO aphs_vocabulary VALUES("1503","en","_LAST_NAME","Last Name");
INSERT INTO aphs_vocabulary VALUES("1506","en","_LAST_NAME_EMPTY_ALERT","Last Name cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("1509","en","_LAST_RUN","Last run");
INSERT INTO aphs_vocabulary VALUES("1512","en","_LAYOUT","Layout");
INSERT INTO aphs_vocabulary VALUES("1515","en","_LEAVE_YOUR_COMMENT","Leave your comment");
INSERT INTO aphs_vocabulary VALUES("1518","en","_LEFT","Left");
INSERT INTO aphs_vocabulary VALUES("1521","en","_LEFT_TO_RIGHT","LTR (left-to-right)");
INSERT INTO aphs_vocabulary VALUES("1524","en","_LEGEND","Legend");
INSERT INTO aphs_vocabulary VALUES("1527","en","_LEGEND_CANCELED","Order was canceled by admin and the room is available again in search");
INSERT INTO aphs_vocabulary VALUES("1530","en","_LEGEND_COMPLETED","Money was paid (fully or partially) and order completed");
INSERT INTO aphs_vocabulary VALUES("1533","en","_LEGEND_PAYMENT_ERROR","An error occurred while processing customer payments");
INSERT INTO aphs_vocabulary VALUES("1536","en","_LEGEND_PREPARING","Room was added to reservation cart, but still not reserved");
INSERT INTO aphs_vocabulary VALUES("1539","en","_LEGEND_REFUNDED","Order was refunded and the room is available again in search");
INSERT INTO aphs_vocabulary VALUES("1542","en","_LEGEND_RESERVED","Room is reserved, but order was not paid yet");
INSERT INTO aphs_vocabulary VALUES("1545","en","_LICENSE","License");
INSERT INTO aphs_vocabulary VALUES("1548","en","_LINK","Link");
INSERT INTO aphs_vocabulary VALUES("1551","en","_LINK_PARAMETER","Link Parameter");
INSERT INTO aphs_vocabulary VALUES("1554","en","_LOADING","loading");
INSERT INTO aphs_vocabulary VALUES("1557","en","_LOCAL_TIME","Local Time");
INSERT INTO aphs_vocabulary VALUES("1560","en","_LOCATION","Location");
INSERT INTO aphs_vocabulary VALUES("1563","en","_LOCATIONS","Locations");
INSERT INTO aphs_vocabulary VALUES("1566","en","_LOCATION_NAME","Location Name");
INSERT INTO aphs_vocabulary VALUES("1569","en","_LOGIN","Login");
INSERT INTO aphs_vocabulary VALUES("1572","en","_LOGINS","Logins");
INSERT INTO aphs_vocabulary VALUES("1575","en","_LOGIN_PAGE_MSG","Use a valid administrator username and password to get access to the Administrator Back-End.<br><br>Return to site <a href=\'index.php\'>Home Page</a><br><br><img align=\'center\' src=\'images/lock.png\' alt=\'\' width=\'92px\'>");
INSERT INTO aphs_vocabulary VALUES("1578","en","_LONG_DESCRIPTION","Long Description");
INSERT INTO aphs_vocabulary VALUES("1581","en","_LOOK_IN","Look in");
INSERT INTO aphs_vocabulary VALUES("1584","en","_MAILER","Mailer");
INSERT INTO aphs_vocabulary VALUES("1587","en","_MAIN","Main");
INSERT INTO aphs_vocabulary VALUES("1590","en","_MAIN_ADMIN","Main Admin");
INSERT INTO aphs_vocabulary VALUES("1593","en","_MAKE_RESERVATION","Make а Reservation");
INSERT INTO aphs_vocabulary VALUES("1596","en","_MANAGE_TEMPLATES","Manage Templates");
INSERT INTO aphs_vocabulary VALUES("1599","en","_MAP_CODE","Map Code");
INSERT INTO aphs_vocabulary VALUES("1602","en","_MAP_OVERLAY","Map Overlay");
INSERT INTO aphs_vocabulary VALUES("1605","en","_MARCH","March");
INSERT INTO aphs_vocabulary VALUES("1608","en","_MASS_MAIL","Mass Mail");
INSERT INTO aphs_vocabulary VALUES("1611","en","_MASS_MAIL_ALERT","Attention: shared hosting services usually have a limit of 200 emails per hour");
INSERT INTO aphs_vocabulary VALUES("1614","en","_MASS_MAIL_AND_TEMPLATES","Mass Mail & Templates");
INSERT INTO aphs_vocabulary VALUES("1617","en","_MAXIMUM_NIGHTS","Maximum Nights");
INSERT INTO aphs_vocabulary VALUES("1620","en","_MAXIMUM_NIGHTS_ALERT","The maximum allowed stay for this period of time from _FROM_ to _TO_ is _NIGHTS_ nights per booking. Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1623","en","_MAX_ADULTS","Max Adults");
INSERT INTO aphs_vocabulary VALUES("1626","en","_MAX_CHARS","(max: _MAX_CHARS_ chars)");
INSERT INTO aphs_vocabulary VALUES("1629","en","_MAX_CHILDREN","Max Children");
INSERT INTO aphs_vocabulary VALUES("1632","en","_MAX_GUESTS","Max Guests");
INSERT INTO aphs_vocabulary VALUES("1635","en","_MAX_OCCUPANCY","Max. Occupancy");
INSERT INTO aphs_vocabulary VALUES("1638","en","_MAX_RESERVATIONS_ERROR","You have reached the maximum number of permitted room reservations, that you have not yet finished! Please complete at least one of them to proceed reservation of new rooms.");
INSERT INTO aphs_vocabulary VALUES("1641","en","_MAY","May");
INSERT INTO aphs_vocabulary VALUES("1644","en","_MD_BACKUP_AND_RESTORE","With Backup and Restore module you can dump all of your database tables to a file download or save to a file on the server, and to restore from an uploaded or previously saved database dump.");
INSERT INTO aphs_vocabulary VALUES("1647","en","_MD_BANNERS","The Banners module allows administrator to display images on the site in random or rotation style.");
INSERT INTO aphs_vocabulary VALUES("1650","en","_MD_BOOKINGS","The Bookings module allows the site owner to define bookings for all rooms, then price them on an individual basis by accommodation and date. It also permits bookings to be taken from customers and managed via administrator panel.");
INSERT INTO aphs_vocabulary VALUES("1653","en","_MD_COMMENTS","The Comments module allows visitors to leave comments on articles and administrator of the site to moderate them.");
INSERT INTO aphs_vocabulary VALUES("1656","en","_MD_CONTACT_US","Contact Us module allows easy create and place on-line contact form on site pages, using predefined code, like: {module:contact_us}.");
INSERT INTO aphs_vocabulary VALUES("1659","en","_MD_CUSTOMERS","The Customers module allows easy customers management on your site. Administrator could create, edit or delete customer accounts. Customers could register on the site and log into their accounts.");
INSERT INTO aphs_vocabulary VALUES("1662","en","_MD_FAQ","The Frequently Asked Questions (faq) module allows admin users to create question and answer pairs which they want displayed on the \'faq\' page.");
INSERT INTO aphs_vocabulary VALUES("1665","en","_MD_GALLERY","The Gallery module allows administrator to create image or video albums, upload album content and dysplay this content to be viewed by visitor of the site.");
INSERT INTO aphs_vocabulary VALUES("1668","en","_MD_NEWS","The News and Events module allows administrator to post news and events on the site, display latest of them at the side block.");
INSERT INTO aphs_vocabulary VALUES("1671","en","_MD_PAGES","Pages module allows administrator to easily create and maintain page content.");
INSERT INTO aphs_vocabulary VALUES("1674","en","_MD_ROOMS","The Rooms module allows the site owner easily manage rooms in your hotel: create, edit or remove them, specify room facilities, define prices and availability for certain period of time, etc.");
INSERT INTO aphs_vocabulary VALUES("1677","en","_MD_TESTIMONIALS","The Testimonials Module allows the administrator of the site to add/edit customer testimonials, manage them and show on the Hotel Site frontend.");
INSERT INTO aphs_vocabulary VALUES("1680","en","_MEAL_PLANS","Meal Plans");
INSERT INTO aphs_vocabulary VALUES("1683","en","_MEAL_PLANS_MANAGEMENT","Meal Plans Management");
INSERT INTO aphs_vocabulary VALUES("1686","en","_MENUS","Menus");
INSERT INTO aphs_vocabulary VALUES("1689","en","_MENUS_AND_PAGES","Menus and Pages");
INSERT INTO aphs_vocabulary VALUES("1692","en","_MENU_ADD","Add Menu");
INSERT INTO aphs_vocabulary VALUES("1695","en","_MENU_CREATED","Menu was successfully created");
INSERT INTO aphs_vocabulary VALUES("1698","en","_MENU_DELETED","Menu was successfully deleted");
INSERT INTO aphs_vocabulary VALUES("1701","en","_MENU_DELETE_WARNING","Are you sure you want to delete this menu? Note: this will make all its menu links invisible to your site visitors!");
INSERT INTO aphs_vocabulary VALUES("1704","en","_MENU_EDIT","Edit Menu");
INSERT INTO aphs_vocabulary VALUES("1707","en","_MENU_LINK","Menu Link");
INSERT INTO aphs_vocabulary VALUES("1710","en","_MENU_LINK_TEXT","Menu Link (max. 40 chars)");
INSERT INTO aphs_vocabulary VALUES("1713","en","_MENU_MANAGEMENT","Menus Management");
INSERT INTO aphs_vocabulary VALUES("1716","en","_MENU_MISSED","Missed menu to update! Please, try again.");
INSERT INTO aphs_vocabulary VALUES("1719","en","_MENU_NAME","Menu Name");
INSERT INTO aphs_vocabulary VALUES("1722","en","_MENU_NAME_EMPTY","Menu name cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("1725","en","_MENU_NOT_CREATED","Menu was not created!");
INSERT INTO aphs_vocabulary VALUES("1728","en","_MENU_NOT_DELETED","Menu was not deleted!");
INSERT INTO aphs_vocabulary VALUES("1731","en","_MENU_NOT_FOUND","No Menus Found");
INSERT INTO aphs_vocabulary VALUES("1734","en","_MENU_NOT_SAVED","Menu was not saved!");
INSERT INTO aphs_vocabulary VALUES("1737","en","_MENU_ORDER","Menu Order");
INSERT INTO aphs_vocabulary VALUES("1740","en","_MENU_ORDER_CHANGED","Menu order was successfully changed");
INSERT INTO aphs_vocabulary VALUES("1743","en","_MENU_SAVED","Menu was successfully saved");
INSERT INTO aphs_vocabulary VALUES("1746","en","_MENU_TITLE","Menu Title");
INSERT INTO aphs_vocabulary VALUES("1749","en","_MENU_WORD","Menu");
INSERT INTO aphs_vocabulary VALUES("1752","en","_MESSAGE","Message");
INSERT INTO aphs_vocabulary VALUES("1755","en","_MESSAGE_EMPTY_ALERT","Message cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1758","en","_META_TAG","Meta Tag");
INSERT INTO aphs_vocabulary VALUES("1761","en","_META_TAGS","META Tags");
INSERT INTO aphs_vocabulary VALUES("1764","en","_METHOD","Method");
INSERT INTO aphs_vocabulary VALUES("1767","en","_MIN","Min");
INSERT INTO aphs_vocabulary VALUES("1770","en","_MINIMUM_NIGHTS","Minimum Nights");
INSERT INTO aphs_vocabulary VALUES("1773","en","_MINIMUM_NIGHTS_ALERT","The minimum allowed stay for the period of time from _FROM_ to _TO_ is _NIGHTS_ nights per booking. Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("1776","en","_MINUTES","minutes");
INSERT INTO aphs_vocabulary VALUES("1779","en","_MO","Mo");
INSERT INTO aphs_vocabulary VALUES("1782","en","_MODULES","Modules");
INSERT INTO aphs_vocabulary VALUES("1785","en","_MODULES_MANAGEMENT","Modules Management");
INSERT INTO aphs_vocabulary VALUES("1788","en","_MODULES_NOT_FOUND","No modules found!");
INSERT INTO aphs_vocabulary VALUES("1791","en","_MODULE_INSTALLED","Module was successfully installed!");
INSERT INTO aphs_vocabulary VALUES("1794","en","_MODULE_INSTALL_ALERT","Are you sure you want to install this module?");
INSERT INTO aphs_vocabulary VALUES("1797","en","_MODULE_UNINSTALLED","Module was successfully un-installed!");
INSERT INTO aphs_vocabulary VALUES("1800","en","_MODULE_UNINSTALL_ALERT","Are you sure you want to un-install this module? All data, related to this module will be permanently deleted form the system!");
INSERT INTO aphs_vocabulary VALUES("1803","en","_MON","Mon");
INSERT INTO aphs_vocabulary VALUES("1806","en","_MONDAY","Monday");
INSERT INTO aphs_vocabulary VALUES("1809","en","_MONTH","Month");
INSERT INTO aphs_vocabulary VALUES("1812","en","_MONTHS","Months");
INSERT INTO aphs_vocabulary VALUES("1815","en","_MS_ACTIVATE_BOOKINGS","Specifies whether booking module is active on a Whole Site, Front-End/Back-End only or inactive");
INSERT INTO aphs_vocabulary VALUES("1818","en","_MS_ADMIN_BOOKING_IN_PAST","Specifies whether to allow booking in the past for admins and hotel owners");
INSERT INTO aphs_vocabulary VALUES("1821","en","_MS_ADMIN_CHANGE_CUSTOMER_PASSWORD","Specifies whether to allow changing customer password by Admin");
INSERT INTO aphs_vocabulary VALUES("1824","en","_MS_ADMIN_CHANGE_USER_PASSWORD","Specifies whether to allow changing user password by Admin");
INSERT INTO aphs_vocabulary VALUES("1827","en","_MS_ALBUMS_PER_LINE","Number of album icons per line");
INSERT INTO aphs_vocabulary VALUES("1830","en","_MS_ALBUM_ICON_HEIGHT","Album icon height");
INSERT INTO aphs_vocabulary VALUES("1833","en","_MS_ALBUM_ICON_WIDTH","Album icon width");
INSERT INTO aphs_vocabulary VALUES("1836","en","_MS_ALBUM_KEY","The keyword that will be replaced with a certain album images (copy and paste it into the page)");
INSERT INTO aphs_vocabulary VALUES("1839","en","_MS_ALERT_ADMIN_NEW_REGISTRATION","Specifies whether to alert admin on new customer registration");
INSERT INTO aphs_vocabulary VALUES("1842","en","_MS_ALLOW_ADDING_BY_ADMIN","Specifies whether to allow adding new customers by Admin");
INSERT INTO aphs_vocabulary VALUES("1845","en","_MS_ALLOW_BOOKING_WITHOUT_ACCOUNT","Specifies whether to allow booking for customer without creating account");
INSERT INTO aphs_vocabulary VALUES("1848","en","_MS_ALLOW_CHILDREN_IN_ROOM","Specifies whether to allow children in the room");
INSERT INTO aphs_vocabulary VALUES("1851","en","_MS_ALLOW_CUSTOMERS_LOGIN","Specifies whether to allow existing customers to login");
INSERT INTO aphs_vocabulary VALUES("1854","en","_MS_ALLOW_CUSTOMERS_REGISTRATION","Specifies whether to allow registration of new customers");
INSERT INTO aphs_vocabulary VALUES("1857","en","_MS_ALLOW_CUST_RESET_PASSWORDS","Specifies whether to allow customers to restore their passwords");
INSERT INTO aphs_vocabulary VALUES("1860","en","_MS_ALLOW_GUESTS_IN_ROOM","Specifies whether to allow guests in the room");
INSERT INTO aphs_vocabulary VALUES("1863","en","_MS_ALLOW_SYSTEM_SUGGESTION","Specifies whether to show system suggestion feature on empty search results");
INSERT INTO aphs_vocabulary VALUES("1866","en","_MS_AUTHORIZE_LOGIN_ID","Specifies Authorize.Net API Login ID");
INSERT INTO aphs_vocabulary VALUES("1869","en","_MS_AUTHORIZE_TRANSACTION_KEY","Specifies Authorize.Net Transaction Key");
INSERT INTO aphs_vocabulary VALUES("1872","en","_MS_AVAILABLE_UNTIL_APPROVAL","Specifies whether to show \'reserved\' rooms in search results until booking is complete");
INSERT INTO aphs_vocabulary VALUES("1875","en","_MS_BANK_TRANSFER_INFO","Specifies a required banking information: name of the bank, branch, account number etc.");
INSERT INTO aphs_vocabulary VALUES("1878","en","_MS_BANNERS_CAPTION_HTML","Specifies whether to allow using of HTML in slideshow captions or not");
INSERT INTO aphs_vocabulary VALUES("1881","en","_MS_BANNERS_IS_ACTIVE","Defines whether banners module is active or not");
INSERT INTO aphs_vocabulary VALUES("1884","en","_MS_BOOKING_MODE","Specifies which mode is turned ON for booking");
INSERT INTO aphs_vocabulary VALUES("1887","en","_MS_BOOKING_NUMBER_TYPE","Specifies the type of booking numbers");
INSERT INTO aphs_vocabulary VALUES("1890","en","_MS_COMMENTS_ALLOW","Specifies whether to allow comments to articles");
INSERT INTO aphs_vocabulary VALUES("1893","en","_MS_COMMENTS_LENGTH","The maximum length of a comment");
INSERT INTO aphs_vocabulary VALUES("1896","en","_MS_COMMENTS_PAGE_SIZE","Defines how much comments will be shown on one page");
INSERT INTO aphs_vocabulary VALUES("1899","en","_MS_CONTACT_US_KEY","The keyword that will be replaced with Contact Us form (copy and paste it into the page)");
INSERT INTO aphs_vocabulary VALUES("1902","en","_MS_CUSTOMERS_CANCEL_RESERVATION","Specifies the number of days before customers may cancel a reservation");
INSERT INTO aphs_vocabulary VALUES("1905","en","_MS_CUSTOMERS_IMAGE_VERIFICATION","Specifies whether to allow image verification (captcha) on customer registration page");
INSERT INTO aphs_vocabulary VALUES("1908","en","_MS_DEFAULT_PAYMENT_SYSTEM","Specifies default payment processing system");
INSERT INTO aphs_vocabulary VALUES("1911","en","_MS_DELAY_LENGTH","Defines a length of delay between sending emails (in seconds)");
INSERT INTO aphs_vocabulary VALUES("1914","en","_MS_DELETE_PENDING_TIME","The maximum pending time for deleting of comment in minutes");
INSERT INTO aphs_vocabulary VALUES("1917","en","_MS_EMAIL","The email address, that will be used to get sent information");
INSERT INTO aphs_vocabulary VALUES("1920","en","_MS_FAQ_IS_ACTIVE","Defines whether FAQ module is active or not");
INSERT INTO aphs_vocabulary VALUES("1923","en","_MS_FIRST_NIGHT_CALCULATING_TYPE","Specifies a type of the \'first night\' value calculating: real or average");
INSERT INTO aphs_vocabulary VALUES("1926","en","_MS_GALLERY_KEY","The keyword that will be replaced with gallery (copy and paste it into the page)");
INSERT INTO aphs_vocabulary VALUES("1929","en","_MS_GALLERY_WRAPPER","Defines a wrapper type for gallery");
INSERT INTO aphs_vocabulary VALUES("1932","en","_MS_IMAGE_GALLERY_TYPE","Allowed types of Image Gallery");
INSERT INTO aphs_vocabulary VALUES("1935","en","_MS_IMAGE_VERIFICATION_ALLOW","Specifies whether to allow image verification (captcha)");
INSERT INTO aphs_vocabulary VALUES("1938","en","_MS_IS_SEND_DELAY","Specifies whether to allow time delay between sending emails.");
INSERT INTO aphs_vocabulary VALUES("1941","en","_MS_ITEMS_COUNT_IN_ALBUM","Specifies whether to show count of images/video under album name");
INSERT INTO aphs_vocabulary VALUES("1944","en","_MS_MAXIMUM_ALLOWED_RESERVATIONS","Specifies the maximum number of allowed room reservations (not completed) per customer");
INSERT INTO aphs_vocabulary VALUES("1947","en","_MS_MAXIMUM_NIGHTS","Defines a maximum number of nights per booking [<a href=index.php?admin=mod_booking_packages>Define by Package</a>]");
INSERT INTO aphs_vocabulary VALUES("1950","en","_MS_MINIMUM_NIGHTS","Defines a minimum number of nights per booking [<a href=index.php?admin=mod_booking_packages>Define by Package</a>]");
INSERT INTO aphs_vocabulary VALUES("1953","en","_MS_NEWS_COUNT","Defines how many news will be shown in news block");
INSERT INTO aphs_vocabulary VALUES("1956","en","_MS_NEWS_HEADER_LENGTH","Defines a length of news header in block");
INSERT INTO aphs_vocabulary VALUES("1959","en","_MS_NEWS_RSS","Defines using of RSS for news");
INSERT INTO aphs_vocabulary VALUES("1962","en","_MS_ONLINE_CREDIT_CARD_REQUIRED","Specifies whether collecting of credit card info is required for \'On-line Orders\'");
INSERT INTO aphs_vocabulary VALUES("1965","en","_MS_PAYMENT_TYPE_2CO","Specifies whether to allow \'2CO\' payment type");
INSERT INTO aphs_vocabulary VALUES("1968","en","_MS_PAYMENT_TYPE_AUTHORIZE","Specifies whether to allow \'Authorize.Net\' payment type");
INSERT INTO aphs_vocabulary VALUES("1971","en","_MS_PAYMENT_TYPE_BANK_TRANSFER","Specifies whether to allow \'Bank Transfer\' payment type");
INSERT INTO aphs_vocabulary VALUES("1974","en","_MS_PAYMENT_TYPE_ONLINE","Specifies whether to allow \'On-line Order\' payment type");
INSERT INTO aphs_vocabulary VALUES("1977","en","_MS_PAYMENT_TYPE_PAYPAL","Specifies whether to allow \'PayPal\' payment type");
INSERT INTO aphs_vocabulary VALUES("1980","en","_MS_PAYMENT_TYPE_POA","Specifies whether to allow \'Pay on Arrival\' (POA) payment type");
INSERT INTO aphs_vocabulary VALUES("1983","en","_MS_PAYPAL_EMAIL","Specifies PayPal (business) email ");
INSERT INTO aphs_vocabulary VALUES("1986","en","_MS_PREPARING_ORDERS_TIMEOUT","Defines a timeout for \'preparing\' orders before automatic deleting (in hours)");
INSERT INTO aphs_vocabulary VALUES("1989","en","_MS_PRE_MODERATION_ALLOW","Specifies whether to allow pre-moderation for comments");
INSERT INTO aphs_vocabulary VALUES("1992","en","_MS_PRE_PAYMENT_TYPE","Defines a pre-payment type (full price, first night only, fixed sum or percentage)");
INSERT INTO aphs_vocabulary VALUES("1995","en","_MS_PRE_PAYMENT_VALUE","Defines a pre-payment value for \'fixed sum\' or \'percentage\' types");
INSERT INTO aphs_vocabulary VALUES("1998","en","_MS_REG_CONFIRMATION","Defines whether confirmation (which type of) is required for registration");
INSERT INTO aphs_vocabulary VALUES("2001","en","_MS_REMEMBER_ME","Specifies whether to allow Remember Me feature");
INSERT INTO aphs_vocabulary VALUES("2004","en","_MS_RESERVATION EXPIRED_ALERT","Specifies whether to send email alert to customer when reservation has expired");
INSERT INTO aphs_vocabulary VALUES("2007","en","_MS_RESERVATION_INITIAL_FEE","Start (initial) fee - the sum that will be added to each booking (fixed value in default currency)");
INSERT INTO aphs_vocabulary VALUES("2010","en","_MS_ROOMS_IN_SEARCH","Specifies what types of rooms to show in search result: all or available rooms only (without fully booked / unavailable)");
INSERT INTO aphs_vocabulary VALUES("2013","en","_MS_ROTATE_DELAY","Defines banners rotation delay in seconds");
INSERT INTO aphs_vocabulary VALUES("2016","en","_MS_ROTATION_TYPE","Different type of banner rotation");
INSERT INTO aphs_vocabulary VALUES("2019","en","_MS_SEARCH_AVAILABILITY_PAGE_SIZE","Specifies the number of rooms/hotels that will be displayed on one page in the search availability results");
INSERT INTO aphs_vocabulary VALUES("2022","en","_MS_SEND_ORDER_COPY_TO_ADMIN","Specifies whether to allow sending a copy of order to admin");
INSERT INTO aphs_vocabulary VALUES("2025","en","_MS_SHOW_BOOKING_STATUS_FORM","Specifies whether to show Booking Status Form on homepage or not");
INSERT INTO aphs_vocabulary VALUES("2028","en","_MS_SHOW_FULLY_BOOKED_ROOMS","Specifies whether to allow showing of fully booked/unavailable rooms in search");
INSERT INTO aphs_vocabulary VALUES("2031","en","_MS_SHOW_NEWSLETTER_SUBSCRIBE_BLOCK","Defines whether to show Newsletter Subscription block or not");
INSERT INTO aphs_vocabulary VALUES("2034","en","_MS_SHOW_NEWS_BLOCK","Defines whether to show News side block or not");
INSERT INTO aphs_vocabulary VALUES("2037","en","_MS_SHOW_RESERVATION_FORM","Specifies whether to show Reservation Form on homepage or not");
INSERT INTO aphs_vocabulary VALUES("2040","en","_MS_TESTIMONIALS_KEY","The keyword that will be replaced with a list of customer testimonials (copy and paste it into the page)");
INSERT INTO aphs_vocabulary VALUES("2043","en","_MS_TWO_CHECKOUT_VENDOR","Specifies 2CO Vendor ID");
INSERT INTO aphs_vocabulary VALUES("2046","en","_MS_USER_TYPE","Type of users, who can post comments");
INSERT INTO aphs_vocabulary VALUES("2049","en","_MS_VAT_INCLUDED_IN_PRICE","Specifies whether VAT fee is included in room and extras prices or not");
INSERT INTO aphs_vocabulary VALUES("2052","en","_MS_VAT_VALUE","Specifies default VAT value for order (in %) &nbsp;[<a href=index.php?admin=countries_management>Define by Country</a>]");
INSERT INTO aphs_vocabulary VALUES("2055","en","_MS_VIDEO_GALLERY_TYPE","Allowed types of Video Gallery");
INSERT INTO aphs_vocabulary VALUES("2058","en","_MUST_BE_LOGGED","You must be logged in to view this page! <a href=\'index.php?customer=login\'>Login</a> or <a href=\'index.php?customer=create_account\'>Create Account for free</a>.");
INSERT INTO aphs_vocabulary VALUES("2061","en","_MY_ACCOUNT","My Account");
INSERT INTO aphs_vocabulary VALUES("2064","en","_MY_BOOKINGS","My Bookings");
INSERT INTO aphs_vocabulary VALUES("2067","en","_MY_ORDERS","My Orders");
INSERT INTO aphs_vocabulary VALUES("2070","en","_NAME","Name");
INSERT INTO aphs_vocabulary VALUES("2073","en","_NEVER","never");
INSERT INTO aphs_vocabulary VALUES("2076","en","_NEWS","News");
INSERT INTO aphs_vocabulary VALUES("2079","en","_NEWSLETTER_PAGE_TEXT","<p>To receive newsletters from our site, simply enter your email and click on \"Subscribe\" button.</p><p>If you later decide to stop your subscription or change the type of news you receive, simply follow the link at the end of the latest newsletter and update your profile or unsubscribe by ticking the checkbox below.</p>");
INSERT INTO aphs_vocabulary VALUES("2082","en","_NEWSLETTER_PRE_SUBSCRIBE_ALERT","Please click on the \"Subscribe\" button to complete the process.");
INSERT INTO aphs_vocabulary VALUES("2085","en","_NEWSLETTER_PRE_UNSUBSCRIBE_ALERT","Please click on the \"Unsubscribe\" button to complete the process.");
INSERT INTO aphs_vocabulary VALUES("2088","en","_NEWSLETTER_SUBSCRIBERS","Newsletter Subscribers");
INSERT INTO aphs_vocabulary VALUES("2091","en","_NEWSLETTER_SUBSCRIBE_SUCCESS","Thank you for subscribing to our electronic newsletter. You will receive an e-mail to confirm your subscription.");
INSERT INTO aphs_vocabulary VALUES("2094","en","_NEWSLETTER_SUBSCRIBE_TEXT","<p>To receive newsletters from our site, simply enter your email and click on \"Subscribe\" button.</p><p>If you later decide to stop your subscription or change the type of news you receive, simply follow the link at the end of the latest newsletter and update your profile or unsubscribe by ticking the checkbox below.</p>");
INSERT INTO aphs_vocabulary VALUES("2097","en","_NEWSLETTER_SUBSCRIPTION_MANAGEMENT","Newsletter Subscription Management");
INSERT INTO aphs_vocabulary VALUES("2100","en","_NEWSLETTER_UNSUBSCRIBE_SUCCESS","You have been successfully unsubscribed from our newsletter!");
INSERT INTO aphs_vocabulary VALUES("2103","en","_NEWSLETTER_UNSUBSCRIBE_TEXT","<p>To unsubscribe from our newsletters, enter your email address below and click the unsubscribe button.</p>");
INSERT INTO aphs_vocabulary VALUES("2106","en","_NEWS_AND_EVENTS","News & Events");
INSERT INTO aphs_vocabulary VALUES("2109","en","_NEWS_MANAGEMENT","News Management");
INSERT INTO aphs_vocabulary VALUES("2112","en","_NEWS_SETTINGS","News Settings");
INSERT INTO aphs_vocabulary VALUES("2115","en","_NEXT","Next");
INSERT INTO aphs_vocabulary VALUES("2118","en","_NIGHT","Night");
INSERT INTO aphs_vocabulary VALUES("2121","en","_NIGHTS","Nights");
INSERT INTO aphs_vocabulary VALUES("2124","en","_NO","No");
INSERT INTO aphs_vocabulary VALUES("2127","en","_NONE","None");
INSERT INTO aphs_vocabulary VALUES("2130","en","_NOTICE_MODULES_CODE","To add available modules to this page just copy and paste into the text:");
INSERT INTO aphs_vocabulary VALUES("2133","en","_NOTIFICATION_MSG","Please send me information about specials and discounts!");
INSERT INTO aphs_vocabulary VALUES("2136","en","_NOTIFICATION_STATUS_CHANGED","Notification status changed");
INSERT INTO aphs_vocabulary VALUES("2139","en","_NOT_ALLOWED","Not Allowed");
INSERT INTO aphs_vocabulary VALUES("2142","en","_NOT_AUTHORIZED","You are not authorized to view this page.");
INSERT INTO aphs_vocabulary VALUES("2145","en","_NOT_AVAILABLE","N/A");
INSERT INTO aphs_vocabulary VALUES("2148","en","_NOT_PAID_YET","Not paid yet");
INSERT INTO aphs_vocabulary VALUES("2151","en","_NOVEMBER","November");
INSERT INTO aphs_vocabulary VALUES("2154","en","_NO_AVAILABLE","Not Available");
INSERT INTO aphs_vocabulary VALUES("2157","en","_NO_BOOKING_FOUND","The number of booking you\'ve entered was not found in our system! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("2160","en","_NO_COMMENTS_YET","No comments yet.");
INSERT INTO aphs_vocabulary VALUES("2163","en","_NO_CUSTOMER_FOUND","No customer found!");
INSERT INTO aphs_vocabulary VALUES("2166","en","_NO_NEWS","No news");
INSERT INTO aphs_vocabulary VALUES("2169","en","_NO_PAYMENT_METHODS_ALERT","No payment methods available! Please contact our technical support.");
INSERT INTO aphs_vocabulary VALUES("2172","en","_NO_RECORDS_FOUND","No records found");
INSERT INTO aphs_vocabulary VALUES("2175","en","_NO_RECORDS_PROCESSED","No records found for processing!");
INSERT INTO aphs_vocabulary VALUES("2178","en","_NO_RECORDS_UPDATED","No records were updated!");
INSERT INTO aphs_vocabulary VALUES("2181","en","_NO_ROOMS_FOUND","Sorry, there are no rooms that match your search. Please change your search criteria to see more rooms.");
INSERT INTO aphs_vocabulary VALUES("2184","en","_NO_TEMPLATE","no template");
INSERT INTO aphs_vocabulary VALUES("2187","en","_NO_USER_EMAIL_EXISTS_ALERT","It seems that you already booked rooms with us! <br>Please click <a href=index.php?customer=reset_account>here</a> to reset your username and get a temporary password. ");
INSERT INTO aphs_vocabulary VALUES("2190","en","_NO_WRITE_ACCESS_ALERT","Please check you have write access to following directories:");
INSERT INTO aphs_vocabulary VALUES("2193","en","_OCCUPANCY","Occupancy");
INSERT INTO aphs_vocabulary VALUES("2196","en","_OCTOBER","October");
INSERT INTO aphs_vocabulary VALUES("2199","en","_OFF","Off");
INSERT INTO aphs_vocabulary VALUES("2202","en","_OFFLINE_LOGIN_ALERT","To log into Admin Panel when site is offline, type in your browser: http://{your_site_address}/index.php?admin=login");
INSERT INTO aphs_vocabulary VALUES("2205","en","_OFFLINE_MESSAGE","Offline Message");
INSERT INTO aphs_vocabulary VALUES("2208","en","_ON","On");
INSERT INTO aphs_vocabulary VALUES("2211","en","_ONLINE","Online");
INSERT INTO aphs_vocabulary VALUES("2214","en","_ONLINE_ORDER","On-line Order");
INSERT INTO aphs_vocabulary VALUES("2217","en","_ONLY","Only");
INSERT INTO aphs_vocabulary VALUES("2220","en","_OPEN","Open");
INSERT INTO aphs_vocabulary VALUES("2223","en","_OPEN_ALERT_WINDOW","Open Alert Window");
INSERT INTO aphs_vocabulary VALUES("2226","en","_OPERATION_BLOCKED","This operation is blocked in Demo Version!");
INSERT INTO aphs_vocabulary VALUES("2229","en","_OPERATION_COMMON_COMPLETED","The operation was successfully completed!");
INSERT INTO aphs_vocabulary VALUES("2232","en","_OPERATION_WAS_ALREADY_COMPLETED","This operation was already completed!");
INSERT INTO aphs_vocabulary VALUES("2235","en","_OR","or");
INSERT INTO aphs_vocabulary VALUES("2238","en","_ORDER","Order");
INSERT INTO aphs_vocabulary VALUES("2241","en","_ORDERS","Orders");
INSERT INTO aphs_vocabulary VALUES("2244","en","_ORDERS_COUNT","Orders count");
INSERT INTO aphs_vocabulary VALUES("2247","en","_ORDER_DATE","Order Date");
INSERT INTO aphs_vocabulary VALUES("2250","en","_ORDER_ERROR","Cannot complete your order! Please try again later.");
INSERT INTO aphs_vocabulary VALUES("2253","en","_ORDER_NOW","Order Now");
INSERT INTO aphs_vocabulary VALUES("2256","en","_ORDER_PLACED_MSG","Thank you! The order has been placed in our system and will be processed shortly. Your booking number is: _BOOKING_NUMBER_.");
INSERT INTO aphs_vocabulary VALUES("2259","en","_ORDER_PRICE","Order Price");
INSERT INTO aphs_vocabulary VALUES("2262","en","_OTHER","Other");
INSERT INTO aphs_vocabulary VALUES("2265","en","_OUR_LOCATION","Our location");
INSERT INTO aphs_vocabulary VALUES("2268","en","_OWNER","Owner");
INSERT INTO aphs_vocabulary VALUES("2271","en","_PACKAGES","Packages");
INSERT INTO aphs_vocabulary VALUES("2274","en","_PACKAGES_MANAGEMENT","Packages Management");
INSERT INTO aphs_vocabulary VALUES("2277","en","_PAGE","Page");
INSERT INTO aphs_vocabulary VALUES("2280","en","_PAGES","Pages");
INSERT INTO aphs_vocabulary VALUES("2283","en","_PAGE_ADD_NEW","Add New Page");
INSERT INTO aphs_vocabulary VALUES("2286","en","_PAGE_CREATED","Page was successfully created");
INSERT INTO aphs_vocabulary VALUES("2289","en","_PAGE_DELETED","Page was successfully deleted");
INSERT INTO aphs_vocabulary VALUES("2292","en","_PAGE_DELETE_WARNING","Are you sure you want to delete this page?");
INSERT INTO aphs_vocabulary VALUES("2295","en","_PAGE_EDIT_HOME","Edit Home Page");
INSERT INTO aphs_vocabulary VALUES("2298","en","_PAGE_EDIT_PAGES","Edit Pages");
INSERT INTO aphs_vocabulary VALUES("2301","en","_PAGE_EDIT_SYS_PAGES","Edit System Pages");
INSERT INTO aphs_vocabulary VALUES("2304","en","_PAGE_EXPIRED","The page you requested has expired!");
INSERT INTO aphs_vocabulary VALUES("2307","en","_PAGE_HEADER","Page Header");
INSERT INTO aphs_vocabulary VALUES("2310","en","_PAGE_HEADER_EMPTY","Page header cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("2313","en","_PAGE_KEY_EMPTY","Page key cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("2316","en","_PAGE_LINK_TOO_LONG","Menu link too long!");
INSERT INTO aphs_vocabulary VALUES("2319","en","_PAGE_MANAGEMENT","Pages Management");
INSERT INTO aphs_vocabulary VALUES("2322","en","_PAGE_NOT_CREATED","Page was not created!");
INSERT INTO aphs_vocabulary VALUES("2325","en","_PAGE_NOT_DELETED","Page was not deleted!");
INSERT INTO aphs_vocabulary VALUES("2328","en","_PAGE_NOT_EXISTS","The page you attempted to access does not exist");
INSERT INTO aphs_vocabulary VALUES("2331","en","_PAGE_NOT_FOUND","No Pages Found");
INSERT INTO aphs_vocabulary VALUES("2334","en","_PAGE_NOT_SAVED","Page was not saved!");
INSERT INTO aphs_vocabulary VALUES("2337","en","_PAGE_ORDER_CHANGED","Page order was successfully changed!");
INSERT INTO aphs_vocabulary VALUES("2340","en","_PAGE_REMOVED","Page was successfully removed!");
INSERT INTO aphs_vocabulary VALUES("2343","en","_PAGE_REMOVE_WARNING","Are you sure you want to move this page to the Trash?");
INSERT INTO aphs_vocabulary VALUES("2346","en","_PAGE_RESTORED","Page was successfully restored!");
INSERT INTO aphs_vocabulary VALUES("2349","en","_PAGE_RESTORE_WARNING","Are you sure you want to restore this page?");
INSERT INTO aphs_vocabulary VALUES("2352","en","_PAGE_SAVED","Page was successfully saved");
INSERT INTO aphs_vocabulary VALUES("2355","en","_PAGE_TEXT","Page text");
INSERT INTO aphs_vocabulary VALUES("2358","en","_PAGE_TITLE","Page Title");
INSERT INTO aphs_vocabulary VALUES("2361","en","_PAGE_UNKNOWN","Unknown page!");
INSERT INTO aphs_vocabulary VALUES("2364","en","_PARAMETER","Parameter");
INSERT INTO aphs_vocabulary VALUES("2367","en","_PARTIALLY_AVAILABLE","Partially Available");
INSERT INTO aphs_vocabulary VALUES("2370","en","_PARTIAL_PRICE","Partial Price");
INSERT INTO aphs_vocabulary VALUES("2373","en","_PASSWORD","Password");
INSERT INTO aphs_vocabulary VALUES("2376","en","_PASSWORD_ALREADY_SENT","Password was already sent to your email. Please try again later.");
INSERT INTO aphs_vocabulary VALUES("2379","en","_PASSWORD_CHANGED","Password was changed.");
INSERT INTO aphs_vocabulary VALUES("2382","en","_PASSWORD_DO_NOT_MATCH","Password and confirmation do not match!");
INSERT INTO aphs_vocabulary VALUES("2385","en","_PASSWORD_FORGOTTEN","Forgotten Password");
INSERT INTO aphs_vocabulary VALUES("2388","en","_PASSWORD_FORGOTTEN_PAGE_MSG","Use a valid administrator e-mail to restore your password to the Administrator Back-End.<br><br>Return to site <a href=\'index.php\'>Home Page</a><br><br><img align=\'center\' src=\'images/password.png\' alt=\'\' width=\'92px\'>");
INSERT INTO aphs_vocabulary VALUES("2391","en","_PASSWORD_IS_EMPTY","Passwords must not be empty and at least 6 characters!");
INSERT INTO aphs_vocabulary VALUES("2394","en","_PASSWORD_NOT_CHANGED","Password was not changed. Please try again!");
INSERT INTO aphs_vocabulary VALUES("2397","en","_PASSWORD_RECOVERY_MSG","To recover your password, please enter your e-mail address and a link will be emailed to you.");
INSERT INTO aphs_vocabulary VALUES("2400","en","_PASSWORD_SUCCESSFULLY_SENT","Your password has been successfully sent to the email address.");
INSERT INTO aphs_vocabulary VALUES("2403","en","_PAST_TIME_ALERT","You cannot perform reservation in the past! Please re-enter dates.");
INSERT INTO aphs_vocabulary VALUES("2406","en","_PAYED_BY","Payed by");
INSERT INTO aphs_vocabulary VALUES("2409","en","_PAYMENT","Payment");
INSERT INTO aphs_vocabulary VALUES("2412","en","_PAYMENTS","Payments");
INSERT INTO aphs_vocabulary VALUES("2415","en","_PAYMENT_COMPANY_ACCOUNT","Payment Company Account");
INSERT INTO aphs_vocabulary VALUES("2418","en","_PAYMENT_DATE","Payment Date");
INSERT INTO aphs_vocabulary VALUES("2421","en","_PAYMENT_DETAILS","Payment Details");
INSERT INTO aphs_vocabulary VALUES("2424","en","_PAYMENT_ERROR","Payment error");
INSERT INTO aphs_vocabulary VALUES("2427","en","_PAYMENT_METHOD","Payment Method");
INSERT INTO aphs_vocabulary VALUES("2430","en","_PAYMENT_REQUIRED","Payment Required");
INSERT INTO aphs_vocabulary VALUES("2433","en","_PAYMENT_SUM","Payment Sum");
INSERT INTO aphs_vocabulary VALUES("2436","en","_PAYMENT_TYPE","Payment Type");
INSERT INTO aphs_vocabulary VALUES("2439","en","_PAYPAL","PayPal");
INSERT INTO aphs_vocabulary VALUES("2442","en","_PAYPAL_NOTICE","Save time. Pay securely using your stored payment information.<br />Pay with <b>credit card</b>, <b>bank account</b> or <b>PayPal</b> account balance.");
INSERT INTO aphs_vocabulary VALUES("2445","en","_PAYPAL_ORDER","PayPal Order");
INSERT INTO aphs_vocabulary VALUES("2448","en","_PAY_ON_ARRIVAL","Pay on Arrival");
INSERT INTO aphs_vocabulary VALUES("2451","en","_PC_BILLING_INFORMATION_TEXT","billing information: address, city, country etc.");
INSERT INTO aphs_vocabulary VALUES("2454","en","_PC_BOOKING_DETAILS_TEXT","order details, list of purchased products etc.");
INSERT INTO aphs_vocabulary VALUES("2457","en","_PC_BOOKING_NUMBER_TEXT","the number of order");
INSERT INTO aphs_vocabulary VALUES("2460","en","_PC_EVENT_TEXT","the title of event");
INSERT INTO aphs_vocabulary VALUES("2463","en","_PC_FIRST_NAME_TEXT","the first name of customer or admin");
INSERT INTO aphs_vocabulary VALUES("2466","en","_PC_HOTEL_INFO_TEXT","information about hotel: name, address, telephone, fax etc.");
INSERT INTO aphs_vocabulary VALUES("2469","en","_PC_LAST_NAME_TEXT","the last name of customer or admin");
INSERT INTO aphs_vocabulary VALUES("2472","en","_PC_PERSONAL_INFORMATION_TEXT","personal information of customer: first name, last name etc.");
INSERT INTO aphs_vocabulary VALUES("2475","en","_PC_REGISTRATION_CODE_TEXT","confirmation code for new account");
INSERT INTO aphs_vocabulary VALUES("2478","en","_PC_STATUS_DESCRIPTION_TEXT","description of payment status");
INSERT INTO aphs_vocabulary VALUES("2481","en","_PC_USER_EMAIL_TEXT","email of user");
INSERT INTO aphs_vocabulary VALUES("2484","en","_PC_USER_NAME_TEXT","username (login) of user");
INSERT INTO aphs_vocabulary VALUES("2487","en","_PC_USER_PASSWORD_TEXT","password for customer or admin");
INSERT INTO aphs_vocabulary VALUES("2490","en","_PC_WEB_SITE_BASED_URL_TEXT","web site base url");
INSERT INTO aphs_vocabulary VALUES("2493","en","_PC_WEB_SITE_URL_TEXT","web site url");
INSERT INTO aphs_vocabulary VALUES("2496","en","_PC_YEAR_TEXT","current year in YYYY format");
INSERT INTO aphs_vocabulary VALUES("2499","en","_PENDING","Pending");
INSERT INTO aphs_vocabulary VALUES("2502","en","_PEOPLE_ARRIVING","People Arriving");
INSERT INTO aphs_vocabulary VALUES("2505","en","_PEOPLE_DEPARTING","People Departing");
INSERT INTO aphs_vocabulary VALUES("2508","en","_PEOPLE_STAYING","People Staying");
INSERT INTO aphs_vocabulary VALUES("2511","en","_PERFORM_OPERATION_COMMON_ALERT","Are you sure you want to perform this operation?");
INSERT INTO aphs_vocabulary VALUES("2516","en","_PERSONAL_DETAILS","Personal Details");
INSERT INTO aphs_vocabulary VALUES("2519","en","_PERSONAL_INFORMATION","Personal Information");
INSERT INTO aphs_vocabulary VALUES("2522","en","_PERSON_PER_NIGHT","Person/Per Night");
INSERT INTO aphs_vocabulary VALUES("2525","en","_PER_NIGHT","Per Night");
INSERT INTO aphs_vocabulary VALUES("2528","en","_PHONE","Phone");
INSERT INTO aphs_vocabulary VALUES("2531","en","_PHONE_EMPTY_ALERT","Phone field cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("2534","en","_PICK_DATE","Open calendar and pick a date");
INSERT INTO aphs_vocabulary VALUES("2537","en","_PLACEMENT","Placement");
INSERT INTO aphs_vocabulary VALUES("2540","en","_PLACE_ORDER","Place Order");
INSERT INTO aphs_vocabulary VALUES("2543","en","_PLAY","Play");
INSERT INTO aphs_vocabulary VALUES("2546","en","_POPULARITY","Popularity");
INSERT INTO aphs_vocabulary VALUES("2549","en","_POPULAR_SEARCH","Popular Search");
INSERT INTO aphs_vocabulary VALUES("2552","en","_POSTED_ON","Posted on");
INSERT INTO aphs_vocabulary VALUES("2555","en","_POST_COM_REGISTERED_ALERT","Your need to be registered to post comments.");
INSERT INTO aphs_vocabulary VALUES("2558","en","_PREDEFINED_CONSTANTS","Predefined Constants");
INSERT INTO aphs_vocabulary VALUES("2561","en","_PREFERRED_LANGUAGE","Preferred Language");
INSERT INTO aphs_vocabulary VALUES("2564","en","_PREPARING","Preparing");
INSERT INTO aphs_vocabulary VALUES("2567","en","_PREVIEW","Preview");
INSERT INTO aphs_vocabulary VALUES("2570","en","_PREVIOUS","Previous");
INSERT INTO aphs_vocabulary VALUES("2573","en","_PRE_PAYMENT","Pre-Payment");
INSERT INTO aphs_vocabulary VALUES("2576","en","_PRICE","Price");
INSERT INTO aphs_vocabulary VALUES("2579","en","_PRICES","Prices");
INSERT INTO aphs_vocabulary VALUES("2582","en","_PRICE_EMPTY_ALERT","Field price cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("2585","en","_PRICE_FORMAT","Price Format");
INSERT INTO aphs_vocabulary VALUES("2588","en","_PRICE_FORMAT_ALERT","Allows to display prices for visitor in appropriate format");
INSERT INTO aphs_vocabulary VALUES("2591","en","_PRINT","Print");
INSERT INTO aphs_vocabulary VALUES("2594","en","_PRIVILEGES","Privileges");
INSERT INTO aphs_vocabulary VALUES("2597","en","_PRIVILEGES_MANAGEMENT","Privileges Management");
INSERT INTO aphs_vocabulary VALUES("2600","en","_PRODUCT","Product");
INSERT INTO aphs_vocabulary VALUES("2603","en","_PRODUCTS","Products");
INSERT INTO aphs_vocabulary VALUES("2606","en","_PRODUCTS_COUNT","Products count");
INSERT INTO aphs_vocabulary VALUES("2609","en","_PRODUCTS_MANAGEMENT","Products Management");
INSERT INTO aphs_vocabulary VALUES("2612","en","_PRODUCT_DESCRIPTION","Product Description");
INSERT INTO aphs_vocabulary VALUES("2615","en","_PROMO_AND_DISCOUNTS","Promo and Discounts");
INSERT INTO aphs_vocabulary VALUES("2618","en","_PROMO_CODE_OR_COUPON","Promo Code or Discount Coupon");
INSERT INTO aphs_vocabulary VALUES("2621","en","_PROMO_COUPON_NOTICE","If you have a promo code or discount coupon please enter it here");
INSERT INTO aphs_vocabulary VALUES("2624","en","_PUBLIC","Public");
INSERT INTO aphs_vocabulary VALUES("2627","en","_PUBLISHED","Published");
INSERT INTO aphs_vocabulary VALUES("2630","en","_PUBLISH_YOUR_COMMENT","Publish your comment");
INSERT INTO aphs_vocabulary VALUES("2633","en","_QTY","Qty");
INSERT INTO aphs_vocabulary VALUES("2636","en","_QUANTITY","Quantity");
INSERT INTO aphs_vocabulary VALUES("2639","en","_QUESTION","Question");
INSERT INTO aphs_vocabulary VALUES("2642","en","_QUESTIONS","Questions");
INSERT INTO aphs_vocabulary VALUES("2645","en","_RATE","Rate");
INSERT INTO aphs_vocabulary VALUES("2648","en","_RATE_PER_NIGHT","Rate per night");
INSERT INTO aphs_vocabulary VALUES("2651","en","_RATE_PER_NIGHT_AVG","Average rate per night");
INSERT INTO aphs_vocabulary VALUES("2654","en","_REACTIVATION_EMAIL","Resend Activation Email");
INSERT INTO aphs_vocabulary VALUES("2657","en","_READY","Ready");
INSERT INTO aphs_vocabulary VALUES("2660","en","_READ_MORE","Read more");
INSERT INTO aphs_vocabulary VALUES("2663","en","_REASON","Reason");
INSERT INTO aphs_vocabulary VALUES("2666","en","_RECORD_WAS_DELETED_COMMON","The record was successfully deleted!");
INSERT INTO aphs_vocabulary VALUES("2669","en","_REFRESH","Refresh");
INSERT INTO aphs_vocabulary VALUES("2672","en","_REFUNDED","Refunded");
INSERT INTO aphs_vocabulary VALUES("2675","en","_REGISTERED","Registered");
INSERT INTO aphs_vocabulary VALUES("2678","en","_REGISTERED_FROM_IP","Registered from IP");
INSERT INTO aphs_vocabulary VALUES("2681","en","_REGISTRATIONS","Registrations");
INSERT INTO aphs_vocabulary VALUES("2684","en","_REGISTRATION_CODE","Registration code");
INSERT INTO aphs_vocabulary VALUES("2687","en","_REGISTRATION_CONFIRMATION","Registration Confirmation");
INSERT INTO aphs_vocabulary VALUES("2690","en","_REGISTRATION_FORM","Registration Form");
INSERT INTO aphs_vocabulary VALUES("2693","en","_REGISTRATION_NOT_COMPLETED","Your registration process is not yet complete! Please check again your email for further instructions or click <a href=index.php?customer=resend_activation>here</a> to resend them again.");
INSERT INTO aphs_vocabulary VALUES("2696","en","_REMEMBER_ME","Remember Me");
INSERT INTO aphs_vocabulary VALUES("2699","en","_REMOVE","Remove");
INSERT INTO aphs_vocabulary VALUES("2702","en","_REMOVED","Removed");
INSERT INTO aphs_vocabulary VALUES("2705","en","_REMOVE_ACCOUNT","Remove Account");
INSERT INTO aphs_vocabulary VALUES("2708","en","_REMOVE_ACCOUNT_ALERT","Are you sure you want to remove your account?");
INSERT INTO aphs_vocabulary VALUES("2711","en","_REMOVE_ACCOUNT_WARNING","If you don\'t think you will use this site again and would like your account deleted, we can take care of this for you. Keep in mind, that you will not be able to reactivate your account or retrieve any of the content or information that was added. If you would like your account deleted, then click Remove button");
INSERT INTO aphs_vocabulary VALUES("2714","en","_REMOVE_LAST_COUNTRY_ALERT","The country selected has not been deleted, because you must have at least one active country for correct work of the site!");
INSERT INTO aphs_vocabulary VALUES("2717","en","_REMOVE_ROOM_FROM_CART","Remove room from the cart");
INSERT INTO aphs_vocabulary VALUES("2720","en","_REPORTS","Reports");
INSERT INTO aphs_vocabulary VALUES("2723","en","_RESEND_ACTIVATION_EMAIL","Resend Activation Email");
INSERT INTO aphs_vocabulary VALUES("2726","en","_RESEND_ACTIVATION_EMAIL_MSG","Please enter your email address then click on Send button. You will receive the activation email shortly.");
INSERT INTO aphs_vocabulary VALUES("2729","en","_RESERVATION","Reservation");
INSERT INTO aphs_vocabulary VALUES("2732","en","_RESERVATIONS","Reservations");
INSERT INTO aphs_vocabulary VALUES("2735","en","_RESERVATION_CART","Reservation Cart");
INSERT INTO aphs_vocabulary VALUES("2738","en","_RESERVATION_CART_IS_EMPTY_ALERT","Your reservation cart is empty!");
INSERT INTO aphs_vocabulary VALUES("2741","en","_RESERVATION_DETAILS","Reservation Details");
INSERT INTO aphs_vocabulary VALUES("2744","en","_RESERVED","Reserved");
INSERT INTO aphs_vocabulary VALUES("2747","en","_RESET","Reset");
INSERT INTO aphs_vocabulary VALUES("2750","en","_RESET_ACCOUNT","Reset Account");
INSERT INTO aphs_vocabulary VALUES("2753","en","_RESTAURANT","Restaurant");
INSERT INTO aphs_vocabulary VALUES("2756","en","_RESTORE","Restore");
INSERT INTO aphs_vocabulary VALUES("2759","en","_RETYPE_PASSWORD","Retype Password");
INSERT INTO aphs_vocabulary VALUES("2762","en","_RIGHT","Right");
INSERT INTO aphs_vocabulary VALUES("2765","en","_RIGHT_TO_LEFT","RTL (right-to-left)");
INSERT INTO aphs_vocabulary VALUES("2768","en","_ROLES_AND_PRIVILEGES","Roles & Privileges");
INSERT INTO aphs_vocabulary VALUES("2771","en","_ROLES_MANAGEMENT","Roles Management");
INSERT INTO aphs_vocabulary VALUES("2774","en","_ROOMS","Rooms");
INSERT INTO aphs_vocabulary VALUES("2777","en","_ROOMS_AVAILABILITY","Rooms Availability");
INSERT INTO aphs_vocabulary VALUES("2780","en","_ROOMS_COUNT","Number of Rooms (in the Hotel)");
INSERT INTO aphs_vocabulary VALUES("2783","en","_ROOMS_FACILITIES","Rooms Facilities");
INSERT INTO aphs_vocabulary VALUES("2786","en","_ROOMS_LAST","last room");
INSERT INTO aphs_vocabulary VALUES("2789","en","_ROOMS_LEFT","rooms left");
INSERT INTO aphs_vocabulary VALUES("2792","en","_ROOMS_MANAGEMENT","Rooms Management");
INSERT INTO aphs_vocabulary VALUES("2795","en","_ROOMS_OCCUPANCY","Rooms Occupancy");
INSERT INTO aphs_vocabulary VALUES("2798","en","_ROOMS_RESERVATION","Rooms Reservation");
INSERT INTO aphs_vocabulary VALUES("2801","en","_ROOMS_SETTINGS","Rooms Settings");
INSERT INTO aphs_vocabulary VALUES("2804","en","_ROOM_AREA","Room Area");
INSERT INTO aphs_vocabulary VALUES("2807","en","_ROOM_DESCRIPTION","Room Description");
INSERT INTO aphs_vocabulary VALUES("2810","en","_ROOM_DETAILS","Room Details");
INSERT INTO aphs_vocabulary VALUES("2813","en","_ROOM_FACILITIES","Room Facilities");
INSERT INTO aphs_vocabulary VALUES("2816","en","_ROOM_FACILITIES_MANAGEMENT","Room Facilities Management");
INSERT INTO aphs_vocabulary VALUES("2819","en","_ROOM_NOT_FOUND","Room was not found!");
INSERT INTO aphs_vocabulary VALUES("2822","en","_ROOM_NUMBERS","Room Numbers");
INSERT INTO aphs_vocabulary VALUES("2825","en","_ROOM_PRICE","Room Price");
INSERT INTO aphs_vocabulary VALUES("2828","en","_ROOM_PRICES_WERE_ADDED","Room prices for new period were successfully added!");
INSERT INTO aphs_vocabulary VALUES("2831","en","_ROOM_TYPE","Room Type");
INSERT INTO aphs_vocabulary VALUES("2834","en","_ROOM_WAS_ADDED","Room was successfully added to your reservation!");
INSERT INTO aphs_vocabulary VALUES("2837","en","_ROOM_WAS_REMOVED","Selected room was successfully removed from your Reservation Cart!");
INSERT INTO aphs_vocabulary VALUES("2840","en","_ROWS","Rows");
INSERT INTO aphs_vocabulary VALUES("2843","en","_RSS_FEED_TYPE","RSS Feed Type");
INSERT INTO aphs_vocabulary VALUES("2846","en","_RSS_FILE_ERROR","Cannot open RSS file to add new item! Please check your access rights to <b>feeds/</b> directory or try again later.");
INSERT INTO aphs_vocabulary VALUES("2849","en","_RUN_CRON","Run cron");
INSERT INTO aphs_vocabulary VALUES("2852","en","_RUN_EVERY","Run every");
INSERT INTO aphs_vocabulary VALUES("2855","en","_SA","Sa");
INSERT INTO aphs_vocabulary VALUES("2858","en","_SAID","said");
INSERT INTO aphs_vocabulary VALUES("2861","en","_SAT","Sat");
INSERT INTO aphs_vocabulary VALUES("2864","en","_SATURDAY","Saturday");
INSERT INTO aphs_vocabulary VALUES("2867","en","_SEARCH","Search");
INSERT INTO aphs_vocabulary VALUES("2870","en","_SEARCH_KEYWORDS","search keywords");
INSERT INTO aphs_vocabulary VALUES("2873","en","_SEARCH_RESULT_FOR","Search Results for");
INSERT INTO aphs_vocabulary VALUES("2876","en","_SEARCH_ROOM_TIPS","<b>Find more rooms by expanding your search options</b>:<br>- Reduce the number of adults in room to get more results<br>- Reduce the number of children in room to get more results<br>- Change your Check-in/Check-out dates<br>");
INSERT INTO aphs_vocabulary VALUES("2879","en","_SEC","Sec");
INSERT INTO aphs_vocabulary VALUES("2882","en","_SELECT","select");
INSERT INTO aphs_vocabulary VALUES("2885","en","_SELECTED_ROOMS","Selected Rooms");
INSERT INTO aphs_vocabulary VALUES("2888","en","_SELECT_FILE_TO_UPLOAD","Select a file to upload");
INSERT INTO aphs_vocabulary VALUES("2891","en","_SELECT_HOTEL","Select Hotel");
INSERT INTO aphs_vocabulary VALUES("2894","en","_SELECT_LANG_TO_UPDATE","Select a language to update");
INSERT INTO aphs_vocabulary VALUES("2897","en","_SELECT_LOCATION","Select Location");
INSERT INTO aphs_vocabulary VALUES("2900","en","_SELECT_REPORT_ALERT","Please select a report type!");
INSERT INTO aphs_vocabulary VALUES("2903","en","_SEND","Send");
INSERT INTO aphs_vocabulary VALUES("2906","en","_SENDING","Sending");
INSERT INTO aphs_vocabulary VALUES("2909","en","_SEND_COPY_TO_ADMIN","Send a copy to admin");
INSERT INTO aphs_vocabulary VALUES("2912","en","_SEND_INVOICE","Send Invoice");
INSERT INTO aphs_vocabulary VALUES("2915","en","_SEO_LINKS_ALERT","If you select this option, make sure SEO Links Section uncommented in .htaccess file");
INSERT INTO aphs_vocabulary VALUES("2918","en","_SEO_URLS","SEO URLs");
INSERT INTO aphs_vocabulary VALUES("2921","en","_SEPTEMBER","September");
INSERT INTO aphs_vocabulary VALUES("2924","en","_SERVER_INFO","Server Info");
INSERT INTO aphs_vocabulary VALUES("2927","en","_SERVER_LOCALE","Server Locale");
INSERT INTO aphs_vocabulary VALUES("2930","en","_SERVICE","Service");
INSERT INTO aphs_vocabulary VALUES("2933","en","_SERVICES","Services");
INSERT INTO aphs_vocabulary VALUES("2936","en","_SETTINGS","Settings");
INSERT INTO aphs_vocabulary VALUES("2939","en","_SETTINGS_SAVED","Changes were saved! Please refresh the <a href=index.php>Home Page</a> to see the results.");
INSERT INTO aphs_vocabulary VALUES("2942","en","_SET_ADMIN","Set Admin");
INSERT INTO aphs_vocabulary VALUES("2945","en","_SET_DATE","Set date");
INSERT INTO aphs_vocabulary VALUES("2948","en","_SET_TIME","Set Time");
INSERT INTO aphs_vocabulary VALUES("2951","en","_SHORT_DESCRIPTION","Short Description");
INSERT INTO aphs_vocabulary VALUES("2954","en","_SHOW","Show");
INSERT INTO aphs_vocabulary VALUES("2957","en","_SHOW_IN_SEARCH","Show in Search");
INSERT INTO aphs_vocabulary VALUES("2960","en","_SHOW_META_TAGS","Show META tags");
INSERT INTO aphs_vocabulary VALUES("2963","en","_SIMPLE","Simple");
INSERT INTO aphs_vocabulary VALUES("2966","en","_SITE_DEVELOPMENT_MODE_ALERT","The site is running in Development Mode! To turn it off change <b>SITE_MODE</b> value in <b>inc/settings.inc.php</b>");
INSERT INTO aphs_vocabulary VALUES("2969","en","_SITE_INFO","Site Info");
INSERT INTO aphs_vocabulary VALUES("2972","en","_SITE_OFFLINE","Site Offline");
INSERT INTO aphs_vocabulary VALUES("2975","en","_SITE_OFFLINE_ALERT","Select whether access to the Site Front-end is available. If Yes, the Front-End will display the message below");
INSERT INTO aphs_vocabulary VALUES("2978","en","_SITE_OFFLINE_MESSAGE_ALERT","A message that displays in the Front-end if your site is offline");
INSERT INTO aphs_vocabulary VALUES("2981","en","_SITE_PREVIEW","Site Preview");
INSERT INTO aphs_vocabulary VALUES("2984","en","_SITE_RANKS","Site Ranks");
INSERT INTO aphs_vocabulary VALUES("2987","en","_SITE_RSS","Site RSS");
INSERT INTO aphs_vocabulary VALUES("2990","en","_SITE_SETTINGS","Site Settings");
INSERT INTO aphs_vocabulary VALUES("2993","en","_SMTP_HOST","SMTP Host");
INSERT INTO aphs_vocabulary VALUES("2996","en","_SMTP_PORT","SMTP Port");
INSERT INTO aphs_vocabulary VALUES("2999","en","_SMTP_SECURE","SMTP Secure");
INSERT INTO aphs_vocabulary VALUES("3002","en","_SORT_BY","Sort by");
INSERT INTO aphs_vocabulary VALUES("3005","en","_STANDARD","Standard");
INSERT INTO aphs_vocabulary VALUES("3008","en","_STANDARD_CAMPAIGN","Targeting Period Campaign");
INSERT INTO aphs_vocabulary VALUES("3011","en","_STANDARD_PRICE","Standard Price");
INSERT INTO aphs_vocabulary VALUES("3014","en","_STARS","Stars");
INSERT INTO aphs_vocabulary VALUES("3017","en","_STARS_1_5","1 star to 5 stars");
INSERT INTO aphs_vocabulary VALUES("3020","en","_STARS_5_1","5 stars to 1 star");
INSERT INTO aphs_vocabulary VALUES("3023","en","_START_DATE","Start Date");
INSERT INTO aphs_vocabulary VALUES("3026","en","_START_FINISH_DATE_ERROR","Finish date must be later than start date! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3029","en","_STATE","State");
INSERT INTO aphs_vocabulary VALUES("3032","en","_STATE_PROVINCE","State/Province");
INSERT INTO aphs_vocabulary VALUES("3035","en","_STATISTICS","Statistics");
INSERT INTO aphs_vocabulary VALUES("3038","en","_STATUS","Status");
INSERT INTO aphs_vocabulary VALUES("3041","en","_STOP","Stop");
INSERT INTO aphs_vocabulary VALUES("3044","en","_SU","Su");
INSERT INTO aphs_vocabulary VALUES("3047","en","_SUBJECT","Subject");
INSERT INTO aphs_vocabulary VALUES("3050","en","_SUBJECT_EMPTY_ALERT","Subject cannot be empty!");
INSERT INTO aphs_vocabulary VALUES("3053","en","_SUBMIT","Submit");
INSERT INTO aphs_vocabulary VALUES("3056","en","_SUBMIT_BOOKING","Submit Booking");
INSERT INTO aphs_vocabulary VALUES("3059","en","_SUBMIT_PAYMENT","Submit Payment");
INSERT INTO aphs_vocabulary VALUES("3062","en","_SUBSCRIBE","Subscribe");
INSERT INTO aphs_vocabulary VALUES("3065","en","_SUBSCRIBE_EMAIL_EXISTS_ALERT","Someone with such email has already been subscribed to our newsletter. Please choose another email address for subscription.");
INSERT INTO aphs_vocabulary VALUES("3068","en","_SUBSCRIBE_TO_NEWSLETTER","Newsletter Subscription");
INSERT INTO aphs_vocabulary VALUES("3071","en","_SUBSCRIPTION_ALREADY_SENT","You have already subscribed to our newsletter. Please try again later or wait _WAIT_ seconds.");
INSERT INTO aphs_vocabulary VALUES("3074","en","_SUBSCRIPTION_MANAGEMENT","Subscription Management");
INSERT INTO aphs_vocabulary VALUES("3077","en","_SUBTOTAL","Subtotal");
INSERT INTO aphs_vocabulary VALUES("3080","en","_SUN","Sun");
INSERT INTO aphs_vocabulary VALUES("3083","en","_SUNDAY","Sunday");
INSERT INTO aphs_vocabulary VALUES("3086","en","_SWITCH_TO_EXPORT","Switch to Export");
INSERT INTO aphs_vocabulary VALUES("3089","en","_SWITCH_TO_NORMAL","Switch to Normal");
INSERT INTO aphs_vocabulary VALUES("3092","en","_SYMBOL","Symbol");
INSERT INTO aphs_vocabulary VALUES("3095","en","_SYMBOL_PLACEMENT","Symbol Placement");
INSERT INTO aphs_vocabulary VALUES("3098","en","_SYSTEM","System");
INSERT INTO aphs_vocabulary VALUES("3101","en","_SYSTEM_EMAIL_DELETE_ALERT","This email template is used by the system and cannot be deleted!");
INSERT INTO aphs_vocabulary VALUES("3104","en","_SYSTEM_MODULE","System Module");
INSERT INTO aphs_vocabulary VALUES("3107","en","_SYSTEM_MODULES","System Modules");
INSERT INTO aphs_vocabulary VALUES("3110","en","_SYSTEM_MODULE_ACTIONS_BLOCKED","All operations with system module are blocked!");
INSERT INTO aphs_vocabulary VALUES("3113","en","_SYSTEM_TEMPLATE","System Template");
INSERT INTO aphs_vocabulary VALUES("3116","en","_TAG","Tag");
INSERT INTO aphs_vocabulary VALUES("3119","en","_TAG_TITLE_IS_EMPTY","Tag &lt;TITLE&gt; cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3122","en","_TARGET","Target");
INSERT INTO aphs_vocabulary VALUES("3125","en","_TARGET_GROUP","Target Group");
INSERT INTO aphs_vocabulary VALUES("3128","en","_TAXES","Taxes");
INSERT INTO aphs_vocabulary VALUES("3131","en","_TEMPLATES_STYLES","Templates & Styles");
INSERT INTO aphs_vocabulary VALUES("3134","en","_TEMPLATE_CODE","Template Code");
INSERT INTO aphs_vocabulary VALUES("3137","en","_TEMPLATE_IS_EMPTY","Template cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3140","en","_TERMS","Terms & Conditions");
INSERT INTO aphs_vocabulary VALUES("3143","en","_TESTIMONIALS","Testimonials");
INSERT INTO aphs_vocabulary VALUES("3146","en","_TESTIMONIALS_MANAGEMENT","Testimonials Management");
INSERT INTO aphs_vocabulary VALUES("3149","en","_TESTIMONIALS_SETTINGS","Testimonials Settings");
INSERT INTO aphs_vocabulary VALUES("3152","en","_TEST_EMAIL","Test Email");
INSERT INTO aphs_vocabulary VALUES("3155","en","_TEST_MODE_ALERT","Test Mode in Reservation Cart is turned ON! To change current mode click <a href=index.php?admin=mod_booking_settings>here</a>.");
INSERT INTO aphs_vocabulary VALUES("3158","en","_TEST_MODE_ALERT_SHORT","Attention: Reservation Cart is running in Test Mode!");
INSERT INTO aphs_vocabulary VALUES("3161","en","_TEXT","Text");
INSERT INTO aphs_vocabulary VALUES("3164","en","_TH","Th");
INSERT INTO aphs_vocabulary VALUES("3167","en","_THU","Thu");
INSERT INTO aphs_vocabulary VALUES("3170","en","_THUMBNAIL","Thumbnail");
INSERT INTO aphs_vocabulary VALUES("3173","en","_THURSDAY","Thursday");
INSERT INTO aphs_vocabulary VALUES("3176","en","_TIME_PERIOD_OVERLAPPING_ALERT","This period of time (fully or partially) was already selected! Please choose another.");
INSERT INTO aphs_vocabulary VALUES("3179","en","_TIME_ZONE","Time Zone");
INSERT INTO aphs_vocabulary VALUES("3182","en","_TO","To");
INSERT INTO aphs_vocabulary VALUES("3185","en","_TODAY","Today");
INSERT INTO aphs_vocabulary VALUES("3188","en","_TOP","Top");
INSERT INTO aphs_vocabulary VALUES("3191","en","_TOTAL","Total");
INSERT INTO aphs_vocabulary VALUES("3194","en","_TOTAL_PRICE","Total Price");
INSERT INTO aphs_vocabulary VALUES("3197","en","_TOTAL_ROOMS","Total Rooms");
INSERT INTO aphs_vocabulary VALUES("3200","en","_TRANSACTION","Transaction");
INSERT INTO aphs_vocabulary VALUES("3203","en","_TRANSLATE_VIA_GOOGLE","Translate via Google");
INSERT INTO aphs_vocabulary VALUES("3206","en","_TRASH","Trash");
INSERT INTO aphs_vocabulary VALUES("3209","en","_TRASH_PAGES","Trash Pages");
INSERT INTO aphs_vocabulary VALUES("3212","en","_TRUNCATE_RELATED_TABLES","Truncate related tables?");
INSERT INTO aphs_vocabulary VALUES("3215","en","_TRY_LATER","An error occurred while executing. Please try again later!");
INSERT INTO aphs_vocabulary VALUES("3218","en","_TRY_SYSTEM_SUGGESTION","Try out system suggestion");
INSERT INTO aphs_vocabulary VALUES("3221","en","_TU","Tu");
INSERT INTO aphs_vocabulary VALUES("3224","en","_TUE","Tue");
INSERT INTO aphs_vocabulary VALUES("3227","en","_TUESDAY","Tuesday");
INSERT INTO aphs_vocabulary VALUES("3230","en","_TYPE","Type");
INSERT INTO aphs_vocabulary VALUES("3233","en","_TYPE_CHARS","Type the characters you see in the picture");
INSERT INTO aphs_vocabulary VALUES("3236","en","_UNCATEGORIZED","Uncategorized");
INSERT INTO aphs_vocabulary VALUES("3239","en","_UNDEFINED","undefined");
INSERT INTO aphs_vocabulary VALUES("3242","en","_UNINSTALL","Uninstall");
INSERT INTO aphs_vocabulary VALUES("3245","en","_UNITS","Units");
INSERT INTO aphs_vocabulary VALUES("3248","en","_UNIT_PRICE","Unit Price");
INSERT INTO aphs_vocabulary VALUES("3251","en","_UNKNOWN","Unknown");
INSERT INTO aphs_vocabulary VALUES("3254","en","_UNSUBSCRIBE","Unsubscribe");
INSERT INTO aphs_vocabulary VALUES("3257","en","_UP","Up");
INSERT INTO aphs_vocabulary VALUES("3260","en","_UPDATING_ACCOUNT","Updating Account");
INSERT INTO aphs_vocabulary VALUES("3263","en","_UPDATING_ACCOUNT_ERROR","An error occurred while updating your account! Please try again later or send information about this error to administration of the site.");
INSERT INTO aphs_vocabulary VALUES("3266","en","_UPDATING_OPERATION_COMPLETED","Updating operation was successfully completed!");
INSERT INTO aphs_vocabulary VALUES("3269","en","_UPLOAD","Upload");
INSERT INTO aphs_vocabulary VALUES("3272","en","_UPLOAD_AND_PROCCESS","Upload and Process");
INSERT INTO aphs_vocabulary VALUES("3275","en","_UPLOAD_FROM_FILE","Upload from File");
INSERT INTO aphs_vocabulary VALUES("3278","en","_URL","URL");
INSERT INTO aphs_vocabulary VALUES("3281","en","_USED_ON","Used On");
INSERT INTO aphs_vocabulary VALUES("3284","en","_USERNAME","Username");
INSERT INTO aphs_vocabulary VALUES("3287","en","_USERNAME_AND_PASSWORD","Username & Password");
INSERT INTO aphs_vocabulary VALUES("3290","en","_USERNAME_EMPTY_ALERT","Username cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3293","en","_USERNAME_LENGTH_ALERT","The length of username cannot be less than 4 characters! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3296","en","_USERS","Users");
INSERT INTO aphs_vocabulary VALUES("3299","en","_USER_EMAIL_EXISTS_ALERT","User with such email already exists! Please choose another.");
INSERT INTO aphs_vocabulary VALUES("3302","en","_USER_EXISTS_ALERT","User with such username already exists! Please choose another.");
INSERT INTO aphs_vocabulary VALUES("3305","en","_USER_NAME","User name");
INSERT INTO aphs_vocabulary VALUES("3308","en","_USE_THIS_PASSWORD","Use this password");
INSERT INTO aphs_vocabulary VALUES("3311","en","_VALUE","Value");
INSERT INTO aphs_vocabulary VALUES("3314","en","_VAT","VAT");
INSERT INTO aphs_vocabulary VALUES("3317","en","_VAT_PERCENT","VAT Percent");
INSERT INTO aphs_vocabulary VALUES("3320","en","_VERSION","Version");
INSERT INTO aphs_vocabulary VALUES("3323","en","_VIDEO","Video");
INSERT INTO aphs_vocabulary VALUES("3326","en","_VIEW_WORD","View");
INSERT INTO aphs_vocabulary VALUES("3329","en","_VISITOR","Visitor");
INSERT INTO aphs_vocabulary VALUES("3332","en","_VISUAL_SETTINGS","Visual Settings");
INSERT INTO aphs_vocabulary VALUES("3335","en","_VOCABULARY","Vocabulary");
INSERT INTO aphs_vocabulary VALUES("3338","en","_VOC_KEYS_UPDATED","Operation was successfully completed. Updated: _KEYS_ keys. Click <a href=\'index.php?admin=vocabulary&filter_by=A\'>here</a> to refresh the site.");
INSERT INTO aphs_vocabulary VALUES("3341","en","_VOC_KEY_UPDATED","Vocabulary key was successfully updated.");
INSERT INTO aphs_vocabulary VALUES("3344","en","_VOC_KEY_VALUE_EMPTY","Key value cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3347","en","_VOC_NOT_FOUND","No keys found");
INSERT INTO aphs_vocabulary VALUES("3350","en","_VOC_UPDATED","Vocabulary was successfully updated. Click <a href=index.php>here</a> to refresh the site.");
INSERT INTO aphs_vocabulary VALUES("3353","en","_WE","We");
INSERT INTO aphs_vocabulary VALUES("3356","en","_WEB_SITE","Web Site");
INSERT INTO aphs_vocabulary VALUES("3359","en","_WED","Wed");
INSERT INTO aphs_vocabulary VALUES("3362","en","_WEDNESDAY","Wednesday");
INSERT INTO aphs_vocabulary VALUES("3365","en","_WEEK_START_DAY","Week Start Day");
INSERT INTO aphs_vocabulary VALUES("3368","en","_WELCOME_CUSTOMER_TEXT","<p>Hello <b>_FIRST_NAME_ _LAST_NAME_</b>!</p>        \n<p>Welcome to Customer Account Panel, that allows you to view account status, manage your account settings and bookings.</p>\n<p>\n   _TODAY_<br />\n   _LAST_LOGIN_\n</p>				\n<p> <b>&#8226;</b> To view this account summary just click on a <a href=\'index.php?customer=home\'>Dashboard</a> link.</p>\n<p> <b>&#8226;</b> <a href=\'index.php?customer=my_account\'>Edit My Account</a> menu allows you to change your personal info and account data.</p>\n<p> <b>&#8226;</b> <a href=\'index.php?customer=my_bookings\'>My Bookings</a> contains information about your orders.</p>\n<p><br /></p>");
INSERT INTO aphs_vocabulary VALUES("3371","en","_WHAT_IS_CVV","What is CVV");
INSERT INTO aphs_vocabulary VALUES("3374","en","_WHOLE_SITE","Whole site");
INSERT INTO aphs_vocabulary VALUES("3377","en","_WITHOUT_ACCOUNT","without account");
INSERT INTO aphs_vocabulary VALUES("3380","en","_WRONG_BOOKING_NUMBER","The booking number you\'ve entered was not found! Please enter a valid booking number.");
INSERT INTO aphs_vocabulary VALUES("3383","en","_WRONG_CHECKOUT_DATE_ALERT","Wrong date selected! Please choose a valid check-out date.");
INSERT INTO aphs_vocabulary VALUES("3386","en","_WRONG_CODE_ALERT","Sorry, the code you have entered was invalid! Please try again.");
INSERT INTO aphs_vocabulary VALUES("3389","en","_WRONG_CONFIRMATION_CODE","Wrong confirmation code or your registration was already confirmed!");
INSERT INTO aphs_vocabulary VALUES("3392","en","_WRONG_COUPON_CODE","This coupon code is invalid or has expired!");
INSERT INTO aphs_vocabulary VALUES("3395","en","_WRONG_FILE_TYPE","Uploaded file is not a valid PHP vocabulary file! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3398","en","_WRONG_LOGIN","Wrong username or password!");
INSERT INTO aphs_vocabulary VALUES("3401","en","_WRONG_PARAMETER_PASSED","Wrong parameters passed - cannot complete operation!");
INSERT INTO aphs_vocabulary VALUES("3404","en","_WYSIWYG_EDITOR","WYSIWYG Editor");
INSERT INTO aphs_vocabulary VALUES("3407","en","_YEAR","Year");
INSERT INTO aphs_vocabulary VALUES("3410","en","_YES","Yes");
INSERT INTO aphs_vocabulary VALUES("3413","en","_YOUR_EMAIL","Your Email");
INSERT INTO aphs_vocabulary VALUES("3416","en","_YOUR_NAME","Your Name");
INSERT INTO aphs_vocabulary VALUES("3419","en","_YOU_ARE_LOGGED_AS","You are logged in as");
INSERT INTO aphs_vocabulary VALUES("3422","en","_ZIPCODE_EMPTY_ALERT","Zip/Postal code cannot be empty! Please re-enter.");
INSERT INTO aphs_vocabulary VALUES("3425","en","_ZIP_CODE","Zip/Postal code");



