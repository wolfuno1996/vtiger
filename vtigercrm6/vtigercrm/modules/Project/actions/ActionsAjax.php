<?php
class Project_ActionsAjax_Action extends Vtiger_Action_Controller {

    function __construct() {
        $this->exposeMethod('CreateProject');
    }
    public function checkPermission(Vtiger_Request $request) {
        return;
    }
    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if(!empty($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }

    }
    public function CreateProject(Vtiger_Request $request) {
        //the new values are added to $_REQUEST for Ajax Save, are removing the Tax details depend on the 'ajxaction' value
        $sourceRecord = $request->get('source_record');
        $oppRecordModel = Vtiger_Record_Model::getInstanceById($sourceRecord);
        $oppName = $oppRecordModel->get('potentialname');
        $oppORG = $oppRecordModel->get('related_to');
        $oppAM = $oppRecordModel->get('amount');
        $oppDES = $oppRecordModel->get('description');
        $projectRecordModel = Vtiger_Record_Model::getCleanInstance('Project');
        $projectRecordModel->set('mode', '');
        $projectRecordModel->set('projectname', $oppName);
        $projectRecordModel->set('linktoaccountscontacts', $oppORG);
        $projectRecordModel->set('targetbudget', $oppAM);
        $projectRecordModel->set('description', $oppDES);
        $projectRecordModel->save();
        $linkDetail = $projectRecordModel->getDetailViewUrl();
        $response = new Vtiger_Response();
        $response->setResult($linkDetail);
        $response->emit();


    }
}
