var QuotingToolUtils = {

    queryString: function () {
        // This function is anonymous, is executed immediately and
        // the return value is assigned to QueryString!
        var query_string = {};
        var query = window.location.search.substring(1);
        var vars = query.split('&');
        for (var i = 0; i < vars.length; i++) {
            var pair = vars[i].split('=');
            // If first entry with this name
            if (typeof query_string[pair[0]] === 'undefined') {
                query_string[pair[0]] = decodeURIComponent(pair[1]);
                // If second entry with this name
            } else if (typeof query_string[pair[0]] === 'string') {
                query_string[pair[0]] = [query_string[pair[0]], decodeURIComponent(pair[1])];
                // If third or later entry with this name
            } else {
                query_string[pair[0]].push(decodeURIComponent(pair[1]));
            }
        }
        return query_string;
    },

    /**
     * Use encodeURIComponent to encode characters outside of the Latin1 range
     * @link http://stackoverflow.com/questions/23223718/failed-to-execute-btoa-on-window-the-string-to-be-encoded-contains-characte
     * @param string
     * @returns {string}
     */
    base64Encode: function (string) {
        if (typeof string === 'undefined' || string === null) {
            return '';
        }

        return window.btoa(unescape(encodeURIComponent(string)));
    },

    /**
     * Use encodeURIComponent to encode characters outside of the Latin1 range
     * @link http://stackoverflow.com/questions/23223718/failed-to-execute-btoa-on-window-the-string-to-be-encoded-contains-characte
     * @param string
     * @returns {string}
     */
    base64Decode: function (string) {
        if (typeof string === 'undefined' || string === null) {
            return '';
        }

        return decodeURIComponent(escape(window.atob(string)));
    },

    /**
     * @param str
     * @param find
     * @param replace
     * @returns {*}
     */
    replaceAll: function (str, find, replace) {
        return str.replace(new RegExp(find, 'g'), replace);
    },

    /**
     * @param {Array|Object} values
     * @returns {*}
     */
    defaultSelected: function (values) {
        // If is array
        if ($.isArray(values) && values.length > 0) {
            return values[0];
        }

        // If is object
        for (var k in values) {
            if (values.hasOwnProperty(k)) {
                return values[k];
            }
        }

        return null;
    },

    /**
     * @param {Array|Object} values
     * @param {String} current
     * @param {String} by
     * @returns {*}
     */
    currentSelected: function (values, current, by) {
        if ($.isArray(values)) {
            var value = null;

            for (var i = 0; i < values.length; i++) {
                value = values[i];

                if (value[by] == current) {
                    return value;
                }
            }
        }

        return null;
    },

    /**
     * @param {Array|Object} items
     * @param {string} order
     * @param {string} by
     * @returns {Array}
     */
    sortByString: function (items, order, by) {
        if (order == AppConstants.ORDER.ASC) {
            items.sort(function (a, b) {
                if (a[by] < b[by])
                    return -1;
                if (a[by] > b[by])
                    return 1;
                return 0;
            });
        }
        else if (order == AppConstants.ORDER.DESC) {
            items.sort(function (a, b) {
                if (a[by] < b[by])
                    return 1;
                if (a[by] > b[by])
                    return -1;
                return 0;
            });
        }

        return items;
    },

    /**
     * @param {Array} items
     * @param {string} order
     * @param {number} by
     * @returns {Array}
     */
    sortByNumber: function (items, order, by) {
        if (order == AppConstants.ORDER.ASC) {
            items.sort(function (a, b) {
                return a[by] - b[by];
            });
        }
        else if (order == AppConstants.ORDER.DESC) {
            items.sort(function (a, b) {
                return b[by] - a[by];
            });
        }

        return items;
    },

    /**
     * Gets styles by a class name
     *
     * @link http://stackoverflow.com/questions/324486/how-do-you-read-css-rule-values-with-javascript
     * @notice The className must be 1:1 the same as in the CSS
     * @param {string} className
     */
    getClassStyles: function (className) {
        var styleSheets = window.document.styleSheets;

        for (var i = 0; i < styleSheets.length; i++) {
            var classes = styleSheets[i].rules || styleSheets[i].cssRules;
            if (!classes)
                continue;

            for (var x = 0; x < classes.length; x++) {
                if (classes[x].selectorText == className) {
                    var ret;
                    if (classes[x].cssText) {
                        ret = classes[x].cssText;
                    } else {
                        ret = classes[x].style.cssText;
                    }

                    if (ret.indexOf(classes[x].selectorText) == -1) {
                        ret = classes[x].selectorText + "{" + ret + "}";
                    }

                    var newRet = ret.replace(classes[x].selectorText, '');
                    newRet = newRet.replace('{', '');
                    newRet = newRet.replace('}', '');
                    newRet = newRet.trim();
                    var arrRet = newRet.split(';');
                    var myRet = {};
                    for (var k in arrRet) {
                        if (arrRet.hasOwnProperty(k)) {
                            var arr = arrRet[k].split(':');
                            var arrKey = arr[0];
                            var arrValue = arr[1];

                            if (typeof arrKey !== 'undefined' && arrKey.trim() != '') {
                                myRet[arrKey.trim()] = arrValue.trim();
                            }
                        }
                    }

                    return myRet;
                }
            }
        }
    },

    /**
     * @param {String} newRet
     * @returns {{}}
     */
    convertInlineStyleToObject: function (newRet) {
        if (!newRet) {
            return {};
        }

        newRet = newRet.trim();
        var arrRet = newRet.split(';');
        var myRet = {};
        for (var k in arrRet) {
            if (arrRet.hasOwnProperty(k)) {
                var arr = arrRet[k].split(':');
                var arrKey = arr[0];
                var arrValue = arr[1];

                if (typeof arrKey !== 'undefined' && arrKey.trim() != '') {
                    myRet[arrKey.trim()] = arrValue.trim();
                }
            }
        }

        return myRet;
    },

    /**
     * @param {Object} objStyle
     * @returns {String}
     */
    convertObjectToInlineStyle: function (objStyle) {
        var style = '';

        if (objStyle) {
            for (var s in objStyle) {
                if (!objStyle.hasOwnProperty(s)) {
                    continue;
                }

                style += s + ': ' + objStyle[s] + '; ';
            }
        }

        return style;
    },

    /**
     * @param {String} valString
     * @returns {Number}
     */
    getCssValue: function (valString) {
        return parseInt(valString.replace(/[^-\d\.]/g, ''));
    },

    /**
     * Fn - getFilenameFromPath
     *
     * @link http://stackoverflow.com/questions/423376/how-to-get-the-file-name-from-a-full-path-using-javascript
     * @param {String} fullPath
     * @returns {String}
     */
    getFilenameFromPath: function (fullPath) {
        return fullPath.replace(/^.*[\\\/]/, '');
    },

    /**
     * @link http://stackoverflow.com/questions/1349404/generate-a-string-of-5-random-characters-in-javascript
     *
     * @param {Number} length
     * @param {String|undefined} append
     * @returns {string}
     */
    makeId: function (length, append) {
        if (typeof append == 'undefined' || append === null)
            append = '';

        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for (var i = 0; i < length; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text + append;
    },

    /**
     * @returns {string}
     */
    getRandomId: function () {
        var thisInstance = this;
        var strTimestamp = Date.now() + '';
        return ('rand_' + thisInstance.makeId(10, strTimestamp));
    },

    getThemeSettings: function () {
        var topMenus = $('#topMenus');
        var backgroundColor = '#ffffff';
        var color = '#000000';

        var navbar = topMenus.find('.navbar-inner');
        if (navbar.length > 0) {
            backgroundColor = navbar.css('background-color');
        }

        var firstTab = navbar.find('.menuBar .tabs a').first();

        if (firstTab.length > 0) {
            color = firstTab.css('color');
        }

        return {
            'background-color': backgroundColor,
            'color': color
        };
    },

    /**
     * @link http://stackoverflow.com/questions/18681788/how-to-get-a-youtube-thumbnail-from-a-youtube-iframe
     *
     * @param iframe
     * @returns {*}
     */
    getYoutubeThumbnailFromIframe: function (iframe) {
        var img_link = null;
        // var iframe           = container.find('iframe:first');
        var iframe_src = iframe.attr('src');
        var youtube_video_id = iframe_src.match(/youtube\.com.*(\?v=|\/embed\/)(.{11})/).pop();

        if (youtube_video_id.length == 11) {
            // var video_thumbnail = $('<img src="//img.youtube.com/vi/'+youtube_video_id+'/maxresdefault.jpg">');
            // container.append(video_thumbnail);
            img_link = '//img.youtube.com/vi/' + youtube_video_id + '/maxresdefault.jpg';
        }

        return img_link;
    }
};

jQuery(document).ready(function () {
    var GetIEVersion = function() {
        var sAgent = window.navigator.userAgent;
        var Idx = sAgent.indexOf("MSIE");

        // If IE, return version number.
        if (Idx > 0)
            return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));

        // If IE 11 then look for Updated user agent string.
        else if (!!navigator.userAgent.match(/Trident\/7\./))
            return 11;

        else
            return 0; //It is not IE
    };
    if (GetIEVersion() > 0){
        // Fix auto add resizeable to textarea on IE
        if (jQuery.isFunction(jQuery.fn.resizable)) {
            jQuery('#quoting_tool-body').find('textarea.hide')
                .resizable('destroy')
                .removeAttr('style');
        }
    }


});
