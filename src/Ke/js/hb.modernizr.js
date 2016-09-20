;(function() {
    //Modernizr.addTest('ios', function () {
    //    return navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false
    //});
    "use strict";
    Modernizr.addTest('weixin', function () {
        return navigator.userAgent.match(/(MicroMessenger)/g) ? true : false
    });
    Modernizr.addTest('weddinge', function () {
        return navigator.userAgent.match(/(weddinge)/g) ? true : false
    });

}());