
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
  `title` varchar(256) NOT NULL,
  `content` text NOT NULL,
  `pub_datetime` DATETIME NOT NULL,
  `author` varchar(40) NOT NULL,
  `category` varchar(60) DEFAULT NULL,
  `sub_category` varchar(60) DEFAULT NULL,
  `source` varchar(256) DEFAULT NULL,
  `view_count` INT DEFAULT 0,
  `comment_count` INT DEFAULT 0,
  `image_path` varchar(128) DEFAULT NULL,-- 以分号分隔，图片路径都存到服务端指定目录下
  `index_thumb` varchar(128) DEFAULT NULL,-- 主List显示的缩略图片路径也在服务端另外指定目录下
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

-- 分类类别 -- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `tbl_category`;
CREATE TABLE `tbl_category` (
  `category` varchar(60) NOT NULL,
  `parent_category` varchar(60) DEFAULT NULL,
  `post_count` INT DEFAULT 0
  PRIMARY KEY (`category`)
) ENGINE=MyISAM DEFAULT CHARSET=UTF8;

-- 类别查询的话可以这样做：点击大类查询按大类做left like，按小类查询就按小类查询吧





















