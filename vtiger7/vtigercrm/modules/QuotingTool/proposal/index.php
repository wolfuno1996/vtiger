<?php
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

chdir(dirname(__FILE__) . '/../../..');
// Get current dir:
// @link: http://php.net/manual/en/function.getcwd.php
// echo getcwd(); exit;
require_once 'config.inc.php';
require_once 'include/utils/utils.php';
require_once 'includes/Loader.php';
vimport('includes.runtime.EntryPoint');
require_once 'modules/Users/Users.php';
include('modules/QuotingTool/QuotingTool.php');

global $adb, $current_user, $site_URL, $document_root, $vtiger_current_version;
$adb = PearDatabase::getInstance();
$current_user = new Users();
$activeAdmin = $current_user->getActiveAdminUser();
$current_user->retrieve_entity_info($activeAdmin->id, 'Users');

$moduleName = 'QuotingTool';
if(version_compare($vtiger_current_version, '7.0.0', '<')) {
    $template_folder= "layouts/vlayout";
}elsE{
    $template_folder= "layouts/v7";
}

$childModule = $_REQUEST['childmodule'];
$isCreateNewRecord = $_REQUEST['iscreatenewrecord'];
$formCreateNewRecord =  $_REQUEST['newrecord'];
$transactionId = $_REQUEST['record'];
$quotingTool = new QuotingTool();
$transactionRecordModel = new QuotingTool_TransactionRecord_Model();
/** @var Vtiger_Record_Model $transactionRecord */
$transactionRecord = $transactionRecordModel->findById($transactionId);

// Invalid transaction
if (!$transactionRecord) {
    header('location: 404.html');
}
$dateTransaction = $transactionRecord->get('created');
$time = strtotime($dateTransaction);
$dateTransaction = date('Y-m-d', $time);
$today = date('Y-m-d');
$diff = date_diff(date_create($dateTransaction), date_create($today));
$interval = $diff->format("%d");
$expire_in_days = $transactionRecord->get('expire_in_days');
if ($expire_in_days < $interval && $expire_in_days != 0) {
    header('location: expire.html');
}

// Check hash session
$session = (isset($_REQUEST['session']) && $_REQUEST['session']) ? $_REQUEST['session'] : '';
$hash = $transactionRecord->get('hash');
$hash = $hash ? $hash : '';

if ($session != $hash) {
    // When invalid session hash
    header('location: 404.html');
}
// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

// Create new SignedRecord
$signedRecordModuleModel = Vtiger_Module_Model::getInstance('SignedRecord');
$newSignedRecordId = 0;
if ($signedRecordModuleModel) {
    $timestamp = time();

    $newSignedRecord = array(
        'signature' => $transactionRecord->get('signature'),
        'signature_name' => $transactionRecord->get('signature_name'),
        'signedrecord_type' => SignedRecord_Record_Model::TYPE_OPENED,
        'signature_date' => date('Y-m-d', $timestamp),
        'cf_signature_time' => date('H:i:s', $timestamp),
        'related_to' => $transactionRecord->get('record_id'),
        'signedrecord_browser' => $_SERVER['HTTP_USER_AGENT'],
        'signedrecord_ip' => get_client_ip(),
        'signedrecord_cookie' => json_encode($_COOKIE),
    );

    $signedRecordModel = Vtiger_Record_Model::getCleanInstance('SignedRecord');
    foreach ($newSignedRecord as $field => $value) {
        $signedRecordModel->set($field, $value);
    }

    $signedRecordModel->save();
    $newSignedRecordId = $signedRecordModel->getId();
}

