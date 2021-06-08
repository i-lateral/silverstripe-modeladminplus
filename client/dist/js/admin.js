
(function ($) {
    $.entwine('ss.modeladminplus', function ($) {

        $('.field.autocomplete input.text').entwine({

            onmatch: function () {
                $(this).autocomplete({
                    _renderMenu: function( ul, items ) {
                        console.log(ul);
                    }
                });
                this._super();
            }
        });
    });   
})(jQuery);