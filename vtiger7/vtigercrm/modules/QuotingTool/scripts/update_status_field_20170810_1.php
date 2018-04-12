<?php
global $adb;
$checkExsist = $adb->pquery("SELECT `fieldname` FROM `vtiger_field` WHERE `columnname`=? AND tablename = ?"
                                ,array('signedrecord_status', 'vtiger_signedrecord'));
if ($adb->num_rows($checkExsist) > 0) {
    $status = $adb->pquery("SELECT `fieldid`, `fieldname` FROM `vtiger_field` WHERE `columnname`=? AND tablename = ?"
                                ,array('status', 'vtiger_signedrecord'));
    if ($adb->num_rows($status) > 0) {
        $idStatusField = $adb->query_result($status, 0, 'fieldid');
        $adb->pquery("UPDATE `vtiger_field` SET `presence` =? WHERE `fieldid` = ?", array( 1, $idStatusField));
        $adb->pquery("ALTER TABLE `vtiger_signedrecord` CHANGE  `status` `signedrecord_status` VARCHAR(50)");
    }
}