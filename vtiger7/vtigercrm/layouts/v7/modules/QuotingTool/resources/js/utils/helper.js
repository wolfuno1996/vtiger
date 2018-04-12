/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var AppHelper = {

    MESSAGE_TYPE: {
        SUCCESS: 'success',
        ERROR: 'error',
        INFO: 'info"'
    },
    KCFINDER_FILE_TYPE: {
        IMAGES: 'images',
        FILES: 'files'
    },

    /**
     * @param {String} text
     * @param {String=} type
     * @param {integer=} delay
     * @param {Boolean=} sticker
     * @param {Boolean=} pnotify_history
     */
    showMessage: function (text, type, delay, sticker, pnotify_history) {
        var thisInstance = this;

        var params = {};
        params['message'] = text;

        var settings = {};
        settings['delay'] = (typeof delay === 'undefined') ? '3000' : delay;
        settings['sticker'] = (typeof sticker === 'undefined') ? false : sticker;
        settings['pnotify_history'] = (typeof pnotify_history === 'undefined') ? false : pnotify_history;

        type = (typeof type === 'undefined') ? thisInstance.MESSAGE_TYPE.ERROR : type;
        if (type == thisInstance.MESSAGE_TYPE.ERROR) {
            return app.helper.showErrorNotification(params, settings);
        } else {
            return app.helper.showSuccessNotification(params, settings);
        }
    },

    /**
     * @Copy from /resources/app.js
     *
     * @param data
     * @param url
     * @param cb
     * @param css
     */
    showModalWindow: function (data, url, cb, css, backdrop) {
        var params = {};

        if (typeof url !== 'undefined') {
            params['url'] = url;
        }

        if (typeof cb !== 'undefined') {
            params['cb'] = cb;
        }

        if (typeof css !== 'undefined') {
            params['css'] = css;
        }

        if (typeof backdrop !== 'undefined') {
            params['backdrop'] = backdrop;
            params['outsideclick'] = true;
        }

        app.helper.showModal(data, params);
    },

    /**
     *
     * @param callback
     */
    hideModalWindow: function (callback) {
        app.helper.hideModal();
    },

    /**
     * Integrate KCFinder
     *
     * @link http://kcfinder.sunhater.com/demos/textbox
     *
     * @param type
     * @param options
     * @param callback
     */
    openKCFinder: function (type, options, callback) {
        if (!type) {
            // Default type
            type = this.KCFINDER_FILE_TYPE.IMAGES;
        }

        if (!options) {
            options = {}
        }
        var defaultOptions = {
            status: 0,
            toolbar: 0,
            location: 0,
            menubasr: 0,
            directories: 0,
            resizable: 1,
            scrollbars: 1,
            width: 800,
            height: 600
        };
        $.extend(defaultOptions, options);

        window.KCFinder = {
            callBack: function (url) {
                // Dispose KCFinder
                window.KCFinder = null;

                if (typeof callback == 'function') {
                    callback(url);
                }
            }
        };
        window.open('kcfinder/browse.php?type=' + type, 'kcfinder_textbox', 'status=' + defaultOptions['status'] + ', toolbar=' + defaultOptions['toolbar']
            + ', location=' + defaultOptions['location'] + ', menubasr=' + defaultOptions['menubasr'] + ', directories=' + defaultOptions['directories']
            + ', resizable=' + defaultOptions['resizable'] + ', scrollbars=' + defaultOptions['scrollbars'] + ', width=' + defaultOptions['width']
            + ', height=' + defaultOptions['height']
        );
    },

    /**
     *
     * @param {jQuery} element
     * @returns {{}}
     */
    getAttributes: function (element) {
        if (!element && element.length === 0) {
            return {};
        }

        var injectAttributes = ['data-id', 'placeholder', 'contenteditable', 'tabindex', 'role', 'aria-label', 'aria-describedby'];
        var attributes = {};
        var jsElement = element[0];
        var attrs = jsElement.attributes;
        var attr = null;
        var attrName = null;

        for (var i = 0; i < attrs.length; i++) {
            attr = attrs[i];
            attrName = attr.nodeName;
            if (injectAttributes.indexOf(attrName) >= 0) {
                continue;
            }

            attributes[attr.nodeName] = attr.nodeValue;
        }

        return attributes;
    },

    /**
     *
     * @param {jQuery} element
     * @returns {String}
     */
    getAttributesString: function (element) {
        var thisInstance = this;
        var attributes = thisInstance.getAttributes(element);
        var attrs = '';

        for (var k in attributes) {
            if (attributes.hasOwnProperty(k))
                attrs += ' ' + k + '="' + attributes[k] + '"';
        }

        return attrs;
    },

    /**
     * Fn - getContentFromHtml
     * @param html
     * @returns {string}
     */
    getContentFromHtml: function (html) {
        var thisInstance = this;
        var data = '';
        html = $(html);
        var tmp = $('#quoting_tool-tmp-content');
        var quotingtoolContent = $('.quoting_tool-content:not(.quoting_tool-cover-page)');
        // Fix tmp size by document content size
        tmp.css({
            'width': quotingtoolContent.width()
        });

        var blockContent = html.find('.content-container.block-handle');

        for (var j = 0; j < blockContent.length; j++) {
            var block = $(blockContent[j]);
            var tmpBlock = block.clone();
            var bound = 0;

            var widgets = tmpBlock.find('.content-container.quoting_tool-draggable');
            if (widgets.length > 0) {
                widgets.remove();
                tmp.empty()
                    .html(tmpBlock[0].outerHTML);

                /**
                 * @link http://stackoverflow.com/questions/10787782/full-height-of-a-html-element-div-including-border-padding-and-margin
                 */
                bound = tmp.outerHeight(true); // gives with margins.
                block.css({
                    'height': bound - 2 /*with border = 1*/
                });
                var table = jQuery(block[0]).find('table');
                if(table.attr("border")== 0) {
                    table.find('td').css('border', '1px solid rgba(255,255,255,0)');
                    // table.find('.tr-table').css('line-height', '16px');
                }
                 jQuery(block[0].firstElementChild.firstElementChild).css({
                    'height': bound - 2 /*with border = 1*/
                })
            }

            var attributes = thisInstance.getAttributesString(block);
            data += '<div ' + attributes + '>';

            var contents = block.find('.content-editable');

            if (contents.length > 0) {
                for (var i = 0; i < contents.length; i++) {
                    var content = contents[i];
                    data += content.innerHTML;
                }
            }

            data += '</div>';
        }

        // Clear tmp
        tmp.empty();

        return data;
    },

    /**
     * Fn - customFocus
     * @param editor - {CKEDITOR}
     */
    customFocus: function (editor) {
        editor.on('focus', function (event) {
            var outer = $(event.target).closest('.content-container');
            outer.addClass('focus-contenteditable');
        });

        // Blur
        editor.on('blur', function (event) {
            var outer = $(event.target).closest('.content-container');
            outer.removeClass('focus-contenteditable');
        });
    },

    /**
     * Fn - customKeyPress
     * @param editor - {CKEDITOR}
     */
    customKeyPress: function (editor) {
        // keydown event in ckeditor
        editor.on('keydown', function (event) {
            var key = event.which;
            var mEditor = editor.editor;
            var root = (mEditor.editable ? mEditor.editable() : (mEditor.mode == 'wysiwyg' ? mEditor.document && mEditor.document.getBody() : mEditor.textarea  ) );
            var firstElement = null;

            if (mEditor.mode == 'wysiwyg') {
                // If the blur is due to a dialog, don't apply the placeholder
                if (CKEDITOR.dialog._.currentTop)
                    return;

                if (!root)
                    return;

                firstElement = root.getFirst();
                firstElement = $(firstElement.$);
            }

            // Focus node
            var focusNode = $(window.getSelection().focusNode);

            // // escape key maps to keycode '27'
            // if (key == 27) {
            //     thisFocus.blur();
            // }

            // Prevent remove the heading tag
            if (key == 8 || key == 46) {
                if ((focusNode && firstElement && focusNode.is(firstElement)) || focusNode.hasClass('quoting_tool-cke-keep-element')) {
                    // Stop
                    event.preventDefault();
                    return false;
                }
            }

        });

    },

    /**
     * Fn -  
     * @param data
     */
    clearOverlayModal: function (data) {
        // console.log('data =', data);
        var blockMsg = data.find('.modal-dialog');
        var blockOverlay = data.find('.modal-backdrop');

        if (blockOverlay.length > 0) {
            // Remove overlay frame
            blockOverlay.remove();
            // blockOverlay.hide();
        }

        // // Add border for popup when remove overlay frame
        // data.css({
        //     'border': '1px solid #c3c3c3',
        //     'box-shadow': '0 0 8px rgba(0, 0, 0, 0.07), 0 0 0 1px rgba(0, 0, 0, 0.06)',
        //     'background-color': '#FFFFFF'
        // });
        // Make draggable
        blockMsg.draggable({
            handle: ".modal-header",
            cursor: 'move',
        });

    },

    formatDate: function (dateFormat, dateObject) {
        return app.getDateInVtigerFormat(dateFormat, dateObject);
    },

    /**
     * Fn - resizeable
     * Allow the components resizeable
     *
     * @param container
     */
    resizeable: function (container, options) {
        if (typeof options === 'undefined') {
            options = {};
        }

        var resizeableOptions = {
            // handles: 'e',
            resize: function (event, ui) {
                var focusElement = $(ui.element[0]);
                var width = parseInt(focusElement.css('width'));
                var height = parseInt(focusElement.css('height'));
                // Change object style
                focusElement.find('.quoting_tool-draggable-object')
                    .css({
                        'width': ((width >= 26) ? width : 26), // Fix checkbox block on PDF
                        'height': height
                    })
                    .find('input:not([type="checkbox"]), select, button')
                    .css({
                        'width': width - 17,
                        'height': height - 14,
                        'line-height' : (height - 14)+"px",
                        // 'font-size': height * 0.68 /* = (68 / 100) = 68% */
                    });

                focusElement.find('.quoting_tool-widget-signature-image').css({
                    'width': '100%'
                });
            },
            resizestop : function(event,ui) {
                var focusElement = $(ui.element[0]);
                $rootScope.calculateWidgetPosition(focusElement);
            }
        };

        // Merge object2 into object1, recursively
        $.extend( true, resizeableOptions, options );

        // Example: 16/9 or 4/3
        var aspectRatio = container.data('aspect-ratio');

        if (typeof aspectRatio !== 'undefined') {
            resizeableOptions['aspectRatio'] = aspectRatio;
        }

        container.resizable(resizeableOptions);
    },

    /**
     * @Copy from layouts/v7/modules/Reports/resources/Edit2.js
     * Function which will get the selected columns with order preserved
     * @return {Array} : array of selected values in order
     */
    getSelectedColumns: function (columnListSelectElement) {
        var select2Element = app.getSelect2ElementFromSelect(columnListSelectElement);

        var selectedValuesByOrder = [];
        var selectedOptions = columnListSelectElement.find('option:selected');

        var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
        orderedSelect2Options.each(function (index, element) {
            var chosenOption = jQuery(element);
            var choiceElement = chosenOption.closest('.select2-search-choice');
            var choiceValue = choiceElement.data('select2Data').id;
            selectedOptions.each(function (optionIndex, domOption) {
                var option = jQuery(domOption);
                if (option.val() == choiceValue) {
                    selectedValuesByOrder.push(option.val());
                    return false;
                }
            });
        });
        return selectedValuesByOrder;
    },

    /**
     * Function which will arrange the selected element choices in order
     * @copy from layouts/v7/modules/Settings/MenuEditor/resources/MenuEditor.js:75
     * @param {jQuery} selectElement
     * @param {Array} selectedOrder
     */
    arrangeSelectChoicesInOrder: function (selectElement, selectedOrder) {
        var select2Element = app.getSelect2ElementFromSelect(selectElement);

        var choicesContainer = select2Element.find('ul.select2-choices');
        var choicesList = choicesContainer.find('li.select2-search-choice');
        var selectedOptions = selectElement.find('option:selected');
        for (var index = selectedOrder.length; index > 0; index--) {
            var selectedValue = selectedOrder[index - 1];
            var option = selectedOptions.filter('[value="' + selectedValue + '"]');
            choicesList.each(function (choiceListIndex, element) {
                var liElement = jQuery(element);
                if (liElement.find('div').html() == option.html()) {
                    choicesContainer.prepend(liElement);
                    return false;
                }
            });
        }
    },

    /**
     * @link resources/app.js:728
     */
    getLanguageString: function () {
        var languages = {};

        var strings = jQuery('#js_strings').text();
        if(strings != '') {
            languages = JSON.parse(strings);
        }

        return languages;
    },

    isHidden : function(element) {
        return element.css('display') == 'none';
    },
    /**
     * Get value by key or get all
     * Clone from _request function on above
     *
     * @param {String=} key
     * @param {String=} url
     * @returns {*}
     */
    getRequestParam: function (key, url) {
        var params = {};
        if(typeof url === 'undefined' ){
            url = window.location.href;
        }
        params.url = url;
        params.data = {};

        if(typeof params.url != 'undefined' && params.url.indexOf('?')!== -1) {
            var urlSplit = params.url.split('?');
            var queryString = urlSplit[1];
            params.url = urlSplit[0];
            var queryParameters = queryString.split('&');
            for(var index=0; index<queryParameters.length; index++) {
                var queryParam = queryParameters[index];
                var queryParamComponents = queryParam.split('=');
                params.data[queryParamComponents[0]] = queryParamComponents[1];
            }
        }

        if (typeof key !== 'undefined') {
            return params.data[key];
        }

        // Default return all params (Object)
        return params.data;
    }

};