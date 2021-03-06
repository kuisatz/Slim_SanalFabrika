<?php
/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace BLL\BLL;

/**
 * Business Layer class for report Configuration entity
 */
class InfoFirmReferences extends \BLL\BLLSlim{
    
    /**
     * constructor
     */
    public function __construct() {
        //parent::__construct();
    }
    
    /**
     * DAta insert function
     * @param array | null $params
     * @return array
     */
    public function insert($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->insert($params);
    }
    
     
    
    /**
     * Check Data function
     * @param array | null $params
     * @return array
     */
    public function haveRecords($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->haveRecords($params);
    }
    
    
    /**
     * Data update function
     * @param array | null $params
     * @return array
     */
    public function update($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->update($params);
    }
    
    /**
     * Data delete function
     * @param array | null $params
     * @return array
     */
    public function delete( $params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->delete($params);
    }

    /**
     * get all data
     * @param array | null $params
     * @return array
     */
    public function getAll($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->getAll($params);
    }
    
    /**
     * Function to fill datagrid on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGrid ($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        $resultSet = $DAL->fillGrid($params);  
        return $resultSet['resultSet'];
    }
    
    /**
     * Function to get datagrid row count on user interface layer
     * @param array | null $params
     * @return array
     */
    public function fillGridRowTotalCount($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        $resultSet = $DAL->fillGridRowTotalCount($params);  
        return $resultSet['resultSet'];
    }
    
     /**
     * Data delete action function
     * @param array | null $params
     * @return array
     */
    public function deletedAct($params = array()) {
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->deletedAct($params);
    }
    
    /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillGridSingular($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillGridSingular($params);
    }
    
    /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillGridSingularRowTotalCount($params = array()) {     
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');
        return $DAL->fillGridSingularRowTotalCount($params);
    }
    
     /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillWithReference($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillWithReference($params);
    }
    
     /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillWithReferenceRtc($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillWithReferenceRtc($params);
    } 
    /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillBeReferenced($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillBeReferenced($params);
    }
        /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillBeReferencedRtc($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillBeReferencedRtc($params);
    } 
   
    /**
     * Data update function   
     * @param array $params
     * @return array
     */
    public function fillWithReferenceNpk($params = array()) {        
        $DAL = $this->slimApp->getDALManager()->get('infoFirmReferencesPDO');     
        return $DAL->fillWithReferenceNpk($params);
    }
    
}

