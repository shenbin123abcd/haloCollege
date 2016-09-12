app.service=(function(){
    "use strict";
    function findRegion(data){
        var deferred=$.Deferred();
        //console.log(data);
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'POST',
                url:'/userinfo/getRegion',
                data:data,
                dataType:'json',
                success: function(res) {
                    if(res.status==1){
                        deferred.resolve(res.data);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            })
        }
        return deferred.promise();
    }
    function findProvince(){
        var deferred=$.Deferred();
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'POST',
                dataType:'json',
                url:'/userinfo/getRegion',
                success:function(res){
                    if(res.status==1){
                        deferred.resolve(res.data);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error:function(res){
                    deferred.reject("网络繁忙请稍后再试");
                }
            })
        }
        return deferred.promise();
    }
    function findCity(data){
        var deferred=$.Deferred();
        sendXhr();
        function sendXhr(){
            $.ajax({
                type:'POST',
                dataType:'json',
                url:'/userinfo/getRegion?province',
                data:data,
                success:function(res){
                    if(res.status==1){
                        deferred.resolve(res.data);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error:function(res){
                    deferred.reject("网络繁忙稍后再试");
                }
            })
        }
        return deferred.promise();
    }
    function sendTicketAddress(data){
        var deferred = $.Deferred();
        var hb_Token='Bearer '+hb.Cookies.getJSON('halobear');
        //console.log(data);
        switch (true){
            case !data.company_name:
                deferred.reject('请输入公司名');
                break;
            case !data.name:
                deferred.reject('请输入姓名');
                break;
            case !data.phone:
                deferred.reject('请输入手机号码');
                break;
            case !hb.validation.checkPhone(data.phone):
                deferred.reject('手机号码格式不正确！');
                break;
            case !data.idnum:
                deferred.reject('请输入用户的身份证号');
                break;
            case data.idnum.length!=4:
                deferred.reject('请输入用户身份证号后四位');
                break;
            case !data.province:
                deferred.reject('请选择所在省份');
                break;
            case !data.city:
                deferred.reject('请选择所在城市');
                break;
            case !data.region:
                deferred.reject('请选择所在区');
                break;
            case !data.address:
                deferred.reject('请输入详细地址');
                break;
            case !data.method:
                deferred.reject('请选择门票快递方式');
                break;

            default:
                sendXhr();
                //deferred.resolve(data);
        }
        function sendXhr(){
            $.ajax({
                url: "/userinfo/userinfoPost",
                data: data,
                type:'POST',
                dataType:"json",
                beforeSend:function(XMLHttpRequest){
                    XMLHttpRequest.setRequestHeader("Authorization",hb_Token);
                },
                success: function(res) {
                    if(res.status==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            })
        }
        return deferred.promise();
    };
    function qiniu(btn,success,allComplete){
        Qiniu.uploader({
            runtimes: 'html5',    //上传模式,依次退化
            browse_button: btn,       //上传选择的点选按钮，**必需**
            //uptoken_url: '/token',            //Ajax请求upToken的Url，**强烈建议设置**（服务端提供）
            uptoken : config.token, //若未指定uptoken_url,则必须指定 uptoken ,uptoken由其他程序生成
            // unique_names: true, // 默认 false，key为文件名。若开启该选项，SDK为自动生成上传成功后的key（文件名）。
            // save_key: true,   // 默认 false。若在服务端生成uptoken的上传策略中指定了 `sava_key`，则开启，SDK会忽略对key的处理
            domain: 'http://qiniu-plupload.qiniudn.com/',   //bucket 域名，下载资源时用到，**必需**
            get_new_uptoken: false,  //设置上传文件的时候是否每次都重新获取新的token
            //container: 'container',           //上传区域DOM ID，默认是browser_button的父元素，
            max_file_size: '5mb',           //最大文件体积限制
            //flash_swf_url: '/Public/Wfc2016/qiniu/Moxie.swf',  //引入flash,相对路径
            max_retries: 3,                   //上传失败最大重试次数
            dragdrop: true,                   //开启可拖曳上传
            //drop_element: 'company-input-right',        //拖曳上传区域元素的ID，拖曳文件或文件夹后可触发上传
            chunk_size: '4mb',
            //分块上传时，每片的体积
            auto_start: true,                 //选择文件后自动上传，若关闭需要自己绑定事件触发上传
            x_vars : {                        //自定义变量
                'filetype' : "head_img"
            },
            // filters: {
            //     mime_types : [
            //         { title : "Image files", extensions : "jpg,gif,png" }
            //     ]
            // },
            multi_selection:false,
            init: {
                'FilesAdded': function(up, file) {
                    app.service.loading.show();
                },
                'BeforeUpload': function(up, file) {
                    // 每个文件上传前,处理相关的事情
                },
                'UploadProgress': function(up, file) {
                    // 每个文件上传时,处理相关的事情
                    //$('#avatar-progress').show().find('.progress-bar').css('width',up.total.percent+'%').text(up.total.percent+'%');
                },
                'FileUploaded': success,
                // 'FileUploaded': function(up){
                // 	console.log(up)
                // },
                'Error': function(up, err, errTip) {
                    //上传出错时,处理相关的事情
                    //console.log(err)
                    //window.index.DIALOG.error('网络繁忙请稍候再试');
                    if(err.code==-601){
                        hb.lib.weui.alert({
                            title:'温馨提示',
                            content:'只能上传jpg,gif,png格式的图片',
                            btn:'确定',
                        })
                    }else{
                        hb.lib.weui.alert({
                            title:'温馨提示',
                            content:err.message,
                            btn:'确定',
                        })
                    }
                },
                // 'UploadComplete': function() {
                // 	//队列文件处理完毕后,处理相关的事情
                // },
                'UploadComplete': allComplete,
                'Key': function(up, file) {
                    // 若想在前端对每个文件的key进行个性化处理，可以配置该函数
                    // 该配置必须要在 unique_names: false , save_key: false 时才生效

                    //var key = "";
                    //// do something with key here
                    //return key
                }
            }
        });
    };
    function toInterview(data){
        var deferred=$.Deferred();
        switch(true){
            case !data.card_name:
                deferred.reject("请输入姓名");
                break;
            case !data.position:
                deferred.reject("请输入职位名");
                break;
            case !data.card_company:
                deferred.reject("请输入公司名");
                break;
            case !data.head_img:
                deferred.reject("请上传您的头像");
                break;
            default:
                deferred.resolve(data);
        }
        return deferred.promise();
    }
    function sendCardInfo(data){
        var deferred=$.Deferred();
        var hb_Token='Bearer '+hb.Cookies.getJSON('halobear');
        //console.log(data);
        sendXhr();
        function sendXhr(){
            $.ajax({
                url:'/userinfo/cardInfoPost',
                data:data,
                type:'POST',
                dataType:'json',
                beforeSend:function(XMLHttpRequest){
                    XMLHttpRequest.setRequestHeader("Authorization",hb_Token);
                },
                success: function(res) {
                    if(res.status==1){
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
    function reEdit(data){
        var deferred=$.Deferred();
        var hb_Token='Bearer '+hb.Cookies.getJSON('halobear');
        switch(true){
            case !data.head_img:
                deferred.reject("请上传您的头像");
                break;
            case !data.name:
                deferred.reject("请输入姓名");
                break;
            case !data.position:
                deferred.reject("请输入职位名");
                break;
            case !data.company_name:
                deferred.reject("请输入公司名");
                break;
            default:
                sendXahr();
                //console.log(data);
        }
        function sendXhr(){
            $.ajax({
                url:'/userinfo/headimgPost',
                data:data,
                type:'POST',
                dataType:'json',
                beforeSend:function(XMLHttpRequest){
                    XMLHttpRequest.setRequestHeader("Authorization",hb_Token);
                },
                success: function(res) {
                    if(res.status==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            })
        }
        return deferred.promise();
    }
    function drawPost(data){
        var deferred=$.Deferred();
        sendXhr();
        function sendXhr(){
            $.ajax({
                url:'/uc/getResult',
                //data:data,
                type:"GET",
                dataType:'json',
                success:function(res){
                    if(res.status==1){
                        deferred.resolve(res.data);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error:function(error){
                    deferred.reject('网络繁忙稍后再试');
                }
            })
        }
        return deferred.promise();
    }
    function myDrawPost(){
        var deferred=$.Deferred();
        sendXhr();
        function sendXhr(){
            $.ajax({
                url:'/uc/mydraw',
                //data:data,
                type:"GET",
                dataType:'json',
                success:function(res){
                    if(res.status==1){
                        deferred.resolve(res.data);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error:function(error){
                    deferred.reject('网络繁忙稍后再试');
                }
            })
        }
    }
    function addAddress(data){
        var deferred = $.Deferred();
        //console.log(data);
        switch (true){
            case !data.name:
                deferred.reject('请输入姓名');
                break;
            case !data.phone:
                deferred.reject('请输入手机号码');
                break;
            case !hb.validation.checkPhone(data.phone):
                deferred.reject('手机号码格式不正确！');
                break;
            case !data.province:
                deferred.reject('请选择所在省份');
                break;
            case !data.city:
                deferred.reject('请选择所在城市');
                break;
            case !data.region:
                deferred.reject('请选择所在区');
                break;
            case !data.address:
                deferred.reject('请输入详细地址');
                break;
            default:
                sendXhr();
            //deferred.resolve(data);
        }
        function sendXhr(){
            $.ajax({
                url: "/uc/addAddress",
                data: data,
                type:'POST',
                dataType:"json",
                success: function(res) {
                    if(res.status==1){
                        deferred.resolve(res);
                    }else{
                        deferred.reject(res.info);
                    }
                },
                error: function(error) {
                    deferred.reject('网络繁忙请稍候再试');
                }
            })
        }
        return deferred.promise();
    };
    var loading=(function(){
        var loadingHtmlStr='' +
            '<div id="loadingToast" class="weui_loading_toast" >' +
            '<div class="weui_mask_transparent"></div>' +
            '<div class="weui_toast">' +
            '<div class="weui_loading">' +
            '<div class="weui_loading_leaf weui_loading_leaf_0"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_1"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_2"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_3"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_4"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_5"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_6"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_7"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_8"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_9"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_10"></div>' +
            '<div class="weui_loading_leaf weui_loading_leaf_11"></div>' +
            '</div>' +
            '<p class="weui_toast_content">头像上传中</p>' +
            '</div>' +
            '</div>' +
            '';
        var $loadingHtml=$(loadingHtmlStr);
        var show=function(){
            $("body").append($loadingHtml);
        };
        var hide=function(){
            $loadingHtml.remove();
        };

        return{
            show:show,
            hide:hide
        }
    }());

    return{
        sendTicketAddress:sendTicketAddress,
        qiniu:qiniu,
        toInterview:toInterview,
        sendCardInfo:sendCardInfo,
        reEdit:reEdit,
        findRegion:findRegion,
        findProvince:findProvince,
        findCity:findCity,
        drawPost:drawPost,
        myDrawPost:myDrawPost,
        addAddress:addAddress,
        loading:loading,
    }

}());