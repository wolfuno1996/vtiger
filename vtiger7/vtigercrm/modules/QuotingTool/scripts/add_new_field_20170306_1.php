<?php
global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool_settings` ADD `expire_in_days` INT(11) NULL DEFAULT '0';";
$params = array();
$rs = $adb->pquery($sql, $params); 