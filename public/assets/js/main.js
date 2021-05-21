var host = window.location.host;
var protocol = window.location.protocol;
var pathname = window.location.pathname;

(function ($) {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit', function () {
        var check = true;

        for (var i = 0; i < input.length; i++) {
            if (validate(input[i]) == false) {
                showValidate(input[i]);
                check = false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function () {
        $(this).focus(function () {
            hideValidate(this);
        });
    });

    function validate(input) {
        if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        } else {
            if ($(input).val().trim() == '') {
                return false;
            }
        }

    }

    function showValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');
    }

    function hideValidate(input) {
        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');
    }



})(jQuery);


/*==================================================================
    [ On click generate token ]*/

function onClickGenerateToken() {
    var z = document.getElementById("js-token-2");
    var x = document.getElementById("js-token");
    var w = document.getElementById("js-message");



    if (!x.value) {



        var xhr = new XMLHttpRequest();

        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                var data = this.response;
                x.value = data;
                z.value = data;
                z.select();
                z.setSelectionRange(0, 99999); /* For mobile devices */
                document.execCommand("copy");
                w.innerHTML += "<p class='pt-3'>Your new api key is generated and copied</p>";
            }
        }

        var url = protocol + "//" + host + "/dashboard/update/apikey";
        xhr.open("GET", url);
        xhr.responseType = "json";
        xhr.send();
    }


}