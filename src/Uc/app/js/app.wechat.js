app.wechat=(function(){
    "use strict";
    function init() {
        wechat.init();
    }
    // var id=hb.Cookies.get('wechat_ticket_id');

    var wechat = {
        init: function() {
            var _this = this;
            var appData=window.appData||{};
            if (typeof(appData.share) !== 'undefined') {
                this.data.title = appData.share.title||this.data.title;
                this.data.content = appData.share.content||this.data.content;
                this.data.link = appData.share.link||this.data.link;
                this.data.logo = appData.share.logo||this.data.logo;
            }
            $.getScript('http://res.wx.qq.com/open/js/jweixin-1.0.0.js', function(data, textStatus) {
                if (textStatus == 'success') {
                    _this.act();
                }
            });
        },
        data:{
            title: '2016-2017中国婚礼策划金熊奖',
            content: '精装限量版金熊奖参赛作品集（套装共2册）',
            link: 'http://ke.halobear.com/uc/book',
            logo: 'http://7ktq5x.com1.z0.glb.clouddn.com/Wfc2016/uc/images/book/goldbear-book-77e9fbb422.jpg?imageView2/1/w/200/h/200'
        },
        act: function() {
            var _this = this;
            $.ajax({
                url: '/uc/getWechat',
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
    }
}());
