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


INSERT INTO `module`(`module_id`, `module_name`, `module_namespace`, `module_abbreviation`, `module_position`) VALUES (8, 'Solicitudes', 'Request', 'request', 5);


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

INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (551, 551, 1, 8, 'requests', 'Solicitudes');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (552, 552, 1, 8, 'add_request', 'Agregar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (553, 553, 1, 8, 'please_select_node', '¡Oops!. Favor seleccionar nodo...');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (554, 554, 1, 8, 'approve', 'Aprobar');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (555, 555, 1, 8, 'reject', 'Rechazar');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (556, 556, 1, 8, 'edit_request', 'Editar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (557, 557, 1, 8, 'problem', 'Problema');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (558, 558, 1, 8, 'subject', 'Asunto');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (559, 559, 1, 8, 'team_fail', 'Equipo/Falla');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (560, 560, 1, 8, 'team', 'Equipo');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (561, 561, 1, 8, 'failure', 'Falla');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (562, 562, 1, 8, 'applicant_details', 'Datos Solicitante');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (654, 654, 1, 8, 'select_a_date_range_to_for_the_request', 'Seleccione un rango de fechas de solicitudes');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (655, 655, 1, 8, 'approve_request', 'Aprobar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (656, 656, 1, 8, 'do_you_want_to_Approve_the_request', '¿Desea Aprobar la Solicitud?');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (657, 657, 1, 8, 'request_reject', 'Rechazar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (658, 658, 1, 8, 'do_you_want_to_reject_the_request', '¿Desea Rechazar la Solicitud?');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (677, 677, 1, 8, 'request_problem', 'Solicitud Problema');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (684, 684, 1, 8, 'request_n', 'Nro. Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (692, 692, 1, 8, 'only_the_state_can_change_applications_issued', 'Solo se permite cambiar el estado a las solicitudes \"Emitidas\"');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (723, 723, 1, 8, 'add_report', 'Agregar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (724, 724, 1, 8, 'export_request', 'Exportar Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (778, 778, 1, 8, 'work_order_number', 'Orden de Trabajo Nro.  ');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (793, 793, 1, 8, 'request_export_folio', 'FOLIO');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (794, 794, 1, 8, 'request_export_asset', 'ACTIVO');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (795, 795, 1, 8, 'request_export_location', 'UBICACION');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (796, 796, 1, 8, 'request_export_problem', 'PROBLEMA');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (797, 797, 1, 8, 'request_export_subject', 'ASUNTO');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (798, 798, 1, 8, 'request_export_creation_date', 'FECHA CREACION');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (799, 799, 1, 8, 'request_export_state', 'ESTADO');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (800, 800, 1, 8, 'request_view', 'Visualizar Solicitudes');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (814, 814, 1, 8, 'request_approve_view', 'Aprobar y Ver OT.');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (951, 951, 1, 8, 'campus_fail', 'Recinto/Falla');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1059, 1059, 1, 8, 'history', 'Historial Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1060, 1060, 1, 8, 'search_request', 'Busqueda Solicitud');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1067, 1067, 1, 8, 'add_service', 'Agregar Servicio');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1068, 1068, 1, 8, 'history_service', 'Historial Servicio');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1069, 1069, 1, 8, 'export_service', 'Exportar Servicio');
INSERT INTO `language_tag`(`language_tag_aux_id`, `language_tag_id`, `language_id`, `module_id`, `language_tag_tag`, `language_tag_value`) VALUES (1070, 1070, 1, 8, 'search_service', 'Busqueda Servicio');
