<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/Slim_SanalFabrika for the canonical source repository
 * @copyright Copyright (c) 2016 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */
namespace Utill\MQ\Factory;


/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be rest service delete calls log
 * @author Mustafa Zeynel Dağlı
 * @since 22/03/2016
 */
class FactoryServiceRestDeleteLog implements \Zend\ServiceManager\FactoryInterface {
    
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $serviceLogMQ = new \Utill\MQ\restEntryMQ();
        $slimApp = $serviceLocator->get('slimApp');
        $request = $slimApp->container['request'];
        $params = $request->params();
        
        $requestHeaderData = $request->headers();
        
        $base = $request->getRootUri();
        $path = $request->getResourceUri();
        $ip = $request->getIp();
        $method = $request->getMethod();
        
        $serviceLogMQ->setChannelProperties(array('queue.name' => \Utill\MQ\abstractMQ::SERVICE_ENTRY_LOG_QUEUE_NAME));
        $message = new \Utill\MQ\MessageMQ\MQMessageServiceLog();
        ;
          
       
        $message->setMessageBody(array('message' => 'Rest servis log!', 
                                      //'s_date'  => date('l jS \of F Y h:i:s A'),
                                      'log_datetime'  => date('Y-m-d G:i:s '),
                                      'pk' => $requestHeaderData['X-Public'],
                                      'pk_temp' => $requestHeaderData['X-Public-Temp'],
                                      'url' => $base,
                                      'path' => $path,
                                      'method' => $method,
                                      'ip' => \Utill\Env\serverVariables::getClientIp(),
                                      'params' => serialize($params),
                                      'type_id' => \Utill\MQ\MessageMQ\MQMessageServiceLog::SERVICE_DELETE_OPERATION,
                                      'logFormat' => 'database'));
        $message->setMessageProperties(array('delivery_mode' => 2,
                                            'content_type' => 'application/json'));
        $serviceLogMQ->setMessage($message->setMessage());
        $serviceLogMQ->basicPublish();
        return $serviceLogMQ;
    }

}
