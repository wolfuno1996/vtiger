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
    <div class="row-fluid widgetQuotingToolContainer">
        <div class="row-fluid">
            <span class="span1">&nbsp;</span>
            {if $TEMPLATE_TOTAL == 0}
                <span class="span10">{vtranslate('LBL_NO_TEMPLATES', $MODULE_NAME)}</span>
                <br>
                <br>
            {else}
                <select name="quotingtool_template" id="lstTemplates" class="span10" size="3">
                    {foreach item=TEMPLATE from=$TEMPLATES name=listview}
                        <option value="{$TEMPLATE->get('id')}">{$TEMPLATE->get('filename')}</option>
                    {/foreach}
                </select>
            {/if}
        </div>
        <div class="row-fluid">
            <span class="span1">&nbsp;</span>
            <ul id="quoting_tool-widget-actions" style="list-style-type: none; margin-left: 16px;">
                {if $TEMPLATE_TOTAL == 0}
                    <li>
                        <a href="index.php?module=QuotingTool&view=Edit&primary_module={$SOURCE_MODULE}"
                           data-action="create_new_template"
                           class="webMnu">
                            <img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-add-template.png"
                                 hspace="5" align="absmiddle" border="0" style="border-radius:3px;">
                            &nbsp;{vtranslate('LBL_CREATE_NEW_TEMPLATE', $MODULE_NAME)}
                        </a>
                    </li>
                {else}
                    <li>
                        <a href="javascript:;"
                           data-action="export"
                           class="webMnu">
                            <img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-pdf.png"
                                 hspace="5" align="absmiddle" border="0" style="border-radius:3px;">
                            &nbsp;{vtranslate('LBL_EXPORT_TO_EXCEL', $MODULE_NAME)}
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;"
                           data-action="send_email"
                           class="webMnu">
                            <img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-mail.png"
                                 hspace="5" align="absmiddle" border="0" style="border-radius:3px;">
                            &nbsp;{vtranslate('LBL_SEND_EMAIL', $MODULE_NAME)}
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;"
                           data-action="preview_and_send_email"
                           class="webMnu">
                            <img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-mail.png"
                                 hspace="5" align="absmiddle" border="0" style="border-radius:3px;">
                            &nbsp;{vtranslate('LBL_PREVIEW_AND_SEND_EMAIL', $MODULE_NAME)}
                        </a>
                    </li>
                    {*<li>*}
                        {*<a href="javascript:;"*}
                           {*data-action="download_with_signature"*}
                           {*class="webMnu">*}
                            {*<img src="layouts/vlayout/modules/QuotingTool/resources/img/icons/widget-download.png"*}
                                 {*hspace="5" align="absmiddle" border="0" style="border-radius:3px;">*}
                            {*&nbsp;{vtranslate('LBL_DOWNLOAD_WITH_SIGNATURE', $MODULE_NAME)}*}
                        {*</a>*}
                    {*</li>*}
                {/if}
            </ul>
        </div>
    </div>
{/strip}