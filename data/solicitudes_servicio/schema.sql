

DROP TABLE IF EXISTS `service_status`;
CREATE TABLE `service_status`  (
  `service_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_status_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `service_status_commentary` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`service_status_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `service_type`;
CREATE TABLE `service_type`  (
  `service_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type_name` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `service_type_commentary` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`service_type_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

DROP TABLE IF EXISTS `service`;
CREATE TABLE `service`  (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `service_type_id` int(11) NULL DEFAULT NULL,
  `service_status_id` int(11) NULL DEFAULT NULL,
  `service_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `service_organism` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `service_phone` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `service_commentary` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`service_id`) USING BTREE,
  INDEX `service_ibfk_1`(`user_id`) USING BTREE,
  INDEX `service_ibfk_2`(`service_type_id`) USING BTREE,
  INDEX `service_ibfk_3`(`service_status_id`) USING BTREE,
  INDEX `service_ibfk_4`(`node_id`) USING BTREE,
  CONSTRAINT `service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_type` (`service_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `service_ibfk_3` FOREIGN KEY (`service_status_id`) REFERENCES `service_status` (`service_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `service_ibfk_4` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;

DROP TABLE IF EXISTS `service_log`;
CREATE TABLE `service_log`  (
  `service_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `service_log_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `service_log_detail` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`service_log_id`) USING BTREE,
  INDEX `service_log_ibfk_1`(`service_id`) USING BTREE,
  INDEX `service_log_ibfk_2`(`user_id`) USING BTREE,
  CONSTRAINT `service_log_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;
