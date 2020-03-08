CREATE TABLE `request_evaluation`  (
  `request_evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_evaluation_name` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`request_evaluation_id`)
);

CREATE TABLE `rdi_status`  (
  `rdi_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `rdi_status_name` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`rdi_status_id`)
);

CREATE TABLE `rdi_admin`  (
  `rdi_admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`rdi_admin_id`),
  INDEX `rdi_admin_ibfk_1`(`user_id`),
  CONSTRAINT `rdi_admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE `rdi`  (
  `rdi_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,  
  `request_evaluation_id` int(11) NULL,
  `rdi_admin_id` int(11) NULL DEFAULT NULL,
  `rdi_description` varchar(1000) NULL DEFAULT NULL,
  `rdi_reject` varchar(2000) NULL DEFAULT NULL,
  `rdi_status_id` int(11) NULL DEFAULT NULL,
  `rdi_organism` varchar(255) NULL DEFAULT NULL,
  `rdi_phone` varchar(255) NULL DEFAULT NULL,
  `rdi_created_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `rdi_updated_at` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rdi_token` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`rdi_id`),
  INDEX `rdi_ibfk_1`(`node_id`),
  INDEX `rdi_ibfk_2`(`user_id`),
  INDEX `rdi_ibfk_3`(`rdi_status_id`),
  INDEX `rdi_ibfk_4`(`request_evaluation_id`),
  INDEX `rdi_ibfk_5`(`rdi_admin_id`),
  CONSTRAINT `rdi_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rdi_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rdi_ibfk_3` FOREIGN KEY (`rdi_status_id`) REFERENCES `rdi_status` (`rdi_status_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rdi_ibfk_4` FOREIGN KEY (`request_evaluation_id`) REFERENCES `request_evaluation` (`request_evaluation_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `rdi_ibfk_5` FOREIGN KEY (`rdi_admin_id`) REFERENCES `rdi_admin` (`rdi_admin_id`) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE `rdi_log`  (
  `rdi_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `rdi_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `rdi_log_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP(0),
  `rdi_log_detail` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`rdi_log_id`),
  INDEX `rdi_id`(`rdi_id`),
  INDEX `user_id`(`user_id`),
  CONSTRAINT `rdi_log_ibfk_1` FOREIGN KEY (`rdi_id`) REFERENCES `rdi` (`rdi_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rdi_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);