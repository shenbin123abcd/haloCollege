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
            var defaultData={
                title: window.document.title,
                content: '',
                link: window.location.href,
                logo: 'http://7xopel.com2.z0.glb.qiniucdn.com/college/wechat_logo.png'
            }
            if (typeof(shareDate) !== 'undefined') {
                this.data.title = shareDate.title||defaultData.title;
                this.data.content = shareDate.content||defaultData.content;
                this.data.link = shareDate.link||defaultData.link;
                this.data.logo = shareDate.logo||defaultData.logo;
            }else{
                this.data=defaultData
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
        data:{},
        act: function() {
            var _this = this;
            $.ajax({
                url: 'http://ke.halobear.com/courses/getWechat',
                // type: 'get',
                data: {
                    url: encodeURIComponent(window.location.href.split('#')[0])
                },
                dataType: 'jsonp',
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
