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
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                app.request.post({data: params}).then(
                    function (err, data) {
                        app.helper.hideProgress();

                        if (err === null) {
                            var message = data['message'];
                            if (message != 'Valid License') {
                                jQuery('#error_message').html(message)
                                    .show();
                            } else {
                                document.location.href = "index.php?module=QuotingTool&view=List&mode=step3";
                            }
                        } else {
                            console.log(err);
                        }
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function () {
            app.helper.showProgress();
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';

            app.request.post({data: params}).then(
                function (err, data) {
                    app.helper.hideProgress();

                    if (err === null) {
                        document.location.href = "index.php?module=QuotingTool&view=List";
                    } else {
                        console.log(err);
                    }
                }
            );
        });
    },
    /* For License page - End */
    registerExportTemplate: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var view = app.view();

        if (view == 'List' && module == "QuotingTool") {
            app.helper.showProgress();
            var params = {
                'action': 'ActionAjax',
                'mode': 'getAllRecord',
                'module': module,
            };
            app.request.post({data: params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if (err === null) {
                        var templates = data;
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
        var html = '<div class="modal myModal fade in" style="display: block;" aria-hidden="false">'
            + '<div class="modal-backdrop fade in"></div>'
            + '<div class="modal-dialog modal-lg">'
            + '<div class="modal-content">'
            + '<form class="form-horizontal" action="#" id="exportTemplate">'
            + '<div class="modal-header">'
            + '<div class="clearfix">'
            + '<div class="pull-right">'
            + '<button type="button" class="close" aria-label="Close" data-dismiss="modal">'
            + '<span aria-hidden="true" class="fa fa-close"></span>'
            + '</button>'
            + '</div>'
            + '<h4 class="pull-left">' + app.vtranslate('Export Template') + '</h4>'
            + '</div>'
            + '</div>'
            + '<div class="modal-body" style="overflow-y: auto;">'
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

        // css {'width': '600px'}
        app.helper.showModal(html, {
            'cb': function (data) {
                //// to do
                // if(jQuery('#hierarchyScroll').height() > 300){
                //     app.helper.showVerticalScroll(jQuery('#hierarchyScroll'), {
                //         setHeight: '680px',
                //         autoHideScrollbar: false,
                //     });
                // }
            }
        });
    },
    importTemplate: function () {
        jQuery("#importTemplate").on("click", function (e) {
            e.preventDefault();
            $('#fileupload').click();
        });
        jQuery('#fileupload').fileupload({
            dataType: 'json',
            add: function (e, data) {
                app.helper.showProgress("Importing");
                data.submit();
            },
            done: function (e, data) {
                app.helper.hideProgress();
                var response = data.result;

                if (response.success) {
                    app.helper.showSuccessNotification({
                        'message': response.result.message
                    });

                    setTimeout(function () {
                        window.location.reload();
                    }, 300);
                } else {
                    app.helper.showErrorNotification({
                        'message': response.message
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
            app.helper.showProgress("Importing");
            app.request.post({data: params}).then(
                function (err, data) {
                    if (err === null) {
                        app.helper.hideProgress();
                        var response = data;
                        if (response && response.message == 'Import Template Successful') {
                            app.helper.showSuccessNotification({
                                'message': response.message
                            });
                            setTimeout(function () {
                                window.location.reload();
                            }, 300);
                        }else {
                            app.helper.showErrorNotification({
                                'message': response.message
                            });
                        }

                    }
                }
            );
        })
    },
    /**
     * Function to register events
     */
    registerEvents: function () {
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});

jQuery(document).ready(function() {
    var instance = new QuotingTool_List_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
    instance.registerExportTemplate();
    instance.importTemplate();
    instance.registerEventImportDefaultTemplates();
    // remove dropdown(customize && uninstall) in view list
    var moduleName = app.getModuleName();
    var view = app.getViewName();
    if(moduleName == 'QuotingTool' && view == 'List') {
        $('#appnav').find('.settingsIcon').remove();
    }
});
