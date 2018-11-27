/**
 * oEmbed plugin for Craft CMS
 *
 * oEmbed JS
 *
 * @author    reganlawton
 * @copyright Copyright (c) 2017 reganlawton
 * @link      https://github.com/wrav
 * @package   oEmbed
 * @since     1.0.0
 */

var oembedOnChangeTimeout = null;

$('input.oembed-field').on('keyup blur change', function () {
    var that = $(this);

    if (oembedOnChangeTimeout != null) {
        clearTimeout(oembedOnChangeTimeout);
    }

    oembedOnChangeTimeout = setTimeout(function() {
        oembedOnChangeTimeout = null;

        var val = that.val();

        if(val) {
            $.ajax({
                type: "GET",
                url: "/admin/oembed/preview?url=" + val + "&options[]=",
                async: true
            }).done(function (res) {
                var preview = that.parent().find('.oembed-preview');
                preview.html('');

                if (res) {
                    preview.html(res);
                } else {
                    preview.html(
                        '<p class="error">Please check your URL.</p>'
                    );
                }
            });
        }

    }, 500);
});
