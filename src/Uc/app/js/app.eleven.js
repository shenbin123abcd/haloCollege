;app.eleven=(function(){
    "use strict";
    var product_1;
    var product_2;
    var ts ;
    function init(){
        inputMask();
        addAddress();

        //

        product_2.add();
        countDown()
        // setTimeout(function(){$('#product-2').click();}, 200);
    }
    function inputMask(){
        product_1=hb.numberBox('#product-1',{
            max:10,
            onChange:function(val){
                if(val>0){
                    product_2.reset();
                    $("#price").text(399*val)
                    $("#pay-bt").removeClass('disabled')
                }else{
                    product_2.reset();
                    //product_2.enable();
                    $("#price").text('-');
                    $("#pay-bt").addClass('disabled')

                }

            }
        });
        product_2=hb.numberBox('#product-2',{
            max:10,
            onChange:function(val){
                if(val>0){
                    product_1.reset();
                    $("#price").text(299*val)
                    $("#pay-bt").removeClass('disabled')

                }else{
                    product_1.reset();
                    //product_1.enable();
                    $("#price").text('-')
                    $("#pay-bt").addClass('disabled')
                }
            }
        });
        productHandleInit();

    }

    function productHandleInit(){
        $('#product-1').on('click',function(){
            if(product_1.val()==0){
                product_1.add();
            }
        });
        $('#product-2').on('click',function(){
            if(product_2.val()==0){
                product_2.add();
            }
        });
        $('#product-1 .number-box').on('click',function(event){
            event.stopPropagation();
        });
        $('#product-2 .number-box').on('click',function(event){
            event.stopPropagation();
        });
    }

    function addAddress(){
        if(window.appData.is_address==0){
            var htmlStr=`
             <div class="tit-box">
                  快递信息
              </div>
              <div class="address-box">
                  <div class="item-list-box">
                                    <div class="item">
                        <div class="title">收件人名</div>
                        <div class="content">
                            <input class="control" id="name" type="text" placeholder="请输入收件人姓名">
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">手机号码</div>
                        <div class="content">
                            <input class="control" id="phone" type="number" placeholder="请输入手机号">
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">所在省份</div>
                        <div class="content">
                            <select app-region-select-level3="1" data-for="address" id="province"  class="control" >
                                <option value="">请选择省份</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">所在城市</div>
                        <div class="content">
                             <select app-region-select-level3="2" data-for="address" id="city" class="control" >
                                <option value="">请选择城市</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">所在区县</div>
                        <div class="content">
                             <select app-region-select-level3="3" data-for="address" id="region" class="control" >
                                <option value="">请选择区县</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">详细地址</div>
                        <div class="content">
                            <input class="control" id="address" type="text" placeholder="街道编号、名称，楼宇地址">
                        </div>
                    </div>
                  </div>
              </div>
            `;
            $('#deliver-wrapper').append(htmlStr);
            app.regionSelect.init();
            initSubmit();
        }else{
            initSubmit();
        }
    }

    function initSubmit(){
        $('#pay-bt').on('click',function(event){
            //event.preventDefault();
            // console.log(111)
            var num1=product_1.val();
            var num2=product_2.val();
            if(num1==0&&num2==0){


                if(ts<=0){
                    hb.lib.weui.alert('活动已结束');
                }else{
                    hb.lib.weui.alert('请选择您要购买的商品，并正确填写数量');
                }
                return;
            }
            if(window.appData.is_address==0){
                if(num1>0&&num2==0){
                    payMoney();
                }else if(num2>0&&num1==0){
                    hb.util.loading.show();
                    app.service.addAddress({
                        'name':$.trim($("#name").val()),
                        'phone':$("#phone").val(),
                        'province':$('#province').val(),
                        'city':$('#city').val(),
                        'region':$('#region').val(),
                        'address':$.trim($("#address").val()),
                    }).then(function(res){
                        hb.util.loading.hide();
                        hb.lib.weui.alert('添加地址成功').then((res)=>{
                            payMoney();
                        });
                    },function(res){
                        hb.util.loading.hide();
                        hb.lib.weui.alert({
                            title:'温馨提示',
                            content:res,
                            btn:'确定',
                        })
                    })

                }else{
                    // console.log(ts)
                    if(ts<=0){
                        hb.lib.weui.alert('活动已结束');
                    }else{
                        hb.lib.weui.alert('自提和快递只能选一种，请重新选择');

                    }
                }
            }else{
                payMoney();
            }

        });
    }

    function payMoney(){
        var num1=product_1.val();
        var num2=product_2.val();
        var data;
        var name;
        if(num1>0&&num2==0){
            data={
                type: 1,
                num:num1,
            };
            name='product_1-'+data.num;
        }else if(num2>0&&num1==0){
            data={
                type: 3,
                num:num2,
            };
            name='product_2-'+data.num;
        }else{
            // console.log(ts)
            if(ts<=0){
                hb.lib.weui.alert('活动已结束');
            }else{
                hb.lib.weui.alert('自提和快递只能选一种，请重新选择');
            }
        }


        app.pay3.callPay(name).callpay({
            data:data,
            onSuccess:function (res) {
                hb.lib.weui.alert('支付成功').then((res)=>{
                    window.location.href='/uc/caseRecord'
                });
            },
            onFail:function (res) {
                hb.lib.weui.alert(res);
            },
        });
    }
    function countDown() {
        timer();

        function timer() {
            ts = (new Date(2016, 10, 12, 0, 0, 0)).getTime() - (new Date().getTime());//计算剩余的毫秒数
            if(ts<=0){
                product_2.val(0);
                product_2.disable();
                return
            }

            var dd = parseInt(ts / 1000 / 60 / 60 / 24, 10);//计算剩余的天数
            var hh = parseInt(ts / 1000 / 60 / 60 , 10);//计算剩余的小时数
            var mm = parseInt(ts / 1000 / 60 % 60, 10);//计算剩余的分钟数
            var ss = parseInt(ts / 1000 % 60, 10);//计算剩余的秒数
            dd = checkTime(dd);
            hh = checkTime(hh);
            mm = checkTime(mm);
            ss = checkTime(ss);

            render(dd , hh, mm , ss);

            setTimeout(timer,1000);
        }
        function checkTime(i) {
            if (i < 10) {
                i = "0" + i;
            }
            return i;
        }
        function render(dd , hh, mm , ss) {
            // console.log( dd + "天" + hh + "时" + mm + "分" + ss + "秒");
            $("#eleven-hh").text(hh);
            $("#eleven-mm").text(mm);
            $("#eleven-ss").text(ss);

        }
    }


    return{
        init:init,
    }
}());


