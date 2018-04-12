/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class QuotingTool_List_Js */
Vtiger_List_Js("QuotingTool_List_Js", {}, {
    /* For License page - Begin */
    init: function () {
        this.initiate();
    },
    /**
     * Function to initiate the step 1 instance
     */
    initiate: function () {
        var step = jQuery(".installationContents").find('.step').val();
        this.initiateStep(step);
    },
    /**
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep: function (stepVal) {
        var step = 'step' + stepVal;
        this.activateHeader(step);
    },

    activateHeader: function (step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#' + step, headersContainer).addClass('active');
    },

    registerActivateLicenseEvent: function () {
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function () {
            var license_key = jQuery('#license_key');
            if (license_key.val() == '') {
                var errorMsg = "License Key cannot be empty";
                license_key.validationEngine('showPrompt', errorMsg, 'error', 'bottomLeft', true);
                aDeferred.reject();
                return aDeferred.promise();
            } else {
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                AppConnector.request(params).then(
                    function (data) {
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                        if (data.success) {
                            var message = data.result.message;
                            if (message != 'Valid License') {
                                jQuery('#error_message').html(message)
                                    .show();
                            } else {
                                document.location.href = "index.php?module=QuotingTool&view=List&mode=step3";
                            }
                        }
                    },
                    function (error) {
                        console.log('error =', error);
                        progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function () {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';

            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (data.success) {
                        document.location.href = "index.php?module=QuotingTool&view=List";
                    }
                },
                function (error) {
                    console.log('error =', error);
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /* For License page - End */
    registerExportTemplate: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var view = jQuery('[name="view"]').val();
        if (view == 'List' && module == "QuotingTool") {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var params = {
                'action': 'ActionAjax',
                'mode': 'getAllRecord',
                'module': module,
            };
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data){
                        var templates = data.result;
                        var button = jQuery("#exportTemplate");
                        if (templates.length > 0) {
                            button.on('click', function () {
                                thisInstance.showListRecord(templates);
                            });
                        }
                    } else {
                        console.log(err);
                    }
                }
            );
        }
    },
    showListRecord: function (templates) {
        var html = '<div id="modalQuotingToolWidget" class="modal-quotingtool-widget">'
            + '<div class="modal-header">'
            + '<button type="button" class="close" data-dismiss="modal" aria-label="Close">'
            + '<span aria-hidden="true">&times;</span>'
            + '</button>'
            + '<h4 class="modal-title" id="myModalLabel">'+ app.vtranslate('Export Template') +'</h4>'
            + '</div>'
            + '<div class="modal-body">'
            + '<form method="post" action="">'
            + '<table id="tableQuotingToolWidget">'
            + '<thead>'
            + '<th>' + app.vtranslate('Template Name') + '</th>'
            + '<th>' + app.vtranslate('Module') + '</th>'
            + '<th>' + app.vtranslate('Description') + '</th>'
            + '<th class="actions">' + app.vtranslate('Export') + '</th>'
            + '</thead>'
            + '<tbody>';
        var template = null;

        if(templates && Array.isArray(templates)) {
            for (var i = 0; i < templates.length; i++) {
                template = templates[i];

                html += '<tr>' +
                    '<td>' + template.filename + '</td>' +
                    '<td>' + template.module + '</td>' +
                    '<td>' + template.description + '</td>' +
                    '<td><a href="index.php?module=QuotingTool&action=ActionAjax&mode=exportTemplateQuotingTool&idtemplate='+template.id+'">' +
                    '<img src="layouts/v7/modules/QuotingTool/resources/img/icons/widget-download.png" /></a></td>' +
                    '</tr>'
            }
        } else {
            html += templates;
        }

        html += '</tbody>'
            + '</table>'
            + '</div>'
            + '</form>'
            + '</div>'
            + '</div>'
            + '</div>';

        app.showModalWindow(html,'#', function (data) {
        }, {'width': '600px'});
    },
    importTemplate: function () {
        jQuery("#importTemplate").on("click", function (e) {
            e.preventDefault();
            $('#fileupload').click();
        });
        jQuery('#fileupload').fileupload({
            dataType: 'json',
            add: function (e, data) {
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                data.submit();
            },
            done: function (e, data) {
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                var response = data.result;

                if (response.success) {
                    Vtiger_Helper_Js.showMessage({
                        type: 'success',
                        text: response.result.message
                    });

                    setTimeout(function () {
                        window.location.reload();
                    }, 300);
                } else {
                    Vtiger_Helper_Js.showMessage({
                        type: 'error',
                        text: response.message
                    });
                }
            }
        });

    },
    registerEventImportDefaultTemplates: function () {
        $(document).find('#default-templates').on('change', function () {
            var focus = $(this);
            var selectedValue = focus.val();
            var params = {
                module : 'QuotingTool',
                action : 'ActionAjax',
                mode: 'ImportDefaultTemplates',
                selectedValue : selectedValue
            };
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    var response = data.result;
                    console.log(response);
                    console.log(response.message);
                    if (response && response.message == 'Import Template Successful') {
                        Vtiger_Helper_Js.showMessage({
                            type: 'success',
                            text: response.message
                        });
                        setTimeout(function () {
                            window.location.reload();
                        }, 300);
                    }else {
                        Vtiger_Helper_Js.showMessage({
                            type: 'error',
                            text: response.message
                        });
                    }
                }
            );
        })
    },

    /**
     * Function to register events
     */
    registerEvents: function () {
        this._super();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerExportTemplate();
        this.importTemplate();
        this.registerEventImportDefaultTemplates();

    }
});