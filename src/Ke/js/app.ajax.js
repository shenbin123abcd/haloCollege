;app.ajax=function( url , settings  ){
    var ajax=$.ajax( url , settings ).then((res)=>{
        // console.log(res)
        if(res.iRet==-1){
            window.location.href=res.data
            return res
        }else{
            return res
        }
    },(res)=>{
        return res
    });
    return ajax;
};

