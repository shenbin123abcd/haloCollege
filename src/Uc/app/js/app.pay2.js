;app.pay2=(function(){
    "use strict";
    var hash={};

    function callPay(name) {
        if(!hash[name]){
            hash[name]=new pay();
        }
        return hash[name];
    }
    function pay() {

    }

    pay.prototype.order_id='';

    pay.prototype.config={
        appId: '',
        nonceStr: '',
        package: '',
        signType: '',
        timeStamp: '',
        paySign: ''
    };

    pay.prototype.getConfig=function(){
        var _this=this;
        return $.ajax({
            url: '/wechatpay/getSign',
            type: 'GET',
            dataType: 'json',
            // data: {id: hb.location.url("?id")} // 门票编号
            // data: {} // 门票编号
            data: _this.opts.data // 门票编号
        });
    };
    pay.prototype.jsApiCall=function(){
        // console.log(this.jsApiCall)
        // console.log(this)
        var _this = this;
        if (this.config.appId == '') {
            this.getConfig().then(function(ret){
                if (ret.status == 1) {
                    _this.config = ret.data.config;
                    _this.order_id = ret.data.order_id;
                    _this.startPay();
                }else if(ret.status == -1){
                    // 未登录
                    window.location.href='/weiTicket/wechat';
                }else{
                    hb.lib.weui.alert(ret.info);
                }
            }, function(){
                hb.lib.weui.alert('网络繁忙，请稍候再试！');
            });
        }else{
            _this.startPay();
        }

    };
    pay.prototype.startPay=function(){
        var _this = this;
        var opts=_this.opts;
        WeixinJSBridge.invoke('getBrandWCPayRequest', _this.config,
            function(res){
                console.log(res);
                console.log(res.err_msg);
                WeixinJSBridge.log(res.err_msg);
                // alert(res.err_code+res.err_desc+res.err_msg);

                if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                    // 支付完成 重置配置
                    // _this.config = {appId: '',nonceStr: '',package: '',signType: '',timeStamp: '',paySign: ''}
                    // alert('支付完成');
                    opts.onSuccess(_this.order_id);
                }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
                    // 支付取消
                    // alert('支付取消');
                    opts.onCancel();
                }else if (res.err_msg == 'get_brand_wcpay_request:fail') {
                    // 支付失败
                    // alert('支付失败');
                    opts.onFail(res.err_desc);
                }
            }
        );
    };
    pay.prototype.opts={};
    
    pay.prototype.callpay=function (opts){
        var _this=this;
        var opts=opts||{};
        opts.onSuccess=opts.onSuccess||function () {};
        opts.onCancel=opts.onCancel||function () {};
        opts.onFail=opts.onFail||function () {};
        opts.data=opts.data||{};
        this.opts=opts;
        // console.log(this.jsApiCall)


        if (typeof WeixinJSBridge == "undefined"){
            if( document.addEventListener ){
                document.addEventListener('WeixinJSBridgeReady', function () {
                    _this.jsApiCall();
                }, false);
            }else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', function () {
                    _this.jsApiCall();
                });
                document.attachEvent('onWeixinJSBridgeReady', function () {
                    _this.jsApiCall();
                });
            }
        }else{
            this.jsApiCall();
        }


    };

    // var wechat = {
    //     init: function(){
    //
    //     },
    //     order_id: '',
    //     config:{
    //         appId: '',
    //         nonceStr: '',
    //         package: '',
    //         signType: '',
    //         timeStamp: '',
    //         paySign: ''
    //     },
    //     getConfig: function(){
    //         var _this=this;
    //         return $.ajax({
    //             url: '/wechatpay/getSign',
    //             type: 'GET',
    //             dataType: 'json',
    //             // data: {id: hb.location.url("?id")} // 门票编号
    //             // data: {} // 门票编号
    //             data: _this.opts.data // 门票编号
    //         });
    //     },
    //     //调用微信JS api 支付
    //     jsApiCall: function(){
    //         console.log(this.jsApiCall)
    //         console.log(this)
    //         var _this = this;
    //         if (this.config.appId == '') {
    //             this.getConfig().then(function(ret){
    //                 if (ret.status == 1) {
    //                     _this.config = ret.data.config;
    //                     _this.order_id = ret.data.order_id;
    //                     _this.startPay();
    //                 }else if(ret.status == -1){
    //                     // 未登录
    //                     window.location.href='/weiTicket/wechat';
    //                 }else{
    //                     hb.lib.weui.alert(ret.info);
    //                 }
    //             }, function(){
    //                 hb.lib.weui.alert('网络繁忙，请稍候再试！');
    //             });
    //         }else{
    //             _this.startPay();
    //         }
    //
    //     },
    //     startPay: function(){
    //         var _this = this;
    //         var opts=_this.opts;
    //         WeixinJSBridge.invoke('getBrandWCPayRequest', _this.config,
    //             function(res){
    //                 console.log(res);
    //                 console.log(res.err_msg);
    //                 WeixinJSBridge.log(res.err_msg);
    //                 // alert(res.err_code+res.err_desc+res.err_msg);
    //
    //                 if(res.err_msg == "get_brand_wcpay_request:ok" ) {
    //                     // 支付完成 重置配置
    //                     // _this.config = {appId: '',nonceStr: '',package: '',signType: '',timeStamp: '',paySign: ''}
    //                     // alert('支付完成');
    //                     opts.onSuccess(_this.order_id);
    //                 }else if (res.err_msg == 'get_brand_wcpay_request:cancel') {
    //                     // 支付取消
    //                     // alert('支付取消');
    //                     opts.onCancel();
    //                 }else if (res.err_msg == 'get_brand_wcpay_request:fail') {
    //                     // 支付失败
    //                     // alert('支付失败');
    //                     opts.onFail(res.err_desc);
    //                 }
    //             }
    //         );
    //     },
    //     // 唤起支付
    //     opts:{},
    //     callpay: function (opts){
    //         var _this=this;
    //         var opts=opts||{};
    //         opts.onSuccess=opts.onSuccess||function () {};
    //         opts.onCancel=opts.onCancel||function () {};
    //         opts.onFail=opts.onFail||function () {};
    //         opts.data=opts.data||{};
    //         this.opts=opts;
    //         // console.log(this.jsApiCall)
    //
    //
    //         if (typeof WeixinJSBridge == "undefined"){
    //             if( document.addEventListener ){
    //                 document.addEventListener('WeixinJSBridgeReady', function () {
    //                     _this.jsApiCall();
    //                 }, false);
    //             }else if (document.attachEvent){
    //                 document.attachEvent('WeixinJSBridgeReady', function () {
    //                     _this.jsApiCall();
    //                 });
    //                 document.attachEvent('onWeixinJSBridgeReady', function () {
    //                     _this.jsApiCall();
    //                 });
    //             }
    //         }else{
    //             this.jsApiCall();
    //         }
    //
    //
    //     }
    // }
    return{
        callPay:callPay,
    }
}());
