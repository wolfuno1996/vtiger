<?php
/* ********************************************************************************
* The content of this file is subject to the Quoting Tool ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */

include_once 'modules/QuotingTool/QuotingTool.php';

/**
 * Class QuotingTool_SelectEmailFields_View
 */
class QuotingTool_SelectEmailFields_View extends Vtiger_IndexAjax_View
{
    /**
     * @param Vtiger_Request $request
     */
    function process(Vtiger_Request $request)
    {
        $quotingTool = new QuotingTool();
        $toEmail = null;
        $id = null;
        $recordId = $request->get('record');
        $moduleName = $request->get('module');
        $relModule = $request->get('relmodule');
        $templateId = $request->get('template_id');
        $viewer = $this->getViewer($request);
        $email_field_list = $quotingTool->getEmailList($relModule, $recordId);

        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RECORDID', $recordId);
        $viewer->assign('RELMODULE', $relModule);
        $viewer->assign('TEMPLATEID', $templateId);
        $viewer->assign('EMAIL_FIELD_LIST', $email_field_list);
        echo $viewer->view('SelectEmailFields.tpl', $moduleName, true);
    }

}