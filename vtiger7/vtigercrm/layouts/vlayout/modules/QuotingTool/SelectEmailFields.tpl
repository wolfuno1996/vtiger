{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div id="sendEmailContainer" class="modelContainer">
        <div class="modal-header contentsBackground">
            <button data-dismiss="modal" class="close" title="{vtranslate('LBL_CLOSE')}">&times;</button>
            <h3>{vtranslate('LBL_SELECT_EMAIL_IDS', $MODULE)}</h3>
        </div>
        <form class="form-horizontal" id="SendEmailFormStep1" method="post" action="index.php">
            <input type="hidden" name="module" value="QuotingTool"/>
            <input type="hidden" name="action" value="PDFHandler"/>
            <input type="hidden" name="mode" value="send_email"/>
            <input type="hidden" name="relmodule" value="{$RELMODULE}"/>
            <input type="hidden" name="record" value="{$RECORDID}"/>
            <input type="hidden" name="template_id" value='{$TEMPLATEID}'/>
            <div class='padding20'>
                <h4>{vtranslate('LBL_MUTIPLE_EMAIL_SELECT_ONE', $SOURCE_MODULE)}</h4>
            </div>
            <div id="multiEmailContainer">
                <div class='padding20'>
                    {if $EMAIL_FIELD_LIST}
                        {foreach item=EMAIL_FIELD_LABEL key=EMAIL_FIELD_NAME from=$EMAIL_FIELD_LIST name=emailFieldIterator}
                            <div class="control-group">
                                <label class="radio">
                                    <input type="radio" class="emailField" name="selectedEmail"
                                           value='{$EMAIL_FIELD_NAME}' {if $smarty.foreach.emailFieldIterator.iteration eq 1} checked="checked" {/if}/>
                                    &nbsp; {$EMAIL_FIELD_LABEL}
                                </label>
                            </div>
                        {/foreach}
                    {else}
                        Does not have any email to select.
                    {/if}
                </div>
            </div>
            <div class='modal-footer'>
                <div class=" pull-right cancelLinkContainer">
                    <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
                <button class="btn addButton" type="submit" name="selectfield">
                    <strong>{vtranslate('LBL_SEND', $MODULE)}</strong></button>
            </div>
            {if $RELATED_LOAD eq true}
                <input type="hidden" name="relatedLoad" value={$RELATED_LOAD}/>
            {/if}
        </form>
    </div>
{/strip}