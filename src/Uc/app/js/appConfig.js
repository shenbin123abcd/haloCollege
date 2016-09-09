(function(){
    "use strict";
    window.appConfig={};
    window.appConfig.debug=!false;
    window.appConfig.version='1.0.0';

    if(window.appConfig.debug){
        window.appConfig.bust='?v='+(new Date().getTime());
        window.appConfig.staticUrl=window.location.protocol+'//'+window.location.hostname+':9000/app/public';
    }else{
        window.appConfig.bust='?v='+window.appConfig.version;
        window.appConfig.staticUrl='';
    }
}());