<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

include('modules/QuotingTool/QuotingTool.php');

/**
 * Class QuotingTool_Install_Action
 */
class QuotingTool_Install_Action extends Vtiger_Action_Controller
{
    /**
     *
     */
    function __construct()
    {
        parent::__construct();
        $this->exposeMethod('downloadMPDF');
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
        $mode = $request->get('mode');
        if (!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    /**
     * Fn - downloadMPDF
     *
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function downloadMPDF(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $quotingTool = new QuotingTool();
        $error = '';
        $srcZip = $quotingTool->pdfLibLink;
        $pdfLibContainer = 'modules/QuotingTool/resources/';
        $pdfLibSource = $pdfLibContainer . 'mpdf/';
        $trgZip = $pdfLibContainer . 'mpdf.zip';

        if (copy($srcZip, $trgZip)) {
            require_once('vtlib/thirdparty/dUnzip2.inc.php');
            $unzip = new dUnzip2($trgZip);
            $unzip->unzipAll(getcwd() . '/' . $pdfLibContainer);
            if ($unzip)
                $unzip->close();

            if (!is_dir($pdfLibSource)) {
                $error = vtranslate('UNZIP_ERROR', $moduleName);
            }
        } else {
            $error = vtranslate('DOWNLOAD_ERROR', $moduleName);
        }

        if ($error == '') {
            $result = array('success' => true, 'message' => '');
        } else {
            $result = array('success' => false, 'message' => $error);
        }

        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

}