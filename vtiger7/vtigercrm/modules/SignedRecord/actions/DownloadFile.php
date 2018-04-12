<?php
/* ********************************************************************************
 * The content of this file is subject to the Signed Record ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Class SignedRecord_DownloadFile_Action
 */
class SignedRecord_DownloadFile_Action extends Vtiger_Action_Controller
{

    /**
     * @param Vtiger_Request $request
     * @throws AppException
     */
    public function checkPermission(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();

        if (!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    /**
     * @param Vtiger_Request $request
     * @return string
     */
    public function process(Vtiger_Request $request)
    {
        $moduleName = $request->getModule();
        $documentRecordModel = Vtiger_Record_Model::getInstanceById($request->get('record'), $moduleName);

        //Download the file
        $filename = $documentRecordModel->get('filename');
        if (file_exists($filename)) {
            $type = mime_content_type($filename);
            if ($type == 'application/pdf') {

                $fileContent = '';

                if (is_readable($filename)) {
                    $fileContent = file_get_contents($filename);
                }

                header("Content-type: ". mime_content_type($filename));
                header("Pragma: public");
                header("Cache-Control: private");
                header("Content-Disposition: attachment; filename=".html_entity_decode(basename($filename),
                        ENT_QUOTES, vglobal('default_charset')));
                header("Content-Description: PHP Generated Data");

                echo $fileContent;
                exit;
            }
        }

        // If invalid
        /**
         * @link http://stackoverflow.com/questions/2548566/go-back-to-previous-page
         * @var string $previous
         */
        $previous = "javascript:history.go(-1)";
        if (isset($_SERVER['HTTP_REFERER'])) {
            $previous = $_SERVER['HTTP_REFERER'];
        }
        header('Location: ' . $previous);
    }
}