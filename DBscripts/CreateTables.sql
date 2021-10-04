-- Available for 8.0.26-0ubuntu0.20.04.2

-- m_priority
-- CREATE TABLE `m_priority` (
--   `level` int NOT NULL,
--   `level_name` varchar(20) NOT NULL,
--   PRIMARY KEY (`level`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- m_status
-- CREATE TABLE `m_status` (
--   `status_id` int NOT NULL,
--   `status_name` varchar(20) NOT NULL,
--   `color_name` varchar(20) DEFAULT NULL,
--   PRIMARY KEY (`status_id`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_product
-- CREATE TABLE `t_product` (
--   `product_code` int NOT NULL,
--   `product_name` varchar(50) NOT NULL,
--   PRIMARY KEY (`product_code`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_ticket
CREATE TABLE `t_ticket` (
  `product_code` int NOT NULL,
  `ticket_id` int NOT NULL,
  `request_date` datetime NOT NULL,
  `request_user` int NOT NULL,
  `target` varchar(20) NOT NULL,
  `ticket_msg` varchar(255) NOT NULL,
  `priority_level` int NOT NULL,
  `is_show` varchar(45) DEFAULT NULL,
  `src_div` int DEFAULT NULL,
  `src_name` varchar(50) DEFAULT NULL,
  `user_affect` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_code`,`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_ticket_authority
CREATE TABLE `t_ticket_authority` (
  `product_code` int NOT NULL,
  `ticket_id` int NOT NULL,
  `user_code` int NOT NULL,
  `update_datetime` datetime NOT NULL,
  PRIMARY KEY (`product_code`,`ticket_id`,`user_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_ticket_lines
CREATE TABLE `t_ticket_lines` (
  `product_code` int NOT NULL,
  `ticket_id` int NOT NULL,
  `lines_no` int NOT NULL,
  `comment` varchar(255) NOT NULL,
  `last_update` datetime NOT NULL,
  `update_user` int DEFAULT NULL,
  PRIMARY KEY (`product_code`,`ticket_id`,`lines_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_ticket_status
CREATE TABLE `t_ticket_status` (
  `product_code` int NOT NULL,
  `ticket_id` int NOT NULL,
  `status` int NOT NULL,
  `update_datetime` datetime NOT NULL,
  `update_user` int NOT NULL,
  PRIMARY KEY (`product_code`,`ticket_id`,`update_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- t_user
-- CREATE TABLE `t_user` (
--   `user_code` int NOT NULL,
--   `user_id` varchar(20) NOT NULL,
--   `user_pw` varchar(20) NOT NULL,
--   `user_name` varchar(20) NOT NULL,
--   `is_delete` int NOT NULL,
--   `authority` tinyint NOT NULL,
--   PRIMARY KEY (`user_code`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

