;app.modal=(function(){
    "use strict";
        var _alert=function(options){
            var deferred = $.Deferred();
            var defaults = {
                title:'提示',
                content:'提示内容',
                btn:'确定',
            };

            if(typeof options=="string"){
                defaults = $.extend(defaults,{
                    content:options
                });
            }else{

            }

            var settings = $.extend( {},defaults, options );

            var alertHtmlStr='' +
                '<div class="weui_dialog_alert" >'+
                '<div class="weui_mask"></div>'+
                '<div class="weui_dialog" style="display: none;" >'+
                '<div class="weui_dialog_hd"><strong class="weui_dialog_title">'+
                settings.title+
                '</strong></div>' +
                '<div class="weui_dialog_bd">'+
                settings.content +
                '</div>' +
                '<div class="weui_dialog_ft">' +
                '<a href="javascript:;" class="weui_btn_dialog primary">'+settings.btn +'</a>' +
                '</div>' +
                ' </div>' +
                ' </div>' +
                '';
            var $alertHtml=$(alertHtmlStr);
            $("body").append($alertHtml);
            $alertHtml.find(".weui_dialog").fadeIn(200);
            var $confirmBt=$alertHtml.find(".weui_btn_dialog");
            $confirmBt.on('click',function(){
                $alertHtml.remove();
                deferred.resolve(true);
            });
            return deferred.promise();
        };

        var _confirm=function(options){
            var deferred = $.Deferred();
            var defaults = {
                title:'提示',
                content:'提示内容',
                rightBtn:'确定',
                leftBtn:'取消',
            };

            if(typeof options=="string"){
                defaults = $.extend(defaults,{
                    content:options
                });
            }else{

            }

            var settings = $.extend( {},defaults, options );
            var confirmHtmlStr=`
                    <div class="weui_dialog_confirm">
                    <div class="weui_mask"></div>
                    <div class="weui_dialog">
                    <div class="weui_dialog_hd"><strong class="weui_dialog_title">${settings.title}</strong></div>
                    <div class="weui_dialog_bd">${settings.content}</div>
                    <div class="weui_dialog_ft">
                    <a href="javascript:;" class="weui_btn_dialog default">${settings.leftBtn}</a>
                    <a href="javascript:;" class="weui_btn_dialog primary">${settings.rightBtn}</a>
                    </div>
                    </div>
                    </div>
                    `;


            var $confirmHtml=$(confirmHtmlStr);
            $("body").append($confirmHtml);
            $confirmHtml.find(".weui_dialog").fadeIn(200);
            var $confirmBt=$confirmHtml.find(".weui_btn_dialog.primary");
            $confirmBt.on('click',function(){
                $confirmHtml.remove();
                deferred.resolve(true);
            });
            var $cancelBt=$confirmHtml.find(".weui_btn_dialog.default");
            $cancelBt.on('click',function(){
                $confirmHtml.remove();
                deferred.reject(false);
            });
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
                '<p class="weui_toast_content">数据加载中</p>' +
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

        var toast=function(msg){
            var toastHtmlStr='' +
                '<div class="weui_msg_toast" >' +
                '<div class="weui_mask_transparent"></div>' +
                '<div class="weui_toast">' +
                '<i class="weui_icon_toast"></i>' +
                '<p class="weui_toast_content">'+msg+'</p>' +
                '</div>' +
                '</div>' +
                '';
            var $toastHtml=$(toastHtmlStr);
            $("body").append($toastHtml);
            var $weui_toast=$toastHtml.find(".weui_toast");
            $weui_toast.fadeIn(200);
            var hideToast=function(){
                $toastHtml.fadeOut(400).remove();
            };
            setTimeout(hideToast,600);
        };

        return{
            alert:_alert,
            confirm:_confirm,
            loading:loading,
            toast:toast,
        };
}());


