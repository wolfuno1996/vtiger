{*/* * *******************************************************************************
* The content of this file is subject to the Google Address Lookup ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="installationContents" style="border:1px solid #ccc;padding:2%;">
        <form name="EditWorkflow" action="index.php" method="post" id="installation_step3" class="form-horizontal">
            <input type="hidden" class="step" value="3" />

            <div class="row">
            <label>
                <h3>{vtranslate('LBL_INSTALLATION_COMPLETED',$QUALIFIED_MODULE)}</h3>
            </label>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <div>
                    <span>
                        The {vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_HAS_BEEN_SUCCESSFULLY',$QUALIFIED_MODULE)}
                    </span>
                </div>
            </div>
            <div class="row">
                <div>
                    <span>
                        {vtranslate('LBL_MORE_EXTENSIONS',$QUALIFIED_MODULE)} - <a style="color: #0088cc; text-decoration:none;" href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a>
                    </span>
                </div>
            </div>

            <div class="row">
                <div><span>{vtranslate('LBL_FEEL_FREE_CONTACT',$QUALIFIED_MODULE)}</span></div>
            </div>
            <div class="clearfix">&nbsp;</div>
            <div class="row">
                <ul style="padding-left: 10px;">
                    <li>{vtranslate('LBL_EMAIL',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<a style="color: #0088cc; text-decoration:none;" href="mailto:Support@VTExperts.com">Support@VTExperts.com</a></li>
                    <li>{vtranslate('LBL_PHONE',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<span>+1 (818) 495-5557</span></li>
                    <li>{vtranslate('LBL_CHAT',$QUALIFIED_MODULE)}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON',$QUALIFIED_MODULE)} <a style="color: #0088cc; text-decoration:none;" href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a></li>
                </ul>
            </div>

            <div class="row" style="text-align: center;">
                <button class="btn btn-success" name="btnFinish" type="button"><strong>{vtranslate('LBL_FINISH', $QUALIFIED_MODULE)}</strong></button>
            </div>
        <div class="clearfix"></div>
        </form>
    </div>
{/strip}