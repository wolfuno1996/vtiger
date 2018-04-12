<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_Record_Model
 */
class QuotingTool_Record_Model extends Vtiger_Record_Model
{
    /**
     * The white list fields to export
     * @var array
     */
    public $quotingToolFields = array('filename', 'module', 'body', 'header', 'content', 'footer', 'anwidget', 'description',
        'email_subject', 'email_content', 'mapping_fields', 'attachments', 'is_active', 'createnewrecords','linkproposal');
    
    /**
     * Function to get the Detail View url for the record
     * @return string - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        return 'index.php?module=QuotingTool&view=Edit&record=' . $this->getId();
    }

    /**
     * @param $id
     * @param $data
     * @return Vtiger_Record_Model
     */
    public function save($id, $data)
    {
        $db = PearDatabase::getInstance();
        $sql = null;
        $params = array();
        $timestamp = date('Y-m-d H:i:s', time());
        $columnNames = array('filename', 'module', 'body', 'header', 'content', 'footer', 'description', 'deleted', 'email_subject',
            'email_content', 'mapping_fields', 'attachments', 'created', 'updated', 'anwidget', 'is_active', 'createnewrecords', 'linkproposal');

        if ($id) {
            $data = array_merge($data, array(
                'updated' => $timestamp
            ));

            $sqlPart2 = "";
            foreach ($data as $name => $value) {
                if (in_array($name, $columnNames)) {
                    $sqlPart2 .= " {$name}=?,";
                }

                $params[] = $value;
            }

            /**
             * Remove the last "," character from string
             * @link http://stackoverflow.com/questions/5592994/remove-the-last-character-from-string
             */
            $sqlPart2 = rtrim($sqlPart2, ',');

            $sqlPart3 = "WHERE id=?";
            $params[] = $id;
            
            $sql = "UPDATE vtiger_quotingtool SET {$sqlPart2} {$sqlPart3}";
        } else {
            $data = array_merge($data, array(
                'created' => $timestamp,
                'updated' => $timestamp
            ));

            $sqlPart2 = " (";
            $sqlPart3 = " (";

            foreach ($data as $name => $value) {
                if (in_array($name, $columnNames)) {
                    $sqlPart2 .= " {$name},";
                    $sqlPart3 .= "?,";
                }

                $params[] = $value;
            }

            /**
             * Remove the last "," character from string
             * @link http://stackoverflow.com/questions/5592994/remove-the-last-character-from-string
             */
            $sqlPart2 = rtrim($sqlPart2, ',');
            $sqlPart2 .= ") ";
            $sqlPart3 = rtrim($sqlPart3, ',');
            $sqlPart3 .= ") ";

            $sql = "INSERT INTO vtiger_quotingtool $sqlPart2 VALUES $sqlPart3";
        }

        // When false
        if (!$db->pquery($sql, $params)) {
            return null;
        }

        $recordId = ($id) ? $id : $db->getLastInsertID();
        return $this->getById($recordId);
    }

