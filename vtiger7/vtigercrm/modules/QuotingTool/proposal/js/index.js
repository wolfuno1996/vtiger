/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var QuotingToolProposal = {
    ACTION_SUBMIT: 'submit',
    ACTION_DOWNLOAD_PDF: 'download_pdf',

    /**
     *
     * @param {Object} container
     */
    resizeSignatureBox: function (container) {
        var signatureName = container.find('.quoting_tool-guest-signature');
        var signatureNameWidth = signatureName.width();
        signatureName.closest('.quoting_tool-guest-signature-box').css({
            'width': signatureNameWidth + 8
        });
    },

    /**
     *
     * @param {Object} container
     * @param {Object} pdfDocument
     * @param {String} headingTypes - List tags which separate by comma "," character: h1, h2, h3, h4, h5, h6
     */
    initHashtags: function (container, pdfDocument, headingTypes) {
        var hashtags = '';
        var tags = pdfDocument.find(headingTypes);
        var tag = null;
        var hashId = null;
        var timestamp = Date.now();
        var info = null;
        var indexing = false;

        if (tags.length > 0) {
            for (var i = 0; i < tags.length; i++) {
                tag = $(tags[i]);
                info = tag.data('info');
                if (typeof info === 'undefined') {
                    info = {};
                }

                hashId = timestamp + '' + i;
                tag.attr('id', hashId);
                indexing = (info['indexing']) ? info['indexing'] : false;

                if (indexing)
                    hashtags += '<li><a href="#' + hashId + '">' + tag.text() + '</a></li>';
            }
        }

        container.html(hashtags);
    },

    /**
     *
     * @param {jQuery} container
     * @param {Array} attachments
     */
    initAttachments: function (container, attachments) {
        var attachment = null;
        var html = '';

        for (var k in attachments) {
            if (attachments.hasOwnProperty(k)) {
                attachment = attachments[k];
                html += '<li>';
                html += '    <a href="' + attachment['full_path'] + '">' + attachment['name'] + '</a>';
                html += '</li>';
            }
        }

        container.append(html);
    },

    // /**
    //  * Fn - updateCurrentDate
    //  * @param element
    //  * @param {Date} date
    //  */
    // updateCurrentDatetime: function (element, date) {
    //     if (typeof date == 'undefined') {
    //         date = new Date();
    //     }
    //
    //     var format = 'mm/dd/yyyy HH:ii';    // Default format
    //     var value = '';
    //
    //     for (var i = 0; i < element.length; i++) {
    //         var elm = $(element[i]);
    //         if (elm.data('datetime-format')) {
    //             format = elm.data('datetime-format');
    //         }
    //
    //         elm.attr('value', QuotingToolUtils.formatDate2(date, format));
    //     }
    // },

    /**
     *
     * @param {Object} container
     * @param {String} name
     * @param {String} image
     */
    updateSignature: function (container, name, image) {
        this.updateSignatureName(container, name);
        this.updateSignatureImage(container, image);
    },

    /**
     *
     * @param {Object} container
     * @param {String} name
     */
    updateSignatureName: function (container, name) {
//        container.find('.quoting_tool-widget-signature-image').html(name);
        $.each($('.quoting_tool-widget-signature-container'), function () {
            container = $(this);
            container.find('.quoting_tool-widget-signature').html(name);
        });
    },

    /**
     *
     * @param {Object} container
     * @param {String} image
     */
    updateSignatureImage: function (container, image) {
//        container.find('.quoting_tool-guest-signature-image').attr('src', image);

        $.each($('.quoting_tool-widget-signature-container'), function () {
            container = $(this);
            container.find('.quoting_tool-widget-signature-image').attr('src', image);
        });
    },

    /**
     *
     * @param {Number} status - value: -1/0/1
     */
    initStatus: function (status) {
        var actions = $('.actions');
        if (status == 1) {
            actions.find('.action.action-accept').removeClass('inactive');
        } else if (status == -1) {
            actions.find('.action.action-decline').removeClass('inactive');
        }
    },

    /**
     * Fn - initBackground
     * @param {Object} container
     * @param {String} background - URL
     */
    initBackground: function (container, background) {
        if (background) {
            background = JSON.parse(background);
            var cssBackground = {
                'background-attachment': 'fixed'
            };

            if (background['image']) {
                cssBackground['background-image'] = 'url(' + background['image'] + ')';
            }

            if (background['size']) {
                cssBackground['background-size'] = background['size'];
            }

            container.css(cssBackground);
        }
    },

    /**
     *
     * @param {jQuery} container
     * @param {jQuery} pdfDocument
     */
    initDocument: function (container, pdfDocument) {
        pdfDocument = $('<div class="proposal-doc"/>').append(pdfDocument);
        // Remove the mark elements
        pdfDocument.find('.removed-element').remove();

        var html = pdfDocument[0].outerHTML;
        // var breakpage = pdfDocument.find('.pagebreak');
        // var breakpageBak = null;
        // var current = null;
        // var currentHtml = '';
        // var replaceBy = null;
        //
        // if (breakpage.length > 0) {
        //     for (var i = 0; i < breakpage.length; i++) {
        //         current = $(breakpage).closest('.content-container');
        //         currentHtml = current[0].outerHTML;
        //         breakpageBak = QuotingToolUtils.base64Encode(currentHtml);
        //         replaceBy = '</div><div class="proposal-doc" data-breakpage="' + breakpageBak + '">';
        //         html = html.replace(currentHtml, replaceBy);
        //     }
        // }

        container.html(html);
    },

    /**
     * Fn - interactiveTextfield
     * @param {jQuery} pdfDocument
     */
    interactiveTextfield: function (pdfDocument) {
        // var thisInstance = this;

        pdfDocument = $('<div/>').html(pdfDocument);
        var inputs = pdfDocument.find('input');
        var input = null;
        var parent = null;
        var val = null;
        var mappingVal = null;
        var info = null;
        var dummy = $('#dummy');
        var dummyWidth = 0;
        var objContainer = null;

        for (var i = 0; i < inputs.length; i++) {
            input = $(inputs[i]);
            parent = input.parent();
            val = input.val();
            dummy.text(val);
            info = input.data('info');

            if (typeof info === 'undefined') {
                info = {};
            }

            if (!info['editable'] && input[0].type != 'checkbox') {
                // Resize text field by text width
                dummyWidth = dummy.width() + 10;
                objContainer = input.closest('.quoting_tool-draggable-object');
                objContainer.css({
                    // 'width': ((dummyWidth >= 26) ? dummyWidth : 26) // Fix checkbox block on PDF
                });

                input.css({
                    'border': 'none',
                    'box-shadow': 'none',
                    // 'width': dummyWidth
                });
                input.attr('readonly', 'readonly');

                // Readonly on checkbox
                /** @link http://stackoverflow.com/questions/155291/can-html-checkboxes-be-set-to-readonly#answer-6905050 */
                if (input[0].type == 'checkbox') {
                    input.attr('onclick', 'return false;');
                }

                continue;
            }

            var fieldDatatype = info['datatype'];
            var fieldId = info['id'];
            var fieldName = info['name'];
            var fieldModuleName = info['module'];
            var required = info['required'];

            if (fieldName && fieldDatatype) {
                if (fieldDatatype == QuotingToolUitypes.MULTI_PICKLIST.datatype) {
                    QuotingToolUitypes.convertTemplate(input, QuotingToolUitypes.MULTI_PICKLIST);
                } 
                else if (fieldDatatype == 'date') {
                    if (!info['date_format']) {
                        info['date_format'] = window.QuotingTool.config.date_format;
                    }

                    // Prepare for datepicker
                    input.attr('data-info', JSON.stringify(info));
                    input.addAttributes({
                        'data-date-format': window.QuotingTool.config.date_format
                    });
                    input.addClasses(['quoting_tool-datepicker', 'dateField']);

                    //
                    parent.addClasses(['date']);
                } 
                else if (fieldDatatype == 'time') {
                    if (!info['time_format']) {
                        info['time_format'] = window.QuotingTool.config.time_format;
                    }

                    // Prepare for timepicker
                    input.attr('data-info', JSON.stringify(info));
                    input.attr('data-format', window.QuotingTool.config.time_format);
                    input.addClasses(['timepicker-default', 'ui-timepicker-input']);
                    // input.after('<span class="add-on cursorPointer"><i class="icon-time"></i></span>');

                    //
                    parent.addClasses(['input-append', 'time']);
                } 
                else if (fieldDatatype == QuotingToolUitypes.PICKLIST.datatype) {
                    QuotingToolUitypes.convertTemplate(input, QuotingToolUitypes.PICKLIST);
                } 
                else if (fieldDatatype == QuotingToolUitypes.BOOLEAN.datatype) {
                    QuotingToolUitypes.convertTemplate(input, QuotingToolUitypes.BOOLEAN);
                } 
                else if (fieldDatatype == QuotingToolUitypes.CURRENCY.datatype) {
                    QuotingToolUitypes.convertTemplate(input, QuotingToolUitypes.CURRENCY);
                }

                // TODO: init custom mapping fields in here...
                if (fieldModuleName == window.QuotingTool.model.module) {
                    if (!window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id]) {
                        window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id] = {}
                    }

                    if (fieldDatatype == QuotingToolUitypes.MULTI_PICKLIST.datatype) {
                        mappingVal = val.split(', ');
                        if (mappingVal) {
                            val = mappingVal.join(' |##| ');
                        }
                    } else if (fieldDatatype == QuotingToolUitypes.BOOLEAN.datatype) {
                        val = (val == 'Yes' || val == 'On' || val == 'True' || val == '1') ? 1 : 0;
                    }

                    window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id][fieldId] = {
                        'id': fieldId,
                        'value': val,
                        'name': fieldName,
                        'module': fieldModuleName,
                        'datatype': fieldDatatype
                    };
                }
            }

            // Mandatory fields
            if (required) {
                input.attr({
                    'required': 'required'
                });
            }
        }

        return pdfDocument.html();
    },

    /**
     * Fn - revertInteractiveTextfield
     * @param {jQuery} pdfDocument
     */
    revertInteractiveTextfield: function (pdfDocument) {
        // var thisInstance = this;

        var inputs = pdfDocument.find('input');
        var input = null;
        var parent = null;
        var val = null;
        var info = null;
        // var objContainer = null;

        for (var i = 0; i < inputs.length; i++) {
            input = $(inputs[i]);
            parent = input.parent();
            val = input.val();
            info = input.data('info');

            if (typeof info === 'undefined') {
                info = {};
            }

            var fieldDatatype = info['datatype'];
            var fieldName = info['name'];

            if (fieldName && fieldDatatype) {
                if (fieldDatatype == QuotingToolUitypes.BOOLEAN.datatype) {
                    QuotingToolUitypes.revertTemplate(input, QuotingToolUitypes.BOOLEAN);
                } else if (fieldDatatype == QuotingToolUitypes.PICKLIST.datatype) {
                    QuotingToolUitypes.revertTemplate(input, QuotingToolUitypes.PICKLIST);
                } else if (fieldDatatype == QuotingToolUitypes.MULTI_PICKLIST.datatype) {
                    QuotingToolUitypes.revertTemplate(input, QuotingToolUitypes.MULTI_PICKLIST);
                } else if (fieldDatatype == QuotingToolUitypes.CURRENCY.datatype) {
                    QuotingToolUitypes.revertTemplate(input, QuotingToolUitypes.CURRENCY);
                }
            }
        }

        return pdfDocument;
    },

    /**
     * Fn - extractDocument
     *
     * @param {jQuery} pdfDocuments
     * @returns {string}
     */
    extractDocument: function (pdfDocuments) {
        pdfDocuments = $(pdfDocuments);
        
        var proposalContentsHtml = '';
        var pContent = null;
        var breakpageBase64 = null;
        for (var i = 0; i < pdfDocuments.length; i++) {
            pContent = $(pdfDocuments[i]);
            breakpageBase64 = pContent.attr('data-breakpage');

            if (typeof breakpageBase64 !== 'undefined') {
                proposalContentsHtml += QuotingToolUtils.base64Decode(breakpageBase64)
            }
            proposalContentsHtml += pContent.html();    // innerHtml
        }

        return proposalContentsHtml;
    },

    /**
     *
     * @param {String} action
     * @param input
     */
    changeAction: function (action, input) {
        input.val(action);
    }

};