<?php

/**
 * OSTİM TEKNOLOJİ Framework 
 *
 * @link      https://github.com/corner82/slim_test for the canonical source repository
 * @copyright Copyright (c) 2015 OSTİM TEKNOLOJİ (http://www.ostim.com.tr)
 * @license   
 */

namespace DAL\PDO;

/**
 * Class using Zend\ServiceManager\FactoryInterface
 * created to be used by DAL MAnager
 * @
 * @author Okan CIRAN
 */
class SysOsbConsultants extends \DAL\DalSlim {

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosundan parametre olarak  gelen id kaydını siler. !!
     * @version v 1.0  08.02.2016
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function delete($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $userId = $this->getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($userId)) {
                $userIdValue = $userId ['resultSet'][0]['user_id'];
                $statement = $pdo->prepare(" 
                UPDATE sys_osb_consultants
                SET  deleted= 1 , active = 1 ,
                     op_user_id = " . $userIdValue . "     
                WHERE id = :id");
                //Execute our DELETE statement.
                $update = $statement->execute();
                $afterRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();

                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $afterRows);
            } else {
                $errorInfo = '23502';  /// 23502  not_null_violation
                $pdo->commit();
                //  $result = $kontrol;
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosundaki tüm kayıtları getirir.  !!
     * @version v 1.0  08.02.2016  
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function getAll($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $statement = $pdo->prepare("
                SELECT 
                        a.id, 
                        u.name AS name,
                        u.surname AS name,
                        a.osb_id,
                        osb.name as osb_name,
                        a.country_id,
                        co.name as country, 		                   
                        a.deleted, 
                        sd.description as state_deleted,                 
                        a.active, 
                        sd1.description as state_active, 
                        a.op_user_id,
                        u1.username AS op_user_name     
                FROM sys_osb_consultants  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
                INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0 
                INNER JOIN info_users u1 ON u1.id = a.op_user_id 
                LEFT JOIN sys_osb osb ON osb.id = a.osb_id 
                LEFT JOIN sys_countrys co on co.id = a.country_id AND co.active =0 AND co.deleted =0                
                ORDER BY u.name                 
                                 ");
            $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {

            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosuna yeni bir kayıt oluşturur.  !!
     * @version v 1.0  08.02.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function insert($params = array()) {

        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $kontrol = $this->haveRecords($params);
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {

                $userId = $this->getUserId(array('pk' => $params['pk']));
                if (!\Utill\Dal\Helper::haveRecord($userId)) {
                    $opUserIdValue = $userId ['resultSet'][0]['user_id'];
                }

                $languageIdValue = 647;
                $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                    $languageIdValue = $languageId ['resultSet'][0]['id'];
                }

                $sql = "
                INSERT INTO sys_osb_consultants(
                        osb_id, country_id, active, user_id, language_id, 
                        language_code, op_user_id )
                VALUES (
                        :osb_id, 
                        :country_id, 
                        :active, 
                        :user_id, 
                        :language_id, 
                        :language_code, 
                        :op_user_id 
                                             )   ";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':osb_id', $params['osb_id'], \PDO::PARAM_INT);
                $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
                $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                $statement->bindValue(':language_id', $languageIdValue, \PDO::PARAM_INT);
                $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_STR);
                $statement->bindValue(':op_user_id', $opUserIdValue, \PDO::PARAM_INT);
                // echo debugPDO($sql, $params);
                $result = $statement->execute();
                $insertID = $pdo->lastInsertId('sys_osb_consultants_id_seq');
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "lastInsertId" => $insertID);
            } else {
                $errorInfo = '23505';
                $pdo->commit();
                $result = $kontrol;
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
                //return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosunda user_id li consultant daha önce kaydedilmiş mi ?  
     * @version v 1.0 15.01.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function haveRecords($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $addSql = "";
            if (isset($params['id'])) {
                $addSql = " AND a.id != " . intval($params['id']) . " ";
            }
            $sql = " 

            SELECT  
                CONCAT(u.name,' ',u.surname) AS name , 
                '" . $params['user_id'] . "' AS value , 
                a.user_id =" . intval($params['user_id']) . " AS control,
                CONCAT(u.name,' ',u.surname, ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message
            FROM sys_osb_consultants  a              
            INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0                 
            WHERE a.user_id = " . intval($params['user_id']) . "
                   " . $addSql . " 
               AND a.deleted =0    
                               ";
            $statement = $pdo->prepare($sql);
            //   echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);

            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * sys_osb_consultants tablosuna parametre olarak gelen id deki kaydın bilgilerini günceller   !!
     * @version v 1.0  08.02.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function update($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $pdo->beginTransaction();
            $kontrol = $this->haveRecords($params);
            if (!\Utill\Dal\Helper::haveRecord($kontrol)) {

                $userId = $this->getUserId(array('pk' => $params['pk'], 'id' => $params['id']));
                if (!\Utill\Dal\Helper::haveRecord($userId)) {
                    $opUserIdValue = $userId ['resultSet'][0]['user_id'];
                }

                $languageIdValue = 647;
                $languageId = SysLanguage::getLanguageId(array('language_code' => $params['language_code']));
                if (!\Utill\Dal\Helper::haveRecord($languageId)) {
                    $languageIdValue = $languageId ['resultSet'][0]['id'];
                }

                $sql = "
                UPDATE sys_osb_consultants
                SET   
                    osb_id= :osb_id, 
                    country_id= :country_id, 
                    active= :active, 
                    user_id = :user_id, 
                    language_id= :language_id, 
                    language_code= :language_code, 
                    op_user_id= :op_user_id, 
                WHERE id = " . intval($params['id']);
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':osb_id', $params['osb_id'], \PDO::PARAM_INT);
                $statement->bindValue(':country_id', $params['country_id'], \PDO::PARAM_INT);
                $statement->bindValue(':active', $params['active'], \PDO::PARAM_INT);
                $statement->bindValue(':user_id', $params['user_id'], \PDO::PARAM_INT);
                $statement->bindValue(':language_id', $languageIdValue, \PDO::PARAM_INT);
                $statement->bindValue(':language_code', $params['language_code'], \PDO::PARAM_INT);
                $statement->bindValue(':op_user_id', $opUserIdValue, \PDO::PARAM_INT);
                $update = $statement->execute();
                $affectedRows = $statement->rowCount();
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "affectedRowsCount" => $affectedRows);
            } else {
                // 23505 	unique_violation
                $errorInfo = '23505'; // $kontrol ['resultSet'][0]['message'];  
                $pdo->commit();
                $result = $kontrol;
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '');
            }
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_osb_consultants tablosundan kayıtları döndürür !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGrid($args = array()) {
        if (isset($args['page']) && $args['page'] != "" && isset($args['rows']) && $args['rows'] != "") {
            $offset = ((intval($args['page']) - 1) * intval($args['rows']));
            $limit = intval($args['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }

        $sortArr = array();
        $orderArr = array();
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else {
            $sort = "u.name";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            //$order = "desc";
            $order = "ASC";
        }

        $whereNameSQL = '';
        if (isset($args['search_name']) && $args['search_name'] != "") {
            $whereNameSQL = " AND LOWER(a.name) LIKE LOWER('%" . $args['search_name'] . "%') ";
        }

        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                  SELECT 
                        a.id, 
                        u.name AS name,
                        u.surname AS name,
                        a.osb_id,
                        osb.name as osb_name,
                        a.country_id,
                        co.name as country, 		                   
                        a.deleted, 
                        sd.description as state_deleted,                 
                        a.active, 
                        sd1.description as state_active, 
                        a.op_user_id,
                        u1.username AS op_user_name     
                FROM sys_osb_consultants  a
                INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
                INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
                INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0 
                INNER JOIN info_users u1 ON u1.id = a.op_user_id 
                LEFT JOIN sys_osb osb ON osb.id = a.osb_id 
                LEFT JOIN sys_countrys co on co.id = a.country_id AND co.active =0 AND co.deleted =0                                
                WHERE a.deleted =0  
                " . $whereNameSQL . "
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            $parameters = array(
                'sort' => $sort,
                'order' => $order,
                'limit' => $pdo->quote($limit),
                'offset' => $pdo->quote($offset),
            );
            //   echo debugPDO($sql, $parameters);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();

            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * user interface datagrid fill operation get row count for widget
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_osb_consultants tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillGridRowTotalCount($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $whereSQL = '';
            $whereSQL1 = ' WHERE ax.deleted =0 ';
            $whereSQL2 = ' WHERE ay.deleted =1 ';
            if (isset($params['search_name']) && $params['search_name'] != "") {
                $whereSQL = " WHERE a.name LIKE '%" . $params['search_name'] . "%' ";
                $whereSQL1 .= " AND ax.name LIKE '%" . $params['search_name'] . "%' ";
                $whereSQL2 .= " AND ay.name LIKE '%" . $params['search_name'] . "%' ";
            }
            $sql = "
               SELECT 
                    COUNT(a.id) AS COUNT ,
                    (SELECT COUNT(ax.id) FROM sys_osb_consultants ax  			
			INNER JOIN sys_specific_definitions sdx ON sdx.main_group = 15 AND sdx.first_group= ax.deleted AND sdx.language_code = 'tr' AND sdx.deleted = 0 AND sdx.active = 0
			INNER JOIN sys_specific_definitions sd1x ON sd1x.main_group = 16 AND sd1x.first_group= ax.active AND sd1x.language_code = 'tr' AND sd1x.deleted = 0 AND sd1x.active = 0                             
			INNER JOIN info_users_detail ux ON ux.root_id = ax.user_id AND ux.active = 0 AND ux.deleted = 0 
			INNER JOIN info_users u1x ON u1x.id = ax.op_user_id 
                     " . $whereSQL1 . " ) AS undeleted_count, 
                    (SELECT COUNT(ay.id) FROM sys_osb_consultants ay
			INNER JOIN sys_specific_definitions sdy ON sdy.main_group = 15 AND sdy.first_group= ay.deleted AND sdy.language_code = 'tr' AND sdy.deleted = 0 AND sdy.active = 0
			INNER JOIN sys_specific_definitions sd1y ON sd1y.main_group = 16 AND sd1y.first_group= ay.active AND sd1y.language_code = 'tr' AND sd1y.deleted = 0 AND sd1y.active = 0                             
			INNER JOIN info_users_detail uy ON uy.root_id = ay.user_id AND uy.active = 0 AND uy.deleted = 0 
			INNER JOIN info_users u1y ON u1y.id = ay.op_user_id 			
                      " . $whereSQL2 . ") AS deleted_count                        
                FROM sys_osb_consultants  a
		INNER JOIN sys_specific_definitions sd ON sd.main_group = 15 AND sd.first_group= a.deleted AND sd.language_code = 'tr' AND sd.deleted = 0 AND sd.active = 0
		INNER JOIN sys_specific_definitions sd1 ON sd1.main_group = 16 AND sd1.first_group= a.active AND sd1.language_code = 'tr' AND sd1.deleted = 0 AND sd1.active = 0                             
		INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0 
		INNER JOIN info_users u1 ON u1.id = a.op_user_id 
                " . $whereSQL . "
                    ";
            $statement = $pdo->prepare($sql);
            echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosundan osb_id si olan kayıtları döndürür !!
     * @version v 1.0  08.02.2016
     * @return array
     * @throws \PDOException
     */
    public function fillOsbConsultantList() {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $osbId = 5; // ostim
            if (isset($params['osb_id']) && $params['osb_id'] != "") {
                $osbId = $params['osb_id'];
            }
            $statement = $pdo->prepare("
              SELECT                    
                    a.id, 	
                    CONCAT(u.name,' ', u.surname) AS name,                  
                    a.active ,
                    0 AS state_type                
                FROM sys_osb_consultants  a                
                INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0                 
                INNER JOIN sys_osb osb ON osb.id = a.osb_id                 
                WHERE a.deleted =0 AND a.active = 0 AND osb.id = " . intval($osbId) . " 
                ORDER BY name              
                               ");
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosundan active kayıtları döndürür !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function fillConsultantList($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $id = 0;
            if (isset($params['id']) && $params['id'] != "") {
                $id = $params['id'];
            }
            $statement = $pdo->prepare("               
		SELECT                    
                    a.id, 	
                    CONCAT(u.name,' ', u.surname) AS name,                  
                    a.active ,
                    0 AS state_type                
                FROM sys_osb_consultants  a                
                INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0                                 
                WHERE a.deleted =0 AND a.active = 0  
                ORDER BY name                   
                                 ");
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            $pdo->rollback();
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ sys_osb_consultants tablosunda en az işi olan consultant id sini döndürür.   
     * @version v 1.0 15.01.2016
     * @param type $params
     * @return array
     * @throws \PDOException
     */
    public function consultantAssign($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $addSql = "";
            if (isset($params['id'])) {
                $addSql = " AND a.id != " . intval($params['id']) . " ";
            }
            $sql = " 

            SELECT  
                CONCAT(u.name,' ',u.surname) AS name , 
                '" . $params['user_id'] . "' AS value , 
                a.user_id =" . intval($params['user_id']) . " AS control,
                CONCAT(u.name,' ',u.surname, ' daha önce kayıt edilmiş. Lütfen Kontrol Ediniz !!!' ) AS message
            FROM sys_osb_consultants  a              
            INNER JOIN info_users_detail u ON u.root_id = a.user_id AND u.active = 0 AND u.deleted = 0                 
            WHERE a.user_id = " . intval($params['user_id']) . "
                   " . $addSql . " 
               AND a.deleted =0    
                               ";
            $statement = $pdo->prepare($sql);
            //   echo debugPDO($sql, $params);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();
            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);

            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            return array("found" => false, "errorInfo" => $e->getMessage());
        }
    }

    /**
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_osb_consultants tablosundan kayıtları döndürür !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function consultantCompletedJobs($params = array()) {

        if (isset($args['page']) && $args['page'] != "" && isset($args['rows']) && $args['rows'] != "") {
            $offset = ((intval($args['page']) - 1) * intval($args['rows']));
            $limit = intval($args['rows']);
        } else {
            $limit = 10;
            $offset = 0;
        }

        $sortArr = array();
        $orderArr = array();
        if (isset($args['sort']) && $args['sort'] != "") {
            $sort = trim($args['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($args['sort']);
        } else {
            $sort = "u.name";
        }

        if (isset($args['order']) && $args['order'] != "") {
            $order = trim($args['order']);
            $orderArr = explode(",", $order);
            //print_r($orderArr);
            if (count($orderArr) === 1)
                $order = trim($args['order']);
        } else {
            //$order = "desc";
            $order = "ASC";
        }

        $whereNameSQL = '';
        if (isset($params['search_name']) && $params['search_name'] != "") {
            $whereNameSQL = " AND LOWER(a.name) LIKE LOWER('%" . $params['search_name'] . "%') ";
        }

        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $sql = "
                SELECT a.id, a.s_datetime, a.op_user_id, a.operation_type_id, a.language_id, a.language_code, 
                    a.service_name, a.table_name, a.about_id, a.s_date
                FROM sys_activation_report a
                INNER JOIN sys_operation_types opt ON opt.parent_id = 2 AND a.operation_type_id = opt.id 
                WHERE a.op_user_id IN 
                (SELECT DISTINCT id FROM info_users WHERE role_id = 2)                              
                WHERE a.deleted = 0  
                " . $whereNameSQL . "
                ORDER BY    " . $sort . " "
                    . "" . $order . " "
                    . "LIMIT " . $pdo->quote($limit) . " "
                    . "OFFSET " . $pdo->quote($offset) . " ";
            $statement = $pdo->prepare($sql);
            //   echo debugPDO($sql, $parameters);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            $errorInfo = $statement->errorInfo();

            if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                throw new \PDOException($errorInfo[0]);
            return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_osb_consultants tablosundan kayıtları döndürür !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getConsPendingFirmProfile($params = array()) {
        if (isset($params['page']) && $params['page'] != "" && isset($params['rows']) && $params['rows'] != "") {
            $offset = ((intval($params['page']) - 1) * intval($params['rows']));
            $limit = intval($params['rows']);
    
        } else {
            $limit = 10;
            $offset = 0;
        }
         

        $sortArr = array();
        $orderArr = array();
        if (isset($params['sort']) && $params['sort'] != "") {
            $sort = trim($params['sort']);
            $sortArr = explode(",", $sort);
            if (count($sortArr) === 1)
                $sort = trim($params['sort']);
        } else {
            $sort = "fp.s_date ASC, fp.c_date";
        }

        if (isset($params['order']) && $params['order'] != "") {
            $order = trim($params['order']);
            $orderArr = explode(",", $order);
            if (count($orderArr) === 1)
                $order = trim($params['order']);
        } else {
            $order = "ASC";
        }

        // sql query dynamic for filter operations
        $sorguStr=null;
        if(isset($params['filterRules'])) {
            $filterRules = trim($params['filterRules']);
            //print_r(json_decode($filterRules));
            $jsonFilter = json_decode($filterRules, true);
            //print_r($jsonFilter[0]->field);
            $sorguExpression = null;
            foreach ($jsonFilter as $std) {
                if($std['value']!=null) {
                    switch (trim($std['field'])) {
                    case 'username':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\' ';
                        $sorguStr.=' AND fpu.username'.$sorguExpression.' ';
                        break;
                    case 'company_name':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\'  ';
                        $sorguStr.=' AND fp.firm_name'.$sorguExpression.' ';

                        break;
                    case 's_date':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\'  ';
                        $sorguStr.='AND  to_char(fp.s_date, \'DD/MM/YYYY\')'.$sorguExpression.' ';

                        break;
                    default:
                        break;
                    }
                }  
            }
        } else {
            $sorguStr=null;
            $filterRules = "";
        }
        
      $sorguStr = rtrim($sorguStr,"AND ");
      //if($sorguStr!="") $sorguStr = "WHERE ".$sorguStr;  
        /*
                 fp.id, 
                    fp.s_date, 
                    fp.c_date, 
                    fp.firm_name AS company_name,
                    fpu.username AS username,
                    op.operation_name,
                    (SELECT communications_no FROM info_users_communications WHERE user_id = fp.op_user_id AND communications_type_id = 2 AND active = 0 AND deleted =0  limit 1 ) as cep,	
                    (SELECT communications_no FROM info_users_communications WHERE user_id = fp.op_user_id AND communications_type_id = 3 AND active = 0 AND deleted =0  limit 1 ) as istel 	                    
         */
        
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $sql = "
                SELECT
                    fp.id as id,
                    fp.s_date,                     
                    fp.firm_name AS company_name,   
                    fpu.username AS username                   
                    FROM sys_osb_consultants a                                
                INNER JOIN info_users u1 ON u1.id = a.user_id AND u1.role_id = 2  AND u1.active = 0 AND u1.deleted = 0                 
		INNER JOIN info_firm_profile fp ON fp.consultant_id = u1.id AND fp.deleted = 0   
		INNER JOIN info_users fpu ON fpu.id = fp.op_user_id  		
                /*INNER JOIN sys_operation_types op ON op.parent_id = 1 AND fp.operation_type_id = op.id AND op.active = 0 AND op.deleted =0  */              
                WHERE a.user_id =" . intval($opUserIdValue) . "                                                
                " . $sorguStr . "
                ORDER BY    " . $sort . " "  
                        . "" . $order . " "
                        . "LIMIT " . $pdo->quote($limit) . " "
                        . "OFFSET " . $pdo->quote($offset) . " ";
                $statement = $pdo->prepare($sql);
                //echo debugPDO($sql, $params);
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();

                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
                $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

    /**
     * user interface datagrid fill operation get row count for widget
     * @author Okan CIRAN
     * @ Gridi doldurmak için sys_osb_consultants tablosundan çekilen kayıtlarının kaç tane olduğunu döndürür   !!
     * @version v 1.0  08.02.2016
     * @param array | null $args
     * @return array
     * @throws \PDOException
     */
    public function getConsPendingFirmProfilertc($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                $opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                $whereSQL = " WHERE a.user_id = " . intval($opUserIdValue);

                 // sql query dynamic for filter operations
        $sorguStr=null;
        if(isset($params['filterRules'])) {
            $filterRules = trim($params['filterRules']);
            //print_r(json_decode($filterRules));
            $jsonFilter = json_decode($filterRules, true);
            //print_r($jsonFilter[0]->field);
            $sorguExpression = null;
            foreach ($jsonFilter as $std) {
                if($std['value']!=null) {
                    switch (trim($std['field'])) {
                    case 'username':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\' ';
                        $sorguStr.=' AND fpu.username'.$sorguExpression.' ';
                        break;
                    case 'company_name':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\'  ';
                        $sorguStr.=' AND fp.firm_name'.$sorguExpression.' ';

                        break;
                    case 's_date':
                        $sorguExpression = ' ILIKE \'%'.$std['value'].'%\'  ';
                        $sorguStr.='AND  to_char(fp.s_date, \'DD/MM/YYYY\')'.$sorguExpression.' ';

                        break;
                    default:
                        break;
                    }
                }  
            }
        } else {
            $sorguStr=null;
            $filterRules = "";
        }

          $sorguStr = rtrim($sorguStr,"AND ");
                $sql = "
               SELECT  
                    COUNT(a.id) AS COUNT                           		  
		FROM sys_osb_consultants a                                
		INNER JOIN info_users u1 ON u1.id = a.user_id AND u1.role_id = 2  AND u1.active = 0 AND u1.deleted = 0                 
		INNER JOIN info_firm_profile fp ON fp.consultant_id = u1.id AND fp.deleted = 0   
		INNER JOIN info_users fpu ON fpu.id = fp.op_user_id  		
		INNER JOIN sys_operation_types op ON op.parent_id = 1 AND fp.operation_type_id = op.id AND op.active = 0 AND op.deleted =0                 		  
                " . $sorguStr . "                

                    ";
                $statement = $pdo->prepare($sql);
              //   echo debugPDO($sql, $params);
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
              //  $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }
    
    /**
     * get consultant confirmation process details
     * @param array $params
     * @return array
     * @throws \PDOException
     */
    public function getConsConfirmationProcessDetails($params = array()) {
        try {
            $pdo = $this->slimApp->getServiceManager()->get('pgConnectFactory');
            $opUserId = InfoUsers::getUserId(array('pk' => $params['pk']));
            if (!\Utill\Dal\Helper::haveRecord($opUserId)) {
                //$opUserIdValue = $opUserId ['resultSet'][0]['user_id'];
                //$whereSQL = " WHERE a.user_id = " . intval($opUserIdValue);

                $sql = " SELECT 
                        a.id as id, 
                        a.profile_public as profile_public, 
                        a.firm_name as firm_name, 
                        a.web_address,                     
                        a.tax_office, 
                        a.tax_no, 
                        a.sgk_sicil_no,
                        a.bagkur_sicil_no,
                        a.ownership_status_id,
                        sd4.description AS owner_ship,
                        a.foundation_year,                        
                        a.language_code, 
                        COALESCE(NULLIF(l.language_eng, ''), l.language) AS language_name,   
                        a.op_user_id,
                        u.username,                         
                        a.firm_name_eng, 
                        a.firm_name_sort,
                        a.country_id, 
                        co.name AS countryname ,  
                        REPLACE(REPLACE (REPLACE( CAST( (SELECT ARRAY(
                        SELECT Concat (sd8x.description ,':',    
                    'Adres : ', ax.address1,ax.address2, 
                    'Posta Kodu = ',ax.postal_code,                  
                    cox.name ,' ',
                    ctx.name ,' ',
                    box.name ,' ',
                    ax.city_name ,'--' )                    
                FROM info_users_addresses  ax                                                                      
                INNER JOIN sys_specific_definitions AS sd8x ON sd8x.main_group =17 AND sd8x.first_group = ax.address_type_id AND sd8x.deleted = 0 AND sd8x.active = 0 AND sd8x.language_id = ax.language_id         
                LEFT JOIN sys_countrys cox on co.id = ax.country_id AND cox.deleted = 0 AND cox.active = 0 AND cox.language_code = ax.language_code                               
                LEFT JOIN sys_city ctx on ctx.id = ax.city_id AND ctx.deleted = 0 AND ctx.active = 0 AND ctx.language_code = ax.language_code                               
                LEFT JOIN sys_borough box on box.id = ax.borough_id AND box.deleted = 0 AND box.active = 0 AND box.language_code = ax.language_code                 
                WHERE ax.deleted =0 AND ax.active =0 
                AND ax.user_id  =  a.op_user_id
                        ))as text),'\"',''),'{',''),'}','') As adresbilgileri,
                          REPLACE(REPLACE (REPLACE( CAST( (SELECT ARRAY(
                SELECT  
                    CONCAT(sd6y.description  ,' : ',ay.communications_no )                  
                FROM info_users_communications ay       
                INNER JOIN sys_specific_definitions sd6y ON sd6y.main_group = 5 AND sd6y.first_group= ay.communications_type_id AND sd6y.language_code = ay.language_code AND sd6y.deleted = 0 AND sd6y.active = 0                     
                WHERE 
                    ay.active =0 AND ay.deleted = 0 AND                   
                    ay.user_id =   a.op_user_id
            ))as text),'\"',''),'{',''),'}','') As iletisimbilgileri

                        
                    FROM info_firm_profile a    
                    INNER JOIN sys_operation_types op ON op.id = a.operation_type_id and  op.language_code = a.language_code  AND op.deleted =0 AND op.active =0
                    LEFT JOIN sys_specific_definitions sd4 ON sd4.main_group = 1 AND sd4.first_group= a.active AND sd4.language_code = a.language_code AND sd4.deleted = 0 AND sd4.active = 0
                    INNER JOIN sys_language l ON l.language_main_code = a.language_code AND l.deleted =0 AND l.active =0 
                    INNER JOIN info_users u ON u.id = a.op_user_id                      
                    LEFT JOIN sys_countrys co on co.id = a.country_id AND co.deleted = 0 AND co.active = 0 AND co.language_code = a.language_code                               
                    WHERE a.id =:profile_id           

                    ";
                $statement = $pdo->prepare($sql);
                $statement->bindValue(':profile_id', $params['profile_id'], \PDO::PARAM_INT);
              //   echo debugPDO($sql, $params);
                $statement->execute();
                $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
                $errorInfo = $statement->errorInfo();
                if ($errorInfo[0] != "00000" && $errorInfo[1] != NULL && $errorInfo[2] != NULL)
                    throw new \PDOException($errorInfo[0]);
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => $result);
            } else {
                $errorInfo = '23502';   // 23502  not_null_violation
                $errorInfoColumn = 'pk';
              //  $pdo->commit();
                return array("found" => true, "errorInfo" => $errorInfo, "resultSet" => '', "errorInfoColumn" => $errorInfoColumn);
            }
        } catch (\PDOException $e /* Exception $e */) {
            //$debugSQLParams = $statement->debugDumpParams();
            return array("found" => false, "errorInfo" => $e->getMessage()/* , 'debug' => $debugSQLParams */);
        }
    }

}

    
    



