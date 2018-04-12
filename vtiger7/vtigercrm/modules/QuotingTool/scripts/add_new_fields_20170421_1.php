<?php
global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `full_header` longtext NULL;";
$params = array();
$rs = $adb->pquery($sql, $params);
$sql1 = "ALTER TABLE `vtiger_quotingtool_transactions` ADD `full_footer` longtext NULL;";
$params = array();
$adb->pquery($sql1, $params);