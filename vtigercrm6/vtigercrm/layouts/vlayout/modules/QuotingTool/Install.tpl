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
    <div class="contentsDiv marginLeftZero">
        <div class="padding1per">
            <div class="editContainer" style="padding-left: 3%; padding-right: 3%">
                <br>
                <h3>{vtranslate('LBL_MODULE_NAME', $MODULE)} {vtranslate('LBL_INSTALL', $MODULE)}</h3>
                <hr>
                <form name="install" id="editLicense" method="POST" action="index.php" class="form-horizontal">
                    <input type="hidden" name="module" value="PDFMaker"/>
                    <input type="hidden" name="view" value="List"/>

                    <div id="step1" class="padding1per" style="border:1px solid #ccc;">
                        <input type="hidden" name="installtype" value="download_src"/>

                        <div class="controls">
                            <div>
                                <strong>{vtranslate('LBL_DOWNLOAD_SRC', $MODULE)}</strong>
                            </div>
                            <br>

                            <div class="clearfix">
                            </div>
                        </div>
                        <div class="controls">
                            <div>
                                {vtranslate('LBL_DOWNLOAD_SRC_DESC1', $MODULE)}
                                <br>
                                <input type="url" value="{$PDF_LIB_LINK}" disabled="disabled" class="span8"/>
                                <br>
                                {vtranslate('LBL_DOWNLOAD_SRC_DESC2', $MODULE)}
                                <br>
                                <input type="text" value="{$PDF_LIB_SOURCE}" disabled="disabled" class="span8"/>
                                {if $MB_STRING_EXISTS eq 'false'}
                                    <br>
                                    {vtranslate('LBL_MB_STRING_ERROR', $MODULE)}
                                {/if}
                            </div>
                            <br>

                            <div class="clearfix">
                            </div>
                        </div>
                        <div class="controls">
                            <span style="display: none; color: red;"
                                  class="quoting_tool-processing">{vtranslate('Downloading...', $MODULE)}</span>
                            <br>
                            <button type="button" id="download_button" class="btn btn-success quoting_tool-downloadLib">
                                <strong>{vtranslate('LBL_DOWNLOAD', $MODULE)}</strong>
                            </button>
                            &nbsp;&nbsp;
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
{/strip}