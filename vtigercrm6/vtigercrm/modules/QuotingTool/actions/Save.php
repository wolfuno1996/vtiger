<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

require_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_Save_Action
 */
class QuotingTool_Save_Action extends Vtiger_Action_Controller
{
    /**
     * @constructor
     */
    function __construct()
    {
        parent::__construct();
        $this->vteLicense();
    }

    /**
     *
     */
    function vteLicense() {
        $vTELicense=new QuotingTool_VTELicense_Model('QuotingTool');
        if(!$vTELicense->validate()){
            header("Location: index.php?module=QuotingTool&view=List&mode=step2");
        }
    }

    /**
     * @param Vtiger_Request $request
     * @return bool
     */
    public function checkPermission(Vtiger_Request $request)
    {
        return;
    }

    /**
     * @param Vtiger_Request $request
     */
    public function process(Vtiger_Request $request)
    {
        $params = array();
        $id = $request->get('record');
        $params['filename'] = $request->get('filename');
        $module = $request->get('module');
        $params['module'] = $request->get('primary_module');
        $params['body'] = $request->get('body');
        $params['header'] = $request->get('header');
        $params['content'] = $request->get('content');
        $params['footer'] = $request->get('footer');
        $params['description'] = $request->get('description');
        $params['expire_in_days'] = $request->get('expire_in_days');
        $params['email_subject'] = $request->get('email_subject');
        $params['email_content'] = $request->get('email_content');
        $params['mapping_fields'] = ($request->get('mapping_fields')) ?
            QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('mapping_fields'), JSON_FORCE_OBJECT)) : null;
        $params['attachments'] = ($request->get('attachments')) ?
            QuotingToolUtils::jsonUnescapedSlashes(json_encode($request->get('attachments'))) : null;
        /** @var QuotingTool_Record_Model $recordModel */
        $recordModel = QuotingTool_Record_Model::getCleanInstance($module);
        $savedId = $recordModel->save($id, $params);

        // Settings
        $SettingRecordModel = new QuotingTool_SettingRecord_Model();
        $settings = $request->get('settings');
        $settingsBackground = $settings['background'] ? QuotingToolUtils::jsonUnescapedSlashes(json_encode($settings['background'], JSON_FORCE_OBJECT)) : null;
        $SettingRecordModel->updateSettingByTemplate($savedId, $settings['description'], $settings['label_accept'], $settings['label_decline'],
            $settingsBackground,$settings['expire_in_days']);

        header('Location:index.php?module=QuotingTool&view=List');
    }
}