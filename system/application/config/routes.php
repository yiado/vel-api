<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
| 	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded. 
|
|	$route['scaffolding_trigger'] = 'scaffolding';
|
| This route lets you set a "secret" word that will trigger the
| scaffolding feature for added security. Note: Scaffolding must be
| enabled in the controller in which you intend to use it.   The reserved 
| routes must come before any wildcard or regular expression routes.
|
*/
$route['gui/principal/(:any)']                                                          = "gui/principal";;
$route['app/(:any)'] 									= "appcontroller/$1";
$route['core/node/(:any)'] 							 	= "core/nodecontroller/$1";
$route['core/nodetypecategory/(:any)'] 		 					= "core/nodetypecategorycontroller/$1";
$route['core/nodetype/(:any)'] 						 		= "core/nodetypecontroller/$1";
$route['core/provider/(:any)'] 						 		= "core/providercontroller/$1";
$route['core/providertype/(:any)'] 				 			= "core/providertypecontroller/$1";
$route['core/measureunit/(:any)'] 				 			= "core/measureunitcontroller/$1";
$route['core/user/(:any)']                      		 			= "core/usercontroller/$1";
$route['core/group/(:any)']                    		 				= "core/groupcontroller/$1";
$route['core/currency/(:any)']                 		 				= "core/currencycontroller/$1";
$route['core/module/(:any)']                    		 			= "core/modulecontroller/$1";
$route['core/permissions/(:any)']               	 				= "core/permissionscontroller/$1";
$route['core/language/(:any)']                  					= "core/languagecontroller/$1";
$route['core/languagetag/(:any)']               					= "core/languagetagcontroller/$1";
$route['core/brand/(:any)']               						= "core/brandcontroller/$1";
$route['core/contract/(:any)']                     					= "core/contractcontroller/$1";
$route['core/contractnode/(:any)']                                                      = "core/contractnodecontroller/$1";
$route['core/contractasset/(:any)']                     				= "core/contractassetcontroller/$1";
$route['core/userprovider/(:any)']                                                      = "core/userprovidercontroller/$1";
$route['core/sendalert/(:any)']                     					= "core/sendalertcontroller/$1";
$route['core/log/(:any)']                                                               = "core/logcontroller/$1";
$route['core/help/(:any)']                                                              = "core/helpcontroller/$1";

//$route['asset/asset/(:any)'] 							 	= "asset/assetcontroller/$1";
//$route['asset/assettype/(:any)'] 					 		= "asset/assettypecontroller/$1";
//$route['asset/assetcondition/(:any)'] 			 				= "asset/assetconditioncontroller/$1";
//$route['asset/assetstatus/(:any)'] 					 		= "asset/assetstatuscontroller/$1";
//$route['asset/assetinsurance/(:any)'] 			 				= "asset/assetinsurancecontroller/$1";
//$route['asset/assetmeasurement/(:any)'] 		 				= "asset/assetmeasurementcontroller/$1";
//$route['asset/assetdocument/(:any)'] 		 					= "asset/assetdocumentcontroller/$1";
//$route['asset/assettriggermeasurementconfig/(:any)']                                    = "asset/assettriggermeasurementconfigcontroller/$1";
//$route['asset/assetotherdataattributeassettype/(:any)']                                 = "asset/assetotherdataattributeassettypecontroller/$1";
//$route['asset/assetotherdataattribute/(:any)']                                          = "asset/assetotherdataattributecontroller/$1";
//$route['asset/assetotherdatavalue/(:any)'] 						= "asset/assetotherdatavaluecontroller/$1";
//$route['asset/assetlog/(:any)'] 							= "asset/assetlogcontroller/$1";
//$route['asset/assetuchileplancheta/(:any)'] 						= "asset/assetuchileplanchetacontroller/$1";
//$route['asset/assetplancheta/(:any)']                                                   = "asset/assetplanchetacontroller/$1";
//$route['asset/assetinventory/(:any)'] 							= "asset/assetinventorycontroller/$1";

