-- MySQL dump 10.13  Distrib 5.1.73, for redhat-linux-gnu (x86_64)
--
-- Host: igeo.ckhscbcb1e5x.us-east-1.rds.amazonaws.com    Database: igeo_template
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.31-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asset`
--

DROP TABLE IF EXISTS `asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `asset_type_id` int(11) DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  `asset_status_id` int(11) DEFAULT NULL,
  `asset_condition_id` int(11) DEFAULT NULL,
  `asset_load_id` int(11) DEFAULT NULL,
  `asset_document_count` int(11) DEFAULT NULL,
  `asset_num_factura` varchar(255) DEFAULT NULL,
  `asset_name` varchar(30) DEFAULT NULL,
  `asset_num_serie` varchar(50) DEFAULT NULL,
  `asset_num_serie_intern` varchar(50) DEFAULT NULL,
  `asset_cost` varchar(50) DEFAULT NULL,
  `asset_current_cost` varchar(50) DEFAULT NULL,
  `asset_purchase_date` date DEFAULT NULL,
  `asset_lifetime` int(11) NOT NULL,
  `asset_last_inventory` date DEFAULT NULL,
  `asset_expiration_date_lifetime` date DEFAULT NULL,
  `asset_description` text,
  `asset_estate` int(11) NOT NULL,
  `asset_upload_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `asset_load_date` date DEFAULT NULL,
  PRIMARY KEY (`asset_id`) USING BTREE,
  KEY `asset_ibfk_1` (`asset_type_id`) USING BTREE,
  KEY `asset_ibfk_2` (`asset_status_id`) USING BTREE,
  KEY `asset_ibfk_3` (`asset_condition_id`) USING BTREE,
  KEY `asset_ibfk_4` (`brand_id`) USING BTREE,
  KEY `asset_ibfk_5` (`provider_id`) USING BTREE,
  KEY `asset_ibfk_6` (`node_id`) USING BTREE,
  KEY `asset_load_7` (`asset_load_id`) USING BTREE,
  CONSTRAINT `asset_ibfk_1` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_type` (`asset_type_id`),
  CONSTRAINT `asset_ibfk_2` FOREIGN KEY (`asset_status_id`) REFERENCES `asset_status` (`asset_status_id`),
  CONSTRAINT `asset_ibfk_3` FOREIGN KEY (`asset_condition_id`) REFERENCES `asset_condition` (`asset_condition_id`),
  CONSTRAINT `asset_ibfk_4` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`),
  CONSTRAINT `asset_ibfk_5` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`),
  CONSTRAINT `asset_ibfk_6` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_load_7` FOREIGN KEY (`asset_load_id`) REFERENCES `asset_load` (`asset_load_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset`
--

LOCK TABLES `asset` WRITE;
/*!40000 ALTER TABLE `asset` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_condition`
--

DROP TABLE IF EXISTS `asset_condition`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_condition` (
  `asset_condition_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_condition_name` varchar(50) DEFAULT NULL,
  `asset_condition_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_condition_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_condition`
--

LOCK TABLES `asset_condition` WRITE;
/*!40000 ALTER TABLE `asset_condition` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_condition` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_document`
--

DROP TABLE IF EXISTS `asset_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_document` (
  `asset_document_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `asset_document_filename` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `asset_document_description` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `asset_document_comments` text COLLATE utf8_unicode_ci,
  `doc_extension_id` int(11) NOT NULL,
  `asset_document_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`asset_document_id`) USING BTREE,
  KEY `asset_document_ibfk_1` (`asset_id`) USING BTREE,
  KEY `asset_document_ibfk_2` (`doc_extension_id`) USING BTREE,
  KEY `asset_document_ibfk_3` (`user_id`) USING BTREE,
  CONSTRAINT `asset_document_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_document_ibfk_2` FOREIGN KEY (`doc_extension_id`) REFERENCES `doc_extension` (`doc_extension_id`),
  CONSTRAINT `asset_document_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_document`
--

LOCK TABLES `asset_document` WRITE;
/*!40000 ALTER TABLE `asset_document` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_insurance`
--

DROP TABLE IF EXISTS `asset_insurance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_insurance` (
  `asset_insurance_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  `asset_insurance_begin_date` date DEFAULT NULL,
  `asset_insurance_expiration_date` date DEFAULT NULL,
  `asset_insurance_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_insurance_id`) USING BTREE,
  KEY `asset_insurance_ibfk_1` (`provider_id`) USING BTREE,
  KEY `asset_insurance_ibfk_2` (`asset_id`) USING BTREE,
  CONSTRAINT `asset_insurance_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`),
  CONSTRAINT `asset_insurance_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_insurance`
--

LOCK TABLES `asset_insurance` WRITE;
/*!40000 ALTER TABLE `asset_insurance` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_insurance` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_inventory`
--

DROP TABLE IF EXISTS `asset_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_inventory` (
  `asset_inventory_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `asset_inventory_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`asset_inventory_id`) USING BTREE,
  KEY `asset_inventory_ibfk_1` (`user_id`) USING BTREE,
  KEY `asset_inventory_ibfk_2` (`asset_id`) USING BTREE,
  KEY `asset_inventory_ibfk_3` (`node_id`) USING BTREE,
  CONSTRAINT `asset_inventory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_inventory_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_inventory_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_inventory`
--

LOCK TABLES `asset_inventory` WRITE;
/*!40000 ALTER TABLE `asset_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_inventory_auxiliar`
--

DROP TABLE IF EXISTS `asset_inventory_auxiliar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_inventory_auxiliar` (
  `asset_inventory_auxiliar_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `asset_inventory_barra` varchar(50) DEFAULT NULL,
  `asset_inventory_interno` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`asset_inventory_auxiliar_id`) USING BTREE,
  KEY `asset_inventory_auxiliar_ibfk_1` (`user_id`) USING BTREE,
  CONSTRAINT `asset_inventory_auxiliar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_inventory_auxiliar`
--