    /**
     * @return array
     */
    static function findAll()
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $rs = $db->pquery("SELECT * FROM `vtiger_quotingtool` WHERE `deleted` != 1");
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param $id
     * @return Vtiger_Record_Model
     */
    public function getById($id)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $sql = "SELECT * FROM `vtiger_quotingtool` WHERE `id`=? AND `deleted` != 1 ORDER BY `id` LIMIT 1";
        $params = array($id);
        $rs = $db->pquery($sql, $params);
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return (count($instances) > 0) ? $instances[0] : null;
    }

    /**
     * @param string $module
     * @return array
     */
    public function findByModule($module)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $uitypes=array();
        // Get related modules
        switch ($module){
            case "Accounts":
                $uitypes=array('51','73','68');
                break;
            case "Contacts":
                $uitypes=array('57','68');
                break;
            case "Campaigns":
                $uitypes=array('58');
                break;
            case "Products":
                $uitypes=array('59');
                break;
            case "Vendors":
                $uitypes=array('75','81');
                break;
            case "Potentials":
                $uitypes=array('76');
                break;
            case "Quotes":
                $uitypes=array('78');
                break;
            case "SalesOrder":
                $uitypes=array('80');
                break;
        }
        if (empty($uitypes)) {
            $uitypes = array('10');
        }
        $arrModules=array();
        // Get child modules
        $queryChild="SELECT * FROM (
            SELECT vtiger_tab.tabid,vtiger_tab.`name` as relmodule
            FROM `vtiger_field`
            INNER JOIN vtiger_tab ON vtiger_field.tabid=vtiger_tab.tabid
            WHERE vtiger_field.presence <> 1 AND uitype IN (" . generateQuestionMarks($uitypes) . ")
            UNION
            SELECT vtiger_field.tabid,vtiger_fieldmodulerel.module as relmodule
            FROM vtiger_fieldmodulerel
            INNER JOIN vtiger_field ON vtiger_fieldmodulerel.fieldid=vtiger_field.fieldid
            WHERE vtiger_field.presence <> 1 AND uitype = 10 AND relmodule = ?
            ) as temp
            WHERE relmodule NOT IN ('Webmails', 'SMSNotifier', 'Emails', 'Integration', 'Dashboard', 'ModComments', 'vtmessages', 'vttwitter','PBXManager')
            AND relmodule <> ?
            ";
        $reChild=$db->pquery($queryChild, array($uitypes, $module, $module));
        if($db->num_rows($reChild)>0) {
            while ($rowChild = $db->fetchByAssoc($reChild)) {
                $arrModules[]=$rowChild['relmodule'];
            }
        }
        
        $sql = "SELECT * FROM (
                SELECT * FROM `vtiger_quotingtool` WHERE `module` = ? AND `deleted` != 1 AND `is_active` = 1 
                UNION
                SELECT * FROM `vtiger_quotingtool` WHERE `module` IN (" . generateQuestionMarks($arrModules) . ") AND `deleted` != 1 AND `is_active` = 1 AND `createnewrecords` = 1 
                ) as temp";

        $params = array($module, $arrModules);
        $rs = $db->pquery($sql, $params);

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }
    public function getRelatedModules($currentModuleModel)
    {
        $relatedModules = array();
        $referenceFields = $currentModuleModel->getFieldsByType('reference');
        /** @var Vtiger_Field_Model $fieldModel */
        foreach ($referenceFields as $fieldModel) {
            $referenceModules = $fieldModel->getReferenceList();
            if (count($referenceModules) == 2 && $referenceModules[0] == 'Campaigns') {
                // Fix when conflict between Users & Campaigns modules
                unset($referenceModules[0]);
            }
            foreach ($referenceModules as $k => $relatedModule) {
                if (!in_array($relatedModule, $relatedModules) && $relatedModule != 'Users') {
                    $relatedModules[] = $relatedModule;
                }
            }
        }
        return $relatedModules;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $db = PearDatabase::getInstance();
        $sql = null;
        $stamp = date('Y-m-d H:i:s', time());

        $sql = "UPDATE vtiger_quotingtool SET deleted=1, updated=? WHERE id=?";
        $params = array($stamp, $id);
        $result = $db->pquery($sql, $params);
        return $result ? true : false;
    }

    public function compileRecord() {
    }

    /**
     * @param int $entityId
     * @param array $fields
     * @param array $options
     * @return array
     */
    public function decompileRecord($entityId = 0, $fields = array(), $options = array()) {
        $quotingTool = new QuotingTool();

        if (!empty($fields)) {
            foreach ($fields as $field) {
                switch ($field) {
                    case 'header':
                    case 'content':
                    case 'footer':
                    case 'email_subject':
                    case 'email_content':
                        $tmp = $this->get($field);
                        $tmp = $tmp ? base64_decode($tmp) : '';
                        if ($entityId != ''  && $_REQUEST['mode'] != 'download' && $_REQUEST['mode'] != 'save_proposal' ) {
                            $tmp = $quotingTool->parseTokens($tmp, $this->get('module'), $entityId);
                        }
                        $this->set($field, $tmp);
                        break;
                    default:
                        break;
                }
            }
        }

        return $this;
    }

}