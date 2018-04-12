$(document).ready(function () {

    /**
     * @link http://stackoverflow.com/questions/946534/insert-text-into-textarea-with-jquery
     */
    $.fn.extend({
        insertAtCaret: function (myValue) {
            return this.each(function (i) {
                if (document.selection) {
                    //For browsers like Internet Explorer
                    this.focus();
                    var sel = document.selection.createRange();
                    sel.text = myValue;
                    this.focus();
                } else if (this.selectionStart || this.selectionStart == '0') {
                    //For browsers like Firefox and Webkit based
                    var startPos = this.selectionStart;
                    var endPos = this.selectionEnd;
                    var scrollTop = this.scrollTop;
                    this.value = this.value.substring(0, startPos) + myValue + this.value.substring(endPos, this.value.length);
                    this.focus();
                    this.selectionStart = startPos + myValue.length;
                    this.selectionEnd = startPos + myValue.length;
                    this.scrollTop = scrollTop;
                } else {
                    this.value += myValue;
                    this.focus();
                }

                // Force update value
                this.setAttribute('value', this.value);
            });
        },

        /**
         * @param {Object} attributes
         */
        addAttributes: function (attributes) {
            for (var a in attributes) {
                if (!attributes.hasOwnProperty(a))
                    continue;

                this.attr(a, attributes[a]);
            }

            return this;
        },

        /**
         * @param {Array} attributeNames
         */
        removeAttributes: function (attributeNames) {
            for (var i = 0; i < attributeNames.length; i++) {
                this.removeAttr(attributeNames[i]);
            }

            return this;
        },

        /**
         * @param {Array} classNames
         */
        addClasses: function (classNames) {
            for (var i = 0; i < classNames.length; i++) {
                this.addClass(classNames[i]);
            }

            return this;
        },

        /**
         * @param {Array} classNames
         */
        removeClasses: function (classNames) {
            for (var i = 0; i < classNames.length; i++) {
                this.removeClass(classNames[i]);
            }

            return this;
        },

        // Get the cusor position
        getCursorPosition: function () {
            var input = this.get(0);
            if (!input) return; // No (input) element found
            if ('selectionStart' in input) {
                // Standard-compliant browsers
                return input.selectionStart;
            } else if (document.selection) {
                // IE
                input.focus();
                var sel = document.selection.createRange();
                var selLen = document.selection.createRange().text.length;
                sel.moveStart('character', -input.value.length);
                return sel.text.length - selLen;
            }
        },

        // Validate the form
        isValid2: function () {
            return this[0].checkValidity()
        },

        /**
         *
         * @returns {{}}
         */
        serializeObject: function () {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function () {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        },

        /**
         *
         * @returns {{}}
         */
        serializeArrayToFormData: function () {
            var formData = new FormData();
            var data = this.serializeArray();

            $.each(data, function () {
                formData.append(this.name, this.value);
            });

            return formData;
        },

        /**
         * @link http://stackoverflow.com/questions/21388959/get-entire-opening-tag-using-jquery
         * @returns {string}
         */
        startTag: function () {
            return this[0].outerHTML.split(this.html())[0];
        },
        endTag: function () {
            return this[0].outerHTML.split(this.html())[1];
        },

        /**
         * @link http://stackoverflow.com/questions/4233265/contenteditable-set-caret-at-the-end-of-the-text-cross-browser/4238971#4238971
         */
        placeCaretAtEnd: function () {
            var thisFocus = $(this);
            var el = thisFocus[0];
            el.focus();

            if (typeof window.getSelection != "undefined"
                && typeof document.createRange != "undefined") {
                var range = document.createRange();
                range.selectNodeContents(el);
                range.collapse(false);
                var sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);
            } else if (typeof document.body.createTextRange != "undefined") {
                var textRange = document.body.createTextRange();
                textRange.moveToElementText(el);
                textRange.collapse(false);
                textRange.select();
            }
        }
    });

});
