/* ********************************************************************************
 * The content of this file is subject to the Quoting Tool ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/**
 * Get value from attribute
 * @link http://stackoverflow.com/questions/12371159/how-to-get-evaluated-attributes-inside-a-custom-directive
 */

(function ($) {
    'use strict';

    var directives = angular.module('AppDirectives', ['AppUtils']);

    /**
     * Resize block
     * @link http://stackoverflow.com/questions/23044338/window-resize-directive
     */
    directives.directive('resize', function ($window, $parse) {
        var footerHeight = 24;

        return {
            /**
             *
             * @param scope
             * @param element
             * @param attributes
             */
            link: function (scope, element, attributes) {
                var objWindow = angular.element($window);
                var objElement = $(element);
                var thisOffset = {};

                scope.$watch(function () {
                    return {
                        'h': objWindow.height(),
                        'w': objWindow.width()
                    };
                }, function (newValue, oldValue) {
                    scope.windowHeight = newValue.h;
                    scope.windowWidth = newValue.w;
                    // var resize = $parse(attributes.resize)(scope);
                    var resize = QuotingToolUtils.convertInlineStyleToObject(attributes.resize);
                    thisOffset = objElement.offset();
                    objElement.css({
                        'height': (newValue.h - thisOffset.top - footerHeight) + 'px'
                    });

                    if (resize) {
                        // Resize with offset
                        if (resize.offset) {
                            objElement.css({
                                'height': (newValue.h - resize.offset) + 'px'
                            });
                        }

                        // Add nicescroll to block
                        if (resize.nicescroll) {
                            objElement.niceScroll({
                                cursorcolor: '#59b671'
                            });
                        }
                    }
                }, true);

                objWindow.bind('resize', function () {
                    scope.$apply();
                });
            }
        };
    });

    /**
     * @link http://stackoverflow.com/questions/22126224/angularjs-ngrepeat-is-applied-double-times-when-compile-the-elements-attribu
     */
    directives.directive('compile', function ($compile) {
        return {
            priority: 1500,
            terminal: true,
            link: function (scope, element, attributes) {
                scope.$watch(
                    function (scope) {
                        return scope.$eval(attributes.compile);
                    },
                    function (value) {
                        //element.html(value);
                        //$compile(element.contents())(scope);

                        element.attr("real", value);
                        element.removeAttr("compile");
                        $compile(element)(scope);
                    }
                );
            }
        };
    });

    directives.directive('inputChange', function () {
        return {
            link: function (scope, element, attributes) {
                $(document).on('change', element, function (event) {
                    var target = $(event.target);
                    var type = event.target.type;

                    switch (type) {
                        case 'checkbox':
                            if (target.is(':checked')) {
                                target.attr('checked', 'checked')
                            } else {
                                target.removeAttr('checked');
                            }
                            break;
                        case 'text':
                            target.attr('value', target.val());
                            break;
                        default:
                            break;
                    }

                });
            }
        };
    });

    directives.directive('onFinishRenderItem', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attribute) {
                var emit = attribute.onFinishRenderItem;

                // if (scope.$last === true) {
                    $timeout(function () {
                        scope.$emit(emit, {target: element});
                    });
                // }
            }
        }
    });

    directives.directive('onFinishRender', function ($timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attribute) {
                var emit = attribute.onFinishRender;

                if (scope.$last === true) {
                    $timeout(function () {
                        scope.$emit(emit, {target: element});
                    });
                }
            }
        }
    });

})(jQuery);
