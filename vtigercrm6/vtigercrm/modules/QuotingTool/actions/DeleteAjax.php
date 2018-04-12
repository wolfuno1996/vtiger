<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class QuotingTool_DeleteAjax_Action
 */
class QuotingTool_DeleteAjax_Action extends Vtiger_DeleteAjax_Action
{
    /**
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->vteLicense();
    }

    /**
     *
     */
    function vteLicense()
    {
        $vTELicense = new QuotingTool_VTELicense_Model('QuotingTool');
        if (!$vTELicense->validate()) {
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
        // By mode
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }

        $data = array();

        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $response = new Vtiger_Response();
        $model = new QuotingTool_Record_Model();
        $success = $model->delete($recordId);
        if ($success) {
            $data['module'] = $moduleName;
            $data['viewname'] = '';
            $response->setResult(array(vtranslate('LBL_DELETED_SUCCESSFULLY', $moduleName)));
        } else {
            $response->setError(200, vtranslate('LBL_DELETED_FAILURE', $moduleName));
        }

        $response->setResult($data);
        $response->emit();
    }

}
