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
                title: '婚礼业全新课程体系  幻熊研习社匠心打造',
                content: '幻熊研习社联合国内外著名婚礼人携手打造婚礼行业全新课程体系，涉及婚礼策划，销售谈单，花艺设计，宴会设计，婚礼手绘......带来最经典的行业干货分享。',
                link: 'http://ke.halobear.com/',
                logo: 'http://ke.halobear.com/Public/Ke/images/wechat-share-default.png'
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
