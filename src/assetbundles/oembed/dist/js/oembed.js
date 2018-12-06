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
var oembedFields = document.querySelector('input.oembed-field');
var errorMessage = '<p class="error">Please check your URL.</p>';

['keyup', 'change'].forEach(function(event) {
    oembedFields.addEventListener(event, function (e) {
        var elem = e.target;

        if (oembedOnChangeTimeout != null) {
            clearTimeout(oembedOnChangeTimeout);
        }

        oembedOnChangeTimeout = setTimeout(function() {
            oembedOnChangeTimeout = null;

            var val = elem.value;

            if(val) {
                var preview = elem.parentNode.querySelector('.oembed-preview');
                var request = new XMLHttpRequest();
                request.open('GET', '/admin/oembed/preview?url=' + val + '&options[]=', true);

                request.onload = function() {

                    if (request.status >= 200 && request.status < 400) {
                        var res = request.responseText;
                        preview.innerHTML = '';

                        if (res) {
                            preview.innerHTML = res;
                        } else {
                            preview.innerHTML = errorMessage;
                        }
                    } else {
                        preview.innerHTML = errorMessage;
                    }
                };

                request.onerror = function() {
                    preview.innerHTML = errorMessage;
                };

                request.send();
            }
        }, 500);
    });
});
