;(function(){
    "use strict";


    function fancyNumber(dom,options){
        var options=options||{};
        var defaults={
            number:0,
            preClass:'hb-fancy-num',
            staticClass:'hb-fancy-num',
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

    fancyNumber.prototype.init=function(){
        var $element=this.$element;
        var settings=this.settings;
        var value=$element.data('number');


        //console.log(value)
        if(value){
            value+='';
            settings.number=value;
        }else{
            settings.number+='';
        }

        var numberArr=settings.number.split('');
        //console.log(numberArr)

        var htmlString=``;


        numberArr.forEach((n,i)=>{
            htmlString+=`<i class="${settings.staticClass} ${settings.preClass}${n}"></i>`
        })

        $element.empty().append(htmlString);

    };






    hb.fancyNumber=function(dom,options){
        return new fancyNumber(dom,options);
    };
}());