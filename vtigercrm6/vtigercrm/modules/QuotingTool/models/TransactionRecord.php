<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_TransactionRecord_Model
 */
class QuotingTool_TransactionRecord_Model extends Vtiger_Record_Model
{
    /**
     * Function to get the Detail View url for the record
     * @return string - Record Detail View Url
     */
    public function getDetailViewUrl()
    {
        return '';
    }

    /**
     * @return array
     */
    static function findAll()
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $rs = $db->pquery("SELECT * FROM `vtiger_quotingtool_transactions` WHERE `deleted` != 1");
        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param int $id
     * @return null
     */
    public function findById($id)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $sql = "SELECT `transaction`.`id`, `transaction`.`module`, `transaction`.`record_id`, `transaction`.`signature`,
                        `transaction`.`signature_name`, `transaction`.`template_id`, `transaction`.`status`, `transaction`.`created`,
                        `transaction`.`updated`, `transaction`.`full_content`, `transaction`.`hash`,
                        `template`.`filename`, `template`.`header`, `template`.`content`, `template`.`footer`, `template`.`description`,
                        `template`.`attachments`,
                        `settings`. `label_accept`, `settings`. `label_decline`, `settings`. `background`, `settings`. `expire_in_days` 
                  FROM `vtiger_quotingtool_transactions` AS `transaction`
                  INNER JOIN `vtiger_quotingtool` AS `template` ON (`transaction`.`template_id` = `template`.`id` AND `template`.deleted != 1)
                  LEFT JOIN `vtiger_quotingtool_settings` AS `settings` ON (`template`.`id` = `settings`.`template_id`)
                WHERE `transaction`.`id`=? AND `transaction`.`deleted` != 1
                ORDER BY `transaction`.`id` DESC
                LIMIT 1";
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
        $sql = "SELECT * FROM `vtiger_quotingtool_transactions` WHERE `module`=? AND `deleted` != 1 LIKE ? ORDER BY `id` DESC";
        $params = array($module);
        $rs = $db->pquery($sql, $params);

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return $instances;
    }

    /**
     * @param int $id
     * @param $templateId
     * @param string $module
     * @param int $recordId
     * @param string $signature
     * @param string $signatureName
     * @param string $content
     * @param string $description
     * @return int|null
     */
    public function saveTransaction($id, $templateId, $module, $recordId, $signature, $signatureName, $content, $description)
    {
        $timestamp = time();
        $stamp = date('Y-m-d H:i:s', $timestamp);
        $db = PearDatabase::getInstance();
        $sql = null;
        $params = null;

        if ($id) {
            $sql = "UPDATE `vtiger_quotingtool_transactions` SET `template_id`=?, `module`=?, `record_id`=?, `signature`=?,
                    `signature_name`=?, `description`=?, `full_content`=?, `updated`=? WHERE id=?";
            $params = array($templateId, $module, $recordId, $signature, $signatureName, $description, $content, $stamp, $id);
        } else {
            $hash = $timestamp . QuotingToolUtils::generateToken();
            $sql = "INSERT INTO `vtiger_quotingtool_transactions` (`template_id`, `module`, `record_id`, `signature`, `signature_name`,
                    `full_content`, `description`, `created`, `updated`, `hash`) VALUES (?,?,?,?,?,?,?,?,?,?)";
            $params = array($templateId, $module, $recordId, $signature, $signatureName, $content, $description, $stamp, $stamp, $hash);
        }
        $result = $db->pquery($sql, $params);
        // When false
        if (!$result)
            return null;

        $returnId = $id ? $id : $db->getLastInsertID();
        return $returnId;
    }

    /**
     * @param int $id
     * @param string $signature
     * @param string $signatureName
     * @param string $dFullContent
     * @param string $description
     * @return int|null
     */
    public function updateSignature($id, $signature, $signatureName, $dFullContent, $description = null)
    {
        $stamp = date('Y-m-d H:i:s', time());
        $db = PearDatabase::getInstance();
        $sql = "UPDATE `vtiger_quotingtool_transactions` SET `signature`=?, `signature_name`=?, `full_content`=?, `description`=?, `updated`=? WHERE `id`=?";
        $params = array($signature, $signatureName, $dFullContent, $description, $stamp, $id);
        $result = $db->pquery($sql, $params);
        return $result ? $id : null;
    }

    /**
     * @param int $id
     * @param int $status
     * @return int|null
     */
    public function changeStatus($id, $status)
    {
        $stamp = date('Y-m-d H:i:s', time());
        $db = PearDatabase::getInstance();
        $sql = "UPDATE `vtiger_quotingtool_transactions` SET `status`=?, `updated`=? WHERE `id`=?";
        $params = array($status, $stamp, $id);
        $result = $db->pquery($sql, $params);
        return $result ? $id : null;
    }

    /**
     * @param string $module
     * @param int $recordId
     * @return Vtiger_Record_Model
     */
    public function getLastTransactionByModule($module, $recordId)
    {
        $db = PearDatabase::getInstance();
        $instances = array();
        $sql = "SELECT * FROM `vtiger_quotingtool_transactions` WHERE `module` LIKE ? AND `record_id`=? AND `deleted` != 1 ORDER BY `id` DESC LIMIT 1";
        $params = array($module, $recordId);
        $rs = $db->pquery($sql, $params);

        if ($db->num_rows($rs)) {
            while ($data = $db->fetch_array($rs)) {
                $instances[] = new self($data);
            }
        }
        return (count($instances) > 0) ? $instances[0] : null;
    }

}