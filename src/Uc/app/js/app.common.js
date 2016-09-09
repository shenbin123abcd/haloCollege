app.common=(function(){
    "use strict";
    function init(){
        app.wechat.init();
        ucShareThis();
    }


    function debugCookie(str) {
        if(appConfig.debug){
            hb.Cookies.set('halobear',str,{ expires: 7 });
            let hbCookie=hb.Cookies.get('halobear');
            alert(hbCookie)
        }
    }


    // debugCookie('OWM4NU94Z1B1NlArc3RYQ2xFTk1ZekZmbnBzelVpV0d0SVlLZkQxMjZKeTd2UTZuRXhaZFFkMHZINlhzaVZtYVRteTYxUkNDVkVWaW5ROE1kbzgyOFFBM0Q4b0g1Y09XWVE=');
    //debugCookie('Y2Y2YXZXazhGc1FkTjFIWUlUOEZPK3BZL3RmVmoxVTZHSy9vR1JpSlRpemZMWUkyeTYrYXovYTJXN21FR2FIN0VVVTdGYlZDUEVRL1Z3YUFpTTRDdk5nalBsbnlleFRLSnc=');

    function ticketProgress() {
        var nanobarVal = $("#sale-progress").data('num');
        var options = {
            classname: 'sale-progress-inner',
            id: 'sale-progress-inner',
            target: document.getElementById('sale-progress'),
            onProgress: function (res) {
                // console.log(res)
                $("#pointer").css({
                    left:res+'%',
                }).text(nanobarVal+'元')
            },
            onFinish:function () {

            }
        };
        var nanobar = new Nanobar( options );
        // move bar
        nanobar.go( nanobarVal/30*100 ); // size bar 76%
        $("#pointer").show();
        if(nanobarVal==0){
            $("#pointer").css({
                'margin-left':-20,
            })
        }

    }
    function ticketProgress2() {
        var $element=$("#sale-progress");

        var val = $element.data('num');
        var max = $element.data('maxNum');
        var min = $element.data('minNum');

        // console.log(val,max)
        // console.log(val,max)


        var htmlStr=`
         <div class="sale-progress"  >
                <div bar class="bar" style="width: 100%;">
                    <div class="pointer" id="pointer" >${val}元</div>
                </div>
            </div>
        `;


        $element.append(htmlStr);
        setTimeout(function () {
            $element.find("[bar]").css({
                width:(1-(max-val)/(max-min))*100+'%'
            })
        })
        

        

    }


    function ucShareThis() {
        $("[uc-share-this]").on('click',function () {
            app.common.guideShare.show();
        })
    }
    var guideShare=(function(url){
        var imgUrl=url;
        var loadingHtmlStr=`
            <div style="position: fixed;z-index: 99999;width: 100%;height: 100%;top: 0;left: 0;background: rgba(0,0,0,0.8);">
                <img style='position: absolute;width: 90px;top: 5px;right: 20px;' src="images/weixin-guide.png">
            </div>
        `;
        var $loadingHtml=$(loadingHtmlStr);

        var show=function(){
            $("body").append($loadingHtml);
            $loadingHtml.on('click touchmove',function(){
                $loadingHtml.remove();
            });
        };

        var hide=function(){
            $loadingHtml.remove();
        };

        return{
            show:show,
            hide:hide
        }
    }());

    function renderPriceNumber(){
        var val=$("#current-sale-price").data("num");
        var currentNum=$("#current-sale-price").data("max");
        // console.log(val);
        hb.fancyNumber("#current-sale-price",{
            number:currentNum,
            preClass:'uc-s-big-num-',
            staticClass:'uc-s-big-num'
        });
        hb.interval(function () {
            var step=(currentNum-val)/30;
            currentNum-=step;
            // console.log(Math.floor(currentNum));
            hb.fancyNumber("#current-sale-price",{
                number:Math.floor(currentNum),
                preClass:'uc-s-big-num-',
                staticClass:'uc-s-big-num'
            });

        },10,30,function () {
            hb.fancyNumber("#current-sale-price",{
                number:val,
                preClass:'uc-s-big-num-',
                staticClass:'uc-s-big-num'
            });

        });

        

        hb.fancyNumber("#current-sale-price",{
            number:val,
            preClass:'uc-s-big-num-',
            staticClass:'uc-s-big-num'
        });
        
        
        

    }

    function getDiscount() {



        $("#get-discount-ajax").on('click',function () {
            hb.lib.weui.alert('优惠已结束');
            return;
            hb.lib.weui.loading.show();
            var _this=this;
            $.ajax(`/uc/getCoupon?type=${hb.location.url('?type')||0}`).done(function (res) {
                hb.lib.weui.loading.hide();
                if(res.status==1){
                    // window.location.reload();
                    $(_this).before(`
                <span class="uc-bt uc-bt-second get-discount disabled" id="get-discount" >
                <img class="bt-img" src="images/bt-text-get-discount.png" alt="">
                </span>`);
                    $(_this).remove();
                    hb.fancyNumber("#current-sale-price",{
                        number:res.data,
                        preClass:'uc-s-big-num-',
                        staticClass:'uc-s-big-num'
                    });
                    $("#sale-progress").find("[bar]").css({
                        width:res.data/appData.ticket_detail_price *100+'%'
                    })
                    $("#sale-progress .pointer").text(res.data+'元');
                    $("#get-discount").on('click',function () {
                        hb.lib.weui.alert('优惠已结束');
                        return;
                        //$("#show-get-discount").show();
                    });
                    $("#show-get-discount [close-modal]").on('click',function () {
                        $("#show-get-discount").hide();
                    });
                }else{
                    hb.lib.weui.alert(res.info)
                }


            }).fail(function (res) {
                hb.lib.weui.alert('网络繁忙请稍候再试')
            })
        });


        $("#get-discount").on('click',function () {
            hb.lib.weui.alert('优惠已结束');
            return;
            $("#show-get-discount").show();
        });
        $("#show-get-discount [close-modal]").on('click',function () {
            $("#show-get-discount").hide();
        });
    }

    function buyTicket() {


        $("#go-pay-bt").on('click',function () {
            var type=hb.location.url('?type')||0;
            if(type==3){
                hb.lib.weui.alert('SVIP票已售罄');
                return
            }

            app.pay.wechat.callpay({
                data:{
                    type:hb.location.url('?type')||0
                },
                onSuccess:function (res) {
                    window.location.reload();
                    // showPaySuccess(res)
                },
                onFail:function (res) {
                    hb.lib.weui.alert(res);
                },
            });
        })
    }

    function showButton() {
        // console.log(appData);

        var btHtmlStr;
        var type=hb.location.url('?type')||0;
        var payBtStr=``;
        if(type==3){
            payBtStr=`<span class="uc-bt uc-bt-main uc-sold-out disabled" id="go-pay-bt" ><img class="bt-img" src="images/bt-text-yishouqing.png" alt=""></span>`
        }else{
            payBtStr=`<button class="uc-bt uc-bt-main uc-index-pay" id="go-pay-bt" ><img class="bt-img" src="images/bt-text-uc-index-pay.png" alt=""></button>`
        }

        if(!appData.state.is_complete) {

            if(appData.state.is_support==1){
                btHtmlStr=`<div class="sale-bt-box-2" >
                <span class="uc-bt uc-bt-second get-discount disabled" id="get-discount" ><img class="bt-img" src="images/bt-text-get-discount.png" alt=""></span>
                ${payBtStr}
            </div>`
            }else{

                btHtmlStr=`<div class="sale-bt-box-2" >
                <button class="uc-bt uc-bt-second get-discount" id="get-discount-ajax" ><img class="bt-img" src="images/bt-text-get-discount.png" alt=""></button>
                ${payBtStr}
            </div>`

            }


        }
        if(appData.state.is_complete) {
            if(appData.state.is_address) {
                if(appData.state.is_card) {

                    if(appData.state.is_course) {

                    }else{
                        btHtmlStr=`<div class="sale-bt-box" >
                        <a href="/course" class="uc-bt uc-bt-main choose-seat" ><img class="bt-img" src="images/bt-text-choose-seat.png" alt=""></a>
                    </div>`
                    }


                }else{
                    btHtmlStr=`<div class="sale-bt-box" >
                        <a href="/uc/address/#cardContainer" class="uc-bt uc-bt-main write-card" ><img class="bt-img" src="images/bt-text-write-card.png" alt=""></a>
                    </div>`
                }
            }else{
                btHtmlStr=`<div class="sale-bt-box" >
                    <a href="/uc/address" class="uc-bt uc-bt-main write-address" ><img class="bt-img" src="images/bt-text-write-address.png" alt=""></a>
                </div>`
            }
        }
        if(appData.state.is_bind) {

        }

        $("#show-bt-box-condition").append(btHtmlStr)


    }

    function showMessage() {
        var currentUrl=hb.location.url('path');
        var type=hb.location.url('?type')||0;
        var endTime=new Date("2016/07/07 20:00:00");
        var currentTime=new Date();
        var isKnownMin50=window.localStorage.getItem('min_50_deadline_known');
        var isKnownMin=window.localStorage.getItem('min_deadline_known');
        var isKnownMinVip=window.localStorage.getItem('min_deadline_known_vip');
        var isKnownMinSVip=window.localStorage.getItem('min_deadline_known_s_vip');

        // console.log(currentUrl);
        // console.log(isKnownMin50);
        // console.log(appData.state.is_complete);
        // console.log(appData.min_amount);


        if(!appData.state.is_complete){

            if(type==0){
                $('#show-discount-message').find("#discount-message-text").html(`
                    <div class="line" >8月13日， </div>
                    <div class="line">普通票最低票价已从 980元 调整为 1280元 </div>
                    `);
                if(isKnownMin!=1280){
                    showNotifyDialog();
                }
                //return;

                //switch (true){
                //    case appData.min_amount==980:
                //        $('#show-discount-message').find("#discount-message-text").html(`
                //    <div class="line" >8月8日晚20点， </div>
                //    <div class="line">普通票最低票价已从 880元 调整为 980元， </div>
                //    <div class="line">未完成支付但已特惠至 880元 的，</div>
                //    <div class="line">也已调整为 980元。</div>
                //    `);
                //        if(isKnownMin!=980&&isKnownMin!=880){
                //            showNotifyDialog();
                //        }
                //        break;
                //    case appData.min_amount==880:
                //        $('#show-discount-message').find("#discount-message-text").html(`
                //    <div class="line" >8月8日晚20点， </div>
                //    <div class="line">普通票最低票价将从 880元 调整为原价 980元， </div>
                //    <div class="line">未完成支付但已特惠至 880元 的，</div>
                //    <div class="line">也将调整为原价 980元。</div>
                //    `);
                //        if(isKnownMin!=appData.min_amount){
                //            showNotifyDialog();
                //        }
                //        break;
                //    case appData.min_amount==780:
                //        $('#show-discount-message').find("#discount-message-text").html(`
                //    <div class="line" >8月1日晚20点， </div>
                //    <div class="line">普通票最低票价将从 780元 调整为 880元， </div>
                //    <div class="line">未完成支付但已特惠至 780元 的，</div>
                //    <div class="line">也将调整为 880元。</div>
                //    `);
                //        if(isKnownMin!=appData.min_amount){
                //            showNotifyDialog();
                //        }
                //        break;
                //    case appData.min_amount==680:
                //        $('#show-discount-message').find("#discount-message-text").html(`
                //    <div class="line" >7月25日晚20点， </div>
                //    <div class="line">普通票最低票价将从 680元 调整为 780元， </div>
                //    <div class="line">未完成支付但已特惠至 680元 的，</div>
                //    <div class="line">也将调整为 780元。</div>
                //    `);
                //        if(isKnownMin!=appData.min_amount){
                //            showNotifyDialog();
                //        }
                //        break;
                //}
            }

            if(type==2){
                switch (true){
                    case appData.min_amount==1280:
                        $('#show-discount-message').find("#discount-message-text").html(`
                    <div class="line" >8月8日晚20点， </div>
                    <div class="line">VIP票最低票价已从 1180元 调整为 1280元， </div>
                    <div class="line">未完成支付但已特惠至 1180元 的，</div>
                    <div class="line">也已调整为 1280元。</div>
                    `);
                        if(isKnownMinVip!=1280&&isKnownMinVip!=1180){
                            showNotifyDialog();
                        }
                        break;
                    case appData.min_amount==1180:
                        $('#show-discount-message').find("#discount-message-text").html(`
                    <div class="line" >8月8日晚20点， </div>
                    <div class="line">VIP票最低票价将从 1180元 调整为原价 1280元， </div>
                    <div class="line">未完成支付但已特惠至 1180元 的，</div>
                    <div class="line">也将调整为原价 1280元。</div>
                    `);
                        if(isKnownMinVip!=appData.min_amount){
                            showNotifyDialog();
                        }
                        break;
                    case appData.min_amount==1080:
                        $('#show-discount-message').find("#discount-message-text").html(`
                    <div class="line" >8月1日晚20点， </div>
                    <div class="line">VIP票最低票价将从 1080元 调整为 1180元， </div>
                    <div class="line">未完成支付但已特惠至 1080元 的，</div>
                    <div class="line">也将调整为 1180元。</div>
                    `);
                        if(isKnownMinVip!=appData.min_amount){
                            showNotifyDialog();
                        }
                        break;
                    case appData.min_amount==980:
                        $('#show-discount-message').find("#discount-message-text").html(`
                    <div class="line" >7月25日晚20点， </div>
                    <div class="line">VIP票最低票价将从 980元 调整为 1080元， </div>
                    <div class="line">未完成支付但已特惠至 980元 的，</div>
                    <div class="line">也将调整为 1080元。</div>
                    `);
                        if(isKnownMinVip!=appData.min_amount){
                            showNotifyDialog();
                        }
                        break;
                }
            }


            // if(type==3){
            //     switch (true){
            //         case appData.min_amount==2980:
            //             $('#show-discount-message').find("#discount-message-text").html(`
            //         <div class="line" >8月8日晚20点， SVIP票最低票价</div>
            //         <div class="line">已从 2880元 调整为 2980元， </div>
            //         <div class="line">未完成支付但已特惠至 2880元 的，</div>
            //         <div class="line">也已调整为 2980元。</div>
            //         `);
            //             if(isKnownMinSVip!=2980&&isKnownMinSVip!=2880){
            //                 showNotifyDialog();
            //             }
            //             break;
            //         case appData.min_amount==2880:
            //             $('#show-discount-message').find("#discount-message-text").html(`
            //         <div class="line" >8月8日晚20点，SVIP票最低票价 </div>
            //         <div class="line"将从 2880元 调整为原价 2980元， </div>
            //         <div class="line">未完成支付但已特惠至 2880元 的，</div>
            //         <div class="line">也将调整为原价 2980元。</div>
            //         `);
            //             if(isKnownMinSVip!=appData.min_amount){
            //                 showNotifyDialog();
            //             }
            //             break;
            //         case appData.min_amount==2780:
            //             $('#show-discount-message').find("#discount-message-text").html(`
            //         <div class="line" >8月1日晚20点， SVIP票最低票价</div>
            //         <div class="line">将从 2780元 调整为 2880元， </div>
            //         <div class="line">未完成支付但已特惠至 2780元 的，</div>
            //         <div class="line">也将调整为 2880元。</div>
            //         `);
            //             if(isKnownMinSVip!=appData.min_amount){
            //                 showNotifyDialog();
            //             }
            //             break;
            //         case appData.min_amount==2680:
            //             $('#show-discount-message').find("#discount-message-text").html(`
            //         <div class="line" >7月25日晚20点，SVIP票最低票价 </div>
            //         <div class="line">将从 2680元 调整为 2780元， </div>
            //         <div class="line">未完成支付但已特惠至 2680元 的，</div>
            //         <div class="line">也将调整为 2780元。</div>
            //         `);
            //             if(isKnownMinSVip!=appData.min_amount){
            //                 showNotifyDialog();
            //             }
            //             break;
            //     }
            // }





        }
        function showNotifyDialog() {
            
            


            if(currentUrl.indexOf('/uc/index')>-1){
                $('#show-discount-message').show().find('.dialog').addClass('animated zoomIn');
            }

            if(currentUrl.indexOf('/uc/ticket')>-1){
                if(appData.is_owner){
                    $('#show-discount-message').show().find('.dialog').addClass('animated zoomIn');
                }
            }


        }




        //consoel.log($("#show-discount-message").find("[remove-modal]"))
        $("#show-discount-message").find("[remove-modal]").on('click',function () {
            //alert(1)
            var type=hb.location.url('?type')||0;
            //alert(type)
            if(type==0){
                window.localStorage.setItem('min_deadline_known',appData.min_amount);
            }
            if(type==2){
                window.localStorage.setItem('min_deadline_known_vip',appData.min_amount);
            }
            if(type==3){
                window.localStorage.setItem('min_deadline_known_s_vip',appData.min_amount);
            }


            $("#show-discount-message").remove();
        });





    }
    

    return{
        init:init,
        ticketProgress:ticketProgress2,
        guideShare:guideShare,
        renderPriceNumber:renderPriceNumber,
        getDiscount:getDiscount,
        buyTicket:buyTicket,
        showButton:showButton,
        showMessage:showMessage,
    }
}());

