/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var QuotingToolUitypes = {
    BOOLEAN: {
        uitype: 56,
        datatype: 'boolean',
        template: '<input type="checkbox">'
    },
    PICKLIST: {
        uitype: 16,
        datatype: 'picklist',
        template: '<select class="select2"></select>'
    },
    MULTI_PICKLIST: {
        uitype: 33,
        datatype: 'multipicklist',
        template: '<select multiple class="select2"></select>'
    },
    CURRENCY: {
        uitype: 117,
        datatype: 'currencyList',
        template: '<select class="select2"></select>'
    },

    /**
     *
     * @param focus
     * @param uitype
     */
    convertTemplate: function (focus, uitype) {
        // var thisInstance = this;
        var info = focus.data('info');
        if (!info) {
            info = {};
        }
        var virtualInfo = {};
        virtualInfo = $.extend(virtualInfo, info);
        var value = focus.val();
        focus.hide();
        var template = uitype.template;
        template = $(template);
        template.attr('data-virtual-field', true);

        switch (uitype) {
            case QuotingToolUitypes.BOOLEAN:
                var values = {
                    'true': 'Yes',
                    'false': 'No'
                };

                if (value && (value == 'Yes' || value == 'True' || value == '1')) {
                    template.attr('checked', 'checked');
                }

                if (value == 'True') {
                    values = {
                        'true': 'True',
                        'false': 'False'
                    };
                } else if (value == '1') {
                    values = {
                        'true': '1',
                        'false': '0'
                    };
                }

                virtualInfo['values'] = values;
                template.attr('data-info', JSON.stringify(virtualInfo));

                break;
            case QuotingToolUitypes.MULTI_PICKLIST:
                value = value.split(', ');

                if (!window.QuotingTool.data.idxValues[info.module]) {
                    window.QuotingTool.data.idxValues[info.module] = {};
                }

                var values = window.QuotingTool.data.idxValues[info.module][info.name];

                if (typeof values === 'undefined') {
                    // Get values in the first
                    var actionParams = {
                        type: 'POST',
                        url: 'action.php',
                        dataType: 'json',
                        async: false,
                        data: {
                            _action: 'get_picklist_values',
                            fields: {}
                        }
                    };
                    if (!actionParams.data.fields[info.module]) {
                        actionParams.data.fields[info.module] = {};
                    }
                    actionParams.data.fields[info.module][info.name] = '';

                    AppConnector.request(actionParams).then(
                        function (response) {
                            if (response.success) {
                                var data = response.result;
                                for (var module in data) {
                                    if (!data.hasOwnProperty(module)) {
                                        continue;
                                    }

                                    if (!window.QuotingTool.data.idxValues[module]) {
                                        window.QuotingTool.data.idxValues[module] = {};
                                    }

                                    var fields = data[module];

                                    for (var field in fields) {
                                        if (!fields.hasOwnProperty(field)) {
                                            continue;
                                        }

                                        values = fields[field];
                                        window.QuotingTool.data.idxValues[module][field] = values;
                                        virtualInfo['values'] = values;
                                        template.attr('data-info', JSON.stringify(virtualInfo));

                                        for (var key in values) {
                                            if (!values.hasOwnProperty(key)) {
                                                continue;
                                            }

                                            var fieldName = values[key];
                                            var option = $('<option>', {
                                                value: fieldName,
                                                text: fieldName
                                            });

                                            if (value && $.inArray(fieldName, value) >= 0 ) {
                                                option.attr('selected', 'selected');
                                            }

                                            template.append(option);
                                        }
                                    }
                                }
                            } else {
                                alert('error');
                            }
                        },
                        function (error, err) {
                            console.log('error =', error);
                            alert(error);
                        });
                } else {
                    virtualInfo['values'] = values;
                    template.attr('data-info', JSON.stringify(virtualInfo));

                    for (var key in values) {
                        if (!values.hasOwnProperty(key)) {
                            continue;
                        }

                        var fieldName = values[key];
                        var option = $('<option>', {
                            value: fieldName,
                            text: fieldName
                        });

                        if (value && $.inArray(fieldName, value) >= 0 ) {
                            option.attr('selected', 'selected');
                        }

                        template.append(option);
                    }
                }

                break;
            case QuotingToolUitypes.PICKLIST:
                if (!window.QuotingTool.data.idxValues[info.module]) {
                    window.QuotingTool.data.idxValues[info.module] = {};
                }

                var values = window.QuotingTool.data.idxValues[info.module][info.name];

                if (typeof values === 'undefined') {
                    // Get values in the first
                    var actionParams = {
                        type: 'POST',
                        url: 'action.php',
                        dataType: 'json',
                        async: false,
                        data: {
                            _action: 'get_picklist_values',
                            fields: {}
                        }
                    };
                    if (!actionParams.data.fields[info.module]) {
                        actionParams.data.fields[info.module] = {};
                    }
                    actionParams.data.fields[info.module][info.name] = '';

                    AppConnector.request(actionParams).then(
                        function (response) {
                            if (response.success) {
                                var data = response.result;
                                for (var module in data) {
                                    if (!data.hasOwnProperty(module)) {
                                        continue;
                                    }

                                    if (!window.QuotingTool.data.idxValues[module]) {
                                        window.QuotingTool.data.idxValues[module] = {};
                                    }

                                    var fields = data[module];

                                    for (var field in fields) {
                                        if (!fields.hasOwnProperty(field)) {
                                            continue;
                                        }

                                        values = fields[field];
                                        window.QuotingTool.data.idxValues[module][field] = values;
                                        virtualInfo['values'] = values;
                                        template.attr('data-info', JSON.stringify(virtualInfo));

                                        for (var key in values) {
                                            if (!values.hasOwnProperty(key)) {
                                                continue;
                                            }

                                            var fieldName = values[key];
                                            var option = $('<option>', {
                                                value: fieldName,
                                                text: fieldName
                                            });
                                            if (value == fieldName) {
                                                option.attr('selected', 'selected');
                                            }

                                            template.append(option);
                                        }
                                    }
                                }
                            } else {
                                alert('error');
                            }
                        },
                        function (error, err) {
                            console.log('error =', error);
                            alert(error);
                        });
                } else {
                    virtualInfo['values'] = values;
                    template.attr('data-info', JSON.stringify(virtualInfo));

                    for (var key in values) {
                        if (!values.hasOwnProperty(key)) {
                            continue;
                        }

                        var fieldName = values[key];
                        var option = $('<option>', {
                            value: fieldName,
                            text: fieldName
                        });
                        if (value == fieldName) {
                            option.attr('selected', 'selected');
                        }

                        template.append(option);
                    }
                }

                break;
            case QuotingToolUitypes.CURRENCY:
                if (!window.QuotingTool.data.idxValues[info.module]) {
                    window.QuotingTool.data.idxValues[info.module] = {};
                }

                var values = window.QuotingTool.data.idxValues[info.module][info.name];

                if (typeof values === 'undefined') {
                    // Get values in the first
                    var actionParams = {
                        type: 'POST',
                        url: 'action.php',
                        dataType: 'json',
                        async: false,
                        data: {
                            _action: 'get_currency_values',
                            fields: {}
                        }
                    };
                    if (!actionParams.data.fields[info.module]) {
                        actionParams.data.fields[info.module] = {};
                    }
                    actionParams.data.fields[info.module][info.name] = '';

                    AppConnector.request(actionParams).then(
                        function (response) {
                            if (response.success) {
                                var data = response.result;
                                for (var module in data) {
                                    if (!data.hasOwnProperty(module)) {
                                        continue;
                                    }

                                    if (!window.QuotingTool.data.idxValues[module]) {
                                        window.QuotingTool.data.idxValues[module] = {};
                                    }

                                    var fields = data[module];

                                    for (var field in fields) {
                                        if (!fields.hasOwnProperty(field)) {
                                            continue;
                                        }

                                        values = fields[field];
                                        window.QuotingTool.data.idxValues[module][field] = values;
                                        virtualInfo['values'] = values;
                                        template.attr('data-info', JSON.stringify(virtualInfo));

                                        for (var key in values) {
                                            if (!values.hasOwnProperty(key)) {
                                                continue;
                                            }

                                            var fieldName = values[key];
                                            var option = $('<option>', {
                                                value: fieldName,
                                                text: fieldName
                                            });
                                            if (value == fieldName) {
                                                option.attr('selected', 'selected');
                                            }

                                            template.append(option);
                                        }
                                    }
                                }
                            } else {
                                alert('error');
                            }
                        },
                        function (error, err) {
                            console.log('error =', error);
                            alert(error);
                        });
                } else {
                    virtualInfo['values'] = values;
                    template.attr('data-info', JSON.stringify(virtualInfo));

                    for (var key in values) {
                        if (!values.hasOwnProperty(key)) {
                            continue;
                        }

                        var fieldName = values[key];
                        var option = $('<option>', {
                            value: fieldName,
                            text: fieldName
                        });
                        if (value == fieldName) {
                            option.attr('selected', 'selected');
                        }

                        template.append(option);
                    }
                }

                break;
            default:
                break;
        }

        focus.after(template);
        return focus;
    },

    /**
     *
     * @param focus
     * @param uitype
     */
    revertTemplate: function (focus, uitype) {
        var thisInstance = this;
        var parent = focus.parent();
        var value = null;
        var virtualField = parent.find('[data-virtual-field]');

        if (!virtualField || virtualField.length == 0) {
            return;
        }

        var virtualInfo = virtualField.data('info');

        switch (uitype) {
            // case QuotingToolUitypes.BOOLEAN:
            //     var checked = virtualField.prop('checked');
            //     value = checked ? virtualInfo.values['true'] : virtualInfo.values['false'];
            //     break;
            case QuotingToolUitypes.PICKLIST:
                // value = virtualField.val();
                // Clean select2 UI
                parent.find('.select2-container.select2').remove();
                break;
            case QuotingToolUitypes.MULTI_PICKLIST:
                // value = virtualField.val();
                // if (value) {
                //     value = value.join(', ');
                // }

                // Clean select2 UI
                parent.find('.select2-container.select2').remove();
                break;
            case QuotingToolUitypes.CURRENCY:
                value = focus.val();

                if (window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id][virtualInfo.id]) {
                    window.QuotingTool.model.custom_mapping_fields[window.QuotingTool.model.record_id][virtualInfo.id]['value']
                        = thisInstance.getIdByValue(virtualInfo, value);
                }

                // Clean select2 UI
                parent.find('.select2-container.select2').remove();
                break;
            default:
                break;
        }

        // Remove virtual field
        virtualField.remove();
        // focus.val(value);
        // For save HTML
        // focus.attr('value', value);
        // focus.show();
        // Remove display css inline style
        focus.css({
            'display': ''
        });

        return parent;
    },

    /**
     * Fn - getIdByValue
     * @param info
     * @param value
     * @returns {*}
     */
    getIdByValue: function (info, value) {
        var values = info['values'];

        if (!value) {
            return 0;
        }

        for (var id in values) {
            if (!values.hasOwnProperty(id)) {
                continue;
            }

            if (values[id] == value) {
                return id;
            }
        }

        return 0;
    }

};