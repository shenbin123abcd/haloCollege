//hb_scroll
;(function($){
    "use strict";

    var initialScreenSize = window.innerHeight;

    var inputStr="input[type='text'],input[type='password'],input[type='number'],input[type='email'],input[type='search'],input[type='url'],input[type='tel'],textarea";

    $("body").on('touchstart',inputStr,function(){
        if (!window.pageYOffset==0){
            startKeyboardShow();
        }

    });

    $("body").on('focus',inputStr,function(){
//    document.querySelector("#input").className='input keyboard'

    //console.log(window.pageYOffset);

        if (window.pageYOffset==0){
            if(navigator.userAgent.match(/(MicroMessenger)/g)){
                var addKeyboard=setInterval(cal,10);
            }else{
                afterKeyboardShow();
            }

        }else{
            afterKeyboardShow();
        }

        function cal(){
//        document.querySelector("body").appendChild(document.createTextNode(window.innerHeight+' '))
            if(window.innerHeight<initialScreenSize){
                afterKeyboardShow();
                window.clearInterval(addKeyboard)
            }
        }
    });
    $("body").on('blur',inputStr,function(){
        afterKeyboardHide();
//    var removeKeyboard=setInterval(function(){
////        document.querySelector("body").appendChild(document.createTextNode(window.innerHeight+' '))
//        if(window.innerHeight==initialScreenSize){
//            document.querySelector("#input").className='input'
//            window.clearInterval(removeKeyboard)
//        }
//    },1);

    })

    function afterKeyboardShow(){
        $("html").addClass('keyboard-show');
    }
    function startKeyboardShow(){
        $("html").addClass('keyboard-start');
    }
    function afterKeyboardHide(){
        $("html").removeClass('keyboard-show').removeClass('keyboard-start');
    }

})(jQuery);
