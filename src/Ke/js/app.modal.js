;app.modal=(function(){
    "use strict";
    var _alert=function(settings={
        pic:'able-seat',
        content:'提示内容',
        btn:'确定',
    }){
        var deferred = $.Deferred();
        var pic='';
        var ableSeatImg='/images/able-seat.png';
        var unableSeatImg='/images/unable-seat.png'
        if(settings.pic=='able-seat'){
            pic=ableSeatImg
        }
        var alertHtmlStr=`
                <div class="modal-alert-block">
                    <div class="modal-block-mask"></div>
                    <div class="modal-block-dialog" style="display: none;" >
                        <div class='modal-dialog-body'>
                            <div class='modal-dialog-pic'><img src="${pic}" alt="" /></div>
                            <div class="modal-dialog-text">${settings.content}</div>
                        </div>
                        <div class='modal-dialog-footer'>
                            ${settings.btn}
                        </div>
                    </div>

                </div>`;
            var $alertHtml=$(alertHtmlStr);
            $("body").append($alertHtml);
            $alertHtml.find(".modal-block-dialog").fadeIn(200);
            var $confirmBt=$alertHtml.find(".modal-dialog-footer");
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

        return{
            alert:_alert,
            confirm:_confirm,
            loading:loading,
        };
}());


