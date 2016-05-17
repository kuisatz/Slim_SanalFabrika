<?php
// test commit for branch slim2
require 'vendor/autoload.php';


use \Services\Filter\Helper\FilterFactoryNames as stripChainers;

/*$app = new \Slim\Slim(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    ));*/

$app = new \Slim\SlimExtended(array(
    'mode' => 'development',
    'debug' => true,
    'log.enabled' => true,
    'log.level' => \Slim\Log::INFO,
    'exceptions.rabbitMQ' => true,
    'exceptions.rabbitMQ.logging' => \Slim\SlimExtended::LOG_RABBITMQ_FILE,
    'exceptions.rabbitMQ.queue.name' => \Slim\SlimExtended::EXCEPTIONS_RABBITMQ_QUEUE_NAME
    ));

/**
 * "Cross-origion resource sharing" kontrolüne izin verilmesi için eklenmiştir
 * @author Mustafa Zeynel Dağlı
 * @since 2.10.2015
 */
$res = $app->response();
$res->header('Access-Control-Allow-Origin', '*');
$res->header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS");

$app->add(new \Slim\Middleware\MiddlewareInsertUpdateDeleteLog());
$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());



    
 

/**
 * Okan CIRAN
 * @since 17-05-2016
 */
$app->get("/pkGetAll_infoFirmAddress/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoFirmAddressBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkGetAll_infoFirmAddress" end point, X-Public variable not found');
  //  $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, 
                                                                $app, 
                                                                $_GET['language_code']));
    }
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    } 
    $resDataMenu = $BLL->getAll(array(
        'language_code' => $vLanguageCode,        
            ));
    $menus = array();
    if (isset($resDataGrid['resultSet'][0]['id'])) {
        foreach ($resDataMenu as $menu) {
            $menus[] = array(
                "id" => $menu["id"],
                "act_parent_id" => intval($flow["act_parent_id"]),
                "firm_id" => $menu["firm_id"],
                "firm_name" => $menu["firm_name"],
                "firm_name_eng" => $menu["firm_name_eng"],
                "firm_building_type_id" => $menu["firm_building_type_id"],
                "firm_building_type" => $menu["firm_building_type"],
                "firm_building_name" => $menu["firm_building_name"], 
                "firm_building_name_eng" => $menu["firm_building_name_eng"],
                "address" => $menu["address"],
                "osb_id" => $menu["osb_id"],
                "osb_name" => $menu["osb_name"],
                "cons_allow_id" => $menu["cons_allow_id"],
                "cons_allow" => $menu["cons_allow"],
                "country_id" => $menu["country_id"],
                "country_name" => $menu["country_name"],
                "city_id" => $menu["city_id"],
                "city_name" => $menu["city_name"],
                "borough_id" => $menu["borough_id"],
                "borough_name" => $menu["borough_name"], 
                "deleted" => $menu["deleted"],
                "state_deleted" => $menu["state_deleted"],
                "active" => $menu["active"],
                "state_active" => $menu["state_active"],
                "language_id" => $menu["language_id"],
                "language_name" => $menu["language_name"],
                "op_user_id" => $menu["op_user_id"],
                "op_username" => $menu["op_user_name"],
                "operation_type_id" => $menu["operation_type_id"],
                "operation_name" => $menu["operation_name"],
                "s_date" => $menu["s_date"],
                "c_date" => $menu["c_date"],
            );
        }
    }
    $app->response()->header("Content-Type", "application/json");
    $app->response()->body(json_encode($menus));
});
  
/**x
 *  * Okan CIRAN
 * @since 17-05-2016
 */
$app->get("/pkDeletedAct_infoFirmAddress/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('infoFirmAddressBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkDeletedAct_infoFirmAddress" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];
 
    $vId = NULL;
    if (isset($_GET['id'])) {
        $stripper->offsetSet('id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    } 

    $stripper->strip(); 
    if ($stripper->offsetExists('id')) {$vId = $stripper->offsetGet('id')->getFilterValue(); }     
    
    $resDataDeleted = $BLL->DeletedAct(array(                  
            'id' => $vId ,    
            'pk' => $pk,        
            ));
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataDeleted));
}
); 

  
/**x
 *  * Okan CIRAN
 * @since 17-05-2016
 */
