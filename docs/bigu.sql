
-- 用户表
DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users` (
  `username` varchar(40) NOT NULL,
  `password` varchar(40) NOT NULL,
  `password_salt` varchar(32) NOT NULL,
  `real_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `status` enum('unapproved','approved') DEFAULT 'unapproved',
  `id_role` int(11) NOT NULL,
  PRIMARY KEY  USING BTREE (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

INSERT INTO `tbl_users` (`username`, `password`, `password_salt`, `status`, `id_role`) VALUES
('admin', '13956c93ab56025e9397ab69957418989ebab847', 'xcNsdaAd73328aDs73oQw223hd', 'approved', 3),
('enrico', 'ed64662ef2d8425bc7654e5d7a09fee0788b72ec', 'xcNsdaAd73328aDs73oQw223hd', 'approved', 2);


-- 角色表
DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(40) NOT NULL,
  `id_parent` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

INSERT INTO `tbl_roles` (`id`, `role`, `id_parent`) VALUES
(3, 'admin', 2),
(2, 'user', 1),
(1, 'guest', 0);


-- 资源表
DROP TABLE IF EXISTS `tbl_resources`;
CREATE TABLE `tbl_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource` varchar(40) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

INSERT INTO `tbl_resources` (`id`, `resource`) VALUES
(1, '*/*/*'),
(3, 'auth/index/index'),
(2, 'auth/*/*');


-- 角色资源权限对照表
DROP TABLE IF EXISTS `tbl_permissions`;
CREATE TABLE `tbl_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL,
  `id_resource` int(11) NOT NULL,
  `permission` enum('allow','deny') NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=UTF8;

-- DATA -- 插入示例数据
INSERT INTO `tbl_permissions` (`id`, `id_role`, `id_resource`, `permission`) VALUES
(1, 3, 1, 'allow'),
(2, 1, 2, 'allow'),
(3, 2, 3, 'deny'),
(4, 3, 3, 'allow'),
(5, 2, 1, 'allow');


-- 文章 -- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_post`;
CREATE TABLE `tbl_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  -- `url` varchar(256) NOT NULL, -- /post/post/view?id= -- [暂时不用]
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `abstract` varchar(1280) NOT NULL,-- 系统抽取摘要
  `pub_datetime` DATETIME NOT NULL,
  `author` varchar(40) NOT NULL,
  `category` varchar(60) DEFAULT NULL,
  `sub_category` varchar(60) DEFAULT NULL,
  `source` varchar(256) DEFAULT NULL,
  `view_count` INT DEFAULT 0,
  `comment_count` INT DEFAULT 0,
  `image_path` varchar(128) DEFAULT NULL,-- 以分号分隔，图片路径都存到服务端指定目录下 -- [暂时不用]
  `index_thumb` varchar(128) DEFAULT NULL,-- 主List显示的缩略图片路径也在服务端另外指定目录下-- 系统抽取主页缩略图
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

-- 评论 -- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_comment`;
CREATE TABLE `tbl_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_post` int(11) NOT NULL,
  `content` text NOT NULL,
  `pub_datetime` DATETIME NOT NULL,
  `author` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

INSERT INTO `tbl_comment`(id_post,content,pub_datetime,author) VALUES('1', '测试评论内容', NOW(), 'admin');

INSERT INTO `tbl_post` VALUES (1,'这是我的测试标题１','<p>这是正文<strong><em><u>内容</u></em></strong>测试。<img alt=\"\" src=\"/upload/img/20131015/63f617ce3dfa72d681f3ab4a7f2963e1.png\" style=\"height:65px; width:114px\" /></p>\r\n','这是正文内容测试。\r\n','2013-10-15 15:43:19','admin','搜索技术','数据存储技术',NULL,0,0,NULL,'/upload/thumb/20131015/a78be6bf724a9c0589297e0abf8c20fd.jpg'),(2,'这是我的测试标题２','<p>这可<strong><s><u>是新</u></s></strong>内容<img alt=\"\" src=\"/upload/img/20131015/fbd1474c9044c4bd215ce9e2337618bf.jpg\" style=\"height:80px; width:665px\" /></p>\r\n','这可是新内容\r\n','2013-10-15 16:19:21','admin',NULL,NULL,NULL,0,0,NULL,'/upload/thumb/20131015/fbd1474c9044c4bd215ce9e2337618bf.jpg'),(3,'我爱自然语言处理１','<p><u><em><img alt=\"cool\" src=\"http://bigu1.local/include/ckeditor/plugins/smiley/images/shades_smile.gif\" style=\"height:20px; width:20px\" title=\"cool\" /></em></u></p>\r\n\r\n<em><u><strong>啦啦啦</strong></u></em></p>\r\n\r\n<p><img alt=\"\" src=\"/upload/img/20131015/050ef24d95ab9cd59d620fdcc2e991e7.png\" style=\"height:32px; width:88px\" /></p>\r\n','\r\n\r\n啦啦啦\r\n\r\n\r\n','2013-10-15 16:22:08','admin','搜索引擎','自然语言处理',NULL,0,0,NULL,'/upload/thumb/20131015/050ef24d95ab9cd59d620fdcc2e991e7.png');

/*
INSERT INTO `tbl_post`(title,content,abstract,pub_datetime,author,category,sub_category,source,view_count,comment_count,image_path,index_thumb) VALUES ('这是我的测试标题１','<p>这是正文<strong><em><u>内容</u></em></strong>测试。<img alt=\"\" src=\"/upload/img/20131015/63f617ce3dfa72d681f3ab4a7f2963e1.png\" style=\"height:65px; width:114px\" /></p>\r\n','这是正文内容测试。\r\n','2013-10-15 15:43:19','admin','搜索技术','数据存储技术',NULL,0,0,NULL,'/upload/thumb/20131015/a78be6bf724a9c0589297e0abf8c20fd.jpg'),('这是我的测试标题２','<p>这可<strong><s><u>是新</u></s></strong>内容<img alt=\"\" src=\"/upload/img/20131015/fbd1474c9044c4bd215ce9e2337618bf.jpg\" style=\"height:80px; width:665px\" /></p>\r\n','这可是新内容\r\n','2013-10-15 16:19:21','admin',NULL,NULL,NULL,0,0,NULL,'/upload/thumb/20131015/fbd1474c9044c4bd215ce9e2337618bf.jpg'),('我爱自然语言处理１','<p><u><em><img alt=\"cool\" src=\"http://bigu1.local/include/ckeditor/plugins/smiley/images/shades_smile.gif\" style=\"height:20px; width:20px\" title=\"cool\" /></em></u></p>\r\n\r\n<em><u><strong>啦啦啦</strong></u></em></p>\r\n\r\n<p><img alt=\"\" src=\"/upload/img/20131015/050ef24d95ab9cd59d620fdcc2e991e7.png\" style=\"height:32px; width:88px\" /></p>\r\n','\r\n\r\n啦啦啦\r\n\r\n\r\n','2013-10-15 16:22:08','admin','搜索引擎','自然语言处理',NULL,0,0,NULL,'/upload/thumb/20131015/050ef24d95ab9cd59d620fdcc2e991e7.png');
*/

-- 分类类别 -- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_category`;
CREATE TABLE `tbl_category` (
  `category` varchar(60) NOT NULL,
  `parent_category` varchar(60) DEFAULT NULL,
  `post_count` INT DEFAULT 0,
  PRIMARY KEY (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

-- 类别查询的话可以这样做：点击大类查询按大类做left like，按小类查询就按小类查询吧
INSERT INTO `tbl_category` VALUES ('搜索引擎','',0),('爬虫技术','搜索引擎',0),('自然语言处理','搜索引擎',0),('数据存储','搜索引擎',0),('互联网金融','',0);




















