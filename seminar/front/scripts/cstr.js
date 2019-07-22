 (function ($) {
    $.fn.extend({

    // With every keystroke capitalize first letter of ALL words in the text
    upperFirstAll: function() {
        $(this).keyup(function(event) {
            var box = event.target;
            var txt = $(this).val();
            var start = box.selectionStart;
            var end = box.selectionEnd;

            $(this).val(txt.toLowerCase().replace(/^(.)|(\s|\-)(.)/g,
            function(c) {
                return c.toUpperCase();
            }));
            box.setSelectionRange(start, end);
        });
        return this;
    },

    // With every keystroke capitalize first letter of the FIRST word in the text
    upperFirst: function() {
        $(this).keyup(function(event) {
            var box = event.target;
            var txt = $(this).val();
            var start = box.selectionStart;
            var end = box.selectionEnd;

            $(this).val(txt.toLowerCase().replace(/^(.)/g,
            function(c) {
                return c.toUpperCase();
            }));
            box.setSelectionRange(start, end);
        });
        return this;
    },

    // Converts with every keystroke the hole text to lowercase
    lowerCase: function() {
        $(this).keyup(function(event) {
            var box = event.target;
            var txt = $(this).val();
            var start = box.selectionStart;
            var end = box.selectionEnd;

            $(this).val(txt.toLowerCase());
            box.setSelectionRange(start, end);
        });
        return this;
    },

    // Converts with every keystroke the hole text to uppercase
    upperCase: function() {
        $(this).keyup(function(event) {
            var box = event.target;
            var txt = $(this).val();
            var start = box.selectionStart;
            var end = box.selectionEnd;

            $(this).val(txt.toUpperCase());
            box.setSelectionRange(start, end);
        });
        return this;
    }

    });
}(jQuery));
