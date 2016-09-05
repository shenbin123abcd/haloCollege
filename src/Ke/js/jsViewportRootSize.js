"use strict";

;(function () {
    "use strict";
    //            alert(navigator.userAgent);
    //            alert(window.innerWidth);
    //

    function setFontSize(mw, mh, tw, th) {

        var deviceWidth = document.documentElement.clientWidth;
        var deviceHeight = document.documentElement.clientHeight;
        var fontSize = 1;
        calSize();

        function calSize() {
            deviceWidth = document.documentElement.clientWidth;
            deviceHeight = document.documentElement.clientHeight;
            if (deviceWidth < deviceHeight) {
                if (deviceWidth < 500) {
                    fontSize = deviceWidth / mw;
                } else {
                    fontSize = deviceWidth / tw;
                }
            } else {
                if (deviceWidth < 1000) {
                    fontSize = deviceWidth / mh;
                } else {
                    fontSize = deviceWidth / th;
                }
            }
            fontSize = deviceWidth / mw;
            document.documentElement.style.fontSize = fontSize * 50 + 'px';
        }
        window.addEventListener("orientationchange", function (event) {
            //alert(event.orientation);

            //deviceWidth=document.documentElement.clientWidth;
            //calSize();

        });

        //
        //            alert(window.innerWidth);
        //
        //            alert(document.documentElement.clientWidth);
    }
    setFontSize(375, 667, 768, 1024);
})();