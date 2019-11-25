ALTER TABLE `infra_info` 
ADD COLUMN `infra_info_terrero_escritura` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_terrero_escritura_total` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_terreno_cad` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_terreno_cad_total` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_construidos_ogcu` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_construidos_ogcu_total` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_uf` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_uf_total` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_emplazamiento` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_emplazamiento_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_emplazamiento_porcent` int(11) NULL DEFAULT 0,

ADD COLUMN `infra_info_calles` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_calles_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_porcent_calles` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_areas_verdes` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_areas_verdes_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_areas_verdes_porcent` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_areas_manejadas` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_areas_manejadas_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_areas_manejadas_porcent` int(11) NULL DEFAULT 0,

ADD COLUMN `infra_info_patios_abiertos` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_patios_abiertos_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_patios_abiertos_porcent` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_recintos_deportivos` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_recintos_deportivos_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_recintos_deportivos_porcent` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_circulaciones_abiertas` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_circulaciones_abiertas_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_circulaciones_abiertas_porcent` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_otras_areas` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_otras_areas_total` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_otras_areas_porcent` double(11, 3) NULL DEFAULT 0.000,

ADD COLUMN `infra_info_estacionamientos_num` int(11) NULL DEFAULT 0,
ADD COLUMN `infra_info_estacionamientos_total` int(11) NULL DEFAULT 0,

ADD COLUMN `infra_info_estacionamientos` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_estacionamientos_total_sector` double(11, 3) NULL DEFAULT 0.000,
ADD COLUMN `infra_info_estacionamientos_porcent` double(11, 3) NULL DEFAULT 0.000;


DROP TABLE IF EXISTS `uf`;
CREATE TABLE `uf`  (
  `uf_id` int(11) NOT NULL AUTO_INCREMENT,
  `uf_value` double(9, 3) NULL DEFAULT NULL,
  `uf_date` date NULL DEFAULT NULL,
  PRIMARY KEY (`uf_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 0 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = Compact;