$app->get("/pkUpdate_infoFirmAddress/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmAddressBLL');   
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkUpdate_infoFirmAddress" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];    
    $vLanguageCode = 'tr'; 
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }       
    $vId = NULL;
    if (isset($_GET['id'])) {
         $stripper->offsetSet('id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['id']));
    }   
    $vActive = 0;
    if (isset($_GET['active'])) {
         $stripper->offsetSet('active',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['active']));
    }   
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
         $stripper->offsetSet('profile_public',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    }   
    $vFirmBuildingTypeId= NULL;
    if (isset($_GET['firm_building_type_id'])) {
         $stripper->offsetSet('firm_building_type_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['firm_building_type_id']));
    } 
    $vFirmBuildingName = NULL;
    if (isset($_GET['firm_building_name'])) {
         $stripper->offsetSet('firm_building_name',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['firm_building_name']));
    }
    $vAddress = NULL;
    if (isset($_GET['address'])) {
         $stripper->offsetSet('address',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['address']));
    }
    $vBoroughId = NULL;
    if (isset($_GET['borough_id'])) {
         $stripper->offsetSet('borough_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['borough_id']));
    }
    $vCityId = NULL;
    if (isset($_GET['city_id'])) {
         $stripper->offsetSet('city_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['city_id']));
    }
    $vCountryId = NULL;
    if (isset($_GET['country_id'])) {
         $stripper->offsetSet('country_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['country_id']));
    }
    
    $vOsbId = NULL;
    if (isset($_GET['osb_id'])) {
         $stripper->offsetSet('osb_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['osb_id']));
    } 
    $vDescription = NULL;
    if (isset($_GET['description'])) {
         $stripper->offsetSet('description',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description']));
    }
    $vDescriptionEng = NULL;
    if (isset($_GET['description_eng'])) {
         $stripper->offsetSet('description_eng',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['description_eng']));
    } 
    
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    } 
    if ($stripper->offsetExists('id')) {
        $vId = $stripper->offsetGet('id')->getFilterValue();
    } 
    if ($stripper->offsetExists('active')) {
        $vActive = $stripper->offsetGet('active')->getFilterValue();
    } 
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    } 
    if ($stripper->offsetExists('firm_building_type_id')) {
        $vFirmBuildingTypeId = $stripper->offsetGet('firm_building_type_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('firm_building_name')) {
        $vFirmBuildingName = $stripper->offsetGet('firm_building_name')->getFilterValue();
    }
    if ($stripper->offsetExists('address')) {
        $vAddress = $stripper->offsetGet('address')->getFilterValue();
    }
    if ($stripper->offsetExists('borough_id')) {
        $vBoroughId = $stripper->offsetGet('borough_id')->getFilterValue();
    }
    if ($stripper->offsetExists('city_id')) {
        $vCityId = $stripper->offsetGet('city_id')->getFilterValue();
    }
    if ($stripper->offsetExists('country_id')) {
        $vCountryId = $stripper->offsetGet('country_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('osb_id')) {
        $vOsbId = $stripper->offsetGet('osb_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('description')) {
        $vDescription = $stripper->offsetGet('description')->getFilterValue();
    }   
    if ($stripper->offsetExists('description_eng')) {
        $vDescriptionEng = $stripper->offsetGet('description_eng')->getFilterValue();
    } 

    $resData = $BLL->update(array(  
            'id' => $vId ,    
            'active' => $vActive ,  
            'language_code' => $vLanguageCode,    
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
            'profile_public' => $vProfilePublic ,  
        
        
        
        
            'osb_id' => $vOsbId, 
            'firm_link' => $vFirmLink,                                
            'pk' => $pk,        
            )); 
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
 

/**x
 *  * Okan CIRAN
 * @since 17-05-2016
 */
$app->get("/pkInsert_infoFirmAddress/", function () use ($app ) {    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();   
    $BLL = $app->getBLLManager()->get('infoFirmAddressBLL');   
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkInsert_infoFirmAddress" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];      
    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
         $stripper->offsetSet('language_code',$stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE,
                                                $app,
                                                $_GET['language_code']));
    }  
    $vNetworkKey = NULL;
    if (isset($_GET['npk'])) {
         $stripper->offsetSet('npk',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['npk']));
    }
    $vProfilePublic = 0;
    if (isset($_GET['profile_public'])) {
         $stripper->offsetSet('profile_public',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['profile_public']));
    }       
    $vSysSocialmediaId = NULL;
    if (isset($_GET['sys_socialmedia_id'])) {
         $stripper->offsetSet('sys_socialmedia_id',$stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['sys_socialmedia_id']));
    } 
    $vFirmLink = NULL;
    if (isset($_GET['firm_link'])) {
         $stripper->offsetSet('firm_link',$stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['firm_link']));
    }
    
    $stripper->strip(); 
    if ($stripper->offsetExists('language_code')) {
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();
    }     
    if ($stripper->offsetExists('npk')) {
        $vNetworkKey = $stripper->offsetGet('npk')->getFilterValue();
    } 
    if ($stripper->offsetExists('profile_public')) {
        $vProfilePublic = $stripper->offsetGet('profile_public')->getFilterValue();
    } 
    if ($stripper->offsetExists('sys_socialmedia_id')) {
        $vSysSocialmediaId = $stripper->offsetGet('sys_socialmedia_id')->getFilterValue();
    } 
    if ($stripper->offsetExists('firm_link')) {
        $vFirmLink = $stripper->offsetGet('firm_link')->getFilterValue();
    } 
      
    $resData = $BLL->insert(array(              
            'language_code' => $vLanguageCode,    
            'network_key' => $vNetworkKey,
            'profile_public' => $vProfilePublic ,                         
            'sys_socialmedia_id' => $vSysSocialmediaId, 
            'firm_link' => $vFirmLink,                                
            'pk' => $pk,        
            ));
 
    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resData));
}
); 
  
