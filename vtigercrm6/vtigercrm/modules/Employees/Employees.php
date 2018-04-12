<?php
include_once 'modules/Vtiger/CRMEntity.php';

class Employees extends Vtiger_CRMEntity{
    var $table_name = 'vtiger_employees';
    var $table_index= 'employeesid';

    /**
     * Mandatory table for supporting custom fields.
     */
    var $customFieldTable = Array('vtiger_employees', 'employeesid');

    /**r
     * Mandatory for Saving, Include tables related to this module.
     */
    var $tab_name = Array('vtiger_crmentity', 'vtiger_employees', 'vtiger_employeescf');

    /**
     * Mandatory for Saving, Include tablename and tablekey columnname here.
     */
    var $tab_name_index = Array(
        'vtiger_crmentity' => 'crmid',
        'vtiger_employees' => 'employeesid',
        'vtiger_employeescf'=>'employeesid');

    /**
     * Mandatory for Listing (Related listview)
     */
    var $list_fields = Array (
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'

        'LBL_EMPLOYEES_TYPE' => Array('employees', 'employees_type'),
        'Assigned To' => Array('crmentity','smownerid')
    );
    var $list_fields_name = Array (
        /* Format: Field Label => fieldname */
        'LBL_EMPLOYEES_TYPE' => Array('employees', 'employees_type'),
        'Assigned To' => 'assigned_user_id',
    );

    // Make the field link to detail view
    var $list_link_field = 'first_name';

    // For Popup listview and UI type support
    var $search_fields = Array(
        /* Format: Field Label => Array(tablename, columnname) */
        // tablename should not have prefix 'vtiger_'
        'LBL_EMPLOYEES_TYPE' => Array('employees', 'employees_type'),
        'Assigned To'=>Array('vtiger_crmentity'=>'smownerid'),
        //'Owner' => Array('vtiger_crmentity', '')
    );
    var $search_fields_name = Array (
        /* Format: Field Label => fieldname */
        'LBL_EMPLOYEES_TYPE' => Array('employees', 'employees_type'),
        'Assigned To' => 'assigned_user_id',
    );

    // For Popup window record selection
    var $popup_fields = Array ('employees_type');

    // For Alphabetical search
    var $def_basicsearch_col = 'employees_type';

    // Column value to use on detail view record text display
    var $def_detailview_recname = 'employees_type';

    // Used when enabling/disabling the mandatory fields for the module.
    // Refers to vtiger_field.fieldname values.
    var $mandatory_fields = Array('employees_type','assigned_user_id');
    //var $mandatory_fields = Array('emprole_desc');

    var $default_order_by = 'employees_type';
    var $default_sort_order='ASC';

    /**
     * Invoked when special actions are performed on the module.
     * @param String Module name
     * @param String Event Type
     */
    function vtlib_handler($moduleName, $eventType) {
        global $adb;
        if($eventType == 'module.postinstall') {
            // TODO Handle actions after this module is installed.
        } else if($eventType == 'module.disabled') {
            // TODO Handle actions before this module is being uninstalled.
        } else if($eventType == 'module.preuninstall') {
            // TODO Handle actions when this module is about to be deleted.
        } else if($eventType == 'module.preupdate') {
            // TODO Handle actions before this module is updated.
        } else if($eventType == 'module.postupdate') {
            // TODO Handle actions after this module is updated.
        }
    }
}