app.wechat=(function(){
    "use strict";
    function init(shareDate) {
        wechat.init(shareDate);
    }
    function getShareDate() {
        return wechat.data;
    }
    // var id=hb.Cookies.get('wechat_ticket_id');
    var wechat = {
        init: function(shareDate) {
            var _this = this;
            if (typeof(shareDate) !== 'undefined') {
                this.data.title = shareDate.title||_this.data.title;
                this.data.content = shareDate.content||_this.data.content;
                this.data.link = shareDate.link||_this.data.link;
                this.data.logo = shareDate.logo||_this.data.logo;
            }
            if(_this.loadedScript){
                _this.act();
            }else{
                $.getScript('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', function(data, textStatus) {
                    if (textStatus == 'success') {
                        _this.loadedScript=true;
                        _this.act();
                    }
                });
            }
        },
        loadedScript:false,
        data:{
            title: '幻熊课堂',
            content: '幻熊课堂',
            link: 'http://ke.halobear.com/',
            logo: 'http://ke.halobear.com/Public/Ke/images/wechat-share-default.png'
        },
        act: function() {
            var _this = this;
            $.ajax({
                url: '/courses/getWechat',
                type: 'get',
                data: {
                    url: encodeURIComponent(window.location.href.split('#')[0])
                },
                dataType: 'json',
                success: function(ret) {
                    wx.config($.extend({
                        // debug:1,
                        jsApiList: ['onMenuShareAppMessage', 'onMenuShareTimeline']
                    }, ret));
                    wx.ready(function() {
                        wx.onMenuShareTimeline({
                            title: _this.data.content,
                            desc: "",
                            link:  _this.data.link,
                            imgUrl:  _this.data.logo,
                            dataUrl: '',
                            success: function(res) {},
                            cancel: function() {}
                        });
                        wx.onMenuShareAppMessage({
                            title: _this.data.title,
                            desc:  _this.data.content,
                            link:  _this.data.link,
                            imgUrl:  _this.data.logo,
                            dataUrl: '',
                            success: function(res) {},
                            cancel: function() {}
                        });
                        wx.onMenuShareQQ({
                            title: _this.data.title,
                            desc:  _this.data.content,
                            link:  _this.data.link,
                            imgUrl:  _this.data.logo,
                            dataUrl: '',
                            success: function(res) {},
                            cancel: function() {}
                        });
                    });
                }
            });
        },
        shareCount: function(){

        }
    };




    return{
        init:init,
        getShareDate:getShareDate,
    }
}());
