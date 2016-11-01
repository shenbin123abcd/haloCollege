;app.service=(function( url , settings ,method ){

    function getDetail(data){
        var deferred=$.Deferred();
        // console.log(data);
        data=data||{};
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'GET',
                // url:`/fake1?d=0`,
                url:`/cehuaGoods/detail`,
                data:data,
                dataType:'json',
                success: function(res) {
                    if(res.iRet==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            });
        }
        return deferred.promise();
    }

    function getDetailPrefer(data){
        var deferred=$.Deferred();
        // console.log(data);
        data=data||{};
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'GET',
                 // url:`/fake1?d=2000`,
                url:`/cehuaGoods/hot`,
                data:data,
                dataType:'json',
                success: function(res) {
                    if(res.iRet==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            });
        }
        return deferred.promise();
    }

    function getDetailService(data){
        var deferred=$.Deferred();
        // console.log(data);
        data=data||{};
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'GET',
                // url:`/fake1?d=300000`,
                url:`/cehuaGoods/hot`,
                data:data,
                dataType:'json',
                success: function(res) {
                    if(res.iRet==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            });
        }
        return deferred.promise();
    }


    function submitOrder(data){
        var deferred=$.Deferred();
        data=data||{};
        // console.log(data);
        switch (true){
            case !data.name:
                deferred.reject('请输入姓名');
                break;
            case !data.contact:
                deferred.reject('请输入手机号码');
                break;
            case !hb.validation.checkPhone(data.contact):
                deferred.reject('手机号码格式不正确！');
                break;
            default:
                sendXhr();
        };

        function sendXhr(){
            $.ajax({
                type:'POST',
                // url:`/fake1?d=300000`,
                url:`/fake1?d=2000`,
                data:data,
                dataType:'json',
                success: function(res) {
                    if(res.iRet==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            });
        }
        return deferred.promise();
    }


    return {
        getDetail,
        getDetailPrefer,
        getDetailService,
        submitOrder,
    };
}());


