//hb_scroll
;(function($){
    "use strict";

    function scroll(dom,options){
        var options=options||{};
        var defaults={
            bottom:100,
            reachBottom:function(){
                //console.log('f')
            }
        };
        var settings = $.extend( {}, defaults, options );

        if(typeof dom=='string'){
            this.$container=$(dom);
        }else{
            this.$container=$(dom);
        }
        this.$container.on('hb_scroll.reachBottom',function(){
            settings.reachBottom()
        });
        this.$container.on('scroll',scrolling);

        var _this=this;
        function scrolling(){
            var cH=_this.$container.outerHeight();
            var cST=_this.$container.scrollTop();
            var cSH;
            if($.isWindow(_this.$container.get()[0])){
                cSH=$(document).height();
            }else{
                cSH=_this.$container[0].scrollHeight;
            }
            //console.log(cH,cSH,cSH-cST-cH);
            if(cSH-cST-cH<settings.bottom){
                _this.$container.trigger('hb_scroll.reachBottom');
            }
        }
        this.off=function(){
            //console.log(_this.$container)
            _this.$container.off('scroll',scrolling);
            _this.$container.off('hb_scroll.reachBottom');
        }
    }





    hb.scroll=function(dom,options){
        return new scroll(dom,options);
    };


})(jQuery);
