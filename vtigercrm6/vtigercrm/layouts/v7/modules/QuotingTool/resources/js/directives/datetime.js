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

    directives.directive('vtigerDatetimepicker', function ($rootScope, $timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                $timeout(function () {
                    var date_format = $rootScope.app.config['date_format'];
                    var time_format = 'HH:mm';
                    if ($rootScope.app.config['hour_format'] == 12) {
                        time_format = 'HH:mm a';
                    }
                    var datetime_format = date_format + ' ' + time_format;
                    attributes.$set('data-datetime-format', datetime_format);
                    attributes.$set('data-date-format', date_format);
                    attributes.$set('data-time-format', time_format);

                    // Update exist values
                    var info = element.data('info');

                    if (typeof info === 'undefined') {
                        info = {};
                    }

                    info['datetime_format'] = datetime_format;
                    info['date_format'] = date_format;
                    info['time_format'] = time_format;
                    attributes.$set('data-info', JSON.stringify(info));

                    var currentTime = '00:00';
                    var timestamp = new Date();
                    var currentDate = AppHelper.formatDate(date_format, timestamp);
                    element.attr({
                        'value': currentDate + ' ' + currentTime
                    });
                });
            }
        };
    });

    directives.directive('vtigerDatepicker', function ($rootScope, $timeout) {
        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                $timeout(function () {
                    var date_format = $rootScope.app.config['date_format'];
                    attributes.$set('data-date-format', date_format);

                    // Update exist values
                    var info = element.data('info');

                    if (typeof info === 'undefined') {
                        info = {};
                    }

                    info['date_format'] = date_format;
                    attributes.$set('data-info', JSON.stringify(info));

                    // Default is current timestamp
                    var timestamp = new Date();
                    var currentDate = AppHelper.formatDate(date_format, timestamp);
                    element.attr({
                        'value': currentDate
                    });
                    element.val(currentDate);
                    /**
                     * @link libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/bootstrap-datepicker.js
                     */
                    element.datepicker({
                        format: date_format,
                        autoclose: true
                    });
                });
            }
        };
    });

})();