/**
 *  * Okan CIRAN
 * @since 17-05-2016
 */
$app->get("/pkFillSingularFirmSocialMedia_infoFirmAddress/", function () use ($app ) {
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();
    $BLL = $app->getBLLManager()->get('infoFirmAddressBLL');
    $headerParams = $app->request()->headers();
    if (!isset($headerParams['X-Public']))
        throw new Exception('rest api "pkFillSingularFirmSocialMedia_infoFirmAddress" end point, X-Public variable not found');
    $pk = $headerParams['X-Public'];

    $vLanguageCode = 'tr';
    if (isset($_GET['language_code'])) {
        $stripper->offsetSet('language_code', $stripChainerFactory->get(stripChainers::FILTER_ONLY_LANGUAGE_CODE, $app, $_GET['language_code']));
    }
    $vNpk = NULL;
    if (isset($_GET['npk'])) {
        $stripper->offsetSet('npk', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2, 
                                                        $app, 
                                                        $_GET['npk']));
    }
    $stripper->strip();
    if ($stripper->offsetExists('language_code'))
        $vLanguageCode = $stripper->offsetGet('language_code')->getFilterValue();    
    if ($stripper->offsetExists('npk'))
        $vNpk = $stripper->offsetGet('npk')->getFilterValue();

    $resDataGrid = $BLL->fillSingularFirmSocialMedia(array(
        'language_code' => $vLanguageCode,
        'network_key' => $vNpk,
        'pk' => $pk,
    ));
   
    $resTotalRowCount = $BLL->fillSingularFirmSocialMediaRtc(array(
        'network_key' => $vNpk,
        'pk' => $pk,
    ));
    $counts=0;
    $flows = array();            
    if (isset($resDataGrid[0]['id'])) {      
        foreach ($resDataGrid as $flow) {
            $flows[] = array(
                "id" => $flow["id"],
                "firm_id" => $flow["firm_id"],
                "firm_name" => $flow["firm_name"],
                "firm_name_eng" => $flow["firm_name_eng"],
                "socialmedia_name" => $flow["socialmedia_name"],
                "socialmedia_eng" => $flow["socialmedia_eng"],
                "firm_link" => $flow["firm_link"],     
                "network_key" => $flow["network_key"],
                "logo" => $flow["logo"],         
                "language_id" => $flow["language_id"],
                "language_name" => $flow["language_name"],
                "attributes" => array("notroot" => true,"active" => $flow["active"],  ),
            );
        }
       $counts = $resTotalRowCount[0]['count'];
     }    

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $counts;
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});





$app->run();