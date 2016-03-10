<?php

// test commit for branch slim2
require 'vendor/autoload.php';

use \Services\Filter\Helper\FilterFactoryNames as stripChainers;


/* $app = new \Slim\Slim(array(
  'mode' => 'development',
  'debug' => true,
  'log.enabled' => true,
  )); */

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

$app->add(new \Slim\Middleware\MiddlewareHMAC());
$app->add(new \Slim\Middleware\MiddlewareSecurity());
$app->add(new \Slim\Middleware\MiddlewareBLLManager());
$app->add(new \Slim\Middleware\MiddlewareDalManager());
$app->add(new \Slim\Middleware\MiddlewareServiceManager());
$app->add(new \Slim\Middleware\MiddlewareMQManager());
 


/**
 *  * Okan CIRAN
 * @since 10-03-2016
 */
$app->get("/pkFillGrid_logConnection/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('logConnectionBLL');

    $headerParams = $app->request()->headers();
    $vPk = $headerParams['X-Public'];

    $resDataGrid = $BLL->fillGrid(array('page' => $_GET['page'],
        'rows' => $_GET['rows'],
        'sort' => $_GET['sort'],
        'order' => $_GET['order'], 
         ));

    $resTotalRowCount = $BLL->fillGridRowTotalCount( );

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],            
            "s_date" => $flow["s_date"],
            "pk" => $flow["pk"],
            "type_id" => $flow["type_id"],
            "type_state" => $flow["type_state"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "log_datetime" => $flow["log_datetime"],
            "attributes" => array("notroot" => true,  
                ),
        );
    }

    $app->response()->header("Content-Type", "application/json");
    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;
    $app->response()->body(json_encode($resultArray));
});

/**
 *  * Okan CIRAN
 * @since 10-03-2016
 */
$app->get("/pkInsert_logConnection/", function () use ($app ) {
    
    $stripper = $app->getServiceManager()->get('filterChainerCustom');
    $stripChainerFactory = new \Services\Filter\Helper\FilterChainerFactory();    
    $BLL = $app->getBLLManager()->get('logConnectionBLL');
    $headerParams = $app->request()->headers();
    $Pk = $headerParams['X-Public'];
    
    $vTypeId = 0;
    if (isset($_GET['type_id'])) {
        $stripper->offsetSet('type_id', $stripChainerFactory->get(stripChainers::FILTER_ONLY_NUMBER_ALLOWED,
                                                $app,
                                                $_GET['type_id']));
    }  
    $vLogDatetime = '2016-02-22 04:00:00';
    if (isset($_GET['log_datetime'])) {
        $stripper->offsetSet('log_datetime', $stripChainerFactory->get(stripChainers::FILTER_PARANOID_LEVEL2,
                                                $app,
                                                $_GET['log_datetime']));
    }  
    
    
    $stripper->strip();
    if($stripper->offsetExists('type_id')) $vTypeId = $stripper->offsetGet('type_id')->getFilterValue();
    if($stripper->offsetExists('log_datetime')) $vLogDatetime = $stripper->offsetGet('log_datetime')->getFilterValue();
    
    $resDataInsert = $BLL->insert(array(        
        'type_id' => $vTypeId,
        'log_datetime' => $vLogDatetime,
        'pk' => $Pk));

    $app->response()->header("Content-Type", "application/json"); 
    $app->response()->body(json_encode($resDataInsert));
}
);
 
  
/**
 *  * Okan CIRAN
 * @since 10-03-2016
 */
$app->get("/pkGetAll_logConnection/", function () use ($app ) {

    $BLL = $app->getBLLManager()->get('logConnectionBLL');
    $resDataGrid = $BLL->getAll();
    $resTotalRowCount = $BLL->fillGridRowTotalCount( );

    $flows = array();
    foreach ($resDataGrid as $flow) {
        $flows[] = array(
            "id" => $flow["id"],            
            "s_date" => $flow["s_date"],
            "pk" => $flow["pk"],
            "type_id" => $flow["type_id"],
            "type_state" => $flow["type_state"],
            "user_id" => $flow["user_id"],
            "username" => $flow["username"],
            "log_datetime" => $flow["log_datetime"],
            "attributes" => array("notroot" => true,  ),
        );
    }

    $app->response()->header("Content-Type", "application/json");

    $resultArray = array();
    $resultArray['total'] = $resTotalRowCount[0]['count'];
    $resultArray['rows'] = $flows;

    /* $app->contentType('application/json');
      $app->halt(302, '{"error":"Something went wrong"}');
      $app->stop(); */

    $app->response()->body(json_encode($resultArray));
});

$app->run();
