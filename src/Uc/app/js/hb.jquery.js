
//jquery plugin
(function($){
    $.fn.oldToggle = function(oddfun,evenfun) {
        var ele = this;
        ele.data('clickState', false);
        ele.on('click',function() {
            if (ele.data('clickState')) {
                evenfun();
            }
            else {
                oddfun();
            }
            ele.data('clickState', !ele.data('clickState'));
        });
    };
})(jQuery);





/**
 * http://www.jacklmoore.com/notes/naturalwidth-and-naturalheight-in-ie/
 *
 *  // Example usage:
 *  var nWidth = $('img#example').naturalWidth(),
 *  nHeight = $('img#example').naturalHeight();
 *
 *
 */

// adds .naturalWidth() and .naturalHeight() methods to jQuery
// for retreaving a normalized naturalWidth and naturalHeight.
(function($) {
    var props = ['Width', 'Height'],
        prop;

    while (prop = props.pop()) {
        (function(natural, prop) {
            $.fn[natural] = (natural in new Image()) ?
                function() {
                    return this[0][natural];
                } :
                function() {
                    var
                        node = this[0],
                        img,
                        value;

                    if (node.tagName.toLowerCase() === 'img') {
                        img = new Image();
                        img.src = node.src,
                            value = img[prop];
                    }
                    return value;
                };
        }('natural' + prop, prop.toLowerCase()));
    }
}(jQuery));

/**
 * 多行省略
 *
 * 匹配中文标点符号：
            String str="[\u3002\uff1b\uff0c\uff1a\u201c\u201d\uff08\uff09\u3001\uff1f\u300a\u300b]"
            该表达式可以识别出： 。 ；  ， ： “ ”（ ） 、 ？ 《 》 这些标点符号。
 * 匹配中文汉字
            String str="[\u4e00-\u9fa5]";
             该表达式可以识别出任何汉字。
 *
 *
 */
(function($) {



    $.fn.hb_ellipsis = function(options) {
        var defaults={};

        if(!options){
            defaults.height='';
        } else if(typeof options=='number'){
            defaults.height=options;

        }else{

            defaults = {
                height: "auto",
                before: function(){},
            };

        }


        var settings = $.extend( {}, defaults, options );

        return this.each(function(){


            var $textContanier = $(this);

            $textContanier.css('height',settings.height)

            $textContanier.wrapInner("<div class=''></div>");


            var $textTarget = $textContanier.children();


            var textContanierHeight = $textContanier.height();

//            var textTargetHeight = $textTarget.outerHeight(); while 需要用动态

            while ( $textTarget.outerHeight() > textContanierHeight) {
                $textTarget.text($textTarget.text().replace(/(\s)*([a-zA-Z0-9]+|\W)(\.\.\.)?$/, "..."));
            };


            $textTarget.text($textTarget.text().replace(/[?-\u3002\uff1b\uff0c\uff1a\u201c\u201d\uff08\uff09\u3001\uff1f\u300a\u300b]\.\.\.$/, "..."));
            //$textTarget.text($textTarget.text().replace(/[@"\p{P}"]\.\.\.$/, "..."));

            //var str='refger?？！!';
            //console.log(str.replace(/[?？！!]/g, "符号们"));




        });

    };
})(jQuery);




//jquery ui zindex

(function($){
    $.fn.hb_zIndex=function( zIndex ) {
        if ( zIndex !== undefined ) {
            return this.css( "zIndex", zIndex );
        }

        if ( this.length ) {
            var elem = $( this[ 0 ] ), position, value;
            while ( elem.length && elem[ 0 ] !== document ) {
                // Ignore z-index if position is set to a value where z-index is ignored by the browser
                // This makes behavior of this function consistent across browsers
                // WebKit always returns auto if the element is positioned
                position = elem.css( "position" );
                if ( position === "absolute" || position === "relative" || position === "fixed" ) {
                    // IE returns 0 when zIndex is not specified
                    // other browsers return a string
                    // we ignore the case of nested elements with an explicit value of 0
                    // <div style="z-index: -10;"><div style="z-index: 0;"></div></div>
                    value = parseInt( elem.css( "zIndex" ), 10 );
                    if ( !isNaN( value ) && value !== 0 ) {
                        return value;
                    }
                }
                elem = elem.parent();
            }
        }

        return 0;
    }
})(jQuery);


