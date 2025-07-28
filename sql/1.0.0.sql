CREATE TABLE IF NOT EXISTS `email_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL UNIQUE COMMENT '模板代码',
  `name` varchar(100) NOT NULL COMMENT '模板名称',
  `subject` varchar(255) NOT NULL COMMENT '邮件标题',
  `content` text NOT NULL COMMENT '邮件内容',
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件模板';