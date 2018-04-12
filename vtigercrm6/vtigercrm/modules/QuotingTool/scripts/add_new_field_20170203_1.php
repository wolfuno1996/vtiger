<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

//error_reporting(E_ERROR);
//ini_set('display_errors', '1');
//
//chdir(dirname(__FILE__) . '/../../..');
//require_once 'config.inc.php';
//require_once 'include/utils/utils.php';
//require_once 'includes/Loader.php';
//vimport('includes.runtime.EntryPoint');
//require_once 'modules/Users/Users.php';
//
global $adb, $current_user;
//$current_user = new Users();
//$activeAdmin = $current_user->getActiveAdminUser();
//$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

// Add hash field for uniquekey purpose
//$sql = "DELIMITER $$
//    DROP PROCEDURE IF EXISTS add_new_field_20170203_1 $$
//    CREATE PROCEDURE add_new_field_20170203_1()
//      BEGIN
//        -- add a column: hash
//        IF NOT EXISTS((SELECT *
//                       FROM information_schema.COLUMNS
//                       WHERE TABLE_SCHEMA = DATABASE()
//                             AND COLUMN_NAME = 'hash' AND TABLE_NAME = 'vtiger_quotingtool_transactions'))
//        THEN
//          ALTER TABLE vtiger_quotingtool_transactions ADD `hash` VARCHAR(255) NULL DEFAULT '';
//        END IF;
//      END $$
//    CALL add_new_field_20170203_1() $$
//    DELIMITER ;";
$sql = "ALTER TABLE vtiger_quotingtool_transactions ADD `hash` VARCHAR(255) NULL DEFAULT '';";
$params = array();
$rs = $adb->pquery($sql, $params);