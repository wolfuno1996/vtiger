/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

(function () {
    'use strict';

    var directives = angular.module('AppDirectives');

    /**
     * select2 directive
     *
     * @link http://stackoverflow.com/questions/29644310/angularjs-custom-select2-directive
     */
    directives.directive('select2', function ($timeout, $parse) {
        return {
            restrict: 'AC',
            require: 'ngModel',
            link: function (scope, element, attributes) {
                $timeout(function () {
                    element.select2();
                    element.select2Initialized = true;

                    // // Fix z-index
                    // var mask = $('.blockUI.blockMsg');
                    // if (mask && mask.length > 0) {
                    //     var maskZIndex = mask.css('z-index');
                    //     maskZIndex = parseInt(maskZIndex);
                    //     var select2Drop = $('.select2-drop');
                    //     select2Drop.css('z-index', maskZIndex + 1);
                    // }
                });

                var refreshSelect = function () {
                    if (!element.select2Initialized) return;
                    $timeout(function () {
                        element.trigger('change');
                    });
                };

                var recreateSelect = function () {
                    if (!element.select2Initialized) return;
                    $timeout(function () {
                        element.select2('destroy');
                        element.select2();
                    });
                };

                scope.$watch(attributes.ngModel, refreshSelect);

                if (attributes.ngOptions) {
                    var list = attributes.ngOptions.match(/ in ([^ ]*)/)[1];
                    // watch for option list change
                    scope.$watch(list, recreateSelect);
                }

                if (attributes.ngDisabled) {
                    scope.$watch(attributes.ngDisabled, refreshSelect);
                }
            }
        };
    });

    /**
     * vtiger-select2 directive
     *
     * @link http://stackoverflow.com/questions/29644310/angularjs-custom-select2-directive
     */
    directives.directive('vtigerSelect2', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                $timeout(function () {
                    var selectElement = vtUtils.showSelect2ElementView(element);
                    var selectedInput = $('<input type="hidden" id="settings_pricing_table_selected_fields">');
                    element.after(selectedInput);

                    var info = element.attr('vtiger-select2');
                    if (info) {
                        info = JSON.parse(info);
                    } else {
                        info = {};
                    }

                    if (info['sortable']) {
                        var select2Element = app.getSelect2ElementFromSelect(selectElement);
                        var select2ChoiceElement = select2Element.find('ul.select2-choices');
                        selectedInput = $('#settings_pricing_table_selected_fields');

                        select2ChoiceElement.sortable({
                            'containment': select2ChoiceElement,
                            start: function () {
                                selectedInput.select2("onSortStart");
                            },
                            update: function () {
                                selectedInput.select2("onSortEnd");
                            }
                        });
                    }
                });
            }
        };
    });

    /**
     * @link http://stackoverflow.com/questions/24745517/angularjs-ng-options-custom-attribute
     */
    directives.directive('customOptionAttributes', function ($timeout, $compile, $parse) {
        return {
            restrict: 'A',
            // priority: 10000,
            link: function optionStylePostLink(scope, element, attributes) {
                $timeout(function () {
                    var strObj = attributes['customOptionAttributes'];

                    scope.$watchCollection(strObj, function (newVal, oldVal) {
                        // if (newVal !== oldVal) {

                            var arrObj = strObj.split('.');
                            var allItems = [];

                            for (var i = 0; i < arrObj.length; i++) {
                                if (i == 0) {
                                    allItems = scope[arrObj[i]];
                                } else {
                                    allItems = allItems[arrObj[i]];
                                }
                            }

                            var options = element.find("option");
                            var objOption = null;

                            for (var i = 0; i < options.length; i++) {
                                objOption = $(options[i]);
                                objOption.attr("data-info", JSON.stringify(allItems[i]));
                            }
                        // }
                    });
                });
            }
        };
    });

})();