// Status
$tranStatus = $transactionRecord->get('status');
$tranStatusText = '';
if ($tranStatus == '1') {
    $tranStatusText = $transactionRecord->get('label_accept');
} else if ($tranStatus == '-1') {
    $tranStatusText = $transactionRecord->get('label_decline');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Document</title>
    <meta name="robots" content="noindex">
    <link rel="stylesheet" type="text/css" href="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/signature-pad/assets/jquery.signaturepad.css">
    <link rel="stylesheet" type="text/css" href="../../../libraries/jquery/select2/select2.css">
    <link rel="stylesheet" type="text/css" href="../../../libraries/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../libraries/bootstrap/css/bootstrap-responsive.css">
    <link rel="stylesheet" type="text/css" href="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/loading-indicator-3.3.1/jquery.loading-indicator.css">
    <link rel="stylesheet" type="text/css" href="../../../libraries/jquery/timepicker/jquery.timepicker.css">
    <link rel="stylesheet" type="text/css" href="../../../libraries/bootstrap/js/eternicode-bootstrap-datepicker/css/datepicker.css">
    <link rel="stylesheet" type="text/css" href="../resources/styles.css">
    <link rel="stylesheet" type="text/css" href="../resources/web.css">
    <link rel="stylesheet" type="text/css" href="css/proposal.css">

    <script type="text/javascript" src="../../../libraries/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="../../../libraries/jquery/jquery-ui/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="../../../libraries/jquery/chosen/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="../../../libraries/jquery/select2/select2.min.js"></script>
    <script type="text/javascript" src="../../../libraries/jquery/timepicker/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="../../../libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/utils/jQuery-customs.js"></script>
    <script type="text/javascript" src="../../../resources/app.js"></script>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/QuotingToolUtils.js"></script>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/utils/helper.js"></script>
    <script type="text/javascript" src="../../../libraries/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/signature-pad/jquery.signaturepad.js"></script>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/signature-pad/assets/json2.min.js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/signature-pad/assets/flashcanvas.js"></script>
    <![endif]-->
    <script type="text/javascript" src="../../../<?= $template_folder; ?>/modules/<?= $moduleName; ?>/resources/js/libs/loading-indicator-3.3.1/jquery.loading-indicator.min.js"></script>
</head>
<body>
<div id="quoting_tool-app">
    <input type="hidden" id="row_type" value="medium">
    <input type="hidden" id="date_format" value="<?= $current_user->date_format; ?>">
    <input type="hidden" id="hour_format" value="<?= $current_user->hour_format; ?>">
    <input type="hidden" id="start_hour" value="<?= $current_user->start_hour; ?>">
    <input type="hidden" id="end_hour" value="<?= $current_user->end_hour; ?>">
    <input type="hidden" id="time_zone" value="<?= $current_user->time_zone; ?>">
    <input type="hidden" id="dayoftheweek" value="<?= $current_user->dayoftheweek; ?>">

    <div id="viewport">
        <div id="quoting_tool-body">
            <form action="action.php" name="frm-proposal-content" method="post" class="proposal-form-submit">
                <input type="hidden" name="_action" value="" />
                <input type="hidden" name="ajxaction" value="DETAILVIEW" /><!--Prevent saveInventoryProductDetails-->
                <input type="hidden" name="module" value="<?= $transactionRecord->get('module'); ?>" />
                <input type="hidden" name="record" value="<?= $transactionId; ?>" />
                <input type="hidden" name="record_id" value="<?= $transactionRecord->get('record_id'); ?>" />
                <input type="hidden" name="name" value="<?= $transactionRecord->get('filename'); ?>" />
                <textarea name="header" class="hide"><?= $transactionRecord->get('header'); ?></textarea>
                <textarea name="content" class="hide"><?= $transactionRecord->get('full_content'); ?></textarea>
                <textarea name="footer" class="hide"><?= $transactionRecord->get('footer'); ?></textarea>
                <textarea name="signature" class="hide"><?= $transactionRecord->get('signature'); ?></textarea>
                <input type="hidden" name="signature_name" value="<?= $transactionRecord->get('signature_name'); ?>" />
                <input type="hidden" name="signature_datetime" value="<?= $transactionRecord->get('updated'); ?>" />
                <input type="hidden" name="status" value="<?= $transactionRecord->get('status'); ?>" />
                <input type="hidden" name="status_text" value="<?= $tranStatusText; ?>" />
                <input type="hidden" name="is_create_new_record" value="<?= $isCreateNewRecord; ?>" />
                <input type="hidden" name="form_create_record" value="<?= $formCreateNewRecord; ?>" />
                <input type="hidden" name="child_module" value="<?= $childModule; ?>" />
                <textarea name="description" class="hide"><?= $transactionRecord->get('description'); ?></textarea>
                <textarea name="background" class="hide"><?= $transactionRecord->get('background'); ?></textarea>
                <textarea name="attachments" class="hide"><?= $transactionRecord->get('attachments'); ?></textarea>
                <textarea name="custom_mapping_fields" class="hide"></textarea>
                <input type="hidden" name="signedrecord_id" value="<?= $newSignedRecordId; ?>" />

                <div id="content">
                    <!--<div class="actions"></div>-->
                    <div class="document" id="web-view-document"></div>
                    <div id="dummy" style="visibility: hidden; display: inline-block;"></div>
                </div>

                <div id="sidebar">
                    <div class="contents actions">
                        <div id="pdf-action-accept" class="pdf-actions">
                            <a href="javascript:;">
                                <div class="action action-accept inactive">
                                <span class="btn btn-success proposal-btn">
                                    <i title="Accept" class="icon-ok pull-left"></i>
                                    <?= $transactionRecord->get('label_accept'); ?>
                                </span>
                                </div>
                            </a>
                        </div>

                        <div id="pdf-action-decline" class="pdf-actions">
                            <a href="javascript:;">
                                <div class="action action-decline inactive">
                                <span class="btn btn-danger proposal-btn">
                                    <i title="Decline" class="icon-ok pull-left"></i>
                                    <?= $transactionRecord->get('label_decline'); ?>
                                </span>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div id="quoting_tool-sidebar-content" class="contents">
                        <dl>
                            <dd>
                                <a href="#quoting_tool-sidebar-hashtag"
                                   data-toggle="collapse" aria-expanded="false" aria-controls="quoting_tool-sidebar-hashtag">
                                    <strong class="proposal-doc-name"><?= $transactionRecord->get('filename'); ?></strong>
                                    <i class="indicator icon-chevron-down pull-right"></i>
                                </a>
                            </dd>
                        </dl>
                        <ul id="quoting_tool-sidebar-hashtag" class="quoting_tool-sidebar collapse in">
                            <li class="selected">
                                <a href="#">Proposal Summary &amp; Scope</a>
                            </li>
                        </ul>

                        <div id="pdf-download" class="pdf-actions">
                            <a class="pdf-download-link"
                               href="javascript:;">
                                <span>Download PDF</span>
                            </a>
                        </div>

                        <div id="pdf-sign" class="pdf-actions">
<!--                            <a class="pdf-download-link" data-toggle="modal" data-target="#myModal" href="#">-->
                            <a class="pdf-download-link" data-target="#myModal" href="javascript:void(0);">
                                <span>Signature</span>
                            </a>
                        </div>
                    </div>

                    <?php if ($transactionRecord->get('description')): ?>
                        <div class="contents quoting_tool-description">
                            <dl>
                                <dd>
                                    <!--<a href="#quoting_tool-description-content" data-toggle="collapse" aria-expanded="false"
                                       aria-controls="quoting_tool-description-content">-->
                                    <a href="javascript:void(0);">
                                        <strong class="proposal-doc-name">Notes</strong>
                                        <!--<i class="indicator icon-chevron-down pull-right"></i>-->
                                    </a>
                                </dd>
                            </dl>
                            <ul id="quoting_tool-description-content" class="quoting_tool-sidebar collapse in">
                                <li class="selected">
                                    <?= $transactionRecord->get('description') ? nl2br($transactionRecord->get('description')) : ''; ?>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($transactionRecord->get('attachments')): ?>
                        <div class="contents quoting_tool-attachments">
                            <dl>
                                <dd>
                                    <a href="#quoting_tool-sidebar-attachment"
                                       data-toggle="collapse" aria-expanded="false" aria-controls="quoting_tool-sidebar-attachment">
                                        <strong class="proposal-doc-name">Attachments</strong>
                                        <i class="indicator icon-chevron-down pull-right"></i>
                                    </a>
                                </dd>
                            </dl>
                            <ul id="quoting_tool-sidebar-attachment" class="quoting_tool-sidebar collapse in">
                            </ul>
                        </div>
                    <?php endif; ?>

                    <!--Paid Area start-->
                    <?php
                    //pay online
                    $enable_an_pay_online = true;
                    $template_id = $transactionRecord->get('template_id');
                    $resultPay = $adb->pquery("SELECT anwidget FROM vtiger_quotingtool WHERE `id` = ? LIMIT 1", array($template_id));
                    if($adb->num_rows($resultPay)){
                        $an_widget = $adb->query_result($resultPay, 0, 'anwidget');
                        if($an_widget!=1){
                            $enable_an_pay_online = false;
                        }
                    }else{
                        $enable_an_pay_online = false;
                    }
                    if(!file_exists($document_root."modules/ANCustomers/libs/InvoiceWidget/QuotingTool.php")){
                        $enable_an_pay_online = false;
                    }

                    if($enable_an_pay_online):
                        require_once "modules/ANCustomers/libs/InvoiceWidget/QuotingTool.php";
                        $currentYear = (int)date('Y');
                        $anQuotingTool = new ANQuotingTool();
                        $anActive = $anQuotingTool->isANEnable();
                        if($anActive):
                            $anInfo = $anQuotingTool->getANInvoiceInfo($transactionRecord->get('record_id'));
                            if(!empty($anInfo) && $anInfo['accountid']):
                                $payments = $anQuotingTool->getPaymentRecords($transactionRecord->get('record_id'), '*%', '', 'amount_paid', '*Failed');
                                $al_payments_not_paid = $anQuotingTool->getPaymentRecords($transactionRecord->get('record_id'), '*%', '', '', '*Failed');
                                $count_payment = count($payments);
                                $count_of_al_payment_not_paid = count($al_payments_not_paid);
                                $full_amount = 0;
                                $full_amount_value = 'sum';
                                if($count_of_al_payment_not_paid==0){
                                    $full_amount = $anInfo['balance'];
                                    $full_amount_value = 'full';
                                }else{
                                    $full_amount = $anInfo['balance'];
                                    $full_amount_value = 'full';
                                    /*foreach($al_payments_not_paid as $v){
                                        $full_amount += $v['amount_paid'];
                                    }*/
                                }
                                if($full_amount>0){
                                    $full_amount = number_format($full_amount, 2);
                                }
                                ?>
                                <script type="text/javascript">
                                    $( function() {
                                        $('#an-paid-form input[name=an_payment_method]').on('click', function(){
                                            $('#an-paid-form #CreditCardSimpleType').hide();
                                            $('#an-paid-form #BankAccountType').hide();
                                            $('#an-paid-form #'+$(this).val()).slideDown('slow');
                                        });

                                        //show echeck type depend on bank account type
                                        var anForm = $('#an-paid-form');
                                        $('#an_account_type', anForm).on('change', function (event) {
                                            event.preventDefault();
                                            var element = $(this);
                                            var bank_account_type = element.val();
                                            if(bank_account_type==''){
                                                $('#an_e_check_type option', anForm).each(function(){
                                                    if($(this).val()!==''){
                                                        $(this).attr('disabled', true);
                                                    }
                                                    $(this).removeAttr('selected');
                                                });
                                                $('#an_e_check_type', anForm).val('');
                                            }else if(bank_account_type=='businessChecking'){
                                                $('#an_e_check_type option', anForm).each(function(){
                                                    if($(this).val()!=='CCD'){
                                                        $(this).attr('disabled', true);
                                                        $(this).removeAttr('selected');
                                                    }else{
                                                        $(this).removeAttr('disabled');
                                                    }
                                                });
                                                $('#an_e_check_type', anForm).val('CCD');
                                            }else{
                                                $('#an_e_check_type option', anForm).each(function(){
                                                    if($(this).val()=='CCD'){
                                                        $(this).attr('disabled', true);
                                                        $(this).removeAttr('selected');
                                                    }else{
                                                        $(this).removeAttr('disabled');
                                                    }
                                                });
                                                $('#an_e_check_type', anForm).val('');
                                            }
                                        });
                                        $('#an_account_type', anForm).trigger('change');

                                        //hanlde other amount
                                        $('#an_amount', anForm).on('change', function (event) {
                                            event.preventDefault();
                                            var amount = $(this).val();
                                            if(amount=='other'){
                                                $('#other_amount', anForm).removeClass('hide');
                                            }else{
                                                $('#other_amount', anForm).addClass('hide');
                                            }
                                        });

                                        $('#other_amount', anForm).on('change', function (event) {
                                            event.preventDefault();
                                            var other_amount = $(this).val();
                                            if(other_amount==''){
                                                other_amount = 0;
                                            }
                                            other_amount = parseFloat(other_amount);
                                            var full_amount = $('#invoice_balance', anForm).val();
                                            if(full_amount==''){
                                                full_amount = 0;
                                            }
                                            full_amount = parseFloat(full_amount);
                                            if(other_amount > full_amount){
                                                alert('Other Amount should be smaller than or equal to Full Amount!');
                                                $('#other_amount', anForm).val(full_amount.toFixed(2));
                                            }
                                        });

                                        //submit form
                                        $('#an-paid-form #an-paid-btn').on('click', function(event){
                                            var element = $(this);
                                            element.attr('disabled', true);
                                            event.preventDefault();
                                            var container = element.closest('#an-paid-form');
                                            var method = container.find('input[name=an_payment_method]').val();
                                            var is_valid = true;
                                            container.find('#'+method+' input').each(function(){
                                                if($.trim($(this).val())==''){
                                                    is_valid = false;
                                                }
                                            });
                                            container.find('#'+method+' select').each(function(){
                                                if($.trim($(this).val())==''){
                                                    is_valid = false;
                                                }
                                            });

                                            var other_amount = $('#other_amount', anForm).val();
                                            if(other_amount==''){
                                                other_amount = 0;
                                            }
                                            var full_amount = $('#invoice_balance', anForm).val();
                                            if(full_amount==''){
                                                full_amount = 0;
                                            }
                                            if(parseFloat(other_amount) > parseFloat(full_amount)){
                                                alert('Other Amount should be smaller than or equal to Full Amount!');
                                                is_valid = false;
                                                return false;
                                            }

                                            if(!is_valid){
                                                element.removeAttr('disabled');
                                                alert('All field is required.');
                                                return false;
                                            }else{
                                                //if fill data full then submit form
                                                var params = {};
                                                params['invoice_id'] = $('#an-paid-form #an_invoice_id').val();
                                                params['account_id'] = $('#an-paid-form #an_account_id').val();
                                                params['customerprofileid'] = $('#an-paid-form #an_profileid').val();
                                                params['customershippingaddressid'] = $('#an-paid-form #an_shippingaddressid').val();
                                                params['amount'] = $('#an-paid-form #an_amount').val();
                                                params['payment_method'] = method;
                                                params['card_number'] = $('#an-paid-form #an_card_number').val();
                                                params['expiration_month'] = $('#an-paid-form #an_expiration_month').val();
                                                params['expiration_year'] = $('#an-paid-form #an_expiration_year').val();
                                                params['ccv'] = $('#an-paid-form #an_ccv').val();
                                                params['bank_name'] = $('#an-paid-form #an_bank_name').val();
                                                params['account_number'] = $('#an-paid-form #an_account_number').val();
                                                params['name_on_account'] = $('#an-paid-form #an_name_on_account').val();
                                                params['routing_number'] = $('#an-paid-form #an_routing_number').val();
                                                params['account_type'] = $('#an-paid-form #an_account_type').val();
                                                params['e_check_type'] = $('#an-paid-form #an_e_check_type').val();
                                                params['other_amount'] = $('#an-paid-form #other_amount').val();
                                                params['_action'] = 'an_paid';
                                                $.ajax({
                                                    type: "POST",
                                                    url: 'action.php',
                                                    data: params, // serializes the form's elements.
                                                    success: function(data)
                                                    {
                                                        alert(data.result);
                                                        if(data.result.indexOf('Error:') === 0){
                                                            element.removeAttr('disabled');
                                                        }else{
                                                            container.find('input').val('').attr('disabled', true);
                                                            container.find('select').val('').attr('disabled', true);
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    } );
                                </script>
                                <style>
                                    #viewport #sidebar{position: absolute;}
                                    .an-payment-widget{padding: 0;margin: 0 auto;}
                                    .an-payment-widget #an-paid-form{padding: 0 15px 15px 15px;}
                                    .an-payment-widget #paid-form h3{margin-top: 0;}
                                    .an-payment-widget input, textarea, select, .uneditable-input{height: auto !important;}
                                    .an-payment-widget .row-fluid > [class*="span"]{margin-left: 0 !important;}
                                    .an-payment-widget .field-required{color: #FF0000;}
                                    .an-payment-widget .e-check{display: none;}
                                    .an-payment-widget .card-icon{height: 16px;vertical-align: top;}
                                    .an-payment-widget #an_e_check_type option:disabled{background-color: #cccccc;}
                                </style>


                                <div class="container-fluid an-payment-widget contents" style="background-color: #FFFFFF;">
                                    <dl>
                                        <dd>
                                            <a href="#quoting_tool-sidebar-hashtag"
                                               data-toggle="collapse" aria-expanded="false" aria-controls="quoting_tool-sidebar-hashtag">
                                                <strong class="proposal-doc-name">Pay Online</strong>
                                                <i class="indicator icon-chevron-down pull-right"></i>
                                            </a>
                                        </dd>
                                    </dl>
                                    <div id="an-paid-form">
                                        <input type="hidden" id="an_invoice_id" value="<?= $transactionRecord->get('record_id'); ?>" />
                                        <input type="hidden" id="an_account_id" value="<?php echo $anInfo['accountid']?>" />
                                        <input type="hidden" id="an_profileid" value="<?php echo $anInfo['customerprofileid']?>" />
                                        <input type="hidden" id="an_shippingaddressid" value="<?php echo $anInfo['customershippingaddressid']?>" />
                                        <input type="hidden" id="invoice_balance" value="<?php echo $anInfo['balance']?>" />
                                        <div class="row-fluid">
                                            <div class="span12">
                                                <h3 style="text-align: center;"><span>Amount</span>
                                                    <select id="an_amount" class="input-large">
                                                        <option value="<?php echo $full_amount_value;?>">Balance: $<?php echo $full_amount?></option>
                                                        <?php if($count_payment>1):?>
                                                            <option value="" disabled>----</option>
                                                            <?php foreach($payments as $payment):?>
                                                                <option value="<?php echo $payment['paymentid']?>">Partial: $<?php echo $payment['amount_paid_display']?></option>
                                                            <?php endforeach;?>
                                                            <option value="" disabled>----</option>
                                                        <?php endif;?>
                                                        <option value="other">Other (Type in the amount)</option>
                                                    </select>
                                                    <input type="number" id="other_amount" min="0" max="<?php echo $full_amount;?>" value="" class="input-large hide" placeholder="Enter other amount..." style="text-align: center;"/>
                                                </h3>
                                            </div>
                                            <div class="span12">
                                                <input type="radio" name="an_payment_method" value="CreditCardSimpleType" checked />
                                                <span>Credit Card</span>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/visa_curved.png" class="card-icon" title="Visa"/>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/mastercard_curved.png" class="card-icon" title="Master Card"/>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/american_express.png" class="card-icon" title="American Express"/>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/discover_straight.png" class="card-icon" title="Discover"/>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/jcb.png" class="card-icon" title="JCB"/>
                                                <div class="credit-card row-fluid" id="CreditCardSimpleType">
                                                    <div class="span12">
                                                        <label>Card Number<span class="field-required">*</span></label>
                                                        <input type="number" id="an_card_number" value="" class="input-large"/>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Expiration MM/YY<span class="field-required">*</span></label>
                                                        <select class="input-small" id="an_expiration_month">
                                                            <option value="">Month</option>
                                                            <option value="01">01</option>
                                                            <option value="02">02</option>
                                                            <option value="03">03</option>
                                                            <option value="04">04</option>
                                                            <option value="05">05</option>
                                                            <option value="06">06</option>
                                                            <option value="07">07</option>
                                                            <option value="08">08</option>
                                                            <option value="09">09</option>
                                                            <option value="10">10</option>
                                                            <option value="11">11</option>
                                                            <option value="12">12</option>
                                                        </select>
                                                        <select class="input-small" id="an_expiration_year">
                                                            <option value="">Year</option>
                                                            <?php for($i=0; $i<=10; $i++):?>
                                                                <option value="<?php echo $i+$currentYear ?>"><?php echo $i+$currentYear ?></option>
                                                            <?php endfor;?>
                                                        </select>
                                                    </div>
                                                    <div class="span12">
                                                        <label>CVV<span class="field-required">*</span></label>
                                                        <input type="number" id="an_ccv" value="" class="input-large"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <input type="radio" name="an_payment_method" value="BankAccountType" />
                                                <span>e-Check</span>
                                                <img src="<?php echo $site_URL  . '/' . $template_folder; ?>/modules/ANCustomers/resources/img/service_echeck.png" class="card-icon" title="Electronic Check"/>
                                                <div class="e-check row-fluid" id="BankAccountType">
                                                    <div class="span12">
                                                        <label>Bank Name<span class="field-required">*</span></label>
                                                        <input type="text" id="an_bank_name" value="" class="input-large"/>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Account Number<span class="field-required">*</span></label>
                                                        <input type="text" id="an_account_number" value="" class="input-large"/>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Name On Account<span class="field-required">*</span></label>
                                                        <input type="text" id="an_name_on_account" value="" class="input-large"/>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Routing Number<span class="field-required">*</span></label>
                                                        <input type="text" id="an_routing_number" value="" class="input-large"/>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Account Type<span class="field-required">*</span></label>
                                                        <select class="input-large" id="an_account_type">
                                                            <option value="">Select an Option</option>
                                                            <option value="checking">Checking</option>
                                                            <option value="savings">Savings</option>
                                                            <option value="businessChecking" selected>Business Checking</option>
                                                        </select>
                                                    </div>
                                                    <div class="span12">
                                                        <label>Electronic Check Type<span class="field-required">*</span></label>
                                                        <select class="input-large" id="an_e_check_type">
                                                            <option value="">Select an Option</option>
                                                            <option value="CCD" selected>CCD</option>
                                                            <option value="PPD">PPD</option>
                                                            <option value="TEL">TEL</option>
                                                            <option value="WEB">WEB</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <hr />
                                                <button class="btn btn-success" id="an-paid-btn" type="button">Pay</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <!--Paid Area end-->

                </div>

                <div class="clear"></div>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>
<div class="clear"></div>

<!-- Modal -->
<div class="modal fade modal-signature" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     style="display: none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Signature</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="" class="sigPad">
                    <!--<label for="name">Print your name</label>-->
                    <input type="text" name="name" id="name" class="name"
                           value="" placeholder="Print your name">
                    <!--<p class="typeItDesc">Review your signature</p>-->
                    <p class="drawItDesc">Draw your signature</p>
                    <ul class="sigNav">
                        <!--<li class="typeIt"><a href="#type-it" class="current">Type It</a></li>-->
                        <li class="drawIt"><a href="#draw-it">Draw It</a></li>
                        <li class="clearButton"><a href="#clear">Clear</a></li>
                    </ul>
                    <div class="sig sigWrapper">
                        <div class="typed"></div>
                        <canvas class="pad" width="500" height="180"></canvas>
                        <input type="hidden" name="output" class="output">
                    </div>
                    <!--<button type="submit">I accept the terms of this agreement.</button>-->
                </form>
            </div>
            <div class="modal-footer">
                <!--<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>-->
                <button type="button" class="btn btn-primary btn-submit" data-dismiss="modal">Accept and sign</button>
            </div>
        </div>
    </div>
</div>
<!--modal- summit-->
<div id="modal-summited" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Submitted</h4>
            </div>
            <div class="modal-body" style="    text-align: center;color: green;font-size: 15px !important; font-weight: bold;">
                <span>Your information has been received. You can now close this page.</span>
            </div>
        </div>

    </div>
</div>

<script type="text/javascript" src="../../../resources/Connector.js"></script>
<script type="text/javascript" src="js/uitypes.js"></script>
<script type="text/javascript" src="js/index.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var body = $('body');
        var homeLoader = body.loadingIndicator({
            useImage: false,
            showOnInit: false
        }).data("loadingIndicator");
        homeLoader.show();

        // Global
        window.QuotingTool = {};
        // Config
        window.QuotingTool.config = {};
        // model
        window.QuotingTool.model = {};
        // data
        window.QuotingTool.data = {};
        window.QuotingTool.data.idxValues = {};

        var proposalDocument = $('#web-view-document');
        var frmProposal = $('form[name="frm-proposal-content"]');
        var inAction = frmProposal.find('[name="_action"]');
        var inModule = frmProposal.find('[name="module"]');
        var inRecordId = frmProposal.find('[name="record_id"]');
        var inSignature = frmProposal.find('[name="signature"]');
        var inSignatureName = frmProposal.find('[name="signature_name"]');
//        var inSignatureDatetime = frmProposal.find('[name="signature_datetime"]');
        var inStatus = frmProposal.find('[name="status"]');
        var inStatusText = frmProposal.find('[name="status_text"]');
        var inContent = frmProposal.find('[name="content"]');
        var inBackground = frmProposal.find('[name="background"]');
        var inAttachments = frmProposal.find('[name="attachments"]');
        var inCustomMappingFields = frmProposal.find('[name="custom_mapping_fields"]');

        // Date format
        var dateFormat = $('#date_format');
        var hourFormat = $('#hour_format');
        window.QuotingTool.config.date_format = dateFormat.val();
        window.QuotingTool.config.time_format = hourFormat.val();

        //
        window.QuotingTool.model.module = inModule.val();
        window.QuotingTool.model.record_id = inRecordId.val();
        var valCustomMappingFields = inCustomMappingFields.val();
        window.QuotingTool.model.custom_mapping_fields = (valCustomMappingFields) ? JSON.parse(valCustomMappingFields) : {};

        // Init default action request
        QuotingToolProposal.changeAction(QuotingToolProposal.ACTION_SUBMIT, inAction);
        // Init document
        var content = QuotingToolUtils.base64Decode(inContent.val());
        // Prepare before append the content
        content = QuotingToolProposal.interactiveTextfield(content);
        QuotingToolProposal.initDocument(proposalDocument, content);

        // Config to show signature button on right panel
        var signatureContainers = $('.quoting_tool-widget-signature-container');
        var btnSignature = $('#pdf-sign');
        var allowSignature = false;
        btnSignature.hide();

        if (signatureContainers && signatureContainers.length > 0) {
            allowSignature = true;
            btnSignature.show();
        }

        var replaceSignatureButton = proposalDocument.find('[data-target="#myModal"]');
        if (replaceSignatureButton.length > 0) {
            $('[data-target="#myModal"]').not(replaceSignatureButton).parent().hide();
        }

        // var after merge content
        var request = QuotingToolUtils.queryString();
        window.QuotingTool.model.id = request['record'];    // transaction id
        var frmSignature = $('.modal-signature');
        var inName = frmSignature.find('[name="name"]');
        var inOutput = frmSignature.find('[name="output"]');
        var proposalContents = $('.proposal-doc');   // With multi pages
        var signatureModal = $('#myModal');
//        var datepickers = $('.quoting_tool-datetimepicker');
        var hashtagContainer = $('#quoting_tool-sidebar-hashtag');
        var attachmentsContainer = $('#quoting_tool-sidebar-attachment');

        // Signature pad
        var sigPad = $('.sigPad').signaturePad({
            lineTop: 114,
            lineWidth: 1,
            drawOnly: true,
            bgColour: 'transparent'
        });

        var valSignature = inSignature.val();
        var valSignatureName = inSignatureName.val();
//        var valSignatureDatetime = inSignatureDatetime.val();
        // Init default name
        inName.val(valSignatureName);

        // Init signature
        if (valSignature) {
            var dSignature = $('<div/>').html(valSignature).text();
            dSignature = JSON.parse(dSignature);
            sigPad.regenerate(dSignature);
            var signatureImage = sigPad.getSignatureImage();
//            QuotingToolProposal.updateCurrentDatetime(datepickers, new Date(valSignatureDatetime));
            // Update signature & resize this box
            QuotingToolProposal.updateSignature(signatureContainers, valSignatureName, signatureImage);
            QuotingToolProposal.resizeSignatureBox(signatureContainers);
        }

        // Init status
        QuotingToolProposal.initStatus(inStatus.val());
        // Set document background
        QuotingToolProposal.initBackground(body, inBackground.val());
        // Init hash tag (index headings)
        QuotingToolProposal.initHashtags(hashtagContainer, proposalContents, 'h1');
        // Init attachments
        var valAttachments = inAttachments.val();
        if (valAttachments) {
            QuotingToolProposal.initAttachments(attachmentsContainer, JSON.parse(valAttachments));
        }

        // Submit proposal
        $('.action').on('click', function (event) {
            event.preventDefault();

            if (!frmProposal.isValid2()) {
                alert('Fill all fields before submit the form');
                return false;
            }

            if (allowSignature && !inSignature.val()) {
//                var sign = confirm('Do you want to sign this document?');
//                if (sign == true) {
                    $('[data-target]').click();
                    return false;
//                }
            }

            // Show loader indicator
            homeLoader.show();

            // Change action
            QuotingToolProposal.changeAction(QuotingToolProposal.ACTION_SUBMIT, inAction);

            var focusInstance = $(this);
            var status = 0;
            if (focusInstance.hasClass('action-accept') && focusInstance.hasClass('inactive')) {
                status = 1;
            } else if (focusInstance.hasClass('action-decline') && focusInstance.hasClass('inactive')) {
                status = -1;
            }
            inStatus.val(status);
            inStatusText.val(focusInstance.find('.proposal-btn').text().trim());
            // Refresh content input
            proposalContents = $('.proposal-doc').clone();
            proposalContents = QuotingToolProposal.revertInteractiveTextfield(proposalContents);
            var proposalContentsHtml = QuotingToolProposal.extractDocument(proposalContents);
            inContent.val(QuotingToolUtils.base64Encode(proposalContentsHtml));
            // Custom mapping fields
            inCustomMappingFields.val(JSON.stringify(window.QuotingTool.model.custom_mapping_fields));

            // Get values in the first
            var actionParams = {
                type: 'POST',
                url: 'action.php',
                dataType: 'json',
                data: frmProposal.serialize()
            };

            AppConnector.request(actionParams).then(
                function (response) {
                    if (response.success) {
                        var parent = focusInstance.closest('.actions');
                        parent.find('.action').not(focusInstance).addClass('inactive');
                        focusInstance.toggleClass('inactive');
                        $("#modal-summited").modal()
                    } else {
                        alert('error');
                    }

                    homeLoader.hide();
                },
                function (error) {
                    console.log('error =', error);
                });
        });

        $('[data-target]').click(function () {
            var thisFocus = $(this);

            if (!frmProposal.isValid2()) {
                alert('Fill all fields before submit the form');
                return false;
            }

            var targetModal = thisFocus.data('target');
            var modal = $(targetModal);
            modal.modal('show');
            setTimeout(function () {
                var btnClose = modal.find('[data-dismiss="modal"]');
                if (btnClose.length > 0) {
                    $(btnClose[0]).focus();
                }
            }, 200);
        });

        // Sign and submit proposal
        frmSignature.on('click', '.btn-submit', function (event) {
            event.preventDefault();

            var signatureCode = inOutput.val();
            var signatureImage = sigPad.getSignatureImage();
            var signatureName = inName.val();

            QuotingToolProposal.updateSignature(signatureContainers, signatureName, signatureImage);
            QuotingToolProposal.resizeSignatureBox(signatureContainers);
//            QuotingToolProposal.updateCurrentDatetime(datepickers, new Date());

            // Update signature to form
            inSignature.val(signatureCode);
            inSignatureName.val(signatureName);
            // Hide signature modal
            signatureModal.modal('hide');

            // Click to submit
            $('.action.action-accept').trigger('click');

            return false;
        });

        // Download PDF
        $('#pdf-download').on('click', function () {
            if (frmProposal.isValid2()) {
                // Change action
                QuotingToolProposal.changeAction(QuotingToolProposal.ACTION_DOWNLOAD_PDF, inAction);
                // Refresh content input
                proposalContents = $('.proposal-doc').clone();
                proposalContents = QuotingToolProposal.revertInteractiveTextfield(proposalContents);
                var proposalContentsHtml = QuotingToolProposal.extractDocument(proposalContents);
                inContent.val(QuotingToolUtils.base64Encode(proposalContentsHtml));
                frmProposal.submit();
            } else {
                alert('Invalid proposal');
            }
        });

        // Trigger to update value
        frmProposal.on('change', 'input, select', function () {
            var thisFocus = $(this);
            var input = thisFocus;
            var inputValue = input.val();
            var type = input[0].type;
            var mappingVal = inputValue;
            var virtualInfo = thisFocus.data('info');
            if (!virtualInfo) {
                virtualInfo = {};
            }

            switch (type) {
                case 'checkbox':
                    if (input.hasClass('interactive_form_item')) {
                        if (input.prop('checked')) {
                            input.attr('checked', 'checked');
                        } else {
                            input.removeAttr('checked');
                        }
                    } else {
                        var dataVirtualField = thisFocus.data('virtual-field');
                        var isVirtualField = (dataVirtualField) ? dataVirtualField : false;

                        if (isVirtualField) {
                            input = thisFocus.siblings('.interactive_form_item');
                        }

                        var isChecked = thisFocus.prop('checked');
                        mappingVal = (isChecked) ? 1 : 0;
                        if (isChecked) {
//                        input.attr('checked', 'checked');
//                        input.val(inputValue);
//                        input.attr('value', inputValue);
                        } else {
//                        input.removeAttr('checked');
                        }
                        inputValue = (isChecked && virtualInfo['values']) ? virtualInfo['values']['true'] : virtualInfo['values']['false'];
                        input.val(inputValue);
                        input.attr('value', inputValue);
                    }

                    break;
                case 'text':
                    input.attr('value', input.val());
                    break;
                case 'select-one':
//                    input = thisFocus.siblings('.interactive_form_item');
//                    inputValue = thisFocus.val();
//                    input.val(inputValue);
//                    input.attr('value', inputValue);
//                    break;
                case 'select-multiple':
                    input = thisFocus.siblings('.interactive_form_item');
                    inputValue = thisFocus.val();
                    if (type == 'select-multiple' && inputValue) {
                        mappingVal = inputValue.join(' |##| ');
                        inputValue = inputValue.join(', ');
                    }
                    input.val(inputValue);
                    input.attr('value', inputValue);
                    break;
                default:
                    break;
            }

            var info = $(input).data('info');
            if (!info) {
                info = {};
            }
            var fieldId = info['id'];

            if (window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id]
                && window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id][fieldId]) {
                window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id][fieldId]['value'] = mappingVal;
//                inCustomMappingFields.val(JSON.stringify(window.QuotingTool.model.custom_mapping_fields));
            }
        });

        // Toggle sidebar
        $('#sidebar').on('click', '[data-toggle="collapse"]', function () {
            $(this).parent().find("i.indicator")
                .toggleClass('icon-chevron-down icon-chevron-up');
        });

        // Datepicker:
        $('.quoting_tool-datepicker').each(function () {
            var thisFocus = $(this);
            var info = thisFocus.data('info');
            var date_format = info.date_format;

            if (info.current_timestamp) {
                // Default is current timestamp
                var timestamp = new Date();
                var currentDate = AppHelper.formatDate(date_format, timestamp);
                thisFocus.attr({
                    'value': currentDate
                });
            }

            if (info.editable) {
                thisFocus.datepicker({
                    format: date_format,
                    autoclose: true
                });
//                app.registerEventForDateFields();
            }
        });

        $('.ui-timepicker-input').each(function () {
            var thisFocus = $(this);
            var info = thisFocus.data('info');

            if (info.editable) {
                app.registerEventForTimeFields();
            }
        });

        $('.select2').select2();

        // Smooth scroll
        // Add smooth scrolling to all links
        $("#quoting_tool-sidebar-hashtag a").on('click', function(event) {

            // Make sure this.hash has a value before overriding default behavior
            if (this.hash !== "") {
                // Prevent default anchor click behavior
                event.preventDefault();

                // Store hash
                var hash = this.hash;

                // Using jQuery's animate() method to add smooth page scroll
                // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
                $('html, body').animate({
                    scrollTop: $(hash).offset().top
                }, 800, function(){

                    // Add hash (#) to URL when done scrolling (default click behavior)
                    window.location.hash = hash;
                });
            } // End if
        });

        // Delay to hide progress indicator
        setTimeout(function () {
            homeLoader.hide();
        }, 2000);
    });

</script>
</body>
</html>