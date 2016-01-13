<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace Services\Filter;


/**
 * service manager layer for filter functions
 * @author Mustafa Zeynel Dağlı
 * @version 13/01/
 */
class FilterChainerCustom implements \Zend\ServiceManager\FactoryInterface {
    
    /**
     * service ceration via factory on zend service manager
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return boolean|\PDO
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        // Create a filter chain and filter for usage
        $filterChain = new \Zend\Filter\FilterChain();
        $filterChain->attach(new \Zend\Filter\StripTags())
                    ->attach(new \Zend\Filter\StringTrim())
                    ->attach(new \Zend\Filter\HtmlEntities())
                    ->attach(new \Zend\Filter\StripNewlines())
                     ;
        return $filterChain;

    }

}
