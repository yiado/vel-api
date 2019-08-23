INSERT INTO `service_status` VALUES (1, 'Solicitado', NULL);
INSERT INTO `service_status` VALUES (2, 'Recepcionada', NULL);
INSERT INTO `service_status` VALUES (3, 'En Proceso', NULL);
INSERT INTO `service_status` VALUES (4, 'Terminadas', NULL);
INSERT INTO `service_status` VALUES (5, 'En Presupuesto', NULL);
INSERT INTO `service_status` VALUES (6, 'Rechazada', NULL);


INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (1, 'Infraestructura', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (2, 'Pintura', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (3, 'Ventana o Vidrios', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (4, 'Eléctrico', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (5, 'Cerrajería', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (6, 'Gasfitería', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (7, 'Carpintería', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (8, 'Aseo', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (9, 'Estacionamiento de Visita', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (10, 'Vigilancia y Seguridad', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (11, 'Sala de Reunión', NULL);
INSERT INTO `service_type`(`service_type_id`, `service_type_name`, `service_type_commentary`) VALUES (12, 'Acceso con TUI', NULL);


INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8000, 8, 'get', 'request/request/get', 0, 722);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8001, 8, 'add', 'request/request/add', 0, 723);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8002, 8, 'update', 'request/request/update', 0, 655);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8003, 8, 'update', 'request/request/update', 0, 657);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8004, 8, 'export', 'request/requestcontroller/export', 0, 724);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8005, 8, 'history', 'request/log/get', 0, 1059);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8006, 8, 'get', 'request/request/get', 0, 1060);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8007, 8, 'get', 'request/service/get', 0, 1064);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8008, 8, 'get', 'request/service/informacion', 0, 1066);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8009, 8, 'add', 'request/service/add', 0, 1067);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8010, 8, 'export', 'request/servicecontroller/export', 0, 1069);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8011, 8, 'history', 'request/servicelog/get', 0, 1068);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8012, 8, 'get', 'request/service/get', 0, 1070);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8013, 8, 'get', 'request/service/get', 0, 1070);
