
(function ($) {
    "use strict";


    /*==================================================================
    [ Validate ]*/
    var input = $('.validate-input .input100');

    $('.validate-form').on('submit',function(){
        var check = true;

        for(var i=0; i<input.length; i++) {
            if(validate(input[i]) == false){
                showValidate(input[i]);
                check=false;
            }
        }

        return check;
    });


    $('.validate-form .input100').each(function(){
        $(this).focus(function(){
           hideValidate(this);
        });
    });

    function validate (input) {
        if($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            if($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                return false;
            }
        }
        else {
            if($(input).val().trim() == ''){
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

function onClickGenerateToken(){
  var z = document.getElementById("js-token-2");
  var x = document.getElementById("js-token");
  var w = document.getElementById("js-message");
  var y = Math.random().toString(36).substr(2, 54)+Math.random().toString(36).substr(2, 54); // IMPORTANT IS NOT DEFINITIVE BECAUSE NEED HTTP REQUEST FOR MORE SECURITY
    
  if(!x.value){
    x.value = y; 
    z.value = y; 
    w.innerHTML += "<p class='pt-3'>Your new api key is generated and copied</p>";

    x.select();
    x.setSelectionRange(0, 99999); /* For mobile devices */
    document.execCommand("copy");
  }

}