$route['asset/asset/(:any)'] 							 	= "asset/assetcontroller/$1";
$route['asset/assettype/(:any)'] 					 		= "asset/assettypecontroller/$1";
$route['asset/assetcondition/(:any)'] 			 				= "asset/assetconditioncontroller/$1";
$route['asset/assetstatus/(:any)'] 					 		= "asset/assetstatuscontroller/$1";
$route['asset/assetinsurance/(:any)'] 			 				= "asset/assetinsurancecontroller/$1";
$route['asset/assetmeasurement/(:any)'] 		 				= "asset/assetmeasurementcontroller/$1";
$route['asset/assetdocument/(:any)'] 		 					= "asset/assetdocumentcontroller/$1";
$route['asset/assettriggermeasurementconfig/(:any)']                                    = "asset/assettriggermeasurementconfigcontroller/$1";
$route['asset/assetotherdataattributeassettype/(:any)']                                 = "asset/assetotherdataattributeassettypecontroller/$1";
$route['asset/assetotherdataattribute/(:any)']                                          = "asset/assetotherdataattributecontroller/$1";
$route['asset/assetotherdatavalue/(:any)'] 						= "asset/assetotherdatavaluecontroller/$1";
$route['asset/assetlog/(:any)'] 							= "asset/assetlogcontroller/$1";
$route['asset/assetuchileplancheta/(:any)'] 						= "asset/assetuchileplanchetacontroller/$1";
$route['asset/assetuchilelistadofolio/(:any)'] 						= "asset/assetuchilelistadofoliocontroller/$1";
$route['asset/assetinventory/(:any)'] 							= "asset/assetinventoryuchilecontroller/$1";

$route['asset/assetload/(:any)'] 							= "asset/assetloadcontroller/$1";

$route['costs/costs/(:any)']                                                            = "costs/costscontroller/$1";
$route['costs/coststype/(:any)']                                                        = "costs/coststypecontroller/$1";
$route['costs/costsmonth/(:any)']                                                        = "costs/costsmonthcontroller/$1";


$route['infra/infragrupo/(:any)'] 			 	 			= "infra/infragrupocontroller/$1";
$route['infra/infrastructure/(:any)'] 			 	 			= "infra/infrastructurecontroller/$1";
$route['infra/infrainfo/(:any)'] 					 	 	= "infra/infrainfocontroller/$1";
$route['infra/infracoordinate/(:any)'] 							= "infra/infracoordinatecontroller/$1";
$route['infra/infrainfooption/(:any)'] 				 			= "infra/infrainfooptioncontroller/$1";
$route['infra/infrainfoconfig/(:any)'] 							= "infra/infrainfoconfigcontroller/$1";
$route['infra/infraotherdatavalue/(:any)']  						= "infra/infraotherdatavaluecontroller/$1";
$route['infra/infraotherdataoption/(:any)'] 						= "infra/infraotherdataoptioncontroller/$1";
$route['infra/infraotherdataattribute/(:any)']  					= "infra/infraotherdataattributecontroller/$1";
$route['infra/infraotherdataattributenodetype/(:any)']                                  = "infra/infraotherdataattributenodetypecontroller/$1";

$route['plan/plan/(:any)'] 								= "plan/plancontroller/$1";
$route['plan/version/(:any)'] 								= "plan/planversioncontroller/$1";
$route['plan/category/(:any)'] 								= "plan/plancategorycontroller/$1";
$route['plan/node/(:any)'] 								= "plan/plannodecontroller/$1";
$route['plan/section/(:any)'] 								= "plan/plansectioncontroller/$1";

$route['doc/document/(:any)'] 								= "doc/docdocumentcontroller/$1";
$route['doc/doccategory/(:any)'] 							= "doc/doccategorycontroller/$1";
$route['doc/docextension/(:any)'] 							= "doc/docextensioncontroller/$1";
$route['doc/docversion/(:any)'] 							= "doc/docversioncontroller/$1";
$route['report/report/(:any)']                     					= "report/reportcontroller/$1";
											
