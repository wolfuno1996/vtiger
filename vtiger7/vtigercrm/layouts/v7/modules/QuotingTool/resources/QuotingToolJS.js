/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class QuotingToolJS */
Vtiger.Class('QuotingToolJS', {}, {
    MODULE: 'QuotingTool',
    detailViewButtoncontainer: null,

    /**
     * Fn - getSelectedTemplates
     * @returns {*}
     */
    getSelectedTemplates: function () {
        var lstTemplates = jQuery('#lstTemplates');
        var selected = lstTemplates.val();

        if (selected == null || selected.length == 0) {
            alert(app.vtranslate('Please select template'));
            return null;
        }

        if (typeof selected !== 'Array') {
            return selected;
        }

        // When multi select
        var strSelected = '';
        for (var i = 0; i < selected.length; i++) {
            strSelected += selected[i] + '+';
        }

        strSelected = strSelected.substring(0, strSelected.length - 1);

        return strSelected;
    },

    /**
     * Function returns the record id
     */
    getRecordId: function () {
        var record = jQuery('#recordId');
        if (record.length) {
            return record.val();
        }
        return false;
    },

    /**
     * Fn - registerWidgetActions
     */
    registerWidgetActions: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var recordId = thisInstance.getRecordId();

        // Export PDF
        jQuery(document).on('click', '[data-action="export"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                if(app.getViewName() == 'List'){
                    var checkBox =  jQuery('input[class="listViewEntriesCheckBox"]:checked');
                    for(var i=0;i < checkBox.length;i++) {
                        window.open('index.php?module=QuotingTool&action=PDFHandler&mode=export&relmodule='
                            + module + '&record=' + checkBox[i].value + '&template_id=' + templateId, '_blank');
                    }
                }else{
                    document.location.href = 'index.php?module=QuotingTool&action=PDFHandler&mode=export&relmodule='
                        + module + '&record=' + recordId + '&template_id=' + templateId;

                }
            }


        });

        // Send email
        jQuery(document).on('click', '[data-action="send_email"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                templateId = thisInstance.getSelectedTemplates();
            }

            if (templateId) {
                app.helper.showProgress();
                var params = {
                    'module': app.getModuleName,
                    'view': 'SelectEmailFields',
                    'mode': 'send_email',
                    'relmodule': module,
                    'record': recordId,
                    'template_id': templateId
                };

                app.request.post({data: params}).then(
                    function (err, data) {
                        app.helper.hideProgress();

                        if (err === null) {
                            var callBackFunction = function (data) {
                                var form = jQuery('#SendEmailFormStep1');
                                var params = app.validationEngineOptions;
                                params.onValidationComplete = function (form, valid) {
                                    if (valid) {
                                        app.helper.hideModal();
                                        app.helper.showProgress();
                                        var data = form.serializeFormData();
                                        app.request.post({data: data}).then(
                                            function (err, data) {
                                                app.helper.hideProgress();

                                                if (err === null) {
                                                    app.helper.showSuccessNotification({
                                                        'title': data.message
                                                    });
                                                } else {
                                                    app.helper.showErrorNotification({
                                                        'title': data.message
                                                    });
                                                }
                                            }
                                        );

                                        return valid;
                                    }
                                };
                                form.validationEngine(params);

                                form.submit(function (e) {
                                    e.preventDefault();
                                })
                            };

                            // css {'width': '350px'}
                            app.helper.showModal(data, {
                                'cb': function (data) {
                                    // to do
                                    if (typeof callBackFunction == 'function') {
                                        callBackFunction(data);
                                    }
                                }
                            });
                        } else {
                            console.log(err);
                        }
                    }
                );
            }
        });

        // Preview and send email
        jQuery(document).on('click', '[data-action="preview_and_send_email"]', function () {
            var thisFocus = $(this);
            // Priority: 1. current button; 2. select box
            var templateId = thisFocus.data('template');

            if (!templateId) {
                // get all selected templates
                templateId = thisInstance.getSelectedTemplates();
            }

            if (!templateId) {
                // Invalid template id
                return;
            }
            var iscreatenewrecord = thisFocus.data('iscreatenewrecord');
            var childModule = thisFocus.data('childmodule');

            // Show indicator
            app.helper.showProgress();
            if(app.getViewName()=='List'){
                var multiRecordId = [];
                var checkBox =  jQuery('input[class="listViewEntriesCheckBox"]:checked');
                for(var i=0;i < checkBox.length;i++) {
                    multiRecordId.push(checkBox[i].value);
                }
                var params = {
                    // 'type': 'POST',
                    // 'url': 'index.php?module=QuotingTool&view=EmailPreviewTemplate&record=' + recordId + '&template_id=' + templateId
                    // 'dataType': 'html',
                    // 'data': {
                    module: 'QuotingTool',
                    view: 'EmailPreviewTemplate',
                    record: recordId,
                    template_id: templateId,
                    isCreateNewRecord: iscreatenewrecord,
                    childModule: childModule,
                    multiRecord:multiRecordId,
                    // }
                };

            }
            else{
                var params = {
                    // 'type': 'POST',
                    // 'url': 'index.php?module=QuotingTool&view=EmailPreviewTemplate&record=' + recordId + '&template_id=' + templateId
                    // 'dataType': 'html',
                    // 'data': {
                    module: 'QuotingTool',
                    view: 'EmailPreviewTemplate',
                    record: recordId,
                    template_id: templateId,
                    isCreateNewRecord: iscreatenewrecord,
                    childModule: childModule,
                    // }
                };
            }


            app.request.post({data: params}).then(
                function (err, data) {
                    if (err === null) {
                        // css {'width': '796px'}
                        app.helper.hideModal().then(function () {
                            app.helper.showModal(data, {
                                'cb': function (data) {
                                    // console.log('aaaa');
                                    thisInstance.registerEventForEmailPopup();
                                }
                            });
                        });
                    } else {
                        console.log(err);
                    }
                }
            );
        });

        // Download PDF with signature
        jQuery(document).on('click', '[data-action="download_with_signature"]', function () {
            var templateId = thisInstance.getSelectedTemplates();

            if (templateId) {
                document.location.href = 'index.php?module=QuotingTool&action=PDFHandler&mode=download_with_signature&relmodule='
                    + module + '&record=' + recordId + '&template_id=' + templateId;
            }
        });

    },

    registerEventForEmailPopup: function () {
        var thisInstance = this;
        thisInstance.registerEmailTags();

        var formEmail = jQuery('#quotingtool_emailtemplate');
        // console.log('formEmail =', formEmail);
        var inEmailSubject = formEmail.find('#email_subject');
        var inEmailContent = formEmail.find('#email_content');

        // console.log('email_content =', jQuery('#email_content'));

        var editorEmailContent = CKEDITOR.replace('email_content', {
            fullPage: true,
            toolbar: [
                {name: 'clipboard', items: ['Undo', 'Redo']},
                {name: 'tools', items: ['Source', 'Maximize', 'Preview']},
                {
                    name: 'editing',
                    groups: ['find', 'selection', 'spellchecker'],
                    items: ['Find', 'Replace', 'SelectAll', 'Scayt']
                },
                /*'/',*/
                {name: 'styles', items: ['Styles', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                '/',
                {name: 'insert', items: ['Image', 'Table']},
                {name: 'links', items: ['Link', 'Unlink']},
                {
                    name: 'basicstyles',
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    //groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    //items: ['Blockquote', 'CreateDiv', '-', 'BidiLtr', 'BidiRtl']
                    items: ['NumberedList', 'BulletedList', 'Outdent', 'Indent', 'JustifyLeft', 'JustifyCenter',
                        'JustifyRight', 'JustifyBlock', 'BidiLtr', 'BidiRtl']
                },
                {name: 'about', items: ['About']}
            ]
        });

        // When update email content
        formEmail.submit(function (event) {
            event.preventDefault();

            if (formEmail.find('input[type=checkbox]:checked').length == 0) {
                app.helper.showErrorNotification({
                    'title': app.vtranslate('Please select atl east one email')
                });

                return;
            }

            // Show indicator
            app.helper.showProgress();

            inEmailSubject.val(QuotingToolUtils.base64Encode(inEmailSubject.val()));
            inEmailContent.val(QuotingToolUtils.base64Encode(editorEmailContent.getData()));
            var data = formEmail.serializeFormData();


            data.multi_record = JSON.parse(jQuery('input[name="multi_record"]').val());

            app.request.post({data: data}).then(
                function (err, data) {
                    if (err === null) {
                        app.helper.showSuccessNotification({
                            'message': data.message
                        });
                    } else {
                        app.helper.showErrorNotification({
                            'message': data.message
                        });
                    }
                }
            ).done(function () {
                app.helper.hideProgress();
                // Hide modal
                app.helper.hideModal();
            });
        });

        setTimeout(function () {
            // Hide indicator
            app.helper.hideProgress();
        }, 300);
    },

    registerWidgetButtons: function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var view = app.view();
        var record = thisInstance.getRecordId();

        // Add Quoting Tool button
        if (view == 'Detail' && record != undefined) {
            //app.helper.showProgress();
            var params = {
                'action': 'ActionAjax',
                'mode': 'getTemplate',
                'module': thisInstance.MODULE,
                'record': record,
                'rel_module': module
            };
            app.request.post({data: params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if (err === null) {
                        var templates = data;
                        // fix issue: button push other button down
                        var currentDiv = $("#appnav").parent();
                        var currentClass = currentDiv.attr("class");
                        var newClass = currentClass.replace('col-lg-5 col-md-5', 'col-lg-7 col-md-7');
                        currentDiv.attr('class', newClass);
                        var prevDiv = currentDiv.prev();
                        currentClass = prevDiv.attr("class");
                        newClass = currentClass.replace('col-lg-7 col-md-7', 'col-lg-5 col-md-5');
                        prevDiv.attr('class', newClass);

                        var navContainer = jQuery('#appnav ul.nav');
                        var button = jQuery('<li><button class="btn btn-primary module-buttons btn-quoting_tool" style="background-color: #1560bd; color: #ffffff">' +
                            '<div class="fa" aria-hidden="true"></div>&nbsp;&nbsp;' + app.vtranslate('Document Designer') +
                            '</button></li>');
                        if (templates.length > 0) {
                            var firstButton = navContainer.find('li:first');
                            firstButton.before(button);
                            button.on('click', function () {

                                thisInstance.showWidgetModal(templates);
                            });
                        }
                    } else {
                        console.log(err);
                    }
                }
            );
        }
    },

    registerWidgetOptions:function () {
        var thisInstance = this;
        var module = app.getModuleName();
        var view = app.view();
        var record = thisInstance.getRecordId();




        // Add Quoting Tool button
        if (view == 'List' && record != undefined) {

            //app.helper.showProgress();
            var params = {
                'action': 'ActionAjax',
                'mode': 'getTemplate',
                'module': thisInstance.MODULE,
                'record': record,
                'rel_module': module
            };
            app.request.post({data: params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if (err === null) {
                        var templates = data;
                        // fix issue: button push other button down
                        var currentDiv = $(".listViewMassActions").parent();
                        var currentClass = currentDiv.attr("class");
                       // var newClass = currentClass.replace('col-lg-5 col-md-5', 'col-lg-7 col-md-7');
                       // currentDiv.attr('class', newClass);
                       // var prevDiv = currentDiv.prev();
                      //  currentClass = prevDiv.attr("class");
                      //  newClass = currentClass.replace('col-lg-7 col-md-7', 'col-lg-5 col-md-5');
                      //  prevDiv.attr('class', newClass);

                        var navContainer = jQuery('.listViewMassActions ul');
                        var button = jQuery('<li><a href="#">' +
                            '<div class="fa" aria-hidden="true"></div>' + app.vtranslate('Document Designer: PDF/Email') +
                            '</a></li>');
                        if (templates.length > 0) {
                            var firstButton = navContainer.find('li:last');
                            firstButton.after(button);
                            button.on('click', function () {
                                //if(templates)
                                thisInstance.showWidgetModal(templates);

                            });
                        }
                    } else {
                        console.log(err);
                    }
                }
            );
        }
    },

    /**
     * @param templates
     */
    showWidgetModal: function (templates) {
        var html = '<div class="modal myModal fade in" style="display: block;" aria-hidden="false">'
            + '<div class="modal-backdrop fade in"></div>'
            + '<div class="modal-dialog modal-lg">'
            + '<div class="modal-content">'
            + '<form class="form-horizontal" action="index.php">'
            + '<div class="modal-header">'
            + '<div class="clearfix">'
            + '<div class="pull-right">'
            + '<button type="button" class="close" aria-label="Close" data-dismiss="modal">'
            + '<span aria-hidden="true" class="fa fa-close"></span>'
            + '</button>'
            + '</div>'
            + '<h4 class="pull-left">' + app.vtranslate('Document Designer (Email/PDF)') + '</h4>'
            + '</div>'
            + '</div>'
            + '<div class="modal-body" style="overflow-y: auto;">'
            + '<table id="tableQuotingToolWidget">'
            + '<thead>'
            + '<th>' + app.vtranslate('Template Name') + '</th>'
            + '<th class="actions">' + app.vtranslate('PDF') + '</th>'
            + '<th class="actions">' + app.vtranslate('Email') + '</th>'
            + '</thead>'
            + '<tbody>';
        var template = null;
        if (templates && Array.isArray(templates)) {
            var currentModule  = app.getModuleName();
            var isCreateNewRecord = '';
            for (var i = 0; i < templates.length; i++) {
                template = templates[i];
                var moduleTemplate = template.modulename;
                if(template.createnewrecords == 1 && currentModule != moduleTemplate) {
                     isCreateNewRecord = 1;
                }else{
                    isCreateNewRecord = 0;
                }
                html += '<tr>' +
                    '<td>' + template.filename + '</td>' +
                    '<td><a href="javascript:;" data-action="export" data-template="' + template.id + '" >' +
                    '<img src="layouts/v7/modules/QuotingTool/resources/img/icons/widget-pdf.png" /></a></td>' +
                    '<td><a href="javascript:;" data-action="preview_and_send_email" data-template="' + template.id + '" data-childmodule = "' + moduleTemplate + '" data-iscreatenewrecord = "' + isCreateNewRecord + '">' +
                    '<img src="layouts/v7/modules/QuotingTool/resources/img/icons/widget-mail.png" /></td>' +
                    '</tr>';
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

    registerEmailTags: function () {
        var selectTags = jQuery('.select2-tags');

        selectTags.each(function () {
            var focus = jQuery(this);
            var tags = focus.data('tags');
            if (typeof tags === 'undefined' || !tags) {
                tags = [];
            }
            var select2params = {tags: tags/*, tokenSeparators: [',']*/};
            // app.showSelect2ElementView(focus, select2params);
            vtUtils.showSelect2ElementView(focus, select2params);
        });
    },

    iconHelpText:function () {
        jQuery('span.icon-helptext').
    },

    registerEvents: function () {
        var thisInstance = this;
        thisInstance.registerWidgetActions();
        thisInstance.registerWidgetButtons();
        thisInstance.registerWidgetOptions();
        thisInstance.iconHelpText();

    }
});

jQuery(document).ready(function () {
    // Add css to screen dose not move when open dialog
    $("head").append("<style>body.modal-open{padding-right: 0px!important;}</style>");
    // // Fix auto add resizeable to textarea on IE
    // if (jQuery.isFunction(jQuery.fn.resizable)) {
    //     jQuery('#quoting_tool-body').find('textarea.hide')
    //         .resizable('destroy')
    //         .removeAttr('style');
    // }

    var instance = new QuotingToolJS();
    instance.detailViewButtoncontainer = jQuery('.detailViewButtoncontainer');
    instance.registerEvents();





});