LOCK TABLES `asset_inventory_auxiliar` WRITE;
/*!40000 ALTER TABLE `asset_inventory_auxiliar` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_inventory_auxiliar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_inventory_auxiliar_proceso`
--

DROP TABLE IF EXISTS `asset_inventory_auxiliar_proceso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_inventory_auxiliar_proceso` (
  `asset_inventory_auxiliar_proceso_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `asset_name` varchar(30) NOT NULL,
  `brand_name` varchar(50) NOT NULL,
  `asset_num_serie_intern` varchar(50) NOT NULL,
  `codigo_auge` varchar(50) NOT NULL,
  `original_location` varchar(300) NOT NULL,
  `departamento_original` varchar(70) NOT NULL,
  `nombre_subrecinto_original` varchar(70) NOT NULL,
  `location_transfer` varchar(300) NOT NULL,
  `departamento_transfer` varchar(70) NOT NULL,
  `nombre_subrecinto_transfer` varchar(70) NOT NULL,
  `situacion` varchar(50) NOT NULL,
  `asset_num_serie` varchar(255) DEFAULT NULL,
  `asset_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_inventory_auxiliar_proceso_id`) USING BTREE,
  KEY `asset_inventory_auxiliar_proceso_ibfk_1` (`node_id`) USING BTREE,
  KEY `asset_inventory_auxiliar_proceso_ibfk_2` (`user_id`) USING BTREE,
  CONSTRAINT `asset_inventory_auxiliar_proceso_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_inventory_auxiliar_proceso_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_inventory_auxiliar_proceso`
--

LOCK TABLES `asset_inventory_auxiliar_proceso` WRITE;
/*!40000 ALTER TABLE `asset_inventory_auxiliar_proceso` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_inventory_auxiliar_proceso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_load`
--

DROP TABLE IF EXISTS `asset_load`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_load` (
  `asset_load_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `asset_load_date` date NOT NULL,
  `asset_load_folio` varchar(11) NOT NULL,
  `asset_load_comment` text,
  `asset_load_foot_signature1` varchar(255) DEFAULT NULL,
  `asset_load_foot_signature2` varchar(255) DEFAULT NULL,
  `asset_load_foot_signature3` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_load_id`) USING BTREE,
  KEY `asset_load_ibfk_1` (`user_id`) USING BTREE,
  KEY `asset_load_folio` (`asset_load_folio`) USING BTREE,
  CONSTRAINT `asset_load_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_load`
--

LOCK TABLES `asset_load` WRITE;
/*!40000 ALTER TABLE `asset_load` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_load` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_log`
--

DROP TABLE IF EXISTS `asset_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_log` (
  `asset_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `asset_log_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `asset_log_type` varchar(50) DEFAULT NULL,
  `asset_log_detail` varchar(255) NOT NULL,
  PRIMARY KEY (`asset_log_id`) USING BTREE,
  KEY `asset_log_ibfk_1` (`user_id`) USING BTREE,
  KEY `asset_log_ibfk_2` (`asset_id`) USING BTREE,
  CONSTRAINT `asset_log_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_log`
--

LOCK TABLES `asset_log` WRITE;
/*!40000 ALTER TABLE `asset_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_measurement`
--

DROP TABLE IF EXISTS `asset_measurement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_measurement` (
  `asset_measurement_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `measure_unit_id` int(11) DEFAULT NULL,
  `asset_measurement_date` date DEFAULT NULL,
  `asset_measurement_cantity` float(10,2) DEFAULT NULL,
  `asset_measurement_comments` text,
  `asset_measurement_last_insert` int(11) DEFAULT NULL,
  PRIMARY KEY (`asset_measurement_id`) USING BTREE,
  KEY `asset_measurement_ibfk_2` (`measure_unit_id`) USING BTREE,
  KEY `asset_measurement_ibfk_1` (`asset_id`) USING BTREE,
  CONSTRAINT `asset_measurement_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_measurement_ibfk_2` FOREIGN KEY (`measure_unit_id`) REFERENCES `measure_unit` (`measure_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_measurement`
--

LOCK TABLES `asset_measurement` WRITE;
/*!40000 ALTER TABLE `asset_measurement` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_measurement` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_other_data_attribute`
--

DROP TABLE IF EXISTS `asset_other_data_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_other_data_attribute` (
  `asset_other_data_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_other_data_attribute_name` varchar(100) NOT NULL,
  PRIMARY KEY (`asset_other_data_attribute_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_other_data_attribute`
--

LOCK TABLES `asset_other_data_attribute` WRITE;
/*!40000 ALTER TABLE `asset_other_data_attribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_other_data_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_other_data_attribute_asset_type`
--

DROP TABLE IF EXISTS `asset_other_data_attribute_asset_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_other_data_attribute_asset_type` (
  `asset_other_data_attribute_asset_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_other_data_attribute_id` int(11) NOT NULL,
  `asset_type_id` int(11) NOT NULL,
  `asset_other_data_attribute_asset_type_order` int(11) NOT NULL,
  PRIMARY KEY (`asset_other_data_attribute_asset_type_id`) USING BTREE,
  KEY `asset_other_data_attribute_asset_type_ibfk_1` (`asset_other_data_attribute_id`) USING BTREE,
  KEY `asset_other_data_attribute_asset_type_ibfk_2` (`asset_type_id`) USING BTREE,
  CONSTRAINT `asset_other_data_attribute_asset_type_ibfk_1` FOREIGN KEY (`asset_other_data_attribute_id`) REFERENCES `asset_other_data_attribute` (`asset_other_data_attribute_id`),
  CONSTRAINT `asset_other_data_attribute_asset_type_ibfk_2` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_type` (`asset_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_other_data_attribute_asset_type`
--

LOCK TABLES `asset_other_data_attribute_asset_type` WRITE;
/*!40000 ALTER TABLE `asset_other_data_attribute_asset_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_other_data_attribute_asset_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_other_data_value`
--

DROP TABLE IF EXISTS `asset_other_data_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_other_data_value` (
  `asset_other_data_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_other_data_attribute_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `asset_other_data_value_value` varchar(255) NOT NULL,
  PRIMARY KEY (`asset_other_data_value_id`) USING BTREE,
  KEY `asset_other_data_value_ibfk_1` (`asset_other_data_attribute_id`) USING BTREE,
  KEY `asset_other_data_value_ibfk_2` (`asset_id`) USING BTREE,
  CONSTRAINT `asset_other_data_value_ibfk_1` FOREIGN KEY (`asset_other_data_attribute_id`) REFERENCES `asset_other_data_attribute` (`asset_other_data_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_other_data_value_ibfk_2` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_other_data_value`
--

LOCK TABLES `asset_other_data_value` WRITE;
/*!40000 ALTER TABLE `asset_other_data_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_other_data_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_report`
--

DROP TABLE IF EXISTS `asset_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_report` (
  `node_id` int(11) DEFAULT NULL,
  `asset_name` varchar(30) CHARACTER SET latin1 DEFAULT NULL,
  `asset_estate` int(11) NOT NULL,
  `asset_num_serie_intern` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `asset_load_date` date DEFAULT NULL,
  `asset_num_factura` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `asset_type_name` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `asset_load_folio` varchar(11) CHARACTER SET latin1 DEFAULT NULL,
  `infra_other_data_value_value` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `asset_other_data_value_value` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  `location` text,
  KEY `asset_report_ibfk_1` (`node_id`) USING BTREE,
  CONSTRAINT `asset_report_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_report`
--

LOCK TABLES `asset_report` WRITE;
/*!40000 ALTER TABLE `asset_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_status`
--

DROP TABLE IF EXISTS `asset_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_status` (
  `asset_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_status_name` varchar(50) DEFAULT NULL,
  `asset_status_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_status_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_status`
--

LOCK TABLES `asset_status` WRITE;
/*!40000 ALTER TABLE `asset_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_trigger_measurement_config`
--

DROP TABLE IF EXISTS `asset_trigger_measurement_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_trigger_measurement_config` (
  `asset_trigger_measurement_config_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(11) NOT NULL,
  `measure_unit_id` int(11) NOT NULL,
  `mtn_plan_id` int(11) NOT NULL,
  `asset_trigger_measurement_config_start` int(11) NOT NULL,
  `asset_trigger_measurement_config_end` int(11) DEFAULT NULL,
  `asset_trigger_measurement_tolerance` int(11) DEFAULT NULL,
  `asset_trigger_measurement_config_notificacion_method` int(11) NOT NULL DEFAULT '0' COMMENT '0 = No configurado,1 = Mail, 2 = SMS, 3= Ambos (mail y sms)',
  `asset_trigger_measurement_config_notificacion_mails` varchar(200) NOT NULL,
  PRIMARY KEY (`asset_trigger_measurement_config_id`) USING BTREE,
  UNIQUE KEY `asset_trigger_measurement_config_id` (`asset_trigger_measurement_config_id`) USING BTREE,
  KEY `asset_trigger_measurement_config_ibfk_1` (`asset_type_id`) USING BTREE,
  KEY `asset_trigger_measurement_config_ibfk_2` (`measure_unit_id`) USING BTREE,
  KEY `mtn_plan_id` (`mtn_plan_id`) USING BTREE,
  CONSTRAINT `asset_trigger_measurement_config_ibfk_1` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_type` (`asset_type_id`),
  CONSTRAINT `asset_trigger_measurement_config_ibfk_2` FOREIGN KEY (`measure_unit_id`) REFERENCES `measure_unit` (`measure_unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_trigger_measurement_config`
--

LOCK TABLES `asset_trigger_measurement_config` WRITE;
/*!40000 ALTER TABLE `asset_trigger_measurement_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_trigger_measurement_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_type`
--

DROP TABLE IF EXISTS `asset_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_type` (
  `asset_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_type_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`asset_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_type`
--

LOCK TABLES `asset_type` WRITE;
/*!40000 ALTER TABLE `asset_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_type_task`
--

DROP TABLE IF EXISTS `asset_type_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset_type_task` (
  `asset_type_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_type_id` int(11) DEFAULT NULL,
  `mtn_task_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`asset_type_task_id`) USING BTREE,
  KEY `asset_type_ibfk_1` (`asset_type_id`) USING BTREE,
  KEY `asset_type_ibfk_2` (`mtn_task_id`) USING BTREE,
  CONSTRAINT `asset_type_ibfk_1` FOREIGN KEY (`asset_type_id`) REFERENCES `asset_type` (`asset_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `asset_type_ibfk_2` FOREIGN KEY (`mtn_task_id`) REFERENCES `mtn_task` (`mtn_task_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_type_task`
--

LOCK TABLES `asset_type_task` WRITE;
/*!40000 ALTER TABLE `asset_type_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_type_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brand`
--

DROP TABLE IF EXISTS `brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand` (
  `brand_id` int(11) NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) NOT NULL,
  PRIMARY KEY (`brand_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brand`
--

LOCK TABLES `brand` WRITE;
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract`
--

DROP TABLE IF EXISTS `contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract` (
  `contract_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) DEFAULT NULL,
  `contract_date_creation` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `contract_date_start` date DEFAULT NULL,
  `contract_date_finish` date DEFAULT NULL,
  `contract_description` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`contract_id`) USING BTREE,
  KEY `contract_ibfk_1` (`provider_id`) USING BTREE,
  CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract`
--

LOCK TABLES `contract` WRITE;
/*!40000 ALTER TABLE `contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_asset`
--

DROP TABLE IF EXISTS `contract_asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_asset` (
  `contract_asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`contract_asset_id`) USING BTREE,
  KEY `contract_asset_ibfk_1` (`asset_id`) USING BTREE,
  KEY `contract_asset_ibfk_2` (`contract_id`) USING BTREE,
  CONSTRAINT `contract_asset_ibfk_1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contract_asset_ibfk_2` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_asset`
--

LOCK TABLES `contract_asset` WRITE;
/*!40000 ALTER TABLE `contract_asset` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_asset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_node`
--

DROP TABLE IF EXISTS `contract_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_node` (
  `contract_node_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `contract_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`contract_node_id`) USING BTREE,
  KEY `contract_node_ibfk_2` (`node_id`) USING BTREE,
  KEY `contract_node_ibfk_1` (`contract_id`) USING BTREE,
  CONSTRAINT `contract_node_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `contract` (`contract_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contract_node_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_node`
--

LOCK TABLES `contract_node` WRITE;
/*!40000 ALTER TABLE `contract_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `costs`
--

DROP TABLE IF EXISTS `costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `costs` (
  `costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `costs_type_id` int(11) NOT NULL,
  `costs_month_id` int(11) NOT NULL,
  `costs_anio` int(11) NOT NULL,
  `costs_number_ticket` varchar(255) NOT NULL,
  `costs_value` int(11) NOT NULL,
  `costs_detail` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`costs_id`) USING BTREE,
  KEY `costs_month_ibfk_1` (`costs_month_id`) USING BTREE,
  KEY `costs_month_ibfk_2` (`costs_type_id`) USING BTREE,
  KEY `costs_month_ibfk_3` (`node_id`) USING BTREE,
  CONSTRAINT `costs_month_ibfk_1` FOREIGN KEY (`costs_month_id`) REFERENCES `costs_month` (`costs_month_id`),
  CONSTRAINT `costs_month_ibfk_2` FOREIGN KEY (`costs_type_id`) REFERENCES `costs_type` (`costs_type_id`),
  CONSTRAINT `costs_month_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `costs`
--

LOCK TABLES `costs` WRITE;
/*!40000 ALTER TABLE `costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `costs_month`
--

DROP TABLE IF EXISTS `costs_month`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `costs_month` (
  `costs_month_id` int(11) NOT NULL AUTO_INCREMENT,
  `costs_month_name` varchar(255) NOT NULL,
  PRIMARY KEY (`costs_month_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `costs_month`
--

LOCK TABLES `costs_month` WRITE;
/*!40000 ALTER TABLE `costs_month` DISABLE KEYS */;
/*!40000 ALTER TABLE `costs_month` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `costs_type`
--

DROP TABLE IF EXISTS `costs_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `costs_type` (
  `costs_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `costs_type_name` varchar(255) NOT NULL,
  PRIMARY KEY (`costs_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `costs_type`
--

LOCK TABLES `costs_type` WRITE;
/*!40000 ALTER TABLE `costs_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `costs_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currency`
--

DROP TABLE IF EXISTS `currency`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_name` varchar(30) DEFAULT NULL,
  `currency_code` varchar(10) DEFAULT NULL,
  `currency_equivalence` int(11) NOT NULL,
  `currency_decimal_character` char(1) NOT NULL,
  `currency_thousands_character` char(1) NOT NULL,
  `currency_number_of_decimal` int(11) NOT NULL,
  PRIMARY KEY (`currency_id`) USING BTREE,
  UNIQUE KEY `index_1` (`currency_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currency`
--

LOCK TABLES `currency` WRITE;
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_category`
--

DROP TABLE IF EXISTS `doc_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_category` (
  `doc_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_category_name` varchar(100) DEFAULT NULL,
  `doc_category_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`doc_category_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_category`
--

LOCK TABLES `doc_category` WRITE;
/*!40000 ALTER TABLE `doc_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_document`
--

DROP TABLE IF EXISTS `doc_document`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_document` (
  `doc_document_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `doc_category_id` int(11) NOT NULL,
  `doc_extension_id` int(11) NOT NULL,
  `doc_document_filename` varchar(255) DEFAULT NULL,
  `doc_document_description` varchar(255) DEFAULT NULL,
  `doc_document_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `doc_current_version_id` int(11) DEFAULT NULL,
  `doc_status_id` int(11) NOT NULL,
  PRIMARY KEY (`doc_document_id`) USING BTREE,
  KEY `doc_document_ibfk_1` (`node_id`) USING BTREE,
  KEY `doc_document_ibfk_2` (`doc_category_id`) USING BTREE,
  KEY `doc_document_ibfk_3` (`doc_extension_id`) USING BTREE,
  CONSTRAINT `doc_document_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doc_document_ibfk_2` FOREIGN KEY (`doc_category_id`) REFERENCES `doc_category` (`doc_category_id`) ON DELETE CASCADE,
  CONSTRAINT `doc_document_ibfk_3` FOREIGN KEY (`doc_extension_id`) REFERENCES `doc_extension` (`doc_extension_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_document`
--

LOCK TABLES `doc_document` WRITE;
/*!40000 ALTER TABLE `doc_document` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_document` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_extension`
--

DROP TABLE IF EXISTS `doc_extension`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_extension` (
  `doc_extension_id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_extension_name` varchar(50) DEFAULT NULL,
  `doc_extension_extension` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`doc_extension_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_extension`
--

LOCK TABLES `doc_extension` WRITE;
/*!40000 ALTER TABLE `doc_extension` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_extension` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doc_version`
--

DROP TABLE IF EXISTS `doc_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doc_version` (
  `doc_version_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `doc_document_id` int(11) NOT NULL,
  `doc_version_code` varchar(45) NOT NULL,
  `doc_version_code_client` varchar(100) DEFAULT NULL,
  `doc_version_filename` varchar(255) DEFAULT NULL,
  `doc_version_comments` text,
  `doc_version_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `doc_version_expiration` date DEFAULT NULL,
  `doc_version_keyword` varchar(255) DEFAULT NULL,
  `doc_version_alert` int(11) DEFAULT NULL,
  `doc_version_alert_email` text,
  `doc_version_notification_email` text,
  `doc_version_internal` date DEFAULT NULL,
  PRIMARY KEY (`doc_version_id`) USING BTREE,
  KEY `doc_version_ibfk_1` (`user_id`) USING BTREE,
  KEY `doc_version_ibfk_2` (`doc_document_id`) USING BTREE,
  CONSTRAINT `doc_version_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `doc_version_ibfk_2` FOREIGN KEY (`doc_document_id`) REFERENCES `doc_document` (`doc_document_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doc_version`
--

LOCK TABLES `doc_version` WRITE;
/*!40000 ALTER TABLE `doc_version` DISABLE KEYS */;
/*!40000 ALTER TABLE `doc_version` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `group_asset_node`
--

DROP TABLE IF EXISTS `group_asset_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `group_asset_node` (
  `id_group_asset_node` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  PRIMARY KEY (`id_group_asset_node`) USING BTREE,
  KEY `group_asset_node_ibfk_1` (`module_id`) USING BTREE,
  KEY `group_asset_node_ibfk_2` (`node_id`) USING BTREE,
  KEY `group_asset_node_ibfk_3` (`user_group_id`) USING BTREE,
  CONSTRAINT `group_asset_node_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`),
  CONSTRAINT `group_asset_node_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`),
  CONSTRAINT `group_asset_node_ibfk_3` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `group_asset_node`
--

LOCK TABLES `group_asset_node` WRITE;
/*!40000 ALTER TABLE `group_asset_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `group_asset_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_configuration`
--

DROP TABLE IF EXISTS `infra_configuration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_configuration` (
  `infra_configuration_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_type_id` int(11) DEFAULT NULL,
  `infra_attribute` varchar(100) DEFAULT NULL,
  `infra_configuration_order` int(11) NOT NULL,
  `infra_the_sumary` int(11) DEFAULT NULL,
  PRIMARY KEY (`infra_configuration_id`) USING BTREE,
  KEY `infra_configuration_ibfk_1` (`node_type_id`) USING BTREE,
  CONSTRAINT `infra_configuration_ibfk_1` FOREIGN KEY (`node_type_id`) REFERENCES `node_type` (`node_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_configuration`
--

LOCK TABLES `infra_configuration` WRITE;
/*!40000 ALTER TABLE `infra_configuration` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_configuration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_coordinate`
--

DROP TABLE IF EXISTS `infra_coordinate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_coordinate` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_longitude` varchar(50) NOT NULL,
  `node_latitude` varchar(50) NOT NULL,
  PRIMARY KEY (`node_id`) USING BTREE,
  CONSTRAINT `infra_coordinate_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_coordinate`
--

LOCK TABLES `infra_coordinate` WRITE;
/*!40000 ALTER TABLE `infra_coordinate` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_coordinate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_grupo`
--

DROP TABLE IF EXISTS `infra_grupo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_grupo` (
  `infra_grupo_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_grupo_nombre` varchar(30) DEFAULT NULL,
  `infra_grupo_order` int(11) DEFAULT '0',
  PRIMARY KEY (`infra_grupo_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_grupo`
--

LOCK TABLES `infra_grupo` WRITE;
/*!40000 ALTER TABLE `infra_grupo` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_grupo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_info`
--

DROP TABLE IF EXISTS `infra_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_info` (
  `infra_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `infra_info_option_id_1` int(11) DEFAULT NULL,
  `infra_info_option_id_2` int(11) DEFAULT NULL,
  `infra_info_option_id_3` int(11) DEFAULT NULL,
  `infra_info_option_id_4` int(11) DEFAULT NULL,
  `infra_info_usable_area` double(11,3) DEFAULT '0.000',
  `infra_info_usable_area_total` double(11,3) DEFAULT '0.000',
  `infra_info_area` double(11,3) DEFAULT '0.000',
  `infra_info_area_total` double(11,3) DEFAULT '0.000',
  `infra_info_volume` double(11,3) DEFAULT '0.000',
  `infra_info_volume_total` double(11,3) DEFAULT '0.000',
  `infra_info_length` double(11,3) DEFAULT '0.000',
  `infra_info_width` double(11,3) DEFAULT '0.000',
  `infra_info_height` double(11,3) DEFAULT '0.000',
  `infra_info_capacity` int(11) DEFAULT '0',
  `infra_info_capacity_total` int(11) DEFAULT '0',
  `infra_info_terrain_area` double(11,3) DEFAULT '0.000',
  `infra_info_terrain_area_total` double(11,3) DEFAULT '0.000',
  `infra_info_additional_1` varchar(255) DEFAULT NULL,
  `infra_info_additional_2` varchar(255) DEFAULT NULL,
  `infra_info_additional_3` varchar(255) DEFAULT NULL,
  `infra_info_additional_4` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`infra_info_id`) USING BTREE,
  KEY `infra_info_ibfk_1` (`infra_info_option_id_1`) USING BTREE,
  KEY `infra_info_ibfk_2` (`infra_info_option_id_2`) USING BTREE,
  KEY `infra_info_ibfk_3` (`infra_info_option_id_3`) USING BTREE,
  KEY `infra_info_ibfk_4` (`infra_info_option_id_4`) USING BTREE,
  KEY `infra_info_ibfk_5` (`node_id`) USING BTREE,
  CONSTRAINT `infra_info_ibfk_1` FOREIGN KEY (`infra_info_option_id_1`) REFERENCES `infra_info_option` (`infra_info_option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `infra_info_ibfk_2` FOREIGN KEY (`infra_info_option_id_2`) REFERENCES `infra_info_option` (`infra_info_option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `infra_info_ibfk_3` FOREIGN KEY (`infra_info_option_id_3`) REFERENCES `infra_info_option` (`infra_info_option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `infra_info_ibfk_4` FOREIGN KEY (`infra_info_option_id_4`) REFERENCES `infra_info_option` (`infra_info_option_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `infra_info_ibfk_5` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_info`
--

LOCK TABLES `infra_info` WRITE;
/*!40000 ALTER TABLE `infra_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_info_option`
--

DROP TABLE IF EXISTS `infra_info_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_info_option` (
  `infra_info_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_info_option_name` varchar(200) DEFAULT NULL,
  `infra_info_option_parent_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`infra_info_option_id`) USING BTREE,
  KEY `infra_info_option_ibfk_1` (`infra_info_option_parent_id`) USING BTREE,
  CONSTRAINT `infra_info_option_ibfk_1` FOREIGN KEY (`infra_info_option_parent_id`) REFERENCES `infra_info_option` (`infra_info_option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_info_option`
--

LOCK TABLES `infra_info_option` WRITE;
/*!40000 ALTER TABLE `infra_info_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_info_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_other_data_attribute`
--

DROP TABLE IF EXISTS `infra_other_data_attribute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_other_data_attribute` (
  `infra_other_data_attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_grupo_id` int(11) DEFAULT '1',
  `infra_other_data_attribute_name` varchar(100) NOT NULL,
  `infra_other_data_attribute_type` char(50) DEFAULT NULL,
  PRIMARY KEY (`infra_other_data_attribute_id`) USING BTREE,
  KEY `infra_other_data_attribute_ibfk_1` (`infra_grupo_id`) USING BTREE,
  CONSTRAINT `infra_other_data_attribute_ibfk_1` FOREIGN KEY (`infra_grupo_id`) REFERENCES `infra_grupo` (`infra_grupo_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_other_data_attribute`
--

LOCK TABLES `infra_other_data_attribute` WRITE;
/*!40000 ALTER TABLE `infra_other_data_attribute` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_other_data_attribute` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_other_data_attribute_node_type`
--

DROP TABLE IF EXISTS `infra_other_data_attribute_node_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_other_data_attribute_node_type` (
  `infra_other_data_attribute_node_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_other_data_attribute_id` int(11) NOT NULL,
  `node_type_id` int(11) NOT NULL,
  `infra_other_data_attribute_node_type_order` int(11) NOT NULL,
  `infra_other_data_attribute_node_type_the_sumary` int(11) DEFAULT NULL,
  PRIMARY KEY (`infra_other_data_attribute_node_type_id`) USING BTREE,
  KEY `infra_other_data_attribute_node_type_ibfk_1` (`infra_other_data_attribute_id`) USING BTREE,
  KEY `infra_other_data_attribute_node_type_ibfk_2` (`node_type_id`) USING BTREE,
  CONSTRAINT `infra_other_data_attribute_node_type_ibfk_1` FOREIGN KEY (`infra_other_data_attribute_id`) REFERENCES `infra_other_data_attribute` (`infra_other_data_attribute_id`),
  CONSTRAINT `infra_other_data_attribute_node_type_ibfk_2` FOREIGN KEY (`node_type_id`) REFERENCES `node_type` (`node_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_other_data_attribute_node_type`
--

LOCK TABLES `infra_other_data_attribute_node_type` WRITE;
/*!40000 ALTER TABLE `infra_other_data_attribute_node_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_other_data_attribute_node_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_other_data_option`
--

DROP TABLE IF EXISTS `infra_other_data_option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_other_data_option` (
  `infra_other_data_option_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_other_data_attribute_id` int(11) NOT NULL,
  `infra_other_data_option_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`infra_other_data_option_id`) USING BTREE,
  KEY `fk_info_option_attribute` (`infra_other_data_option_id`) USING BTREE,
  KEY `infra_other_data_option_ibfk_1` (`infra_other_data_attribute_id`) USING BTREE,
  CONSTRAINT `infra_other_data_option_ibfk_1` FOREIGN KEY (`infra_other_data_attribute_id`) REFERENCES `infra_other_data_attribute` (`infra_other_data_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_other_data_option`
--

LOCK TABLES `infra_other_data_option` WRITE;
/*!40000 ALTER TABLE `infra_other_data_option` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_other_data_option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `infra_other_data_value`
--

DROP TABLE IF EXISTS `infra_other_data_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `infra_other_data_value` (
  `infra_other_data_value_id` int(11) NOT NULL AUTO_INCREMENT,
  `infra_other_data_attribute_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `infra_other_data_option_id` int(11) DEFAULT NULL,
  `infra_other_data_value_value` varchar(255) NOT NULL,
  PRIMARY KEY (`infra_other_data_value_id`) USING BTREE,
  KEY `infra_other_data_value_ibfk_1` (`infra_other_data_attribute_id`) USING BTREE,
  KEY `infra_other_data_value_ibfk_2` (`infra_other_data_option_id`) USING BTREE,
  KEY `infra_other_data_value_ibfk_3` (`node_id`) USING BTREE,
  CONSTRAINT `infra_other_data_value_ibfk_1` FOREIGN KEY (`infra_other_data_attribute_id`) REFERENCES `infra_other_data_attribute` (`infra_other_data_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `infra_other_data_value_ibfk_2` FOREIGN KEY (`infra_other_data_option_id`) REFERENCES `infra_other_data_option` (`infra_other_data_option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `infra_other_data_value_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `infra_other_data_value`
--

LOCK TABLES `infra_other_data_value` WRITE;
/*!40000 ALTER TABLE `infra_other_data_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `infra_other_data_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language`
--

DROP TABLE IF EXISTS `language`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language` (
  `language_id` int(11) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(100) NOT NULL,
  `language_default` int(1) NOT NULL,
  PRIMARY KEY (`language_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language`
--

LOCK TABLES `language` WRITE;
/*!40000 ALTER TABLE `language` DISABLE KEYS */;
INSERT INTO `language` VALUES (1,'Español',1);
/*!40000 ALTER TABLE `language` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `language_tag`
--

DROP TABLE IF EXISTS `language_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `language_tag` (
  `language_tag_aux_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Corresponde a un id usado solo para que Doctrine funcione correctamente con la tabla',
  `language_tag_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `language_tag_tag` varchar(255) NOT NULL,
  `language_tag_value` varchar(255) NOT NULL,
  PRIMARY KEY (`language_tag_aux_id`) USING BTREE,
  KEY `language_tag_ibfk_1` (`language_id`) USING BTREE,
  KEY `language_tag_ibfk_2` (`module_id`) USING BTREE,
  KEY `language_tag_id` (`language_tag_id`) USING BTREE,
  CONSTRAINT `language_tag_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `language_tag_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1088 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `language_tag`
--

LOCK TABLES `language_tag` WRITE;
/*!40000 ALTER TABLE `language_tag` DISABLE KEYS */;
INSERT INTO `language_tag` VALUES (1,1,1,1,'save','Guardar'),(2,2,1,1,'nnew','Nuevo'),(3,3,1,1,'eexport','Exportar'),(4,4,1,1,'add','Agregar'),(5,5,1,5,'add_node_action','Agregar nodos'),(6,6,1,5,'root','Raíz'),(7,7,1,5,'level_up','Subir'),(8,8,1,1,'edit_name','Editar Nombre'),(9,9,1,1,'cut','Cortar'),(10,10,1,1,'copy','Copiar'),(11,11,1,1,'paste','Pegar'),(12,12,1,1,'ddelete','Eliminar'),(13,13,1,1,'name','Nombre'),(14,14,1,1,'category','Categoría'),(15,15,1,1,'type','Tipo'),(16,16,1,5,'structural_data','Datos Estructurales'),(17,17,1,1,'other_data','Otros Datos'),(18,18,1,5,'add_items','Agregar elementos'),(19,19,1,5,'advanced_mode','Modo avanzado'),(20,20,1,5,'prefix','Prefijo'),(21,21,1,1,'quantity','Cantidad'),(22,22,1,5,'infrastructure','Infraestructura'),(23,23,1,1,'message_delete_items','Usted debe seleccionar un ítem para eliminar'),(24,24,1,1,'message_loading_information','Cargando información'),(25,25,1,1,'message_guarding_information','Guardando información'),(26,26,1,1,'message_please_wait','Por favor espere'),(27,27,1,1,'message_generating_file','Generando archivo...'),(28,28,1,5,'simple_mode','Modo Simple'),(29,29,1,1,'close','Cerrar'),(30,30,1,1,'error','Error'),(31,31,1,1,'message_extjs_client_invalid','Los campos de formulario no podrán ser presentados con valores no válidos'),(32,32,1,1,'message_extjs_failed_connection','Conexión fallida'),(33,33,1,1,'eexport_list','Exportar Lista'),(34,34,1,1,'file_name','Nombre de Archivo'),(35,35,1,1,'output_type','Tipo de Salida'),(36,36,1,5,'edit_node_action','Editar Nodo'),(37,37,1,3,'plans','Planos'),(38,38,1,1,'message_up_document','Subiendo documento'),(39,39,1,1,'message_show_all_versions','Mostrar todas las versiones'),(40,40,1,1,'description','Descripción'),(41,41,1,1,'current_version','Versión Actual'),(42,42,1,1,'uploaded_by','Cargado Por'),(43,43,1,1,'creation_date','Fecha de Creación'),(44,44,1,3,'add_plans_action','Agregar nuevos planos'),(45,45,1,3,'select_svg','Seleccione un svg'),(46,46,1,3,'plan','Plano'),(47,47,1,1,'version','Versión'),(48,48,1,1,'comment','Comentarios'),(49,49,1,1,'new_version','Nueva Versión'),(50,50,1,1,'details','Detalle'),(51,51,1,3,'detail_plan','Detalle Plano'),(52,52,1,1,'user_magazine','Usuario Cargador'),(53,53,1,3,'upload_date','Fecha de Carga'),(54,54,1,1,'versions','Versiones'),(55,55,1,3,'edit_version_plan_title','Edición de la Versión del Plano'),(56,56,1,1,'documents','Documentos'),(57,57,1,2,'add_document_action','Agregar nuevos documentos'),(58,58,1,2,'delete_document_action','Eliminar los documentos'),(59,59,1,1,'confirmation','Confirmación'),(60,60,1,1,'message_really_want_delete','¿Está Realmente seguro de Eliminar el documento y todas sus versiones?'),(61,61,1,1,'name_extension','Nombre Extensión'),(62,62,1,1,'delete_version','Eliminar Versión'),(63,63,1,1,'name_version','Nombre Versión'),(64,64,1,1,'select_document','Seleccione un documento'),(65,65,1,1,'document','Documento'),(66,66,1,2,'edit_version_document_title','Edición de la Versión del Documento'),(67,67,1,1,'filters','Filtros'),(68,68,1,1,'clean','Limpiar'),(69,69,1,1,'state','Estado'),(70,70,1,1,'condition','Condición'),(71,71,1,1,'message_required_fields','Por favor ingresar los campos obligatorios'),(72,72,1,1,'provider','Proveedor'),(73,73,1,1,'start_date','Fecha de Inicio'),(74,74,1,1,'end_date','Fecha de Fin'),(75,75,1,5,'cut_node_action','Cortar nodos'),(76,76,1,5,'copy_node_action','Copiar nodos'),(77,77,1,1,'date','Fecha'),(78,78,1,1,'value','Valor'),(79,79,1,1,'unit','Unidad'),(80,80,1,1,'asset','Activo'),(81,81,1,1,'requested_by','Solicitado Por'),(82,82,1,1,'search','Buscar'),(83,83,1,1,'searching','Buscando en'),(84,84,1,5,'edit_node_action','Editar nodos'),(85,85,1,1,'printer','Imprimir'),(86,86,1,1,'not_available','No disponible'),(87,87,1,5,'paste_node_action','Pegar nodos copiados o cortados'),(88,88,1,1,'price','Precio'),(89,89,1,1,'time','Tiempo'),(90,90,1,5,'delete_node_action','Eliminar nodos'),(91,91,1,5,'move_node_action','Mover nodos'),(92,92,1,5,'add_node_type_category_action','Agregar nuevas categorías para los nodos'),(93,93,1,5,'edit_node_type_category_action','Editar las categorías para los nodos'),(94,94,1,5,'delete_node_type_category_action','Eliminar las categorías para los nodos'),(95,95,1,5,'add_node_type_action','Agregar nuevos tipos de nodos'),(96,96,1,5,'edit_node_type_action','Editar tipos de nodos'),(97,97,1,5,'delete_node_type_action','Eliminar los tipos de nodos'),(98,98,1,1,'add_measure_unit_action','Agregar nuevas unidades de medida'),(99,99,1,1,'edit_measure_unit_action','Editar las unidades de medida'),(100,100,1,1,'delete_measure_unit_action','Eliminar las unidades de medida'),(101,101,1,1,'add_provider_type_action','Agregar nuevos tipos de proveedores'),(102,102,1,1,'edit_provider_type_action','Editar los tipos de proveedores'),(103,103,1,1,'delete_provider_type_action','Eliminar los tipos de proveedores'),(104,104,1,1,'add_provider_action','Agregar nuevos proveedores'),(105,105,1,1,'edit_provider_action','Editar los proveedores'),(106,106,1,1,'delete_provider_action','Eliminar los proveedores'),(107,107,1,1,'add_curency_action','Agregar nuevos tipos de monedas'),(108,108,1,1,'edit_curency_action','Editar tipos de monedas'),(109,109,1,1,'delete_curency_action','Eliminar los tipos de monedas'),(110,110,1,5,'edit_title_node','Editar Nodo'),(111,111,1,2,'edit_document_action','Editar los documentos'),(112,112,1,2,'add_document_version_action','Agregar nuevas versiones de los documentos'),(113,113,1,2,'edit_document_version_action','Editar versiones de los documentos'),(114,114,1,2,'delete_document_version_action','Eliminar versiones de los documentos'),(115,115,1,2,'add_doc_extension_action','Agregar extensiones permitidas para los documentos'),(116,116,1,2,'edit_doc_extension_action','Editar las extensiones permitidas para los documentos'),(117,117,1,2,'delete_doc_extension_action','Eliminar extensiones permitidas para los documentos'),(118,118,1,2,'add_document_category_action','Agregar nuevas categorí­as para los documentos'),(119,119,1,2,'edit_document_category_action','Editar categorías para los documentos'),(120,120,1,2,'delete_document_category_action','Eliminar las categorías de los documentos'),(121,121,1,2,'download_document_action','Descargar documentos'),(122,122,1,3,'edit_category_plan_action','Editar las categorías de los Planos'),(123,123,1,3,'delete_category_plan_action','Eliminar las categorí­as de los Planos'),(124,124,1,3,'add_plan_version_action','Agregar nuevas versiones de los Planos'),(125,125,1,3,'edit_plan_version_action','Editar las versiones de los Planos'),(126,126,1,3,'delete_plan_version_action','Eliminar las versiones de los Planos'),(127,127,1,3,'add_plan_category_action','Agregar nuevas categorí­as para los Planos'),(128,128,1,3,'export_list_plan_action','Exportar la lista de Planos'),(129,129,1,3,'add_plan_title','Agregar Plano'),(130,130,1,3,'add_plan_version_title','Agregar una nueva versión del Plano'),(131,131,1,2,'add_document_title','Agregar Documento'),(132,132,1,2,'add_version_document_title','Agregar nueva versión del Documento'),(133,133,1,5,'node_type','Tipo de Nodo'),(134,134,1,5,'attributes','Atributos'),(135,135,1,5,'dynamic_data','Datos Dinámicos (Otros Datos)'),(136,136,1,5,'tag_name','Nombre de Etiqueta'),(137,137,1,5,'field_type','Tipo de Campo'),(138,138,1,5,'dynamic_data_associate','Asociar Datos Dinámicos (Otros Datos)'),(139,139,1,5,'add_categories_infrastructure','Agregar categorías a la Infraestructura'),(140,140,1,5,'name_category','Nombre de la Categoría'),(141,141,1,5,'infrastructure_category_edition','Edición  de la Categoría de la Infraestructura'),(142,142,1,5,'add_type_node','Agregar Tipo de Nodo'),(143,143,1,5,'name_type_node','Nombre del Tipo de Nodo'),(144,144,1,5,'edit_type_node','Edición  del Tipo de Nodo '),(145,145,1,5,'add_dynamic_data','Agregar Datos Dinámicos'),(146,146,1,5,'message_node_type_invalid','** No puede cambiar el Tipo al campo de Tipo Selecci&oacute;n.   Solo puede editar las opciones del campo'),(147,147,1,1,'text','Texto'),(148,148,1,1,'number','Número'),(149,149,1,1,'decimal','Decimal'),(150,150,1,1,'selection','Selección'),(151,151,1,5,'edit_selection_field_options','Edición  de las opciones del campo de Selección'),(152,152,1,5,'edit_dynamic_data','Edición de los  Datos Dinámicos'),(153,153,1,5,'selection_field_options','Opciones del Campo Selección'),(154,154,1,5,'add_selection_field_options','Agregar opciones al campo de selección'),(155,155,1,5,'option_name','Nombre Opción'),(156,156,1,5,'edit_options_field','Edición  de las opciones del campo'),(157,157,1,5,'add_infra_other_data_attribute_action','Agregar nuevos datos dinámicos para los nodos'),(158,158,1,5,'edit_infra_other_data_attribute_action','Editar los datos dinámicos de los nodos'),(159,159,1,5,'delete_infra_other_data_attribute_action','Eliminar los datos dinámicos para los nodos'),(160,160,1,1,'add_category','Agregar Categoría'),(161,161,1,1,'edit_category','Editar Categoría'),(162,162,1,2,'extensions','Extensiones'),(163,163,1,2,'add_category_document','Agregar categorías de documentos'),(164,164,1,5,'add_node_other_data_action','Agregar otros datos estructurales a los nodos'),(165,165,1,5,'edit_node_other_data_action','Editar los otros datos estructurales del tipo de nodo '),(166,166,1,5,'delete_node_other_data_action','Eliminar los otros datos estructurales del tipo de nodo'),(167,167,1,5,'add_node_other_data_option_action','Agregar opciones de los campos de selección dinámicos'),(168,168,1,2,'edit_category_documents','Edición de la Categoría de documentos'),(169,169,1,2,'add_extensions_document','Agregar extensiones al documento'),(170,170,1,2,'examples_extensions','Extensiones (Ej: pdf, xls, doc, docx, svg, png)'),(171,171,1,2,'edit_document_extensions','Edición de las extensiones del documento'),(172,172,1,1,'add_type','Agregar Tipo'),(173,173,1,1,'name_type','Nombre Tipo'),(174,174,1,5,'edit_node_other_data_option_action','Editar opciones de los campos de selección dinámicos'),(175,175,1,5,'delete_node_other_data_option_action','Eliminar opciones de los campos de selección dinámicos'),(176,176,1,5,'add_infra_info_action','Guardar los datos estructurales del nodo'),(177,177,1,5,'config_infra_info_action','Asociar datos estructurales a los tipos de nodos'),(178,178,1,5,'export_list_infra_action','Exportar la lista de nodos'),(179,179,1,5,'add_infra_other_data_value_action','Guardar los otros datos asociados al tipo de nodo'),(180,180,1,6,'add_user_action','Agregar nuevos usuarios del Sistema'),(181,181,1,6,'edit_user_action','Editar los usuarios del Sistema'),(182,182,1,6,'delete_user_action','Eliminar los usuarios del Sistema'),(183,183,1,6,'add_group_action','Agregar nuevos grupos de usuarios al Sistema'),(184,184,1,6,'edit_group_action','Editar los grupos de usuarios del Sistema'),(185,185,1,6,'delete_group_action','Eliminar los grupos de usuarios del Sistema'),(186,186,1,6,'add_permissions_group_action','Administrar los permisos de acceso de los grupos'),(187,187,1,6,'set_tree_permissions_group','Administrar los accesos al árbol de los grupos'),(188,188,1,6,'add_user_group_action','Agregar usuarios a los grupos'),(190,190,1,6,'users_groups','Usuarios/Grupos'),(191,191,1,6,'users','Usuarios'),(192,192,1,6,'enable','Habilitar'),(193,193,1,6,'enable_user','¿Desea Habilitar al(os) Usuario(s)?'),(194,194,1,6,'select_user_enable','Usted debe seleccionar al menos un usuario para habilitar.'),(195,195,1,6,'disable','Deshabilitar'),(196,196,1,6,'disable_user','¿Desea Deshabilitar al usuario(s)?'),(197,197,1,6,'select_user_disable','Usted debe seleccionar al menos un usuario para deshabilitar.'),(198,198,1,6,'email','E-mail'),(199,199,1,6,'username','Nombre y Apellido'),(200,200,1,6,'groups','Grupos'),(201,201,1,6,'want_delete_group','¿Desea eliminar el(os) grupo(s)?'),(202,202,1,6,'select_groups_delete','Usted debe seleccionar al menos un grupo para eliminar.'),(203,203,1,6,'config_groups','Configurar Grupos'),(204,204,1,6,'add_user_group','Agregar grupo de usuarios'),(205,205,1,6,'edit_users_group','Edición del  grupo de usuarios'),(206,206,1,6,'user_group_settings','Configuración del grupo de usuarios'),(207,207,1,6,'users_outside_group','Usuarios fuera del grupo'),(208,208,1,6,'users_in_groups','Usuarios en el grupo'),(209,209,1,6,'notification','Notificación'),(210,210,1,6,'permissions','Permisos'),(211,211,1,6,'module','Módulo'),(212,212,1,6,'action_not_assigned_group','Acciones del modulo no asignadas al grupo'),(213,213,1,6,'group_permissions','Permisos del Grupo'),(214,214,1,6,'add_users_system','Agregar Usuarios al Sistema'),(215,215,1,6,'english_username','User Name'),(216,216,1,6,'associate_user_groups','Puede asociar grupos al usuario inmediatamente'),(217,217,1,6,'users_the_groups','Grupos del usuario'),(218,218,1,6,'available_groups','Grupos disponibles'),(219,219,1,6,'edit_users_sistem','Edici&oacute;n de los Usuario del Sistema'),(220,220,1,1,'message_success','Éxito'),(221,221,1,1,'general','General'),(222,222,1,1,'providers','Proveedores'),(223,223,1,1,'contact','Contacto'),(224,224,1,1,'phone','Teléfono'),(225,225,1,1,'type_currency','Tipo de moneda'),(226,226,1,1,'code','Código'),(227,227,1,1,'equivalence','Equivalencia'),(228,228,1,1,'measurement_unit','Unidad de medida'),(229,229,1,1,'add_provider_type','Agregar tipo de proveedor'),(230,230,1,1,'edit_provider_type','Edición del tipo de proveedor'),(231,231,1,1,'add_provider','Agregar Proveedor'),(232,232,1,1,'fax','Fax'),(233,233,1,1,'edit_provider','Edición del Proveedor'),(234,234,1,1,'add_currency','Agregar tipo de moneda'),(235,235,1,1,'character_decimal','Carácter para decimales'),(236,236,1,1,'character_thousands','Carácter para miles y millares'),(237,237,1,1,'number_decimal','Cantidad de decimales'),(238,238,1,1,'edit_type_currency','Edición para  tipo de moneda'),(239,239,1,1,'add_type_measurement','Agregar tipo de unidad de medida'),(240,240,1,1,'edit_type_measurement ','Edición del tipo de unidad de medida'),(241,241,1,6,'access_tree','Acceso al árbol'),(242,242,1,6,'group','Grupo'),(243,243,1,6,'level','Nivel'),(244,244,1,6,'branch','Rama'),(245,245,1,3,'planimetry','Planimetría'),(246,246,1,6,'language','Idioma'),(247,247,1,6,'modules','Módulos'),(248,248,1,1,'people','Personas'),(249,249,1,1,'set_vista','Ajustar Vista'),(250,250,1,1,'zoom','Zoom'),(251,251,1,1,'layers','Capas'),(252,252,1,1,'oops','¡Ups!'),(253,253,1,1,'title_login','Login'),(254,254,1,1,'user','Usuario'),(255,255,1,6,'password','Contraseña'),(256,256,1,1,'authenticate','Entrar'),(257,257,1,1,'validating_user','Validando usuario...'),(258,258,1,1,'please_wait','Por favor espere...'),(259,259,1,1,'loading_permissions','Cargando permisos'),(260,260,1,1,'lloading','Cargando...'),(261,261,1,1,'problem_loading_permits','Problema cargando los permisos. Por favor reintentar'),(262,262,1,1,'work_area','Área de Trabajo'),(263,263,1,1,'admin_panel','Panel de administración'),(264,264,1,1,'preferences','Preferencias'),(265,265,1,1,'cession_close','Cerrar Sesión'),(266,266,1,1,'section_was_completed','¡La sesión de usuario fue finalizada!'),(267,267,1,5,'infra_info_area','SUPERFICIE CONSTRUIDA'),(268,268,1,5,'infra_info_area_total','SUPERFICIE CONSTRUIDA TOTAL'),(269,269,1,5,'infra_info_usable_area','SUPERFICIE'),(270,270,1,5,'infra_info_usable_area_total','SUPERFICIE TOTAL'),(271,271,1,5,'infra_info_volume','VOLUMEN'),(272,272,1,5,'infra_info_volume_total','VOLUMEN TOTAL'),(273,273,1,5,'infra_info_length','LARGO'),(274,274,1,5,'infra_info_width','ANCHO'),(275,275,1,5,'infra_info_height','ALTO'),(276,276,1,5,'infra_info_capacity','CAPACIDAD'),(277,277,1,5,'infra_info_capacity_total','CAPACIDAD TOTAL'),(278,278,1,5,'infra_info_additional_1','ADICIONAL 1'),(279,279,1,5,'infra_info_additional_2','ADICIONAL 2'),(280,280,1,5,'infra_info_additional_3','ADICIONAL 3'),(281,281,1,5,'infra_info_additional_4','ADICIONAL 4'),(282,282,1,5,'infra_info_option_id_1','COMBO1'),(283,283,1,5,'infra_info_option_id_2','COMBO2'),(284,284,1,5,'infra_info_option_id_3','COMBO3'),(285,285,1,5,'infra_info_option_id_4','COMBO4'),(286,286,1,1,'sure_exit_system','¿Seguro que desea salir del sistema?'),(287,287,1,1,'languages','Idiomas'),(288,288,1,1,'in_use_system','En uso por el Sistema'),(289,289,1,1,'yes','Si'),(290,290,1,1,'add_language','Agregar lenguaje'),(291,291,1,1,'language_reference','Idioma de referencia'),(292,292,1,1,'set_current_language','Establecer como Idioma Actual'),(293,293,1,1,'edit_language','Edición del Idioma'),(294,294,1,1,'settings','Configuración'),(295,295,1,1,'select_filter_search','Seleccione los filtros para realizar una búsqueda'),(296,296,1,1,'select_date_range','Seleccione un rango de fechas de creación del documento'),(297,297,1,1,'view_access','Visualizar'),(298,298,1,1,'extencion','Extensiones'),(299,299,1,2,'document_type','Tipo de Documento'),(300,300,1,1,'there_nodes','No hay nodos que mostrar'),(301,301,1,5,'depth','Profundidad'),(302,302,1,5,'direct_nodes','Nodos directos (Todos los nodos hijo directo del nodo seleccionado)'),(303,303,1,5,'complete_branch_selected_node','Rama completa del nodo seleccionado ( Campus -> edificio -> piso )'),(304,304,1,5,'node_type','Tipos de Nodos'),(305,305,1,5,'node_types_show','No hay tipos de nodo que mostrar'),(306,306,1,1,'range_date_expiration','Seleccione un rango de fechas de expiración del Activo'),(307,307,1,1,'select_date_range_plane','Seleccione un rango de fechas de creación del Plano'),(308,308,1,1,'edit_filter','Modificar Filtro'),(309,309,1,1,'personal_data','Datos Personales'),(310,310,1,1,'change_language','Cambiar Idiomas'),(311,311,1,6,'current_password','Contraseña Actual'),(312,312,1,6,'confirm_password','Confirmar Contraseña'),(313,313,1,6,'password_requirements','Requisitos Contraseña'),(314,314,1,6,'example_password','La contraseña debe contener ocho caracteres como mínimo y doce como máximo ingrese solo  números y letras      Ejemplo: clave1234.'),(315,315,1,1,'invalid_authentication_data','Datos de autenticación no válidos.'),(316,316,1,5,'dynamic_data_can_not_be_eliminated','El Dato Dinámico  no puede ser eliminado por estar asociado a un Tipo de Nodo.'),(317,317,1,5,'node_information_stored_successfully','Información del nodo guardada con éxito.'),(318,318,1,1,'currency_associated_lists','El tipo de moneda está asociado a una o más listas de precios.'),(319,319,1,1,'problems_creating_language','Problemas al crear el idioma.'),(320,320,1,1,'language_problems_updating','Problemas al actualizar el idioma'),(321,321,1,1,'delete_the_default_language','No puede eliminar la hoja de idiomas por defecto. Seleccione otra hoja de idiomas como hoja por defecto y vuelva a intentarlo.'),(322,322,1,1,'problems_eliminating_language','Problemas al eliminar el idioma.'),(323,323,1,1,'tag_successfully_updated','Tag actualizado con éxito'),(324,324,1,1,'type_measurement_associated_assets','El tipo de unidad de medida está asociado a uno o más activos'),(325,325,1,1,'category_node_type_eliminated_associated_node','La categoría del tipo de nodo no puede ser eliminada por estar asociada a un nodo.'),(326,326,1,2,'type_extension_not_allowed','Tipo de extensión no permitida'),(327,327,1,1,'icone_not_qualify','Icono no cumple con los requisitos. Revisar que sea GIF y que tenga el alto y ancho igual 16px.'),(328,328,1,5,'can_not_delete_node','El tipo de nodo no puede ser eliminado por estar asociada a un nodo.'),(329,329,1,1,'successfully_assigned_permission','Permisos al grupo asignados con éxito.'),(330,330,1,1,'access_successfully_appointed_group','Accesos del grupo asignados con éxito.'),(331,331,1,2,'failed_delete_extension_document','Error al eliminar la extensión del documento'),(332,332,1,1,'the_provider_type_associated','El tipo de proveedor está asociado a uno o más proveedores.'),(333,333,1,2,'category_document_type_cant_be_eliminated','La categorí­a del tipo de documento no puede ser eliminada por estar asociada a un documento'),(334,334,1,1,'password_does_not_match_its_confirmation','La contraseña nueva no coincide con su confirmación.'),(335,335,1,1,'incorrect_password','Contraseña incorrecta'),(336,336,1,3,'category_plan_cant_be_eliminated','La categoría  no puede ser eliminada por estar asociada a un plano'),(337,337,1,3,'successfully_updated_plan','Plan actualizado con éxito.'),(338,338,1,1,'charger','Cargador'),(339,339,1,1,'creation','Creación'),(340,340,1,1,'commentary','Comentario'),(341,341,1,1,'its_validity_expired_user','Usuario expiro su vigencia'),(342,342,1,1,'disabled_users','Usuario Inhabilitado'),(343,343,1,1,'expiration_date','Fecha de Expiración'),(344,344,1,1,'searching','Búsqueda'),(345,345,1,6,'the_password_is','La contraseña del usuario es'),(346,346,1,3,'use_category_for_linking_nodes','Categoría en uso para la vinculación de los nodos'),(347,347,1,1,'the_user_name_already_exists_in_the_database','El nombre de usuario ya existe en la Base de Datos'),(348,348,1,6,'new_password','Nueva contraseña'),(349,349,1,3,'view_related_node_level','Ver relación nodo – plano'),(350,350,1,3,'showing_approximate_relation_node_level','Aproximar al mostrar relación nodo – plano'),(351,351,1,3,'set_vista','Ajustar Vista'),(352,352,1,3,'color_line','Color/Línea'),(353,353,1,3,'enable_selection_of_flat_objects','Habilitar la selección de objetos del plano'),(354,354,1,3,'associating_lines','Asociar Líneas'),(355,355,1,3,'successfully_associated_lines','¡Líneas asociadas exitosamente!'),(356,356,1,3,'visible_sign','¿Visible?'),(357,357,1,3,'layer','Capa'),(358,358,1,3,'apply','Aplicar'),(359,359,1,3,'view_settings','Configuración de Vista'),(360,360,1,3,'color','Color'),(361,361,1,3,'line_width','Ancho de la Línea'),(362,362,1,1,'select_image','Seleccione una imagen'),(363,363,1,2,'file_name_latest_version','Nombre de Archivo y Última versión'),(364,364,1,1,'section','Sección'),(365,365,1,1,'is_administrator','¿Es Administrador?'),(366,366,1,5,'message_really_want_delete','¿Está seguro de eliminar la opción y todas las opciones asociadas a este campo de selección?'),(367,367,1,1,'select_date_range_expiration','Seleccione un rango de fechas de expiración del Usuario'),(368,368,1,5,'configure_tab','Configurar ficha'),(369,369,1,5,'set_merge_fields','Configurar campos combinados'),(370,370,1,5,'selection_field_options','Opciones del campo de selección'),(371,371,1,5,'add_option_to_select_field','Agregar opción al campo de selección'),(372,372,1,5,'edit_the_option_selection_field','Editar la opción del campo de selección'),(373,373,1,1,'button_login','Entrar'),(374,374,1,5,'option_selection_recorded_successfully','Opción del campo de selección registrada con éxito'),(375,375,1,5,'option_deleted_successfully','Opción eliminada con éxito'),(376,376,1,5,'problems_eliminating_option','Problemas al eliminar la opción'),(377,377,1,2,'problems_associated_document_version','Problemas con el archivo asociado a la versión del documento'),(378,378,1,2,'you_can_not_delete_the_latest_version','No se puede borrar la última versión, debe eliminar el Documento'),(379,379,1,5,'infra_info_terrain_area','SUPERFICIE TERRENO'),(380,380,1,5,'infra_info_terrain_area_total','SUPERFICIE TERRENO TOTAL'),(381,381,1,6,'full_access_to_the_tree','Acceso Completo al árbol'),(382,382,1,6,'system_administrator','Administrador del Sistema'),(383,383,1,1,'edit','Editar'),(384,384,1,5,'view_action','Visualizar'),(385,385,1,3,'view_action','Visualizar Planimetría'),(386,386,1,5,'edit_map','Editar Mapa'),(387,387,1,4,'assets','Activos'),(388,388,1,4,'add_asset_action','Agregar nuevos activos'),(389,389,1,4,'delete_asset_action','Eliminar activos'),(390,390,1,4,'serial_number','Número de serie'),(391,391,1,4,'internal_number','Número Interno'),(392,392,1,4,'asset_type','Tipo de Activo'),(393,393,1,4,'current_cost','Costo Actual'),(394,394,1,4,'purchase_date','Fecha de compra'),(395,395,1,4,'active_filter','Filtrar Activo'),(396,396,1,4,'active_editing','Edición de Activo'),(397,397,1,4,'additional','Adicional'),(398,398,1,4,'assurances','Garantías'),(399,399,1,4,'edit_assurances_action','Editar garantías'),(400,400,1,4,'add_assurances_action','Agregar Garantía'),(401,401,1,4,'delete_assurances_action','Eliminar Garantí­as'),(402,402,1,1,'provider_type','Tipo de Proveedor'),(403,403,1,4,'measurement','Lecturas'),(404,404,1,4,'edit_measurement_action','Editar lecturas del activo'),(405,405,1,4,'add_measurement_action','Agregar nuevas lecturas a los activos'),(406,406,1,4,'delete_measurement_action','Eliminar lecturas de los activos'),(407,407,1,4,'edit_asset_action','Editar activos'),(408,408,1,4,'edit_asset_type_action','Editar tipos de activos'),(409,409,1,4,'delete_asset_type_action','Eliminar tipos de activos'),(410,410,1,4,'add_asset_type_action','Agregar tipo de activos'),(411,411,1,4,'add_asset_status_action','Agregar tipos de estados para los activos'),(412,412,1,4,'edit_asset_status_action','Editar tipos de estados para los activos'),(413,413,1,4,'delete_asset_status_action','Eliminar tipos de estados para los activos'),(414,414,1,4,'add_asset_condition_action','Agregar condición a los activos'),(415,415,1,4,'edit_asset_condition_action','Editar condición a los activos'),(416,416,1,4,'delete_asset_condition_action','Eliminar condición a los activos'),(417,417,1,4,'add_asset_insurance_action','Agregar seguros a los activos'),(418,418,1,4,'edit_asset_insurance_action','Editar seguros de los activos'),(419,419,1,4,'delete_asset_insurance_action','Eliminar seguros de los activos'),(420,420,1,4,'add_asset_insurance_status_action','Agregar estados para las garantías'),(421,421,1,4,'edit_asset_insurance_status_action','Editar estados para las garantí­as'),(422,422,1,4,'delete_asset_insurance_status_action','Eliminar estados para las garantías'),(423,423,1,4,'export_list_asset_action','Exportar la lista de activos'),(427,427,1,4,'edit_measurement_title','Edición de la Lectura del Activo'),(428,428,1,4,'edit_assurances_title','Edición de la Garantía del Activo'),(429,429,1,4,'add_asset_title','Agregar Activo'),(430,430,1,4,'name_type_asset','Nombre del Tipo de Activo'),(431,431,1,4,'state_asset','Estado del Activo'),(432,432,1,4,'name_state','Nombre del Estado'),(433,433,1,4,'asset_condition','Condición del Activo'),(434,434,1,4,'condition_name','Nombre de la Condición'),(435,435,1,4,'edit_type_asset','Edición del Tipo de Activo'),(436,436,1,4,'add_asset_state','Agregar Estado al Activo'),(437,437,1,4,'edit_asset_state','Edición del Estado del Activo'),(438,438,1,4,'add_asset_condition','Agregar Condición al Activo'),(439,439,1,4,'edit_condition_asset','Edición de la Condición del Activo'),(440,440,1,4,'add_measurement','Agregar Lectura'),(441,441,1,4,'condition_successfully_eliminated','Condición  eliminada con éxito'),(442,442,1,4,'condition_not_eliminated_associated','La Condición no puede ser eliminada por estar asociada a un Activo'),(443,443,1,4,'state_not_delete_associated','El Estado no puede ser eliminado por estar asociada a un Activo'),(444,444,1,4,'type_assets_not_eliminated_associated_assets','El Tipo de Activo no puede ser eliminado por estar asociada a un Activo'),(445,445,1,4,'num_series','Núm. Serie'),(446,446,1,4,'num_series_internal','Núm. Serie Interno'),(447,447,1,4,'purchase_value','Valor de Compra'),(448,448,1,4,'present_value','Valor Actual'),(449,449,1,4,'lifetime','Años de vida Útil '),(450,450,1,4,'end_date','Fecha de término'),(451,451,1,4,'msg_end_date_lifetime','Seleccione un rango de fechas para buscar términos de vida útil de los activos'),(452,452,1,4,'edition_the_active_document','Edición del Documento del Activo'),(453,453,1,4,'date_lifetime','Término de la vida Útil'),(454,454,1,4,'save_changes','Guardar Cambios'),(455,455,1,4,'in_progress','En Curso'),(456,456,1,4,'expired','Vencida'),(457,457,1,1,'brand','Marca'),(458,458,1,4,'registered_successfully_assurance','Garantía registrada con éxito'),(459,459,1,4,'updated_successfully_assurance','Garantía actualizada con éxito'),(460,460,1,4,'successfully_eliminated_assurance','Garantía eliminada con éxito'),(461,461,1,4,'not_delete_mark_for_associated_an_active','No se puede Eliminar la Marca por estar asociada a un Activo'),(462,462,1,4,'edit_brand','Editar Marca'),(463,463,1,4,'add_brand','Agregar Marca'),(464,464,1,4,'name_brand','Nombre Marca'),(465,465,1,4,'interval_or_range','Intervalo o Rango'),(466,466,1,4,'type_of_asset_configuration_is_deleted_successfully','Configuración de tipo de activo eliminada con éxito'),(467,467,1,4,'addressees','Destinatarios'),(468,468,1,4,'new_configuration','Nueva Configuración'),(469,469,1,4,'when_registering_a_measurement','Al Registrar una Medición'),(470,470,1,4,'equals','A cada'),(471,471,1,4,'operating_out_of_range','Fuera del Rango de Operación'),(472,472,1,4,'mail_addresses_separated_by_commas','Mail de los destinatarios separados por comas ( , ) '),(473,473,1,4,'send_email','Mail (Enviar correo Electrónico)'),(474,474,1,4,'send_text_messages_to_cell','SMS (Enviar mensajes de Texto al Celular)'),(475,475,1,4,'there_interval','Existe un intervalo para este Tipo de Activo con esta Medici&oacute;n'),(476,476,1,4,'there_range','Existe un rango para este Tipo de Activo con esta Medici&oacute;n'),(477,477,1,4,'editing_the_configuration_of_reading','Edición de la configuración de la Lectura'),(478,478,1,4,'not_notification','Sin Notificación'),(479,479,1,4,'mail','Mail'),(480,480,1,4,'sms','SMS'),(481,481,1,4,'mail_and_sms','Mail y SMS'),(482,482,1,7,'maintenance','Mantenimiento'),(483,483,1,7,'generate_work_order','Generar Orden de Trabajo'),(484,484,1,7,'folio','Folio'),(485,485,1,7,'type_ot','Tipo de O.T.'),(486,486,1,7,'new_work_order','Nueva Orden de Trabajo'),(487,487,1,7,'error_creating_ot','Error al crear la OT'),(488,488,1,7,'ot_cancelled','O.T. Anulada'),(489,489,1,1,'task','Tarea'),(490,490,1,1,'task_name','Nombre Tarea'),(491,491,1,7,'input','Insumo'),(492,492,1,7,'other_costs','Otros Costos'),(493,493,1,7,'name_costs','Nombre Costos'),(494,494,1,7,'planning','Planificación'),(495,495,1,7,'start','Iniciar el'),(496,496,1,7,'finish','Terminar el'),(497,497,1,7,'current_status','Estado actual'),(498,498,1,7,'value_service','Valor Servicio'),(499,499,1,7,'total_other_costs','Total otros costos'),(500,500,1,7,'total_ot','Total O.T.'),(501,501,1,7,'add_task','Agregar Tarea'),(502,502,1,7,'Select_input_using_task','Seleccione los insumos a usar en la tarea'),(503,503,1,7,'input_name','Nombre de Insumo'),(504,504,1,7,'amount_greater_zero','La cantidad debe ser mayor a 0 (cero)'),(505,505,1,7,'unit_price','Precio Unitario'),(506,506,1,7,'total_price','Precio Total'),(507,507,1,7,'add_other_costs','Agregar Otros Costos'),(508,508,1,7,'name_costs','Nombre del Costo'),(509,509,1,7,'work_order_number','Orden de Trabajo Nº'),(510,510,1,7,'edit_task','Editar Tarea'),(511,511,1,7,'edit_other_costs','Editar Otros Costos'),(512,512,1,7,'add_wo_action','Crear nuevas órdenes de trabajo'),(513,513,1,7,'edit_wo_action','Editar las órdenes de trabajo'),(514,514,1,7,'delete_wo_action','Eliminar las órdenes de trabajo'),(515,515,1,7,'add_possibles_status_wo_action','Agregar nuevos estados posibles para las OT'),(516,516,1,7,'edit_possibles_status_wo_action','Editar los posibles estados para las OT'),(517,517,1,7,'delete_possibles_status_wo_action','Eliminar los posibles estados para la OT'),(518,518,1,7,'add_types_wo_action','Agregar nuevos tipos de OT'),(519,519,1,7,'edit_types_wo_action','Editar los tipos de OT'),(520,520,1,7,'delete_types_wo_action','Eliminar los tipos de OT'),(521,521,1,7,'add_other_costs_action','Agregar un costo a la lista de otros costos'),(522,522,1,7,'edit_other_costs_action','Editar un costo de la lista de otros costos'),(523,523,1,7,'delete_other_costs_action','Eliminar un costo a la lista de otros costos'),(524,524,1,7,'add_wo_other_costs_action','Agregar otros costos a las OT'),(525,525,1,7,'edit_wo_other_costs_action','Editar otros costos a la OT'),(526,526,1,7,'delete_wo_ohter_costs_action','Eliminar los otros costos asociados a la OT'),(527,527,1,7,'add_task_action','Agregar nuevas tareas para los servicios en las OT'),(528,528,1,7,'edit_task_action','Editar las tareas para los servicios en las OT'),(529,529,1,7,'delete_task_action','Eliminar las tareas para los servicios de las OT'),(530,530,1,7,'add_task_wo_action','Agregar tareas a los servicios de la OT'),(531,531,1,7,'edit_task_wo_action','Editar tareas de los servicios de la OT'),(532,532,1,7,'delete_task_wo_action','Eliminar las tareas a los servicios de la OT'),(533,533,1,7,'add_component_task_wo_action','Agregar insumos a las tareas en los servicios de la OT'),(534,534,1,7,'edit_component_task_wo_action','Editar los insumos a las tareas en los servicios de la OT'),(535,535,1,7,'delete_component_task_wo_action','Eliminar los insumos a las tareas en los servicios de la OT'),(536,536,1,7,'add_compponent_action','Agregar nuevos insumos al listado de insumos'),(537,537,1,7,'edit_component_action','Editar los insumos del listado de insumos'),(538,538,1,7,'delete_component_action','Eliminar los insumos del listado de insumos'),(539,539,1,7,'add_type_component_action','Agregar nuevos tipos de insumos'),(540,540,1,7,'edit_type_component_action','Editar los tipos de insumos'),(541,541,1,7,'delete_type_component_action','Eliminar los tipos de insumos'),(542,542,1,7,'add_wo_flow_action','Configurar el flujo de estados para los tipos de OT'),(543,543,1,7,'edit_wo_flow_action','Editar la configuración del flujo de estados para los tipos de OT'),(544,544,1,7,'delete_wo_flow_action','Eliminar la configuración del flujo de estados para los tipos OT'),(545,545,1,7,'delete_component_price_list_action','Eliminar insumos de la lista de precios'),(546,546,1,7,'subject_mail_predictive_ot','OT predictiva generada con folio'),(547,547,1,7,'readings_settings','Configuración de lecturas'),(548,548,1,7,'notification_type','Tipo de notificación '),(549,549,1,7,'add_configuration_reading','Agregar configuración a la Lectura'),(550,550,1,7,'general_information','Datos Generales'),(551,551,1,8,'requests','Solicitudes'),(552,552,1,8,'add_request','Agregar Solicitud'),(553,553,1,8,'please_select_node','¡Oops!. Favor seleccionar nodo...'),(554,554,1,8,'approve','Aprobar'),(555,555,1,8,'reject','Rechazar'),(556,556,1,8,'edit_request','Editar Solicitud'),(557,557,1,8,'problem','Problema'),(558,558,1,8,'subject','Asunto'),(559,559,1,8,'team_fail','Equipo/Falla'),(560,560,1,8,'team','Equipo'),(561,561,1,8,'failure','Falla'),(562,562,1,8,'applicant_details','Datos Solicitante'),(563,563,1,6,'loading','Cargando'),(564,564,1,4,'dynamic_data_assets','Datos Dinámicos (Activos)'),(565,565,1,4,'dynamic_data_associate_assets','Asociar Datos Dinámicos (Activos)'),(566,566,1,6,'full_access','¿Acceso Completo?'),(567,567,1,4,'dynamic_data_not_eliminated_by_being_associated','El Dato Dinámico  no puede ser eliminado por estar asociado a un Tipo de Activo.'),(568,568,1,4,'with_success_save_asset','Información del activo guardada con éxito.'),(569,569,1,4,'with_success_save_asset','Información del activo guardada con éxito.'),(570,570,1,4,'registered_with_success_read','Lectura registrada con éxito'),(571,571,1,9,'reports','Reportes'),(572,572,1,1,'operation_successful','Operación realizada con éxito'),(573,573,1,1,'operation_not_performed','Operación no realizada'),(574,574,1,7,'plan','Plan'),(575,575,1,7,'component_type','Tipo de Componente'),(576,576,1,7,'component','Componente'),(577,577,1,7,'weight','Peso'),(578,578,1,7,'model','Modelo'),(579,579,1,7,'manufacturer','Fabricante'),(580,580,1,1,'list_price','Lista de Precio'),(581,581,1,7,'provider_name','Nombre Proveedor'),(582,582,1,7,'you_have_to_select_an_item_to_set','Usted tiene que seleccionar un ítem para configurar'),(583,583,1,7,'task_time_days','Tiempo Tarea (días)'),(584,584,1,7,'add_plan','Agregar Plan'),(585,585,1,7,'adit_plan','Editar Plan'),(586,586,1,7,'the_plan_task_login','Ingresar Tarea Al Plan'),(587,587,1,7,'periodicity_days','Periodicidad (días)'),(588,588,1,7,'configuration_plan','Configuración del Plan'),(589,589,1,7,'edit_plan_task','Editar Tarea del Plan'),(590,590,1,7,'add_type_ot','Agregar Tipo de O.T.'),(591,591,1,7,'type_name_ot','Nombre de Tipo de O.T.'),(592,592,1,7,'duration_of_type_ot_in_hours','Duración del Tipo de la O.T. en (Horas)'),(593,593,1,7,'edit_type_ot','Editar Tipo de O.T.'),(594,594,1,7,'add_component_type','Agregar Tipo de Componente'),(595,595,1,7,'component_type_name','Nombre del Tipo de Componente'),(596,596,1,7,'edit_component_type','Editar Tipo de Componente'),(597,597,1,7,'add_component','Agregar Componente'),(598,598,1,7,'edit_component','Editar Componente'),(599,599,1,7,'name_of_other_costs','Nombre de Otros Costos'),(600,600,1,7,'add_price_list','Agregar Lista de Precio'),(601,601,1,7,'edit_price_list','Editar Precio de Lista'),(602,602,1,7,'setting_the_price_list','Configuración de la Lista de Precios'),(603,603,1,7,'component_name','Nombre Componente'),(604,604,1,7,'enter_components_list_price','Ingresar Componentes a la Lista de Precio'),(605,605,1,7,'currency','Moneda'),(606,606,1,1,'optical_fiber','Fibra Óptica'),(607,607,1,7,'you_can_not_delete_because_it_is_associated','No se puede Eliminar por estar Asociado'),(608,608,1,5,'map','Mapa'),(609,609,1,1,'action','Acción'),(610,610,1,1,'date_time','Fecha/Hora'),(611,611,1,1,'move','Mover'),(612,612,1,1,'select','Seleccionar'),(613,613,1,1,'relocate','Reubicar'),(614,614,1,1,'perform_internal_search','Realizar búsqueda interna'),(615,615,1,1,'unsubscribe','Dar de Baja'),(616,616,1,1,'responsible_mail','Correo responsable'),(617,617,1,2,'keywords','Palabras Claves'),(618,618,1,2,'select_date_range_of_document_upload','Seleccione un rango de fechas de carga del documento'),(619,619,1,2,'select_range_of_document_expiration_dates','Seleccione un rango de fechas de expiración del documento'),(620,620,1,2,'alert_days','Días de Alerta'),(621,621,1,2,'version_should_be_entered_as','Debe ingresarlo como Versión'),(622,622,1,2,'you_must_enter_the_same_document','Debe ingresar el mismo documento'),(623,623,1,3,'set_category_for_linking_nodes','Establecer categoría para la vinculación de los nodos'),(624,624,1,3,'last_version','Última Versión'),(625,625,1,4,'asset_log_creation','Alta'),(626,626,1,4,'asset_log_move','Reubicación'),(627,627,1,4,'export_plancheta','Exportar Plancheta'),(628,628,1,6,'root_node','Root Node'),(629,629,1,9,'name_report','Nombre del Reporte'),(630,630,1,7,'contracts','Contratos'),(631,631,1,1,'are_you_sure_you_want_to_delete','¿Está seguro que desea Eliminar?'),(632,632,1,7,'add_contracts','Agregar Contratos'),(633,633,1,7,'contract_edit','Edición del Contrato'),(634,634,1,7,'assets_associated_with_the_contract','Activos Asociados al Contrato'),(635,635,1,4,'name_asset','Nombre de Activo'),(636,636,1,7,'add_to_the_contract_assets','Agregar Activos al Contrato'),(637,637,1,6,'location','Ubicación'),(638,638,1,7,'you_must_select_at_least_one_activity_to_add','Usted debe seleccionar al menos un activo para agregar'),(639,639,1,5,'manage_maps','Administrar Mapas'),(640,640,1,7,'associate_plan_assets','Asociar Activo al Plan'),(641,641,1,7,'you_must_select_at_least_one_active_to_associate','Usted debe seleccionar al menos un activo para asociar'),(642,642,1,7,'enter_the_supplier_should_be_obliged','Debe Ingresar el proveedor por obligación'),(643,643,1,7,'setting_up_plan','Configurando el Plan'),(644,644,1,1,'users_and_providers','Usuarios y Proveedores'),(645,645,1,7,'delete_sure_the_relation','¿Está seguro de Eliminar la relación?'),(646,646,1,1,'associate_users_and_provider','Asociar Usuarios y Proveedor'),(647,647,1,7,'change_dates','Cambiar Fechas'),(648,648,1,7,'select_range_of_dates_of_creation_of_the_ot','Seleccione Rango de Fechas de Creación de la O.T.'),(649,649,1,1,'route','Ruta'),(650,650,1,7,'do_not_believe_the_ot_you_must_first_select_an_asset','No se creó la O.T. Usted debe seleccionar un Activo primero'),(651,651,1,7,'error_when_entering_the_job','Error al ingresar la tarea'),(652,652,1,1,'asset_search','Buscador de Activo'),(653,653,1,7,'new_date','Nueva Fecha'),(654,654,1,8,'select_a_date_range_to_for_the_request','Seleccione un rango de fechas de solicitudes'),(655,655,1,8,'approve_request','Aprobar Solicitud'),(656,656,1,8,'do_you_want_to_Approve_the_request','¿Desea Aprobar la Solicitud?'),(657,657,1,8,'request_reject','Rechazar Solicitud'),(658,658,1,8,'do_you_want_to_reject_the_request','¿Desea Rechazar la Solicitud?'),(659,659,1,4,'asset_details','Detalles Activo'),(660,660,1,3,'show_all','Mostrar Todos'),(661,661,1,3,'hide_all','Ocultar Todos'),(662,662,1,4,'inventory','Inventario'),(663,663,1,4,'upload_file','Subir Archivo'),(664,664,1,4,'translate','Trasladar'),(665,665,1,4,'sure_move_assets_to_the_site','¿Está seguro de trasladar activos al recinto?'),(666,666,1,4,'you_must_select_an_Item_to_translate','Usted debe seleccionar un ítem para trasladar'),(667,667,1,4,'origin_return','Retornar Origen'),(668,668,1,4,'sure_to_return_assets_to_room_of_origin','¿Está seguro de retornar activos a recinto de origen?'),(669,669,1,4,'original_location','Ubicación Original'),(670,670,1,4,'inventory_update','Actualizar Inventario'),(671,671,1,4,'archive','Archivo'),(672,672,1,4,'perform_movement_of_assets','Realizar movimiento de activos'),(673,673,1,4,'generate_report_of_inconsistency','Generar reporte de incongruencia'),(674,674,1,4,'processing_file','Procesando archivo...'),(675,675,1,4,'tracking','Tracking'),(676,676,1,1,'problems','Problemas'),(677,677,1,8,'request_problem','Solicitud Problema'),(678,678,1,1,'user_type','Tipo Usuario'),(679,679,1,1,'user_provider','Usuario proveedor'),(680,680,1,7,'tolerance','Tolerancia'),(681,681,1,7,'tolerance_to_100','Tolerancia al %'),(682,682,1,1,'all','Todas'),(683,683,1,1,'please_retry_general_error','Error general...favor reintentar'),(684,684,1,8,'request_n','Nro. Solicitud'),(685,685,1,1,'warning','¡Advertencia!'),(686,686,1,1,'help','Ayuda'),(687,687,1,1,'movements','Movimientos'),(688,688,1,4,'location_transfer','Ubicación Traslado'),(689,689,1,4,'the_value_is_equal_to_the_last_inserted_reading','El Valor es  Igual que la Última Lectura Insertada'),(690,690,1,4,'the_value_is_less_than_the_last_reading_inserted','El Valor es Menor que la Última Lectura Insertada'),(691,691,1,4,'need_provider_asset','Necesita un proveedor el activo'),(692,692,1,8,'only_the_state_can_change_applications_issued','Solo se permite cambiar el estado a las solicitudes \"Emitidas\"'),(693,693,1,2,'download_zip','Descargar Zip'),(694,694,1,1,'mail_notification','Notificación Correo'),(695,695,1,1,'expiration_mail_alert','Alerta Correo Expiración'),(696,696,1,1,'back_to_search','Volver a buscar'),(697,697,1,7,'editing_state_of_the_ot','Edición del Estado de la O.T.'),(698,698,1,7,'add_to_ot_state','Agregar Estado a la O.T.'),(699,699,1,1,'state_ot','Estado de la O.T.'),(700,700,1,7,'configuring_the_states_of_the_ot','Configuración de los Estados de la O.T.'),(701,701,1,1,'user_access','Acceso Usuario'),(702,702,1,1,'provider_access','Acceso Proveedor'),(703,703,1,7,'duration_of_status','Duración del Estado (Hrs)'),(704,704,1,7,'delete_is_really_sure_in_this_configuration_the_state','¿Está Realmente seguro de Eliminar el Estado en esta configuración?'),(705,705,1,7,'scroll_down','Desplazar abajo'),(706,706,1,7,'you_must_select_a_state_to_move_up','Usted debe seleccionar un estado para mover arriba'),(707,707,1,7,'scroll_up','Desplazar arriba'),(708,708,1,7,'you_must_select_a_state_to_move_down','Usted debe seleccionar un estado para mover abajo'),(709,709,1,7,'edit_configuration_states','Editar Configuración de Estados'),(710,710,1,7,'include_closed_orders','Incluir Órdenes Cerradas'),(711,711,1,7,'created_by','Creado por :'),(712,712,1,7,'closed_order','Orden Cerrada'),(713,713,1,7,'change_of_status','Cambio de Estado'),(714,714,1,7,'range','Rango'),(715,715,1,4,'State_configuration_for_the_type_of_protective_order','Configurar estado para el tipo de orden Preventivo'),(716,716,1,4,'move_assets','Mover Activos'),(717,717,1,4,'asset_export','Exportar Activos'),(718,718,1,4,'view_assets','Visualizar Activos'),(719,719,1,7,'view_maintenance','Visualizar Mantenimiento'),(720,720,1,7,'add_work_order','Agregar Orden de Trabajo'),(721,721,1,7,'edit_work_order','Editar Orden de Trabajo'),(722,722,1,9,'view_reports','Visualizar Reportes'),(723,723,1,8,'add_report','Agregar Solicitud'),(724,724,1,8,'export_request','Exportar Solicitud'),(725,725,1,2,'view_documents ','Visualizar Documentos'),(726,726,1,5,'data_export','Exportar Datos'),(727,727,1,3,'export_data','Exportar Datos'),(728,728,1,3,'delete_versions','Eliminar Versiones'),(729,729,1,7,'you_must_select_a_node_to_find','Debes seleccionar un nodo para buscar.'),(730,730,1,4,'reading_configuration_and_entered','Configuración de la Lectura ya ingresada'),(731,731,1,2,'successfully_deleted_document','Documento eliminado con éxito'),(732,732,1,10,'add_cost','Agregar Gasto'),(733,733,1,1,'january','Enero'),(734,734,1,1,'february','Febrero'),(735,735,1,1,'march','Marzo'),(736,736,1,1,'april','Abril'),(737,737,1,1,'may','Mayo'),(738,738,1,1,'june','Junio'),(739,739,1,1,'july','Julio'),(740,740,1,1,'august','Agosto'),(741,741,1,1,'september','Septiembre'),(742,742,1,1,'october','Octubre'),(743,743,1,1,'november','Noviembre'),(744,744,1,1,'december','Diciembre'),(745,745,1,10,'costs','Gastos'),(746,746,1,1,'month','Mes'),(747,747,1,1,'year','Año'),(748,748,1,10,'ballot_number_or_invoice','Nº de Documento'),(749,749,1,10,'edit_cost','Editar Gasto'),(750,750,1,10,'export_costs','Exportar Gastos'),(751,751,1,1,'transaction_management','Administración de Transacciones'),(752,752,1,1,'type_of_action','Tipo de Acción'),(753,753,1,1,'ip','IP'),(754,754,1,1,'select_date_range_for_which_to_search','Seleccione un rango de fecha por el cual desea buscar'),(755,755,1,1,'log_details','Detalles del Log'),(756,756,1,1,'field_name','Nombre del Campo'),(757,757,1,1,'before','Antes'),(758,758,1,1,'after','Después'),(759,759,1,1,'export_log','Exportar Log'),(760,760,1,1,'elimination_confirmation_message','¿Está realmente seguro de borrar la categoría del Nodo?'),(761,761,1,1,'cost_not_to_delete_message_being_associated','El Nombre del Gasto no puede ser eliminado por estar asociada a un Gasto.'),(762,762,1,1,'elimination_confirmation_message_costs','¿Está realmente seguro de borrar el Nombre de Gasto?'),(763,763,1,10,'add_name_cost','Agregar Nombre de Gasto'),(764,764,1,10,'edit_name_costs','Editar Nombre de Gasto'),(765,765,1,2,'bulk_upload_zip','Carga Masiva Zip'),(766,766,1,10,'concept','Concepto'),(767,767,1,1,'there_is_a_name','Existe el Nombre'),(768,768,1,3,'sure_to_remove_all_information_associated_with_the_node','¿Está seguro de eliminar el Nodo y toda la información asociada al Nodo?'),(769,769,1,4,'edit_document','Editar Documento'),(770,770,1,4,'add_document_title','Agregar Documento'),(771,771,1,1,'root_node','Nodo Raíz'),(772,772,1,10,'get_cost','Visualizar Costos'),(773,773,1,10,'delete_cost','Eliminar Gasto'),(774,774,1,1,'report_permissions','Permisos del Reporte'),(775,775,1,1,'available_groups','Grupos Disponibles'),(776,776,1,1,'groups_associated','Grupos Asociados'),(777,777,1,1,'must_select_a_report','Debe seleccionar un reporte'),(778,778,1,8,'work_order_number','Orden de Trabajo Nro.  '),(779,779,1,1,'you_have_to_select_an_item_to_set','Debe seleccionar un item'),(780,780,1,4,'can_not_be_relocated_in_the_same_place','No se puede reubicar en el mismo lugar'),(781,781,1,4,'assets_can_not_be_eliminated_by_being_associated_with_an_ot','El Activo no puede ser eliminado por estar asociado a una OT'),(782,782,1,4,'asset_log_low','Activo dado de baja'),(783,783,1,1,'maintain_the_configuration','Mantener la configuración'),(784,784,1,1,'leaving_the_existing_configuration_and_adding_the_change','Dejando la configuración ya existente y agregando el cambio'),(785,785,1,2,'contract_expiration_warning_notice','Alerta aviso vencimiento contrato'),(786,786,1,2,'mail_document_name','NOMBRE DOCUMENTO : '),(787,787,1,2,'mail_category','CATEGORIA : '),(788,788,1,2,'mail_version','VERSION : '),(789,789,1,2,'mail_description','DESCRIPCION : '),(790,790,1,2,'mail_expiration_date','FECHA DE EXPIRACION : '),(791,791,1,2,'mail_location','UBICACION : '),(792,792,1,2,'mail_alert_document','Alerta de Documento – iGeo'),(793,793,1,8,'request_export_folio','FOLIO'),(794,794,1,8,'request_export_asset','ACTIVO'),(795,795,1,8,'request_export_location','UBICACION'),(796,796,1,8,'request_export_problem','PROBLEMA'),(797,797,1,8,'request_export_subject','ASUNTO'),(798,798,1,8,'request_export_creation_date','FECHA CREACION'),(799,799,1,8,'request_export_state','ESTADO'),(800,800,1,8,'request_view','Visualizar Solicitudes'),(801,801,1,2,'detail_excel_format','Formato Excel Detalle'),(802,802,1,2,'file_name','Nombre Archivo'),(803,803,1,2,'bulk_upload_zip_excel','Carga Masiva Zip + Excel (Detalle)'),(804,804,1,2,'bulk_upload_documents','Carga Masiva de Documentos'),(805,805,1,2,'download_excel_format','Descarga Formato Excel (Detalle)'),(806,806,1,2,'select_a_zip','Seleccionar un Zip (.zip)'),(807,807,1,2,'select_a_excel','Seleccionar un Excel (.xls)'),(808,808,1,2,'document_details','Detalle del Documento'),(809,809,1,2,'can_not_open_the_zip_file','No se puede Abrir el Archivo ZIP'),(810,810,1,2,'extensions_not_allowed_in_zip_file','Extensiones No permitidas en archivo zip'),(811,811,1,2,'the_amount_of_zip_files_is_different_the_line_valid_file_excel','La cantidad de archivos del zip, es distinta, a las lineas validas del Archivo Excel'),(812,812,1,2,'category_not_valid','Categoria No valida'),(813,813,1,2,'file_not_found','Archivo No Encontrado'),(814,814,1,8,'request_approve_view','Aprobar y Ver OT.'),(817,817,1,4,'tasks_associated_asset_class','Tareas Asociadas a Tipo de Activo'),(818,818,1,4,'login_to_type_active_task','Ingresar Tarea al Tipo de Activo'),(819,819,1,1,'not_editable','No Editable'),(820,820,1,1,'checkbox','Checkbox'),(821,821,1,4,'date_inventory','Fecha Inventario'),(822,822,1,4,'date_last_inventory','Fecha Último Inventario'),(823,823,1,2,'message_download_items','Usted debe seleccionar un ítem para Descargar'),(824,824,1,2,'the_file_has_to_be_zip','El Archivo Tiene que ser Zip'),(832,832,1,1,'select_date_range_to_search_active_load','Seleccione un rango de fechas para buscar carga de Activo'),(833,833,1,2,'this_sure_restore_or_document','Está Seguro de Restaurar'),(834,834,1,1,'you_must_select_at_least_one_record','Debe seleccionar al menos un registro'),(835,835,1,1,'restore','Restaurar'),(836,836,1,1,'bin','Papelera'),(837,837,1,1,'this_insurance_send_to_the_trash_or_document','Está Seguro de Enviar a la Papelera'),(838,838,1,1,'successfully_restored_record','Registro(s) Restaurado(s) con &eacute;xito'),(839,839,1,1,'sent_to_the_trash_registration_successfully','Registro(s) Enviado(s) a la Papelera con &eacute;xito'),(840,840,1,5,'Infra_maintenance','Mantención Infra'),(841,841,1,5,'budget','Presupuesto'),(842,842,1,1,'total','Total'),(843,843,1,1,'tasks','Tareas'),(844,844,1,1,'node','Nodo'),(845,845,1,1,'applicant','Solicitante'),(846,846,1,1,'responsible','Responsable'),(847,847,1,1,'detail_plan','Detalle Plan'),(848,848,1,1,'please_select_at_least_one_record_to_delete','Favor seleccionar al menos un registro para eliminar'),(849,849,1,1,'remove_your_task','Desea eliminar la Tarea?'),(850,850,1,1,'rrecord','Registro'),(851,851,1,1,'new_task','Nueva Tarea'),(852,852,1,1,'working_time','Tiempo de Trabajo'),(853,853,1,1,'edit_list','Editar Lista'),(854,854,1,1,'general','Generales'),(855,855,1,1,'associated_tasks','Tareas Asociadas'),(856,856,1,1,'measure','Medida'),(857,857,1,1,'associating_tasks','Asociar Tareas'),(858,858,1,1,'new_applicant','Nuevo Solicitante'),(859,859,1,1,'edit_applicant','Editar Solicitante'),(860,860,1,1,'new_manager','Nuevo Responsable'),(861,861,1,1,'edit_responsible','Editar Responsable'),(862,862,1,1,'group_name','Nombre Grupo'),(863,863,1,1,'group_tags','Etiquetas del Grupo'),(864,864,1,1,'add_groups','Agregar Grupos'),(865,865,1,1,'vview','Vista'),(866,866,1,1,'ddefault','Defecto'),(867,867,1,1,'architecture','Arquitectura'),(868,868,1,1,'campus','Recinto'),(869,869,1,4,'chair','Silla'),(870,870,1,4,'table','Mesa'),(871,871,1,4,'projector','Proyector'),(872,872,1,4,'split','Split'),(873,873,1,4,'not_registered_in_active_igeo','Activo no registrado en Igeo.'),(874,874,1,4,'node_not_registered','Nodo no registrado.'),(875,875,1,4,'it_has_not_been_moved_from_Hometown','No se ha movido del Lugar de origen'),(876,876,1,5,'attribute_has_an_associate','Tiene un atributo Asociado'),(877,877,1,5,'living_area','Superficie Útil'),(878,878,1,1,'commune','Comuna'),(879,879,1,1,'branch','Sucursal'),(880,880,1,1,'address','Dirección'),(881,881,1,1,'deputy_manager','SubGerencia'),(882,882,1,1,'property_type','Tipo de Propiedad'),(883,883,1,1,'branch_category','Categoría  Sucursal'),(884,884,1,1,'branch_code','Código  Sucursal'),(885,885,1,1,'cost_center_branch','Centro de Costo Sucursal'),(886,886,1,1,'organizational_unit','Unidad Organizacional'),(887,887,1,1,'staffing','Dotación de personal'),(888,888,1,1,'womens_purse','Dotación Femenina'),(889,889,1,1,'men_purse','Dotación Masculina'),(890,890,1,1,'region','Región'),(891,891,1,1,'floor','Piso'),(892,892,1,1,'type_of_lighting','Tipo de Iluminación'),(893,893,1,1,'type_of_furniture','Tipo de Mobiliario'),(894,894,1,1,'type_of_floor','Tipo de Piso'),(895,895,1,1,'using_type','Tipo de Uso'),(896,896,1,1,'venue_name','Nombre de Recinto'),(897,897,1,1,'height','Altura'),(898,898,1,1,'type_sky','Tipo de Cielo'),(899,899,1,1,'serviestado','ServiEstado'),(900,900,1,1,'date_time_charge','Fecha/hora Carga'),(901,901,1,1,'access','Accesos'),(902,902,1,1,'city','Ciudad'),(903,903,1,1,'name_of_lessor','Nombre de Arrendador'),(904,904,1,1,'ruth_landlord','Rut Arrendador'),(905,905,1,1,'term_contract','Vigencia Contrato'),(906,906,1,1,'income_in_pesos','Renta en Pesos'),(907,907,1,1,'rent_uf','Renta en UF'),(908,908,1,1,'overall','Totales'),(909,909,1,1,'type_of_contract','Tipo de Contrato'),(910,910,1,1,'to_value','Valor Tasación'),(911,911,1,5,'capacity_used','CAPACIDAD UTILIZADA'),(912,912,1,5,'platform','PLATAFORMA'),(913,913,1,5,'trade_platform','PLATAFORMA COMERCIAL'),(914,914,1,5,'area','AREA'),(915,915,1,1,'amount_posts','Cantidad Puestos'),(916,916,1,1,'distribution','Distribución'),(917,917,1,1,'enclosure_type','Tipo Recinto'),(918,918,1,1,'climate_exchange','Tipo Climatización'),(919,919,1,1,'termination_floor','Terminación Piso'),(920,920,1,1,'wall_termination','Terminación Muro'),(921,921,1,1,'termination_sky','Terminación Cielo'),(922,922,1,1,'faculty','Facultad'),(923,923,1,5,'apportionment','Prorrateo'),(924,924,1,1,'send_to_trash','Enviar a Papelera'),(925,925,1,1,'view_versions','Ver Versiones'),(926,926,1,1,'versions_of_stock','Versiones de Imagenes'),(927,927,1,1,'replicating_settings','Replicar Configuración'),(928,928,1,1,'selecting_nodes_to_replicate_types_configuration','Selección de Tipos de Nodos a Replicar la Configuración'),(929,929,1,1,'answer','Replicar'),(930,930,1,1,'you_must_select_one_or_more_types_of_nodes_to_replicate','Debe seleccionar uno o mas tipos de nodos para replicar'),(931,931,1,7,'the_contract_associates_venues','Recintos Asociados al Contrato'),(932,932,1,1,'trade_route','Ruta Recinto'),(933,933,1,1,'you_must_select_a_node','Debe seleccionar un Nodo'),(934,934,1,7,'add_to_campus_contract','Agregar Recinto al Contrato'),(935,935,1,7,'associate_venues_plan','Asociar Recintos al Plan'),(936,936,1,7,'you_must_select_at_least_one_node_to_associate','Usted debe seleccionar al menos un Nodo para Asociar'),(937,937,1,7,'you_should_look_for_some_type_of_enclosure','Debe buscar por algun tipo de recinto'),(938,938,1,7,'you_must_add_a_provider_under_contract','Debe agregar un Proveedor mediante un contrato'),(939,939,1,1,'duration_of_task','Duración de la Tarea (Horas)'),(940,940,1,7,'must_be_within_the_folder_locations_to_create_work_order','Debe estar dentro de la Carpeta Sucursales Para Crear Orden de Trabajo'),(941,941,1,1,'showing','Mostrando {0} - {1} de {2}'),(942,942,1,1,'no_results','Sin resultados.'),(943,943,1,7,'contract_for_supplier_selection','Selección de contrato para Proveedor'),(944,944,1,7,'direct_costs','Costos Directos'),(945,945,1,7,'name_direct_cost','Nombre Costo Directo'),(946,946,1,7,'percentage','Porcentaje'),(947,947,1,7,'total_direct_costs','Total Costos Directos'),(948,948,1,7,'total_work_order','Total O.T. +(iva)'),(949,949,1,7,'input_type','Tipo de Insumo'),(950,950,1,1,'email_resolver','Correo Resolutor'),(951,951,1,8,'campus_fail','Recinto/Falla'),(952,952,1,7,'select_a_type_of_node_or_save_configuration','Seleccione un Tipo de Nodo o guarde la configuración del Nodo Seleccionado o bien el Tipo de Nodo Seleccionado no Tiene Configuración'),(953,953,1,7,'exists_configuration_node_type','Ya Existe Configuración en Tipo de Nodo:'),(954,954,1,7,'if_you_want_to_delete_the_component_must_first_delete','Si Desea Eliminar el Componente Debe Eliminar Primero la OT que la contiene en su lista de Insumo de la Tarea'),(955,955,1,7,'you_can_not_delete_the_system_reserved_for_types','NO SE PUEDEN BORRAR LOS TIPOS RESERVADOS PARA EL SISTEMA'),(956,956,1,7,'there_is_no_value_Uf','No Existe Valor Uf Para la Fecha de creación de la OT'),(957,957,1,5,'campus','Campus'),(958,958,1,5,'building','Edificio'),(959,959,1,7,'display_work_order','Visualizar Orden de Trabajo'),(960,960,1,7,'print_work_order','Imprimir Orden de Trabajo'),(961,961,1,7,'delete_task','Eliminar Tarea'),(962,962,1,7,'save_ot','Guardar Orden de Trabajo'),(963,963,1,7,'display_contract','Visualizar Contratos'),(964,964,1,7,'clear_contract','Eliminar Contratos'),(965,965,1,7,'edit_contract','Editar Contratos'),(966,966,1,7,'set_contract','Configurar Contratos'),(967,967,1,7,'no_supplier','No existe proveedor'),(968,968,1,1,'maintenance_type','Tipo de Mantenimiento'),(969,969,1,1,'asset_load_date','Fecha de Carga del Activo'),(970,970,1,1,'you_can_not_delete_the_group','No se puede Eliminar el Grupo por tener asociada a un Usuario'),(971,971,1,4,'load_collect','Cargar Collect'),(972,972,1,1,'generate_reports','Generar Reportes'),(973,973,1,4,'finish_collect','Finalizar COLLECT (Limpiar Tablas)'),(974,974,1,4,'this_insurance_empty_tables','Esta seguro de Vaciar las Tablas'),(975,975,1,1,'bulk_generate_reports','Generar Reportes Masivos'),(976,976,1,4,'generate_report_inventory_control','Generar reporte de Control de Inventario'),(977,977,1,4,'generate_report_missing','Generar reporte de Faltantes'),(978,978,1,4,'generate_report_transferred','Generar reporte de Trasladados'),(979,979,1,4,'generate_report_unregistered','Generar reporte de no Registrados en IGEO'),(980,980,1,4,'generate_asset_report_without_changes','Generar reporte de Activos sin Cambios'),(981,981,1,1,'download','Descargar'),(982,982,1,4,'auge_code','Código Auge'),(983,983,1,4,'original_department','Departamento Original'),(984,984,1,4,'original_name_subrecinto','Nombre Subrecinto Original'),(985,985,1,4,'department_of_transportation','Departamento de Traslado'),(986,986,1,4,'subrecinto_name_transfer','Nombre Subrecinto de Traslado'),(987,987,1,4,'processing_inventory','Procesando inventario...'),(988,988,1,4,'i_selected_the_option_generate_reports','Collect Cargado con Éxito y Procesado... Seleccioné la Opción Generar Reportes'),(989,989,1,4,'cleaned_tables_success','Tablas Limpiadas con Éxito'),(990,990,1,1,'department','Departamento'),(991,991,1,4,'venue_name_subrecinto','Nombre Recinto / Nombre Subrecinto'),(992,992,1,1,'situation','Situación'),(993,993,1,4,'active_not_registered','ACTIVO NO REGISTRADO EN IGEO'),(994,994,1,4,'node_not_registered','NODO NO REGISTRADO EN IGEO'),(995,995,1,4,'conformitie','CONFORMIDADES'),(996,996,1,4,'transferred','TRASLADADO'),(997,997,1,4,'missing','FALTANTE'),(998,998,1,4,'no_missing','NO HAY FALTANTES'),(999,999,1,4,'the_inventory_has_been_loaded_and_processing','El Inventario ha sido cargado y Procesado'),(1000,1000,1,4,'active_not_logged_and_the_location_is_not_recorded','El activo no esta Registrado y la ubicación no esta registrada al momento de cargar'),(1001,1001,1,4,'join_this_active_and_the_location_is_not_registered','El activo esta Registrado y la ubicación no esta registrada'),(1002,1002,1,4,'general_report','Reporte General'),(1003,1003,1,4,'there_is_internal_number','Existe el Numero Interno'),(1004,1004,1,1,'selection_made_successfully','Selección realizada exitosamente'),(1005,1005,1,1,'set_photography_home','Configurar Fotografía de Portada'),(1006,1006,1,1,'just_can_select_a_registry_to_set_up_your_photography_home','Solo Puede Seleccionar un Registro para Configurar su Fotografía de Portada'),(1007,1007,1,1,'lis_of_photos','Listado de Fotografías'),(1008,1008,1,1,'load_photography_home','Cargar Fotografía de Portada'),(1009,1009,1,1,'this_sure_as_home_leave_this_photograph_of_this_venue','Esta Seguro de Dejar esta Fotografía como Portada de Este Recinto'),(1010,1010,1,1,'please_select_one_photography','Favor Seleccionar una sola Fotografía'),(1011,1011,1,1,'photography_home','Fotografía de Portada'),(1012,1012,1,2,'documents_are_repeated','Los Documentos repetidos son:'),(1013,1013,1,2,'move_documents','Mover Documentos'),(1014,1014,1,2,'do_you_want_to_move_documents_that_are_not_repeated','¿Desea Mover los Documentos que no están repetidos?'),(1015,1015,1,4,'bulk_upload','Carga Masiva'),(1016,1016,1,4,'invoice_number','Número de Factura'),(1017,1017,1,4,'excel_bulk_upload','Carga Masiva Excell'),(1018,1018,1,4,'top_load_excel','Subir Carga Excell'),(1019,1019,1,4,'remove_bulk_upload','Eliminar Carga Masiva'),(1020,1020,1,4,'really_remove_the_load_and_all_associated_assets_at_is','¿Esta Seguro de Eliminar la Carga y Todos los Activos Asociados a está?'),(1021,1021,1,4,'folio_number','Numero de Folio'),(1022,1022,1,4,'select_a_date_range_bulk_upload','Seleccione un Rango de fecha de Carga Masiva'),(1023,1023,1,4,'list_loaded_with_folio_cargo_assets','Listado Activos Cargados con Folio de Carga => '),(1024,1024,1,4,'load_folio','Folio de Carga'),(1025,1025,1,4,'upload_excel_asset','Subir Excell de Activos'),(1026,1026,1,1,'img','img'),(1027,1027,1,1,'doc','doc'),(1028,1028,1,1,'all','Todo'),(1029,1029,1,1,'list','Listado'),(1030,1030,1,1,'gallery','Galeria'),(1031,1031,1,2,'rotate_left','Rotar Izquierda'),(1032,1032,1,2,'rotating_picture','Rotando Imagen'),(1033,1033,1,2,'rotate_right','Rotar Derecha'),(1034,1034,1,3,'view_selected','Vista Seleccionada'),(1035,1035,1,3,'income_layer','Ingreso de la Capa'),(1036,1036,1,3,'linkeado','Linkeado'),(1037,1037,1,3,'you_must_select_a_layer_to_relate','Debe Seleccionar una Capa para relacionar'),(1038,1038,1,3,'select_layers','Seleccionar Capas'),(1039,1039,1,5,'load_factor_percentage','FACTOR DE OCUPACIÓN (PORCENTAJE)'),(1040,1040,1,3,'print_summary','Imprimir Resumen'),(1041,1041,1,3,'you_must_have_applied_for_vista_print_summary','Debe haber Aplicado Vista para Imprimir Resumen'),(1042,1042,1,3,'apply_vista','Aplicar Vista'),(1043,1043,1,3,'you_must_check_one_of_the_layers_to_display_selected_view','Debe marcar una de las capas para mostrar vista seleccionada'),(1044,1044,1,3,'nodes_relationship_layers','Relación Nodos Capas'),(1045,1045,1,3,'detail_spaces','Detalle Espacios'),(1046,1046,1,3,'version_plano','Versión del Plano'),(1047,1047,1,5,'viewed_m2_selected','SUPERFICIE M2 CONSULTADOS (SELECCIONADOS)'),(1048,1048,1,5,'load_factor_percentage','FACTOR DE OCUPACIÓN (PORCENTAJE)'),(1049,1049,1,5,'no_campus','No existe el Recinto :'),(1050,1050,1,3,'there_is_the_type_of_asset','No existe el Tipo de Activo :'),(1051,1051,1,3,'there_brand','No existe la Marca :'),(1052,1052,1,3,'there_the_state','No existe el Estado :'),(1053,1053,1,3,'no_condition','No existe la Condición :'),(1054,1054,1,3,'there_is_the_serial_number','Existe el Numero de Serie :'),(1055,1055,1,3,'the_date_format_is_incorrect','El Formato de fecha no es correcta :'),(1056,1056,1,7,'to_remove_the_first_list_must_remove_the_il_o_that_contains_in_its_task','Si Desea Eliminar la Lista primero debe eliminar la OT que la contiene en su Tarea'),(1057,1057,1,4,'export_plancheta_by_level','Exportar Plancheta por Nivel (Piso)'),(1058,1058,1,4,'select_an_enclosure_type_type_level','Seleccione un Tipo de Recinto Tipo Nivel (Piso)'),(1059,1059,1,8,'history','Historial Solicitud'),(1060,1060,1,8,'search_request','Busqueda Solicitud'),(1061,1061,1,11,'iot','Dispositivos IOT'),(1062,1062,1,1,'planimetry_documents','Panimetría y documentos'),(1063,1063,1,1,'inventoriable_furniture_management','Gestión de inmuebles inventariables'),(1064,1064,1,1,'services','Servicios'),(1065,1065,1,1,'others','Otros'),(1067,1067,1,8,'add_service','Agregar Servicio'),(1068,1068,1,8,'history_service','Historial Servicio'),(1069,1069,1,8,'export_service','Exportar Servicio'),(1070,1070,1,8,'search_service','Busqueda Servicio'),(1071,1071,1,8,'services','Servicios'),(1072,1072,1,8,'edit_request_service','Editar Solicitud de Servicio'),(1074,1074,1,8,'data_request','Datos Solicitud'),(1075,1075,1,8,'rejection','Rechazo'),(1076,1076,1,8,'type_of_request','Tipo de Solicitud'),(1077,1077,1,8,'request_status','Estado de Solicitud'),(1078,0,1,8,'folio_number','Nº Folio'),(1079,0,1,8,'invoice_name','Nombre Factura'),(1080,0,1,8,'invoice_number','Nº Factura'),(1081,0,1,8,'purchase_order_name','Nombre Orden de Compra'),(1082,0,1,8,'purchase_order_number','Nº Orden de Compra'),(1083,0,1,1,'user_name','Nombre de Usuario'),(1084,0,1,8,'folio','Folio'),(1085,0,1,8,'bill','Factura'),(1086,0,1,8,'purchase_order','Orden de Compra'),(1087,0,1,8,'rejected_by','Rechazada Por');
/*!40000 ALTER TABLE `language_tag` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log`
--

DROP TABLE IF EXISTS `log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log` (
  `log_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_type_id` int(11) NOT NULL,
  `log_date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `log_ip` varchar(255) NOT NULL,
  `log_description` text NOT NULL,
  PRIMARY KEY (`log_id`) USING BTREE,
  KEY `log_ibfk_1` (`log_type_id`) USING BTREE,
  KEY `log_ibfk_2` (`user_id`) USING BTREE,
  CONSTRAINT `log_ibfk_1` FOREIGN KEY (`log_type_id`) REFERENCES `log_type` (`log_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log`
--

LOCK TABLES `log` WRITE;
/*!40000 ALTER TABLE `log` DISABLE KEYS */;
/*!40000 ALTER TABLE `log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_detail`
--

DROP TABLE IF EXISTS `log_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_detail` (
  `log_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `log_id` int(11) NOT NULL,
  `log_detail_param` varchar(255) NOT NULL,
  `log_detail_value_old` varchar(255) NOT NULL,
  `log_detail_value_new` varchar(255) NOT NULL,
  PRIMARY KEY (`log_detail_id`) USING BTREE,
  KEY `log_detail_ibfk_1` (`log_id`) USING BTREE,
  CONSTRAINT `log_detail_ibfk_1` FOREIGN KEY (`log_id`) REFERENCES `log` (`log_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_detail`
--

LOCK TABLES `log_detail` WRITE;
/*!40000 ALTER TABLE `log_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_type`
--

DROP TABLE IF EXISTS `log_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_type` (
  `log_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `log_type_name` varchar(150) NOT NULL,
  `log_type_description` varchar(150) NOT NULL,
  `log_type_template` text,
  PRIMARY KEY (`log_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_type`
--

LOCK TABLES `log_type` WRITE;
/*!40000 ALTER TABLE `log_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `log_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mail_template`
--

DROP TABLE IF EXISTS `mail_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mail_template` (
  `mail_template_id` int(11) NOT NULL AUTO_INCREMENT,
  `mail_template_name` varchar(255) DEFAULT NULL,
  `mail_template_subjet` text,
  `mail_template_comment` text,
  PRIMARY KEY (`mail_template_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mail_template`
--

LOCK TABLES `mail_template` WRITE;
/*!40000 ALTER TABLE `mail_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `mail_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `measure_unit`
--

DROP TABLE IF EXISTS `measure_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `measure_unit` (
  `measure_unit_id` int(11) NOT NULL AUTO_INCREMENT,
  `measure_unit_name` varchar(30) DEFAULT NULL,
  `measure_unit_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`measure_unit_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `measure_unit`
--

LOCK TABLES `measure_unit` WRITE;
/*!40000 ALTER TABLE `measure_unit` DISABLE KEYS */;
/*!40000 ALTER TABLE `measure_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module` (
  `module_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(100) NOT NULL,
  `module_namespace` varchar(100) NOT NULL,
  `module_abbreviation` varchar(30) DEFAULT NULL,
  `module_position` int(11) DEFAULT NULL,
  PRIMARY KEY (`module_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module`
--

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;
INSERT INTO `module` VALUES (1,'General','General','general',NULL),(2,'Documento','Document','doc',3),(3,'Planimetria','Plan','plan',2),(4,'Activo','Asset','asset',4),(5,'Infraestructura','Infrastructure','infra',1),(6,'Core','Core','core',NULL),(7,'Mantenimiento','Maintenance','mtn',NULL),(8,'Solicitudes','Request','request',5),(9,'Reportes','Report','report',8),(10,'Gastos','Costs','costs',NULL),(11,'IOT','Iot','iot',9);
/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `module_action`
--

DROP TABLE IF EXISTS `module_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `module_action` (
  `module_action_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) NOT NULL,
  `module_action_name` varchar(100) NOT NULL,
  `module_action_uri` varchar(255) NOT NULL,
  `module_action_is_public` int(11) NOT NULL COMMENT '0= Privada, 1 = Publica',
  `language_tag_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`module_action_id`) USING BTREE,
  KEY `module_action_ibfk_1` (`module_id`) USING BTREE,
  KEY `module_action_ibfk_2` (`language_tag_id`) USING BTREE,
  CONSTRAINT `module_action_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `module_action_ibfk_2` FOREIGN KEY (`language_tag_id`) REFERENCES `language_tag` (`language_tag_aux_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10006 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `module_action`
--

LOCK TABLES `module_action` WRITE;
/*!40000 ALTER TABLE `module_action` DISABLE KEYS */;
INSERT INTO `module_action` VALUES (30,6,'getSibling','core/nodecontroller/getSibling',1,NULL),(33,5,'get','infra/infrainfo/get',1,NULL),(34,5,'get','infra/infrainfoconfig/get',1,NULL),(35,5,'get','infra/infrainfooption/get',1,NULL),(36,5,'get','infra/infraotherdatavalue/get',1,NULL),(37,5,'get','infra/infraotherdataoption/get',1,NULL),(38,5,'get','infra/infraotherdataattribute/get',1,NULL),(39,5,'add','infra/infraotherdataattribute/add',2,157),(40,5,'update','infra/infraotherdataattribute/update',2,158),(41,5,'delete','infra/infraotherdataattribute/delete',2,159),(42,5,'get','infra/infraotherdataattributenodetype/get',1,NULL),(43,5,'add','infra/infraotherdataattributenodetype/add',2,164),(44,5,'update','infra/infraotherdataattributenodetype/update',2,165),(45,5,'delete','infra/infraotherdataattributenodetype/delete',2,166),(46,5,'add','infra/infraotherdataoption/add',2,167),(47,5,'update','infra/infraotherdataoption/update',2,174),(48,5,'delete','infra/infraotherdataoption/delete',2,175),(52,5,'exportList','infra/infrastructurecontroller/exportList',1,178),(54,2,'get','doc/document/get',1,NULL),(58,2,'get','doc/docversion/get',1,NULL),(62,2,'get','doc/docextension/get',1,NULL),(63,2,'add','doc/docextension/add',1,115),(64,2,'update','doc/docextension/update',1,116),(65,2,'delete','doc/docextension/delete',1,117),(66,2,'get','doc/doccategory/get',1,NULL),(67,2,'add','doc/doccategory/add',2,118),(68,2,'update','doc/doccategory/update',2,119),(69,2,'delete','doc/doccategory/delete',2,120),(71,3,'get','plan/category/get',1,NULL),(72,3,'add','plan/category/add',2,127),(73,3,'update','plan/category/update',2,122),(74,3,'delete','plan/category/delete',2,123),(75,3,'get','plan/plan/get',1,NULL),(76,3,'getAll','plan/plan/getAll',1,NULL),(77,3,'get','plan/version/get',1,NULL),(79,3,'update','plan/version/update',1,125),(80,3,'delete','plan/version/delete',1,126),(82,3,'exportList','plan/plan/exportList',1,128),(83,6,'autentication','core/auth/autentication',1,NULL),(84,6,'permissions','core/auth/permissions',1,NULL),(87,6,'getList','core/nodetypecategory/getList',1,NULL),(88,6,'add','core/nodetypecategory/add',2,92),(89,6,'update','core/nodetypecategory/update',2,93),(90,6,'delete','core/nodetypecategory/delete',2,94),(91,6,'getList','core/nodetype/getList',1,NULL),(92,6,'add','core/nodetype/add',2,95),(93,6,'update','core/nodetype/update',2,96),(94,6,'delete','core/nodetype/delete',2,97),(95,6,'logout','core/auth/logout',1,NULL),(96,6,'get','core/measureunit/get',1,NULL),(97,6,'add','core/measureunit/add',2,98),(98,6,'update','core/measureunit/update',2,99),(99,6,'delete','core/measureunit/delete',2,100),(100,6,'get','core/providertype/get',1,NULL),(101,6,'add','core/providertype/add',2,101),(102,6,'update','core/providertype/update',2,102),(103,6,'delete','core/providertype/delete',2,103),(104,6,'get','core/provider/get',1,NULL),(105,6,'add','core/provider/add',2,104),(106,6,'update','core/provider/update',2,105),(107,6,'delete','core/provider/delete',2,106),(108,6,'get','core/user/get',1,NULL),(109,6,'add','core/user/add',2,180),(110,6,'update','core/user/update',2,181),(111,6,'status','core/user/status',1,182),(112,6,'get','core/currency/get',1,NULL),(113,6,'add','core/currency/add',2,107),(114,6,'update','core/currency/update',2,108),(115,6,'delete','core/currency/delete',2,109),(116,6,'get','core/group/get',1,NULL),(117,6,'add','core/group/add',2,183),(118,6,'update','core/group/update',2,184),(119,6,'delete','core/group/delete',2,185),(120,6,'get','core/module/get',1,NULL),(121,6,'add','core/module/add',0,NULL),(122,6,'update','core/module/update',0,NULL),(123,6,'delete','core/module/delete',0,NULL),(124,6,'getActionModule','core/module/getActionModule',1,NULL),(125,6,'get','core/permissions/get',1,NULL),(126,6,'getUsers','core/group/getUsers',1,NULL),(127,6,'usersOutsideGroup','core/group/usersOutsideGroup',1,NULL),(128,6,'groups','core/user/groups',1,NULL),(129,6,'expand','core/nodecontroller/expand',1,NULL),(130,5,'add','infra/infrainfoconfig/add',2,177),(131,6,'expand','core/permissions/expand',1,NULL),(132,6,'setTree','core/permissions/setTree',2,187),(133,6,'addUser','core/group/addUser',2,188),(134,6,'add','core/permissions/add',2,186),(135,6,'addGroup','core/user/addGroup',2,183),(136,6,'isLoggedIn','core/auth/isLoggedIn',1,NULL),(137,5,'checkaccessnode','core/user/checkaccessnode',1,NULL),(138,5,'search','core/node/search',1,NULL),(139,5,'get','infra/infracoordinate/get',1,NULL),(2000,2,'download','doc/document/download',0,121),(2001,2,'add','doc/document/add',0,57),(2002,2,'delete','doc/document/delete',0,58),(2003,2,'add','doc/docversion/add',0,112),(2004,2,'update','doc/docversion/update',0,113),(2005,2,'delete','doc/docversion/delete',0,114),(2006,2,'get','doc/document/get',0,725),(2007,2,'addToZip','doc/docdocumentcontroller/addToZip',0,693),(2008,2,'edit','doc/document/edit',0,1013),(3000,3,'view_action','plan/plan/get',0,385),(3001,3,'add','plan/plan/add',0,129),(3002,3,'add','plan/version/add',0,124),(3003,3,'associate_flat_objects','plan/node/save',0,353),(3004,3,'exportList','plan/plan/exportList',0,727),(3005,3,'delete','plan/version/delete',0,728),(4000,4,'get','asset/asset/get',0,718),(4001,4,'add','asset/asset/add',0,429),(4002,4,'update ','asset/asset/update',0,407),(4003,4,'edit','asset/assetcontroller/edit',0,716),(4004,4,'delete','asset/asset/delete',0,389),(4005,4,'exportList','asset/asset/exportList',0,717),(4006,4,'export_plancheta','asset/assetuchileplancheta/exportPlancheta',0,627),(4007,4,'add','asset/assetinventory/add',0,662),(4008,4,'addAssetMasivo','asset/assetload/addAssetMasivo',0,1015),(4009,4,'exportPlanchetaNivel','asset/assetuchileplancheta/exportPlanchetaNivel',0,1057),(5000,5,'view_action','infra/node/getSibling',0,384),(5001,5,'add','infra/infraotherdatavalue/add',0,179),(5002,5,'delete','core/nodecontroller/delete',0,90),(5003,5,'edit','core/nodecontroller/edit',0,36),(5004,5,'add','infra/infrainfo/add',0,176),(5005,5,'addSibling','core/nodecontroller/addSibling',0,5),(5006,5,'add','infra/infracoordinate/edit',0,639),(5007,5,'report','report/vaciado1',0,NULL),(5008,5,'exportList','infra/infrastructurecontroller/exportList',0,726),(7000,7,'get','mtn/wocontroller/getNode',0,959),(7001,7,'get','mtn/wocontroller/get',0,483),(7002,7,'updateDate','mtn/wo/updateDate',0,647),(7003,7,'updateState','mtn/wo/updateState',0,713),(7004,7,'exportPdf','mtn/woexportarnodepdf/exportPdf',0,960),(7005,7,'add','mtn/taskcontroller/add',0,501),(7006,7,'delete','mtn/taskcontroller/add',0,961),(7007,7,'addCorrectiveNode','mtn/wocontroller/addCorrectiveNode',0,962),(7008,7,'add','mtn/contractcontroller/add',0,632),(7009,7,'get','mtn/contractcontroller/get',0,963),(7010,7,'delete','mtn/contractcontroller/delete',0,964),(7011,7,'update','mtn/contractcontroller/update',0,965),(7012,7,'getByIdNode','core/nodecontroller/getByIdNode',0,966),(8000,8,'get','request/request/get',0,722),(8001,8,'add','request/request/add',0,723),(8002,8,'update','request/request/update',0,655),(8003,8,'update','request/request/update',0,657),(8004,8,'export','request/requestcontroller/export',0,724),(8005,8,'history','request/log/get',0,1059),(8006,8,'get','request/request/get',0,1060),(8007,8,'get','request/service/get',0,1064),(8008,8,'get','request/service/informacion',0,NULL),(8009,8,'add','request/service/add',0,1067),(8010,8,'export','request/servicecontroller/export',0,1069),(8011,8,'history','request/servicelog/get',0,1068),(8012,8,'get','request/service/get',0,1070),(8013,8,'get','request/service/get',0,1070),(9000,9,'get','report/report/get',0,722),(10000,10,'add','costs/costs/add',0,732),(10001,10,'get','costs/costs/get',0,772),(10002,10,'update','costs/costs/update',0,749),(10003,10,'delete','costs/costs/delete',0,773),(10004,10,'export','costs/costs/export',0,750),(10005,11,'view','iot/iot/getDevice',1,750);
/*!40000 ALTER TABLE `module_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_component`
--

DROP TABLE IF EXISTS `mtn_component`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_component` (
  `mtn_component_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_component_type_id` int(11) DEFAULT NULL,
  `mtn_component_name` varchar(20) DEFAULT NULL,
  `mtn_component_weight` varchar(20) DEFAULT NULL,
  `brand_id` int(11) DEFAULT NULL,
  `mtn_component_model` varchar(20) DEFAULT NULL,
  `mtn_component_manufacturer` varchar(20) DEFAULT NULL,
  `mtn_component_comment` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mtn_component_id`) USING BTREE,
  UNIQUE KEY `index_fk_component_id` (`mtn_component_id`) USING BTREE,
  KEY `mtn_component_ibfk_1` (`mtn_component_type_id`) USING BTREE,
  KEY `mtn_component_ibfk_2` (`brand_id`) USING BTREE,
  CONSTRAINT `mtn_component_ibfk_1` FOREIGN KEY (`mtn_component_type_id`) REFERENCES `mtn_component_type` (`mtn_component_type_id`),
  CONSTRAINT `mtn_component_ibfk_2` FOREIGN KEY (`brand_id`) REFERENCES `brand` (`brand_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_component`
--

LOCK TABLES `mtn_component` WRITE;
/*!40000 ALTER TABLE `mtn_component` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_component` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_component_type`
--

DROP TABLE IF EXISTS `mtn_component_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_component_type` (
  `mtn_component_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_component_type_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`mtn_component_type_id`) USING BTREE,
  UNIQUE KEY `mtn_component_type_id` (`mtn_component_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_component_type`
--

LOCK TABLES `mtn_component_type` WRITE;
/*!40000 ALTER TABLE `mtn_component_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_component_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_config_state`
--

DROP TABLE IF EXISTS `mtn_config_state`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_config_state` (
  `mtn_config_state_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_work_order_type_id` int(11) NOT NULL,
  `mtn_system_work_order_status_id` int(11) NOT NULL,
  `mtn_config_state_access_user` int(11) DEFAULT NULL,
  `mtn_config_state_access_provider` int(11) DEFAULT NULL,
  `mtn_config_state_order` int(11) DEFAULT NULL,
  `mtn_config_state_default` int(11) DEFAULT NULL,
  `mtn_config_state_duration` int(11) DEFAULT NULL,
  PRIMARY KEY (`mtn_config_state_id`) USING BTREE,
  KEY `mtn_config_state_ibfk_1` (`mtn_work_order_type_id`) USING BTREE,
  KEY `mtn_config_state_ibfk_2` (`mtn_system_work_order_status_id`) USING BTREE,
  CONSTRAINT `mtn_config_state_ibfk_1` FOREIGN KEY (`mtn_work_order_type_id`) REFERENCES `mtn_work_order_type` (`mtn_work_order_type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mtn_config_state_ibfk_2` FOREIGN KEY (`mtn_system_work_order_status_id`) REFERENCES `mtn_system_work_order_status` (`mtn_system_work_order_status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_config_state`
--

LOCK TABLES `mtn_config_state` WRITE;
/*!40000 ALTER TABLE `mtn_config_state` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_config_state` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_maintainer_type`
--

DROP TABLE IF EXISTS `mtn_maintainer_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_maintainer_type` (
  `mtn_maintainer_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_maintainer_type_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mtn_maintainer_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_maintainer_type`
--

LOCK TABLES `mtn_maintainer_type` WRITE;
/*!40000 ALTER TABLE `mtn_maintainer_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_maintainer_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_other_costs`
--

DROP TABLE IF EXISTS `mtn_other_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_other_costs` (
  `mtn_other_costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_other_costs_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`mtn_other_costs_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_other_costs_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_other_costs`
--

LOCK TABLES `mtn_other_costs` WRITE;
/*!40000 ALTER TABLE `mtn_other_costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_other_costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_percentages`
--

DROP TABLE IF EXISTS `mtn_percentages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_percentages` (
  `mtn_percentages_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `mtn_percentages_value_lower` int(11) DEFAULT NULL,
  `mtn_percentages_value_upper` int(11) DEFAULT NULL,
  `mtn_percentages_viatico` int(11) DEFAULT NULL,
  `mtn_percentages_general_expenses` int(11) DEFAULT NULL,
  `mtn_percentages_utility` int(11) DEFAULT NULL,
  PRIMARY KEY (`mtn_percentages_id`) USING BTREE,
  KEY `mtn_percentages_ibfk_1` (`node_id`) USING BTREE,
  CONSTRAINT `mtn_percentages_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_percentages`
--

LOCK TABLES `mtn_percentages` WRITE;
/*!40000 ALTER TABLE `mtn_percentages` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_percentages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_plan`
--

DROP TABLE IF EXISTS `mtn_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_plan` (
  `mtn_plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_plan_name` varchar(50) DEFAULT NULL,
  `mtn_plan_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mtn_plan_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_plan`
--

LOCK TABLES `mtn_plan` WRITE;
/*!40000 ALTER TABLE `mtn_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_plan_task`
--

DROP TABLE IF EXISTS `mtn_plan_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_plan_task` (
  `mtn_plan_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_plan_id` int(11) NOT NULL,
  `mtn_task_id` int(11) NOT NULL,
  `mtn_plan_task_interval` int(11) NOT NULL,
  PRIMARY KEY (`mtn_plan_task_id`) USING BTREE,
  KEY `mtn_plan_task_ibfk_1` (`mtn_plan_id`) USING BTREE,
  KEY `mtn_plan_task_ibfk_2` (`mtn_task_id`) USING BTREE,
  CONSTRAINT `mtn_plan_task_ibfk_1` FOREIGN KEY (`mtn_plan_id`) REFERENCES `mtn_plan` (`mtn_plan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_plan_task_ibfk_2` FOREIGN KEY (`mtn_task_id`) REFERENCES `mtn_task` (`mtn_task_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_plan_task`
--

LOCK TABLES `mtn_plan_task` WRITE;
/*!40000 ALTER TABLE `mtn_plan_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_plan_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_price_list`
--

DROP TABLE IF EXISTS `mtn_price_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_price_list` (
  `mtn_price_list_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `mtn_price_list_date_validity_start` date DEFAULT NULL,
  `mtn_price_list_date_validity_finish` date DEFAULT NULL,
  PRIMARY KEY (`mtn_price_list_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_price_list_id`) USING BTREE,
  KEY `mtn_price_list_ibfk_1` (`currency_id`) USING BTREE,
  KEY `mtn_price_list_ibfk_2` (`provider_id`) USING BTREE,
  CONSTRAINT `mtn_price_list_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_price_list_ibfk_2` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_price_list`
--

LOCK TABLES `mtn_price_list` WRITE;
/*!40000 ALTER TABLE `mtn_price_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_price_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_price_list_component`
--

DROP TABLE IF EXISTS `mtn_price_list_component`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_price_list_component` (
  `mtn_price_list_component_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_price_list_id` int(11) DEFAULT NULL,
  `mtn_component_id` int(11) DEFAULT NULL,
  `mtn_price_list_component_price` int(11) DEFAULT NULL,
  PRIMARY KEY (`mtn_price_list_component_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_price_list_component_id`) USING BTREE,
  KEY `mtn_price_list_component_ibfk_1` (`mtn_price_list_id`) USING BTREE,
  KEY `mtn_price_list_component_ibfk_2` (`mtn_component_id`) USING BTREE,
  CONSTRAINT `mtn_price_list_component_ibfk_1` FOREIGN KEY (`mtn_price_list_id`) REFERENCES `mtn_price_list` (`mtn_price_list_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_price_list_component_ibfk_2` FOREIGN KEY (`mtn_component_id`) REFERENCES `mtn_component` (`mtn_component_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_price_list_component`
--

LOCK TABLES `mtn_price_list_component` WRITE;
/*!40000 ALTER TABLE `mtn_price_list_component` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_price_list_component` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_status_log`
--

DROP TABLE IF EXISTS `mtn_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_status_log` (
  `mtn_status_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `mtn_work_order_id` int(11) NOT NULL,
  `mtn_config_state_id` int(11) NOT NULL,
  `mtn_status_log_datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mtn_status_log_comments` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`mtn_status_log_id`) USING BTREE,
  KEY `mtn_status_log_ibfk_1` (`mtn_config_state_id`) USING BTREE,
  KEY `mtn_status_log_ibfk_2` (`mtn_work_order_id`) USING BTREE,
  KEY `mtn_status_log_ibfk_3` (`user_id`) USING BTREE,
  CONSTRAINT `mtn_status_log_ibfk_1` FOREIGN KEY (`mtn_config_state_id`) REFERENCES `mtn_config_state` (`mtn_config_state_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mtn_status_log_ibfk_2` FOREIGN KEY (`mtn_work_order_id`) REFERENCES `mtn_work_order` (`mtn_work_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_status_log_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_status_log`
--

LOCK TABLES `mtn_status_log` WRITE;
/*!40000 ALTER TABLE `mtn_status_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_status_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_system_work_order_status`
--

DROP TABLE IF EXISTS `mtn_system_work_order_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_system_work_order_status` (
  `mtn_system_work_order_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_system_work_order_status_name` varchar(50) NOT NULL,
  PRIMARY KEY (`mtn_system_work_order_status_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_system_work_order_status`
--

LOCK TABLES `mtn_system_work_order_status` WRITE;
/*!40000 ALTER TABLE `mtn_system_work_order_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_system_work_order_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_task`
--

DROP TABLE IF EXISTS `mtn_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_task` (
  `mtn_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_task_time` int(11) DEFAULT NULL,
  `mtn_task_name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`mtn_task_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_task_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_task`
--

LOCK TABLES `mtn_task` WRITE;
/*!40000 ALTER TABLE `mtn_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_work_order`
--

DROP TABLE IF EXISTS `mtn_work_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_work_order` (
  `mtn_work_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_work_order_folio` varchar(11) NOT NULL,
  `mtn_config_state_id` int(11) NOT NULL,
  `mtn_work_order_creator_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `asset_measurement_id` int(11) DEFAULT '1',
  `provider_id` int(11) DEFAULT NULL,
  `mtn_work_order_requested_by` varchar(255) NOT NULL,
  `mtn_work_order_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `mtn_work_order_date` date NOT NULL,
  `mtn_work_order_comment` text,
  `mtn_work_order_status` int(11) DEFAULT '0',
  `mtn_work_order_closed` int(1) DEFAULT '0',
  PRIMARY KEY (`mtn_work_order_id`) USING BTREE,
  KEY `mtn_work_order_ibfk_1` (`mtn_config_state_id`) USING BTREE,
  KEY `mtn_work_order_ibfk_2` (`mtn_work_order_creator_id`) USING BTREE,
  KEY `mtn_work_order_ibfk_3` (`asset_id`) USING BTREE,
  KEY `mtn_work_order_ibfk_4` (`provider_id`) USING BTREE,
  KEY `fk_requested_id` (`mtn_work_order_requested_by`) USING BTREE,
  KEY `asset_measurement_id` (`asset_measurement_id`) USING BTREE,
  CONSTRAINT `mtn_work_order_ibfk_1` FOREIGN KEY (`mtn_config_state_id`) REFERENCES `mtn_config_state` (`mtn_config_state_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mtn_work_order_ibfk_2` FOREIGN KEY (`mtn_work_order_creator_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mtn_work_order_ibfk_3` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `mtn_work_order_ibfk_4` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_work_order`
--

LOCK TABLES `mtn_work_order` WRITE;
/*!40000 ALTER TABLE `mtn_work_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_work_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_work_order_other_costs`
--

DROP TABLE IF EXISTS `mtn_work_order_other_costs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_work_order_other_costs` (
  `mtn_work_order_other_costs_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_work_order_id` int(11) DEFAULT NULL,
  `mtn_other_costs_id` int(11) DEFAULT NULL,
  `mtn_work_order_other_costs_costs` int(11) DEFAULT NULL,
  `mtn_work_order_other_costs_comment` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mtn_work_order_other_costs_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_work_order_other_costs_id`) USING BTREE,
  KEY `mtn_work_order_other_costs_ibfk_1` (`mtn_work_order_id`) USING BTREE,
  KEY `mtn_work_order_other_costs_ibfk_2` (`mtn_other_costs_id`) USING BTREE,
  CONSTRAINT `mtn_work_order_other_costs_ibfk_1` FOREIGN KEY (`mtn_work_order_id`) REFERENCES `mtn_work_order` (`mtn_work_order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_work_order_other_costs_ibfk_2` FOREIGN KEY (`mtn_other_costs_id`) REFERENCES `mtn_other_costs` (`mtn_other_costs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_work_order_other_costs`
--

LOCK TABLES `mtn_work_order_other_costs` WRITE;
/*!40000 ALTER TABLE `mtn_work_order_other_costs` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_work_order_other_costs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_work_order_task`
--

DROP TABLE IF EXISTS `mtn_work_order_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_work_order_task` (
  `mtn_work_order_task_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_task_id` int(11) NOT NULL,
  `mtn_work_order_id` int(11) NOT NULL,
  `mtn_work_order_task_time_job` int(11) DEFAULT NULL,
  `mtn_work_order_task_price` int(11) DEFAULT NULL,
  `mtn_work_order_task_comment` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`mtn_work_order_task_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_work_order_task_id`) USING BTREE,
  KEY `mtn_work_order_task_ibfk_1` (`mtn_task_id`) USING BTREE,
  KEY `mtn_work_order_task_ibfk_2` (`mtn_work_order_id`) USING BTREE,
  CONSTRAINT `mtn_work_order_task_ibfk_1` FOREIGN KEY (`mtn_task_id`) REFERENCES `mtn_task` (`mtn_task_id`),
  CONSTRAINT `mtn_work_order_task_ibfk_2` FOREIGN KEY (`mtn_work_order_id`) REFERENCES `mtn_work_order` (`mtn_work_order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_work_order_task`
--

LOCK TABLES `mtn_work_order_task` WRITE;
/*!40000 ALTER TABLE `mtn_work_order_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_work_order_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_work_order_task_component`
--

DROP TABLE IF EXISTS `mtn_work_order_task_component`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_work_order_task_component` (
  `mtn_work_order_task_component_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_price_list_component_id` int(11) DEFAULT NULL,
  `mtn_work_order_task_id` int(11) DEFAULT NULL,
  `mtn_work_order_component_price` int(11) DEFAULT NULL,
  `mtn_component_id` int(11) DEFAULT NULL,
  `mtn_work_order_component_amount` int(11) DEFAULT NULL,
  PRIMARY KEY (`mtn_work_order_task_component_id`) USING BTREE,
  UNIQUE KEY `mtn_work_order_task_component_id` (`mtn_work_order_task_component_id`) USING BTREE,
  KEY `mtn_work_order_task_component_ibfk_1` (`mtn_price_list_component_id`) USING BTREE,
  KEY `mtn_work_order_task_component_ibfk_2` (`mtn_work_order_task_id`) USING BTREE,
  KEY `mtn_work_order_task_component_ibfk_3` (`mtn_component_id`) USING BTREE,
  CONSTRAINT `mtn_work_order_task_component_ibfk_1` FOREIGN KEY (`mtn_price_list_component_id`) REFERENCES `mtn_price_list_component` (`mtn_price_list_component_id`),
  CONSTRAINT `mtn_work_order_task_component_ibfk_2` FOREIGN KEY (`mtn_work_order_task_id`) REFERENCES `mtn_work_order_task` (`mtn_work_order_task_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `mtn_work_order_task_component_ibfk_3` FOREIGN KEY (`mtn_component_id`) REFERENCES `mtn_component` (`mtn_component_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_work_order_task_component`
--

LOCK TABLES `mtn_work_order_task_component` WRITE;
/*!40000 ALTER TABLE `mtn_work_order_task_component` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_work_order_task_component` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mtn_work_order_type`
--

DROP TABLE IF EXISTS `mtn_work_order_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mtn_work_order_type` (
  `mtn_work_order_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `mtn_work_order_type_name` varchar(30) DEFAULT NULL,
  `mtn_work_order_type_abbreviation` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`mtn_work_order_type_id`) USING BTREE,
  UNIQUE KEY `index_1` (`mtn_work_order_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mtn_work_order_type`
--

LOCK TABLES `mtn_work_order_type` WRITE;
/*!40000 ALTER TABLE `mtn_work_order_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `mtn_work_order_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node`
--

DROP TABLE IF EXISTS `node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_type_id` int(11) NOT NULL,
  `node_parent_id` int(11) DEFAULT NULL,
  `node_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` smallint(5) NOT NULL,
  `node_document_id_default` int(11) DEFAULT NULL,
  PRIMARY KEY (`node_id`) USING BTREE,
  KEY `node_ibfk_1` (`node_type_id`) USING BTREE,
  KEY `node_ibfk_2` (`node_parent_id`) USING BTREE,
  KEY `node_ibfk_3` (`node_document_id_default`) USING BTREE,
  CONSTRAINT `node_ibfk_1` FOREIGN KEY (`node_type_id`) REFERENCES `node_type` (`node_type_id`) ON UPDATE CASCADE,
  CONSTRAINT `node_ibfk_2` FOREIGN KEY (`node_parent_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_ibfk_3` FOREIGN KEY (`node_document_id_default`) REFERENCES `doc_document` (`doc_document_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node`
--

LOCK TABLES `node` WRITE;
/*!40000 ALTER TABLE `node` DISABLE KEYS */;
/*!40000 ALTER TABLE `node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node_migracion`
--

DROP TABLE IF EXISTS `node_migracion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_migracion` (
  `node_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_type_id` int(11) NOT NULL,
  `node_parent_id` int(11) DEFAULT NULL,
  `node_name` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `lft` int(11) NOT NULL,
  `rgt` int(11) NOT NULL,
  `level` smallint(5) NOT NULL,
  PRIMARY KEY (`node_id`) USING BTREE,
  KEY `node_migracion_ibfk_1` (`node_type_id`) USING BTREE,
  KEY `node_parent_id` (`node_parent_id`) USING BTREE,
  CONSTRAINT `node_migracion_ibfk_1` FOREIGN KEY (`node_type_id`) REFERENCES `node_type` (`node_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `node_migracion_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node_migracion`
--

LOCK TABLES `node_migracion` WRITE;
/*!40000 ALTER TABLE `node_migracion` DISABLE KEYS */;
/*!40000 ALTER TABLE `node_migracion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node_type`
--

DROP TABLE IF EXISTS `node_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_type` (
  `node_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_category_id` int(11) DEFAULT NULL,
  `node_type_category_id` int(11) NOT NULL,
  `node_type_name` varchar(100) DEFAULT NULL,
  `node_type_location` int(1) DEFAULT '0',
  PRIMARY KEY (`node_type_id`) USING BTREE,
  KEY `node_type_ibfk_1` (`node_type_category_id`) USING BTREE,
  CONSTRAINT `node_type_ibfk_1` FOREIGN KEY (`node_type_category_id`) REFERENCES `node_type_category` (`node_type_category_id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node_type`
--

LOCK TABLES `node_type` WRITE;
/*!40000 ALTER TABLE `node_type` DISABLE KEYS */;
INSERT INTO `node_type` VALUES (1,1,1,'CAMPUS',0),(2,NULL,1,'EDIFICIO',0),(3,NULL,1,'NIVEL',0),(4,NULL,2,'OFICINA',0),(5,NULL,2,'SALA',0),(6,NULL,2,'LABORATORIO',0),(7,NULL,2,'TALLER',0),(8,NULL,2,'COMEDOR',0),(9,NULL,2,'SERVICIOS',0),(10,NULL,2,'DORMITORIO',0),(11,NULL,2,'INSTALACIONES',0),(12,NULL,2,'BODEGA',0),(13,NULL,2,'OTRO',0),(14,NULL,2,'SIN_INFORMACION',0),(15,NULL,2,'CIRCULACION',0),(16,NULL,2,'CERRADO_SIN_DEPENDENCIA',0),(17,NULL,2,'OTRAS_INSTITUCIONES_EN_RECINTOS_UNIVERSITARIOS',0),(18,NULL,2,'PISCINA',0),(19,NULL,2,'TERRAZA',0),(20,NULL,2,'VIVERO_DE_INVESTIGACION',0),(21,NULL,2,'ESTACIONAMIENTO',0),(22,NULL,2,'PATIO',0),(23,NULL,1,'DIRECCION',0),(24,NULL,1,'PLANTA',0),(25,NULL,1,'Pruebaicono',0),(26,NULL,1,'IGLESIA',0),(27,NULL,1,'CAPILLA',0),(28,NULL,1,'UNIVERSIDAD',0),(29,NULL,1,'DIRECCION TIPO 2',0),(30,NULL,2,'AUDITORIO',0),(33,NULL,3,'SENSORES',0),(36,NULL,6,'Prueba',0),(37,3,4,'prueba88',0);
/*!40000 ALTER TABLE `node_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `node_type_category`
--

DROP TABLE IF EXISTS `node_type_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `node_type_category` (
  `node_type_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_type_category_name` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`node_type_category_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `node_type_category`
--

LOCK TABLES `node_type_category` WRITE;
/*!40000 ALTER TABLE `node_type_category` DISABLE KEYS */;
INSERT INTO `node_type_category` VALUES (1,'EDIFICACION','domain'),(2,'RECINTOS','location_city'),(3,'DISPOSITIVOS IOT','rss_feed'),(4,'CAMPUS',NULL),(5,'LABORATORIOS',NULL),(6,'CIUDAD',NULL);
/*!40000 ALTER TABLE `node_type_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan`
--

DROP TABLE IF EXISTS `plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `plan_category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `plan_current_version` int(1) DEFAULT NULL,
  `plan_filename` varchar(100) DEFAULT NULL,
  `plan_version` varchar(100) DEFAULT NULL,
  `plan_comments` text,
  `plan_description` varchar(100) DEFAULT NULL,
  `plan_datetime` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`plan_id`) USING BTREE,
  KEY `plan_ibfk_1` (`node_id`) USING BTREE,
  KEY `plan_ibfk_2` (`user_id`) USING BTREE,
  KEY `plan_ibfk_3` (`plan_category_id`) USING BTREE,
  CONSTRAINT `plan_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plan_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `plan_ibfk_3` FOREIGN KEY (`plan_category_id`) REFERENCES `plan_category` (`plan_category_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan`
--

LOCK TABLES `plan` WRITE;
/*!40000 ALTER TABLE `plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_category`
--

DROP TABLE IF EXISTS `plan_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan_category` (
  `plan_category_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_category_name` varchar(150) DEFAULT NULL,
  `plan_category_description` text,
  `plan_category_default` int(11) DEFAULT '0',
  PRIMARY KEY (`plan_category_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_category`
--

LOCK TABLES `plan_category` WRITE;
/*!40000 ALTER TABLE `plan_category` DISABLE KEYS */;
INSERT INTO `plan_category` VALUES (1,'PLANO BASE','PLANTA ARQUITECTURA',1),(3,'Arquitectura',NULL,0),(4,'BIM',NULL,0);
/*!40000 ALTER TABLE `plan_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_node`
--

DROP TABLE IF EXISTS `plan_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan_node` (
  `plan_node_id` int(10) NOT NULL AUTO_INCREMENT,
  `plan_id` int(10) NOT NULL,
  `node_id` int(10) NOT NULL,
  `plan_section_id` int(11) DEFAULT NULL,
  `handler` text NOT NULL,
  PRIMARY KEY (`plan_node_id`) USING BTREE,
  KEY `plan_node_ibfk_1` (`plan_id`) USING BTREE,
  KEY `plan_node_ibfk_2` (`node_id`) USING BTREE,
  KEY `plan_node_ibfk_3` (`plan_section_id`) USING BTREE,
  CONSTRAINT `plan_node_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plan_node_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plan_node_ibfk_3` FOREIGN KEY (`plan_section_id`) REFERENCES `plan_section` (`plan_section_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_node`
--

LOCK TABLES `plan_node` WRITE;
/*!40000 ALTER TABLE `plan_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `plan_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `plan_section`
--

DROP TABLE IF EXISTS `plan_section`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plan_section` (
  `plan_section_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_id` int(11) NOT NULL,
  `plan_section_name` varchar(255) NOT NULL,
  `plan_section_color` varchar(10) DEFAULT NULL,
  `plan_section_status` int(1) DEFAULT '1',
  PRIMARY KEY (`plan_section_id`) USING BTREE,
  KEY `plan_section_ibfk_1` (`plan_id`) USING BTREE,
  CONSTRAINT `plan_section_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `plan` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `plan_section`
--

LOCK TABLES `plan_section` WRITE;
/*!40000 ALTER TABLE `plan_section` DISABLE KEYS */;
/*!40000 ALTER TABLE `plan_section` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider`
--

DROP TABLE IF EXISTS `provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provider` (
  `provider_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_type_id` int(11) DEFAULT NULL,
  `provider_name` varchar(30) DEFAULT NULL,
  `provider_contact` varchar(45) DEFAULT NULL,
  `provider_phone` varchar(20) DEFAULT NULL,
  `provider_fax` varchar(20) DEFAULT NULL,
  `provider_email` varchar(40) DEFAULT NULL,
  `provider_description` text,
  PRIMARY KEY (`provider_id`) USING BTREE,
  KEY `provider_ibfk_1` (`provider_type_id`) USING BTREE,
  CONSTRAINT `provider_ibfk_1` FOREIGN KEY (`provider_type_id`) REFERENCES `provider_type` (`provider_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider`
--

LOCK TABLES `provider` WRITE;
/*!40000 ALTER TABLE `provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `provider_type`
--

DROP TABLE IF EXISTS `provider_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `provider_type` (
  `provider_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_type_name` varchar(50) DEFAULT NULL,
  `provider_type_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`provider_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `provider_type`
--

LOCK TABLES `provider_type` WRITE;
/*!40000 ALTER TABLE `provider_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `module_id` int(11) DEFAULT NULL,
  `report_name` varchar(50) DEFAULT NULL,
  `report_url` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`report_id`) USING BTREE,
  KEY `report_ibfk_1` (`module_id`) USING BTREE,
  CONSTRAINT `report_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `module` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report`
--

LOCK TABLES `report` WRITE;
/*!40000 ALTER TABLE `report` DISABLE KEYS */;
INSERT INTO `report` VALUES (4,5,'Vaciado Recintos','index.php/report/vaciado1'),(5,5,'Vaciado Simplificado','index.php/report/vaciado2'),(6,5,'Informe Mineduc - por edificio','index.php/report/informeMineduc'),(7,4,'Codigo Recintos','index.php/asset/assetuchileplancheta/exportPlanchetaNivel'),(8,4,'Vaciado Activos','index.php/asset/asset/exportCompleto3');
/*!40000 ALTER TABLE `report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `report_user_group`
--

DROP TABLE IF EXISTS `report_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `report_user_group` (
  `report_user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) DEFAULT NULL,
  `user_group_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`report_user_group_id`) USING BTREE,
  KEY `report_user_group_ibfk_1` (`report_id`) USING BTREE,
  KEY `report_user_group_ibfk_2` (`user_group_id`) USING BTREE,
  CONSTRAINT `report_user_group_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `report` (`report_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `report_user_group_ibfk_2` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `report_user_group`
--

LOCK TABLES `report_user_group` WRITE;
/*!40000 ALTER TABLE `report_user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `report_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_folio` varchar(11) DEFAULT NULL,
  `request_problem_id` int(11) DEFAULT NULL,
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_date_creation` date NOT NULL,
  `request_date_resolution` date DEFAULT NULL,
  `request_subject` varchar(100) NOT NULL,
  `request_description` text NOT NULL,
  `mtn_work_order_id` int(11) DEFAULT NULL,
  `request_status_id` int(11) DEFAULT NULL,
  `request_requested_by` varchar(100) DEFAULT NULL,
  `request_mail` varchar(100) DEFAULT NULL,
  `request_fono` int(11) DEFAULT NULL,
  `request_requested_by_comment` text,
  PRIMARY KEY (`request_id`) USING BTREE,
  KEY `request_ibfk_1` (`user_id`) USING BTREE,
  KEY `request_ibfk_2` (`request_problem_id`) USING BTREE,
  KEY `request_ibfk_3` (`request_status_id`) USING BTREE,
  CONSTRAINT `request_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `request_ibfk_2` FOREIGN KEY (`request_problem_id`) REFERENCES `request_problem` (`request_problem_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `request_ibfk_3` FOREIGN KEY (`request_status_id`) REFERENCES `request_status` (`request_status_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request`
--

LOCK TABLES `request` WRITE;
/*!40000 ALTER TABLE `request` DISABLE KEYS */;
/*!40000 ALTER TABLE `request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_problem`
--

DROP TABLE IF EXISTS `request_problem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_problem` (
  `request_problem_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_problem_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`request_problem_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_problem`
--

LOCK TABLES `request_problem` WRITE;
/*!40000 ALTER TABLE `request_problem` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_problem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `request_status`
--

DROP TABLE IF EXISTS `request_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request_status` (
  `request_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `request_status_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`request_status_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request_status`
--

LOCK TABLES `request_status` WRITE;
/*!40000 ALTER TABLE `request_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `request_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service`
--

DROP TABLE IF EXISTS `service`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_type_id` int(11) DEFAULT NULL,
  `service_status_id` int(11) DEFAULT NULL,
  `service_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `service_organism` varchar(255) DEFAULT NULL,
  `service_phone` varchar(255) DEFAULT NULL,
  `service_commentary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_id`) USING BTREE,
  KEY `service_ibfk_1` (`user_id`) USING BTREE,
  KEY `service_ibfk_2` (`service_type_id`) USING BTREE,
  KEY `service_ibfk_3` (`service_status_id`) USING BTREE,
  KEY `service_ibfk_4` (`node_id`) USING BTREE,
  CONSTRAINT `service_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_ibfk_2` FOREIGN KEY (`service_type_id`) REFERENCES `service_type` (`service_type_id`),
  CONSTRAINT `service_ibfk_3` FOREIGN KEY (`service_status_id`) REFERENCES `service_status` (`service_status_id`),
  CONSTRAINT `service_ibfk_4` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service`
--

LOCK TABLES `service` WRITE;
/*!40000 ALTER TABLE `service` DISABLE KEYS */;
/*!40000 ALTER TABLE `service` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_log`
--

DROP TABLE IF EXISTS `service_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_log` (
  `service_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `service_log_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `service_log_detail` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_log_id`) USING BTREE,
  KEY `service_log_ibfk_1` (`service_id`) USING BTREE,
  KEY `service_log_ibfk_2` (`user_id`) USING BTREE,
  CONSTRAINT `service_log_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `service` (`service_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_log`
--

LOCK TABLES `service_log` WRITE;
/*!40000 ALTER TABLE `service_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_status`
--

DROP TABLE IF EXISTS `service_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_status` (
  `service_status_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_status_name` varchar(255) DEFAULT NULL,
  `service_status_commentary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_status_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_status`
--

LOCK TABLES `service_status` WRITE;
/*!40000 ALTER TABLE `service_status` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `service_type`
--

DROP TABLE IF EXISTS `service_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `service_type` (
  `service_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_type_name` varchar(255) NOT NULL,
  `service_type_commentary` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`service_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `service_type`
--

LOCK TABLES `service_type` WRITE;
/*!40000 ALTER TABLE `service_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `service_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud`
--

DROP TABLE IF EXISTS `solicitud`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud` (
  `solicitud_id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `solicitud_type_id` int(11) DEFAULT NULL,
  `solicitud_estado_id` int(11) DEFAULT NULL,
  `solicitud_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solicitud_folio` varchar(255) DEFAULT NULL,
  `solicitud_factura_archivo` varchar(255) DEFAULT NULL,
  `solicitud_factura_nombre` varchar(255) DEFAULT NULL,
  `solicitud_factura_numero` varchar(255) DEFAULT NULL,
  `solicitud_oc_archivo` varchar(255) DEFAULT NULL,
  `solicitud_oc_nombre` varchar(255) DEFAULT NULL,
  `solicitud_oc_numero` varchar(255) DEFAULT NULL,
  `solicitud_comen_user` varchar(255) DEFAULT NULL,
  `solicitud_comen_admin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`solicitud_id`) USING BTREE,
  KEY `solicitud_ibfk_1` (`user_id`) USING BTREE,
  KEY `solicitud_ibfk_2` (`solicitud_type_id`) USING BTREE,
  KEY `solicitud_ibfk_3` (`solicitud_estado_id`) USING BTREE,
  KEY `solicitud_ibfk_4` (`node_id`) USING BTREE,
  CONSTRAINT `solicitud_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `solicitud_ibfk_2` FOREIGN KEY (`solicitud_type_id`) REFERENCES `solicitud_type` (`solicitud_type_id`),
  CONSTRAINT `solicitud_ibfk_3` FOREIGN KEY (`solicitud_estado_id`) REFERENCES `solicitud_estado` (`solicitud_estado_id`),
  CONSTRAINT `solicitud_ibfk_4` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud`
--

LOCK TABLES `solicitud` WRITE;
/*!40000 ALTER TABLE `solicitud` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_estado`
--

DROP TABLE IF EXISTS `solicitud_estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_estado` (
  `solicitud_estado_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_estado_nombre` varchar(255) DEFAULT NULL,
  `solicitud_estado_comentario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`solicitud_estado_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_estado`
--

LOCK TABLES `solicitud_estado` WRITE;
/*!40000 ALTER TABLE `solicitud_estado` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud_estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_log`
--

DROP TABLE IF EXISTS `solicitud_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_log` (
  `solicitud_log_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `solicitud_log_fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `solicitud_log_detalle` varchar(255) NOT NULL,
  PRIMARY KEY (`solicitud_log_id`) USING BTREE,
  KEY `solicitud_log_ibfk_1` (`solicitud_id`) USING BTREE,
  KEY `solicitud_log_ibfk_2` (`user_id`) USING BTREE,
  CONSTRAINT `solicitud_log_ibfk_1` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitud` (`solicitud_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `solicitud_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_log`
--

LOCK TABLES `solicitud_log` WRITE;
/*!40000 ALTER TABLE `solicitud_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `solicitud_type`
--

DROP TABLE IF EXISTS `solicitud_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `solicitud_type` (
  `solicitud_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_type_nombre` varchar(255) DEFAULT NULL,
  `solicitud_type_comentario` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`solicitud_type_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `solicitud_type`
--

LOCK TABLES `solicitud_type` WRITE;
/*!40000 ALTER TABLE `solicitud_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `solicitud_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_ids`
--

DROP TABLE IF EXISTS `tbl_ids`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_ids` (
  `node_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `lft` int(11) DEFAULT NULL,
  `rgt` int(11) DEFAULT NULL,
  `total` int(11) DEFAULT NULL,
  KEY `tbl_ids_ibfk_1` (`node_id`) USING BTREE,
  CONSTRAINT `tbl_ids_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_ids`
--

LOCK TABLES `tbl_ids` WRITE;
/*!40000 ALTER TABLE `tbl_ids` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_ids` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_reporte`
--

DROP TABLE IF EXISTS `tbl_reporte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_reporte` (
  `node_id` int(11) NOT NULL,
  `node_type` int(11) NOT NULL,
  `node_type_category_id` int(11) NOT NULL,
  `level_node` smallint(5) NOT NULL,
  `lft_node` int(11) NOT NULL,
  `rgt_node` int(11) NOT NULL,
  `infra_attribute` varchar(255) DEFAULT NULL,
  `infra_other_data_attribute_id` int(11) DEFAULT NULL,
  `infra_other_data_attribute_node_type_order` int(11) DEFAULT NULL,
  `data_option` int(11) DEFAULT NULL,
  `infra_info_id` int(11) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  KEY `tbl_reporte_ibfk_1` (`node_id`) USING BTREE,
  KEY `tbl_reporte_ibfk_2` (`node_type_category_id`) USING BTREE,
  KEY `tbl_reporte_ibfk_3` (`node_type`) USING BTREE,
  CONSTRAINT `tbl_reporte_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_reporte_ibfk_2` FOREIGN KEY (`node_type_category_id`) REFERENCES `node_type_category` (`node_type_category_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_reporte_ibfk_3` FOREIGN KEY (`node_type`) REFERENCES `node_type` (`node_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_reporte`
--

LOCK TABLES `tbl_reporte` WRITE;
/*!40000 ALTER TABLE `tbl_reporte` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_reporte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp`
--

DROP TABLE IF EXISTS `temp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp` (
  `nom` varchar(255) NOT NULL,
  `serie` varchar(255) NOT NULL,
  `interno` varchar(255) NOT NULL,
  `marca` varchar(255) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `valor` varchar(255) NOT NULL,
  `fecha_compra` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `recinto` varchar(255) NOT NULL,
  `naturaleza` varchar(255) NOT NULL,
  `marca_id` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `nodo_id` int(11) NOT NULL,
  `naturaleza_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp`
--

LOCK TABLES `temp` WRITE;
/*!40000 ALTER TABLE `temp` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `temp_codigo`
--

DROP TABLE IF EXISTS `temp_codigo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_codigo` (
  `infra_other_data_value_id` int(11) NOT NULL DEFAULT '0',
  `infra_other_data_attribute_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `infra_other_data_option_id` int(11) DEFAULT NULL,
  `infra_other_data_value_value` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  KEY `temp_codigo_ibfk_1` (`node_id`) USING BTREE,
  KEY `temp_codigo_ibfk_2` (`infra_other_data_value_id`) USING BTREE,
  KEY `temp_codigo_ibfk_3` (`infra_other_data_attribute_id`) USING BTREE,
  KEY `temp_codigo_ibfk_4` (`infra_other_data_option_id`) USING BTREE,
  CONSTRAINT `temp_codigo_ibfk_1` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `temp_codigo_ibfk_2` FOREIGN KEY (`infra_other_data_value_id`) REFERENCES `infra_other_data_value` (`infra_other_data_value_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `temp_codigo_ibfk_3` FOREIGN KEY (`infra_other_data_attribute_id`) REFERENCES `infra_other_data_attribute` (`infra_other_data_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `temp_codigo_ibfk_4` FOREIGN KEY (`infra_other_data_option_id`) REFERENCES `infra_other_data_option` (`infra_other_data_option_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `temp_codigo`
--

LOCK TABLES `temp_codigo` WRITE;
/*!40000 ALTER TABLE `temp_codigo` DISABLE KEYS */;
/*!40000 ALTER TABLE `temp_codigo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(150) NOT NULL,
  `user_username` varchar(30) NOT NULL,
  `user_password` varchar(200) DEFAULT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_type` varchar(10) DEFAULT NULL,
  `language_id` int(11) NOT NULL,
  `user_expiration` date DEFAULT NULL,
  `user_status` int(11) NOT NULL,
  `user_tree_full` int(11) NOT NULL,
  `user_default_module` int(11) DEFAULT NULL,
  `user_preference` int(11) NOT NULL DEFAULT '1',
  `user_path` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`) USING BTREE,
  KEY `user_ibfk_1` (`language_id`) USING BTREE,
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `language` (`language_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'administrador','admin','f6fdffe48c908deb0f4c3bd36c032e72','administatrador@igeo.cl','A',1,NULL,0,0,NULL,1,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group`
--

DROP TABLE IF EXISTS `user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group` (
  `user_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_name` varchar(200) NOT NULL,
  PRIMARY KEY (`user_group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group`
--

LOCK TABLES `user_group` WRITE;
/*!40000 ALTER TABLE `user_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group_action`
--

DROP TABLE IF EXISTS `user_group_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_action` (
  `user_group_action_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `module_action_id` int(11) NOT NULL,
  PRIMARY KEY (`user_group_action_id`) USING BTREE,
  KEY `user_group_action_ibfk_1` (`module_action_id`) USING BTREE,
  KEY `user_group_action_ibfk_2` (`user_group_id`) USING BTREE,
  CONSTRAINT `user_group_action_ibfk_1` FOREIGN KEY (`module_action_id`) REFERENCES `module_action` (`module_action_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_group_action_ibfk_2` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group_action`
--

LOCK TABLES `user_group_action` WRITE;
/*!40000 ALTER TABLE `user_group_action` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group_action` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group_node`
--

DROP TABLE IF EXISTS `user_group_node`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_node` (
  `user_group_node_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_group_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  PRIMARY KEY (`user_group_node_id`) USING BTREE,
  KEY `user_group_node_ibfk_1` (`user_group_id`) USING BTREE,
  KEY `user_group_node_ibfk_2` (`node_id`) USING BTREE,
  CONSTRAINT `user_group_node_ibfk_1` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_group_node_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`node_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group_node`
--

LOCK TABLES `user_group_node` WRITE;
/*!40000 ALTER TABLE `user_group_node` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group_node` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_group_user`
--

DROP TABLE IF EXISTS `user_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_group_user` (
  `user_group_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_group_user_id`) USING BTREE,
  KEY `user_group_user_ibfk_1` (`user_id`) USING BTREE,
  KEY `user_group_user_ibfk_2` (`user_group_id`) USING BTREE,
  CONSTRAINT `user_group_user_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`),
  CONSTRAINT `user_group_user_ibfk_2` FOREIGN KEY (`user_group_id`) REFERENCES `user_group` (`user_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_group_user`
--

LOCK TABLES `user_group_user` WRITE;
/*!40000 ALTER TABLE `user_group_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_group_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_provider`
--

DROP TABLE IF EXISTS `user_provider`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_provider` (
  `user_provider_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `provider_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_provider_id`) USING BTREE,
  KEY `user_provider_ibfk_1` (`provider_id`) USING BTREE,
  KEY `user_provider_ibfk_2` (`user_id`) USING BTREE,
  CONSTRAINT `user_provider_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `provider` (`provider_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_provider_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_provider`
--

LOCK TABLES `user_provider` WRITE;
/*!40000 ALTER TABLE `user_provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_provider` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vista_1`
--

DROP TABLE IF EXISTS `vista_1`;
/*!50001 DROP VIEW IF EXISTS `vista_1`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vista_1` (
 `node_id` tinyint NOT NULL,
  `node_type` tinyint NOT NULL,
  `node_type_category_id` tinyint NOT NULL,
  `level_node` tinyint NOT NULL,
  `lft_node` tinyint NOT NULL,
  `rgt_node` tinyint NOT NULL,
  `infra_attribute` tinyint NOT NULL,
  `infra_other_data_attribute_id` tinyint NOT NULL,
  `infra_other_data_attribute_node_type_order` tinyint NOT NULL,
  `data_option` tinyint NOT NULL,
  `infra_info_id` tinyint NOT NULL,
  `value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vista_2`
--

DROP TABLE IF EXISTS `vista_2`;
/*!50001 DROP VIEW IF EXISTS `vista_2`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vista_2` (
 `node_id` tinyint NOT NULL,
  `node_type` tinyint NOT NULL,
  `node_type_category_id` tinyint NOT NULL,
  `level_node` tinyint NOT NULL,
  `lft_node` tinyint NOT NULL,
  `rgt_node` tinyint NOT NULL,
  `infra_attribute` tinyint NOT NULL,
  `infra_other_data_attribute_id` tinyint NOT NULL,
  `infra_other_data_attribute_node_type_order` tinyint NOT NULL,
  `data_option` tinyint NOT NULL,
  `infra_info_id` tinyint NOT NULL,
  `value` tinyint NOT NULL
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vista_1`
--

/*!50001 DROP TABLE IF EXISTS `vista_1`*/;
/*!50001 DROP VIEW IF EXISTS `vista_1`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_1` AS select `nd`.`node_id` AS `node_id`,`nd`.`node_type_id` AS `node_type`,`nt`.`node_type_category_id` AS `node_type_category_id`,`nd`.`level` AS `level_node`,`nd`.`lft` AS `lft_node`,`nd`.`rgt` AS `rgt_node`,(select `t`.`language_tag_value` from (`language_tag` `t` join `module` `m` on(((`t`.`module_id` = `m`.`module_id`) and (`m`.`module_namespace` = 'Infrastructure')))) where (`t`.`language_tag_tag` = `ic`.`infra_attribute`)) AS `infra_attribute`,NULL AS `infra_other_data_attribute_id`,NULL AS `infra_other_data_attribute_node_type_order`,NULL AS `data_option`,`ic`.`infra_configuration_id` AS `infra_info_id`,`ic`.`infra_attribute` AS `value` from ((`infra_configuration` `ic` join `node_type` `nt` on((`nt`.`node_type_id` = `ic`.`node_type_id`))) join `node` `nd` on(((`nd`.`node_type_id` = `nt`.`node_type_id`) and (`nd`.`level` > 0)))) order by `nd`.`lft`,`nd`.`level` desc,`ic`.`infra_configuration_id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_2`
--

/*!50001 DROP TABLE IF EXISTS `vista_2`*/;
/*!50001 DROP VIEW IF EXISTS `vista_2`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_2` AS select `nd`.`node_id` AS `node_id`,`nd`.`node_type_id` AS `node_type`,`nt`.`node_type_category_id` AS `node_type_category_id`,`nd`.`level` AS `level_node`,`nd`.`lft` AS `lft_node`,`nd`.`rgt` AS `rgt_node`,`atb`.`infra_other_data_attribute_name` AS `infra_attribute`,`atb`.`infra_other_data_attribute_id` AS `infra_other_data_attribute_id`,`ont`.`infra_other_data_attribute_node_type_order` AS `infra_other_data_attribute_node_type_order`,`dv`.`infra_other_data_option_id` AS `data_option`,NULL AS `infra_info_id`,if((`atb`.`infra_other_data_attribute_type` = 5),`ido`.`infra_other_data_option_name`,`dv`.`infra_other_data_value_value`) AS `value` from (((((`infra_other_data_attribute_node_type` `ont` join `node_type` `nt` on((`nt`.`node_type_id` = `ont`.`node_type_id`))) join `node` `nd` on(((`nd`.`node_type_id` = `nt`.`node_type_id`) and (`nd`.`level` > 0)))) join `infra_other_data_attribute` `atb` on((`atb`.`infra_other_data_attribute_id` = `ont`.`infra_other_data_attribute_id`))) left join `infra_other_data_value` `dv` on(((`dv`.`infra_other_data_attribute_id` = `atb`.`infra_other_data_attribute_id`) and (`dv`.`node_id` = `nd`.`node_id`)))) left join `infra_other_data_option` `ido` on((`ido`.`infra_other_data_option_id` = `dv`.`infra_other_data_option_id`))) order by `nd`.`lft`,`nd`.`level` desc,`ont`.`infra_other_data_attribute_node_type_order` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-08-29 15:05:09
