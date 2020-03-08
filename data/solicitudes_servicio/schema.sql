DROP TABLE IF EXISTS `service_status`;
CREATE TABLE `service_status`  (
  `service_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_status_name` varchar(255) NULL DEFAULT NULL,
  `service_status_commentary` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`service_status_id`)
);


DROP TABLE IF EXISTS `service_type`;
CREATE TABLE `service_type`  (
  `service_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type_name` varchar(255) NOT NULL,
  `service_type_commentary` varchar(255) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`service_type_id`),
  INDEX `user_id`(`user_id`),
  CONSTRAINT `service_type_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS `service`;
CREATE TABLE `service`  (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `request_evaluation_id` int(11) NULL,
  `service_type_id` int(11) NULL DEFAULT NULL,
  `service_status_id` int(11) NULL DEFAULT NULL,
  `service_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `service_organism` varchar(255) NULL DEFAULT NULL,
  `service_phone` varchar(255) NULL DEFAULT NULL,
  `service_commentary` varchar(255) NULL DEFAULT NULL,
  `service_reject` varchar(2000) NULL DEFAULT NULL,
  PRIMARY KEY (`service_id`),
  INDEX `user_id`(`user_id`),
  INDEX `service_type_id`(`service_type_id`),
  INDEX `service_status_id`(`service_status_id`),
  INDEX `node_id`(`node_id`),
  INDEX `request_evaluation`(`request_evaluation_id`),
  CONSTRAINT `service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_type` (`service_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `service_ibfk_3` FOREIGN KEY (`service_status_id`) REFERENCES `service_status` (`service_status_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `service_ibfk_4` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_ibfk_5` FOREIGN KEY (`request_evaluation_id`) REFERENCES `request_evaluation` (`request_evaluation_id`) ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS `service_log`;
CREATE TABLE `service_log`  (
  `service_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `service_log_date` timestamp(0) NULL DEFAULT CURRENT_TIMESTAMP,
  `service_log_detail` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`service_log_id`),
  INDEX `service_id`(`service_id`),
  INDEX `user_id`(`user_id`),
  CONSTRAINT `service_log_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);


DROP TABLE IF EXISTS `solicitud_estado`;
CREATE TABLE `solicitud_estado`  (
  `solicitud_estado_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_estado_nombre` varchar(255) NULL DEFAULT NULL,
  `solicitud_estado_comentario` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`solicitud_estado_id`)
);


DROP TABLE IF EXISTS `solicitud_type`;
CREATE TABLE `solicitud_type`  (
  `solicitud_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_type_nombre` varchar(255) NULL DEFAULT NULL,
  `solicitud_type_comentario` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`solicitud_type_id`)
);


DROP TABLE IF EXISTS `solicitud`;
CREATE TABLE `solicitud`  (
  `solicitud_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NULL DEFAULT NULL,
  `user_id` int(11) NULL DEFAULT NULL,
  `solicitud_type_id` int(11) NULL DEFAULT NULL,
  `solicitud_estado_id` int(11) NULL DEFAULT NULL,
  `solicitud_fecha` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solicitud_folio` varchar(255) NULL DEFAULT NULL,
  `solicitud_factura_archivo` varchar(255) NULL DEFAULT NULL,
  `solicitud_factura_nombre` varchar(255) NULL DEFAULT NULL,
  `solicitud_factura_numero` varchar(255) NULL DEFAULT NULL,
  `solicitud_oc_archivo` varchar(255) NULL DEFAULT NULL,
  `solicitud_oc_nombre` varchar(255) NULL DEFAULT NULL,
  `solicitud_oc_numero` varchar(255) NULL DEFAULT NULL,
  `solicitud_comen_user` varchar(255) NULL DEFAULT NULL,
  `solicitud_comen_admin` varchar(255) NULL DEFAULT NULL,
  PRIMARY KEY (`solicitud_id`),
  INDEX `user_id`(`user_id`),
  INDEX `solicitud_type_id`(`solicitud_type_id`),
  INDEX `solicitud_estado_id`(`solicitud_estado_id`),
  INDEX `node_id`(`node_id`),
  CONSTRAINT `solicitud_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `solicitud_ibfk_2` FOREIGN KEY (`solicitud_type_id`) REFERENCES `solicitud_type` (`solicitud_type_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `solicitud_ibfk_3` FOREIGN KEY (`solicitud_estado_id`) REFERENCES `solicitud_estado` (`solicitud_estado_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `solicitud_ibfk_4` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1s CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `solicitud_log`;
CREATE TABLE `solicitud_log`  (
  `solicitud_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `solicitud_log_fecha` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solicitud_log_detalle` varchar(255) NOT NULL,
  PRIMARY KEY (`solicitud_log_id`),
  INDEX `solicitud_id`(`solicitud_id`),
  INDEX `user_id`(`user_id`),
  CONSTRAINT `solicitud_log_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitud` (`solicitud_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `solicitud_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
);