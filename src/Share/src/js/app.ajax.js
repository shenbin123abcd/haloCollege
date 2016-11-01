

;app.ajax=function( url , settings  ){
    //var ajax=( url , settings )=>{
    var hashHistory=ReactRouter.hashHistory;
    let token=hb.Cookies.getJSON('koubei_token');
    if(token){
        var defaultSetting={
            url:url,
            data:settings,
            headers:{Authorization:`Bearer ${token}`},
        };
        var newSetting=$.extend({},defaultSetting,settings)

        return $.ajax(newSetting).then((res)=>{
            if(res.iRet==-1){
                // alert('未授权');
                ReactRouter.hashHistory.push('/cehua/noAuth')
                return res
            }else{
                return res
            }
        },(res)=>{
            return res
        });
    }else{
        var deferred = $.Deferred();
        // alert('未授权');
        ReactRouter.hashHistory.push('/cehua/noAuth')
        return deferred.promise();
    }
    //}
    //return ajax;
};


