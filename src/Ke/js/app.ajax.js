;app.ajax=function( url , settings  ){
    let params=hb.location.url("?wechat_code");
    if(params){
        $.ajax({
            url:'/courses/setLogin',
            data:{
                wechat_code:params
            }
        })
    }
    var ajax=$.ajax( url , settings ).then((res)=>{
        // console.log(res)
        if(res.iRet==-1){
            window.location.href=res.data;
            return res
        }else{
            return res
        }
    },(res)=>{
        return res
    });
    return ajax;
};


