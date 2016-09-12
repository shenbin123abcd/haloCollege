;(function(){
    "use strict";

    function numberBox(dom,options){
        var options=options||{};
        var defaults={
            max:99999999,
            afterAdd:function(){},
            afterMinus:function(){},
            onKeyup:function(){},
            onChange:function(){},
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

    numberBox.prototype.init=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');


        $numberControl.on('focus',function(){
            var val=$(this).val();
            if(val==0){
                $numberControl.val('')
            }
            //$(this).prop('type','number')
        });
        $numberControl.on('blur',function(){
            //$(this).prop('type','text')
            //console.log($(this).val());
            var val=$(this).val();
            if(!val){
                $numberControl.val(0)
            }else if(val>settings.max){
                $numberControl.val(settings.max);
            }
            settings.onChange($numberControl.val());
        });
        $numberControl.on('keyup',function(){
            //$(this).prop('type','text')
            var val=$(this).val();
            //console.log(val);
            val=parseInt(val);
            _this.switchActive(val);
            settings.onKeyup();
            settings.onChange(val);
            //$(this).val(val.replace(/[^\d+]/g,''))
            //$(this).val(val.replace(/\.[\d+]/g,''))
        });

        $numberMinus.on('click',function(){

            _this.minus();
            //var val=$numberControl.val();
            ////console.log(val);
            //val=parseInt(val);
            //var newVal=val-1;
            //_this.switchActive(newVal);
            //if(newVal>=1&&newVal<=settings.max){
            //    $numberControl.val(newVal);
            //}else if(newVal>settings.max){
            //    $numberControl.val(settings.max);
            //}else{
            //    $numberControl.val(0)
            //}
            //settings.afterMinus(newVal);
            //settings.onChange(newVal);
        });
        $numberAdd.on('click',function(){
            _this.add();
            //var val=$numberControl.val();
            ////console.log(val);
            //val=parseInt(val);
            //var newVal=val+1;
            //_this.switchActive(newVal);
            //if(newVal>=0&&newVal<=settings.max){
            //    $numberControl.val(newVal)
            //}else if(newVal>settings.max){
            //    $numberControl.val(settings.max);
            //}else{
            //    $numberControl.val(1)
            //}
            //settings.afterAdd(newVal);
            //settings.onChange(newVal);

        });

    };
    numberBox.prototype.disable=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');
        $numberControl.prop('disabled',true);
        $numberMinus.prop('disabled',true);
        $numberAdd.prop('disabled',true);
    };
    numberBox.prototype.enable=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');
        $numberControl.prop('disabled',false);
        $numberMinus.prop('disabled',false);
        $numberAdd.prop('disabled',false);
    };
    numberBox.prototype.reset=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');
        $numberControl.val(0);
        _this.switchActive(0);
    };

    numberBox.prototype.val=function(newVal){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');
        if(newVal){
            _this.switchActive(newVal);
            if(newVal>=0&&newVal<=settings.max){
                $numberControl.val(newVal)
            }else if(newVal>settings.max){
                $numberControl.val(settings.max);
            }else{
                $numberControl.val(newVal)
            }
            settings.onChange(newVal);
        }else{
            return $numberControl.val();
        }

    };


    numberBox.prototype.add=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');

        var val=$numberControl.val();
        //console.log(val);
        val=parseInt(val);
        var newVal=val+1;
        _this.switchActive(newVal);
        if(newVal>=0&&newVal<=settings.max){
            $numberControl.val(newVal)
        }else if(newVal>settings.max){
            $numberControl.val(settings.max);
        }else{
            $numberControl.val(1)
        }
        settings.afterAdd(newVal);
        settings.onChange(newVal);

    };

    numberBox.prototype.minus=function(){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');

        var val=$numberControl.val();
        //console.log(val);
        val=parseInt(val);
        var newVal=val-1;
        _this.switchActive(newVal);
        if(newVal>=1&&newVal<=settings.max){
            $numberControl.val(newVal);
        }else if(newVal>settings.max){
            $numberControl.val(settings.max);
        }else{
            $numberControl.val(0)
        }
        settings.afterMinus(newVal);
        settings.onChange(newVal);
    };

    numberBox.prototype.switchActive=function(val){
        var _this=this;
        var element=_this.element;
        var $element=_this.$element;
        var settings=_this.settings;

        var $numberBox = $element;
        var $numberControl = $element.find('[number-control]');
        var $numberMinus = $element.find('[number-minus]');
        var $numberAdd = $element.find('[number-add]');


        if(val>0){
            $numberBox.addClass('active');
        }else{
            $numberBox.removeClass('active');
        }

    };


    hb.numberBox=function(dom,options){
        return new numberBox(dom,options);
    };
}());