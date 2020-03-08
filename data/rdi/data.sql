INSERT INTO `rdi_status`(`rdi_status_id`, `rdi_status_name`) VALUES (1, 'Solicitada');
INSERT INTO `rdi_status`(`rdi_status_id`, `rdi_status_name`) VALUES (2, 'Rechazada');
INSERT INTO `rdi_status`(`rdi_status_id`, `rdi_status_name`) VALUES (3, 'En proceso');
INSERT INTO `rdi_status`(`rdi_status_id`, `rdi_status_name`) VALUES (4, 'Terminada');

INSERT INTO `request_evaluation`(`request_evaluation_id`, `request_evaluation_name`) VALUES (1, 'Conforme');
INSERT INTO `request_evaluation`(`request_evaluation_id`, `request_evaluation_name`) VALUES (2, 'No conforme');



INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 1, 'rdi', 'Requerimiento de información');


/**
* Modificar id lenguage_tag en module_action luego de instalar en cada cliente ya que el autoincrementable puede variar
*/
INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 8, 'rdi_add', 'Agregar');
INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 8, 'rdi_export', 'Exportar');
INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 8, 'rdi_history', 'Historial');
INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 8, 'rdi_status', 'Estados');
INSERT INTO `language_tag`(`language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (0, 1, 8, 'rdi_search', 'Buscar');

INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8016, 8, 'add', 'request/rdi/add', 0, 1151);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8017, 8, 'export', 'request/rdi/export', 0, 1152);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8018, 8, 'history', 'request/rdilog/get', 0, 1153);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8019, 8, 'status', 'request/rdistatus/get', 0, 1154);
INSERT INTO `module_action`(`module_action_id`, `module_id`, `module_action_name`, `module_action_uri`, `module_action_is_public`, `language_tag_id`) VALUES (8020, 8, 'search', 'request/rdi/get', 0, 1156);
