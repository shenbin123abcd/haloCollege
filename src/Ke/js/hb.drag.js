;(function(){
    "use strict";

    function drag(dom,options){
        var options=options||{};
        var defaults={
            maxScale:3,
            minScale:0.5,
            position:{
                left:0,
                top:0,
                scale:1
            },
            onPinchMove:function(){
                //console.log('f')
            },
            onPinchEnd:function(){
                //console.log('f')
            },
            onPanMove:function(){
                //console.log('f')
            },
            onPanEnd:function(){
                //console.log('f')
            },
        };
        this.settings = $.extend( {}, defaults, options );

        if(typeof dom=='string'){
            this.element=document.querySelector(dom);
        }else{
            this.element=dom;
        }
        this.$element=$(this.element);
        this.init();
        //console.log($el,el);
    }


    drag.prototype.init=function(){
        var _this=this;
        this.hammertime = new Hammer(this.element, {});
        this.hammertime.get('pinch').set({ enable: true });
        this.hammertime.get('pan').set({ direction: Hammer.DIRECTION_ALL });

        var element = this.element;
        var $element = this.$element;
        var $elementParent = this.$element.parent();

        var objWidth = $element.outerWidth();
        var objHeight = $element.outerHeight();
        var objParentWidth = $elementParent.innerWidth();
        var objParentHeight = $elementParent.innerHeight();
        var objParentBorderLeftWidth = Number($elementParent.css("border-left-width").replace(/px|rem|%|em/,''));
        var objParentBorderTopWidth = Number($elementParent.css("border-top-width").replace(/px|rem|%|em/,''));
        var objParentBorderOffsetLeft = $elementParent.offset().left;
        var objParentBorderOffsetTop = $elementParent.offset().top;

        var elementParentRect =$elementParent.get()[0].getBoundingClientRect();

        var leftGap=objParentBorderLeftWidth+elementParentRect.left;
        var topGap=objParentBorderTopWidth+elementParentRect.top;


        //console.log(leftGap,topGap);

        if(objParentWidth/objWidth>objParentHeight/objHeight){
            this.settings.minScale=objParentWidth/objWidth;
        }else{
            this.settings.minScale=objParentHeight/objHeight;
        }


        var position=this.settings.position;


        var lNow=position.left;
        var tNow=position.top;

        var scaleNow=position.scale;
        var scaleLast=0;
        var center={
            x:objWidth/2,
            y:objHeight/2,
        };
        this.settings.center=center;


        this.settings.constant={
            leftGap:leftGap,
            topGap:topGap,
        };



        $element.css({
            transform: `translate3d(${lNow}px,${tNow}px,0)`,
        });

        //this.hammertime.on('swipe', function(ev) {
        //    console.log('swipe',ev);
        //});

        this.hammertime.on('panmove', function(ev) {
            //console.log('pan',ev);
            //console.log('pan',lNow,tNow);

            //console.log(ev.deltaX,ev.deltaY,ev.velocityX,ev.velocityY,ev.distance);

            lNow=ev.deltaX+position.left;
            tNow=ev.deltaY+position.top;



            //lNow=(ev.deltaX+position.left)*scaleNow;
            //tNow=(ev.deltaY+position.top)*scaleNow;


            //if( lNow<(objParentWidth-objWidth+(objWidth-objWidth*scaleNow-(center.x-center.x*scaleNow))) ){
            //    lNow=objParentWidth-objWidth+(objWidth-objWidth*scaleNow-(center.x-center.x*scaleNow))
            //}
            //
            //
            //if( tNow<(objParentHeight-objHeight+(objHeight-objHeight*scaleNow-(center.y-center.y*scaleNow))) ){
            //    tNow=objParentHeight-objHeight+(objHeight-objHeight*scaleNow-(center.y-center.y*scaleNow))
            //}



            //
            //if( lNow<(objParentWidth-objWidth) ){
            //    lNow=objParentWidth-objWidth;
            //}
            //
            //if( tNow<(objParentHeight-objHeight) ){
            //    tNow=objParentHeight-objHeight;
            //}



            if( lNow<(objParentWidth-objWidth*scaleNow) ){
                lNow=objParentWidth-objWidth*scaleNow;
            }

            if( tNow<(objParentHeight-objHeight*scaleNow) ){
                tNow=objParentHeight-objHeight*scaleNow;
            }


            //if( lNow>-(center.x-center.x*scaleNow)){
            //    lNow=-(center.x-center.x*scaleNow);
            //}
            //
            //
            //if( tNow>-(center.y-center.y*scaleNow)){
            //    tNow=-(center.y-center.y*scaleNow);
            //}


            if( lNow>0){
                lNow=0;
            }

            if( tNow>0){
                tNow=0;
            }





            //console.log(lNow,tNow)

            //
            //$element.css({
            //    transform: `translate3d(${lNow}px,${tNow}px,0)`,
            //});
            $element.css({
                transform: `translate3d(${lNow}px,${tNow}px,0) scale3d(${position.scale},${position.scale},${position.scale})`,
                transformOrigin: `0px 0px 0px`,
            });

            //console.log(lNow,tNow);
            //position.left=lNow;
            //position.top=tNow;
            _this.settings.onPanMove({
                left:lNow,
                top:tNow,
                scale:position.scale,
            });
        });

        this.hammertime.on('panend', function(ev) {
            position.left=lNow;
            position.top=tNow;
            //console.log('panpanend',lNow,tNow);
            //console.log('pinch',ev);
            _this.settings.onPanEnd(position);

        });



        this.hammertime.on('pinchstart', function(ev) {
            lNow=position.left;
            tNow=position.top;
            console.log('ev pinchstart',ev);
            console.log('ev.scale pinchstart',ev.scale);
            elementParentRect =$elementParent.get()[0].getBoundingClientRect();
            leftGap=objParentBorderLeftWidth+elementParentRect.left;
            topGap=objParentBorderTopWidth+elementParentRect.top;
            _this.settings.constant={
                leftGap:leftGap,
                topGap:topGap,
            };



            //center={
            //    x:ev.center.x-leftGap-lNow,
            //    y:ev.center.y-topGap-tNow,
            //};
            //console.log(center,lNow,tNow)
            //$element.css({
            //    transformOrigin: `${ev.center.x-leftGap-lNow}px ${ev.center.y-topGap-tNow}px 0`,
            //});
            scaleLast=0;
        });


        this.hammertime.on('pinchmove', function(ev) {
            //console.log('pinch',ev);
            //console.log('lNow,tNow',lNow,tNow);
            //console.log('ev.scale pinchmove',ev.scale);

            if(scaleLast){
                var deltaScale=ev.scale-scaleLast;
            }else{
                var deltaScale=0;
            }
            //console.log('deltaScale',deltaScale);

            scaleNow=position.scale+deltaScale;

            if(scaleNow>_this.settings.maxScale){
                scaleNow=_this.settings.maxScale;

            }
            if(scaleNow<_this.settings.minScale){
                scaleNow=_this.settings.minScale;
            }

            if(scaleNow!=_this.settings.maxScale&&scaleNow!=_this.settings.minScale){

                //center={
                //    x:ev.center.x-leftGap,
                //    y:ev.center.y-topGap,
                //};


                center={
                    x:(ev.center.x-leftGap-lNow),
                    y:(ev.center.y-topGap-tNow),
                };





                //
                //lNow=-(center.x*scaleNow-center.x);
                //tNow=-(center.y*scaleNow-center.y);


                lNow=-(center.x*deltaScale/2)+lNow;
                tNow=-(center.y*deltaScale/2)+tNow;


                //console.log('lNow,tNow',lNow,tNow);
                //console.log('scaleNow',scaleNow);
                //console.log('centerx',center.x,'centery',center.y);

                //
                //console.log('scaleNow',scaleNow,lNow,tNow,ev.center.x,ev.center.y);
                //console.log('gap',leftGap,topGap);
                //console.log('elementParentRect',elementParentRect.top,elementParentRect.left)
                //console.log('$elementParent.offset()',$elementParent.offset().top)

                //console.log('position.left,position.top',position.left,position.top,position.scale);
                //$element.css({
                //
                //    transform: `translate3d(${lNow}px,${tNow}px,0) scale3d(${scaleNow},${scaleNow},${scaleNow})`,
                //    transformOrigin: `${ev.center.x-leftGap-lNow}px ${ev.center.y-topGap-tNow}px 0`,
                //});

                $element.css({
                    transform: `translate3d(${lNow}px,${tNow}px,0) scale3d(${scaleNow},${scaleNow},${scaleNow})`,
                    transformOrigin: `0px 0px 0px`,
                });

            }



            position.scale=scaleNow;
            scaleLast=ev.scale;

            _this.settings.onPinchMove(position);
            //console.log('pinch',scaleNow,'deltaScale',deltaScale);
        });

        this.hammertime.on('pinchcancel', function(ev) {
            //alert('pinchcancel')
        })



        this.hammertime.on('pinchend', function(ev) {

        //    //alert(scaleNow);
            position.scale=scaleNow;
            scaleLast=0;
        //
        //
        //    var maxL=objParentWidth-objWidth*scaleNow;
        //    var maxT=objParentHeight-objHeight*scaleNow;
        //
        //    if( lNow<maxL ){
        //        lNow=maxL
        //    }
        //
        //    if( tNow<maxT ){
        //        tNow=maxT
        //    }
        //    var minL=0;
        //    var minT=0;
        //
        //    if(lNow<minL){
        //        lNow=minL;
        //    }
        //    if(tNow<minT){
        //        tNow=minT;
        //    }
        //    $element.css({
        //        transform: `translate3d(${lNow}px,${tNow}px,0) scale3d(${position.scale},${position.scale},${position.scale})`,
        //        transformOrigin: `0px 0px 0px`,
        //    });


            if( lNow<(objParentWidth-objWidth*scaleNow) ){
                lNow=objParentWidth-objWidth*scaleNow;
            }

            if( tNow<(objParentHeight-objHeight*scaleNow) ){
                tNow=objParentHeight-objHeight*scaleNow;
            }

            if( lNow>0){
                lNow=0;
            }

            if( tNow>0){
                tNow=0;
            }

            $element.css({
                transform: `translate3d(${lNow}px,${tNow}px,0) scale3d(${position.scale},${position.scale},${position.scale})`,
                transformOrigin: `0px 0px 0px`,
            });
            position.left=lNow;
            position.top=tNow;
            _this.settings.onPinchEnd(position);
        });

    };

    drag.prototype.move=function(x,y){
        var _this=this;
        var $element = this.$element;
        var position=this.settings.position;
        position.left=x;
        position.top=y;
        $element.css({
            transform: `translate3d(${x}px,${y}px,0) scale3d(${position.scale},${position.scale},${position.scale})`,
        });
    };

    drag.prototype.scale=function(scale){
        var _this=this;
        var $element = this.$element;
        var position=this.settings.position;
        var center=this.settings.center;
        var leftGap=this.settings.constant.leftGap;
        var topGap=this.settings.constant.topGap;

        position.scale=scale;




        $element.css({
            transform: `translate3d(${position.left}px,${position.top}px,0) scale3d(${scale},${scale},${scale})`,
        });
    };




    hb.drag=function(dom,options){
        return new drag(dom,options);
    };
}());