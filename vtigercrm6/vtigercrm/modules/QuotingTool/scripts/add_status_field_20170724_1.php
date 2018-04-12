<?php
global $adb;
$sql = "ALTER TABLE `vtiger_quotingtool` ADD `is_active` int(1) NULL DEFAULT 1";
$params = array();
$rs = $adb->pquery($sql, $params);