$route['mtn/posstatus/(:any)']                  					= "mtn/possiblestatuscontroller/$1";
$route['mtn/wo/(:any)']                         					= "mtn/wocontroller/$1";
$route['mtn/wotype/(:any)']                     					= "mtn/wotypecontroller/$1";
$route['mtn/othercosts/(:any)']                 					= "mtn/othercostscontroller/$1";
$route['mtn/task/(:any)']                       					= "mtn/taskcontroller/$1";
$route['mtn/component/(:any)']                  					= "mtn/componentcontroller/$1";
$route['mtn/componenttype/(:any)']             						= "mtn/componenttypecontroller/$1";
$route['mtn/wotask/(:any)']                     					= "mtn/wotaskcontroller/$1";
$route['mtn/woothercosts/(:any)']               					= "mtn/woothercostscontroller/$1";
$route['mtn/pricelistcomponent/(:any)']         					= "mtn/pricelistcomponentcontroller/$1";
$route['mtn/pricelist/(:any)']         							= "mtn/pricelistcontroller/$1";
$route['mtn/wotaskcomponent/(:any)']            					= "mtn/wotaskcomponentcontroller/$1";
$route['mtn/typecomponent/(:any)']              					= "mtn/typecomponentcontroller/$1";
$route['mtn/woflow/(:any)']                     					= "mtn/woflowcontroller/$1";
$route['mtn/plan/(:any)']                     						= "mtn/plancontroller/$1";
$route['mtn/plantask/(:any)']                     					= "mtn/plantaskcontroller/$1";  
$route['mtn/configstate/(:any)']                     					= "mtn/configstatecontroller/$1";

$route['request/problem/(:any)']                     					= "request/requestproblemcontroller/$1";
$route['request/request/(:any)']                     					= "request/requestcontroller/$1";
$route['request/status/(:any)']                     					= "request/requeststatuscontroller/$1";

$route['iot/iot/(:any)']                     					        = "iot/iotcontroller/$1";

//Desarrollo de la Solicitud Universidad de Chile
$route['request/solicitud/(:any)']                     					= "request/solicitudcontroller/$1";
$route['request/estado/(:any)']                     					= "request/solicitudestadocontroller/$1";
$route['request/tipo/(:any)']                     					= "request/solicitudtipocontroller/$1";
$route['request/log/(:any)']                     					= "request/solicitudlogcontroller/$1";

$route['request/service/(:any)']                     					= "request/servicecontroller/$1";
$route['request/servicestatus/(:any)']                     				= "request/servicestatuscontroller/$1";
$route['request/servicetype/(:any)']                     				= "request/servicetypecontroller/$1";
$route['request/servicelog/(:any)']                     				= "request/servicelogcontroller/$1";

$route['request/rdi/(:any)']                     					= "request/rdicontroller/$1";
$route['request/rdistatus/(:any)']                                                      = "request/rdistatuscontroller/$1";
$route['request/rdilog/(:any)']                     					= "request/rdilogcontroller/$1";

$route['network/fo/(:any)']                     					= "network/focontroller/$1";

/** administrator **/
$route['administrator']                    						= "gui/administrator";

$route['default_controller'] 								= "gui/principal";
$route['core/auth'] 									= "core/authcontroller";
$route['core/auth/(:any)'] 								= "core/authcontroller/$1";

$route['inframtn/wo/(:any)']                     					= "inframtn/wocontroller/$1";
$route['inframtn/calendar/(:any)']                     					= "inframtn/calendar/$1";
$route['inframtn/wotype/(:any)']                     					= "inframtn/wotypecontroller/$1";
$route['inframtn/task/(:any)']                     					= "inframtn/taskcontroller/$1";
$route['inframtn/wonodestatus/(:any)']                     				= "inframtn/wonodestatuscontroller/$1";
$route['inframtn/wotask/(:any)']                     				        = "inframtn/wotaskcontroller/$1";
$route['inframtn/nodebudget/(:any)']                     				= "inframtn/nodebudgetcontroller/$1";
$route['inframtn/nodebudgetTask/(:any)']                     				= "inframtn/nodebudgetTaskcontroller/$1";
$route['inframtn/nodepricelist/(:any)']                     				= "inframtn/nodepricelistcontroller/$1";
$route['inframtn/nodepricelistTask/(:any)']                     			= "inframtn/nodepricelistTaskcontroller/$1";
$route['inframtn/applicant/(:any)']                     			= "inframtn/applicantcontroller/$1";
$route['inframtn/responsible/(:any)']                     			= "inframtn/responsiblecontroller/$1";
$route['inframtn/nodeplan/(:any)']                                              = "inframtn/nodeplancontroller/$1";
$route['inframtn/nodeplantask/(:any)']                                              = "inframtn/nodeplantaskcontroller/$1";
$route['inframtn/wodocument/(:any)']                                              = "inframtn/wodocumentcontroller/$1";

$route['qr/(:any)']                                                                     = "qr/qrcontroller/$1";


$route['scaffolding_trigger']								= "";


/* End of file routes.php */
/* Location: ./system/application/config/routes.php */