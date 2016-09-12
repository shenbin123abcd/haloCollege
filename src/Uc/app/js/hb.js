
;(function(window,document){
    "use strict";

    var haloBear=(function(){
        return {};
    }());
    /*
     agent

     */
    (function(window, document,undefined){
        "use strict";
        var agent=(function(){
            return{};
        }());
        haloBear.agent=agent;
    }(window, document));


    /*
     hb.agent

     */
    (function(window, document,undefined){
        "use strict";
        var device = function(){
            var u = window.navigator.userAgent,app = window.navigator.appVersion;
            switch (true){
                case (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1):
                    return "android";
                    break;
                case (u.indexOf('iPhone') > -1):
                    return "iPhone";
                    break;
                case (u.indexOf('iPad') > -1):
                    return "iPad";
                    break;
                default:
                    return "unknown-device";
            }
        };
        var isMobile = function(){
            var u = window.navigator.userAgent;
            //console.log(u);
            if(u.match(/Android|iPhone|iPad|iPod|IEMobile|BlackBerry/i)){
                return true;
            }
            else {
                return false;
            }
        };
        var os = function(){
            var u = window.navigator.userAgent;

            switch (true){
                case (!!u.match(/Android/i)):
                    return "android";
                    break;
                case (!!u.match(/iPhone|iPad|iPod/i)):
                    return "ios";
                    break;
                default:
                    return "unknown-os";
            }
        };

        var browser = function(){
            var u = window.navigator.userAgent, app = window.navigator.appVersion;
            switch (true){
                case (u.indexOf('MicroMessenger') > -1) :
                    return "weixin";
                    break;
                default:
                    return "unknown-browser";
            }
        };

        haloBear.agent.device=device;
        haloBear.agent.isMobile=isMobile;
        haloBear.agent.browser=browser;
        haloBear.agent.os=os;
    }(window, document));
    /*
     *
     *
     * https://github.com/filamentgroup/loadCSS
     * 修改
     *onloadCSS
     *loadCSS
     *  为
     * hb.onloadCSS
     * hb.loadCSS
     *
     */
    haloBear.newSpinner=function(){
        /**
         * Copyright (c) 2011-2014 Felix Gnass
         * Licensed under the MIT license
         * http://spin.js.org/
         *
         * Example:
         var opts = {
      lines: 12             // The number of lines to draw
    , length: 7             // The length of each line
    , width: 5              // The line thickness
    , radius: 10            // The radius of the inner circle
    , scale: 1.0            // Scales overall size of the spinner
    , corners: 1            // Roundness (0..1)
    , color: '#000'         // #rgb or #rrggbb
    , opacity: 1/4          // Opacity of the lines
    , rotate: 0             // Rotation offset
    , direction: 1          // 1: clockwise, -1: counterclockwise
    , speed: 1              // Rounds per second
    , trail: 100            // Afterglow percentage
    , fps: 20               // Frames per second when using setTimeout()
    , zIndex: 2e9           // Use a high z-index by default
    , className: 'spinner'  // CSS class to assign to the element
    , top: '50%'            // center vertically
    , left: '50%'           // center horizontally
    , shadow: false         // Whether to render a shadow
    , hwaccel: false        // Whether to use hardware acceleration (might be buggy)
    , position: 'absolute'  // Element positioning
    }
         var target = document.getElementById('foo')
         var spinner = new Spinner(opts).spin(target)
         */
            "use strict";
            var prefixes = ['webkit', 'Moz', 'ms', 'O'] /* Vendor prefixes */
                , animations = {} /* Animation rules keyed by their name */
                , useCssAnimations /* Whether to use CSS animations or setTimeout */
                , sheet /* A stylesheet to hold the @keyframe or VML rules. */

            /**
             * Utility function to create elements. If no tag name is given,
             * a DIV is created. Optionally properties can be passed.
             */
            function createEl (tag, prop) {
                var el = document.createElement(tag || 'div')
                    , n

                for (n in prop) el[n] = prop[n]
                return el
            }

            /**
             * Appends children and returns the parent.
             */
            function ins (parent /* child1, child2, ...*/) {
                for (var i = 1, n = arguments.length; i < n; i++) {
                    parent.appendChild(arguments[i])
                }

                return parent
            }

            /**
             * Creates an opacity keyframe animation rule and returns its name.
             * Since most mobile Webkits have timing issues with animation-delay,
             * we create separate rules for each line/segment.
             */
            function addAnimation (alpha, trail, i, lines) {
                var name = ['opacity', trail, ~~(alpha * 100), i, lines].join('-')
                    , start = 0.01 + i/lines * 100
                    , z = Math.max(1 - (1-alpha) / trail * (100-start), alpha)
                    , prefix = useCssAnimations.substring(0, useCssAnimations.indexOf('Animation')).toLowerCase()
                    , pre = prefix && '-' + prefix + '-' || ''

                if (!animations[name]) {
                    sheet.insertRule(
                        '@' + pre + 'keyframes ' + name + '{' +
                        '0%{opacity:' + z + '}' +
                        start + '%{opacity:' + alpha + '}' +
                        (start+0.01) + '%{opacity:1}' +
                        (start+trail) % 100 + '%{opacity:' + alpha + '}' +
                        '100%{opacity:' + z + '}' +
                        '}', sheet.cssRules.length)

                    animations[name] = 1
                }

                return name
            }

            /**
             * Tries various vendor prefixes and returns the first supported property.
             */
            function vendor (el, prop) {
                var s = el.style
                    , pp
                    , i

                prop = prop.charAt(0).toUpperCase() + prop.slice(1)
                if (s[prop] !== undefined) return prop
                for (i = 0; i < prefixes.length; i++) {
                    pp = prefixes[i]+prop
                    if (s[pp] !== undefined) return pp
                }
            }

            /**
             * Sets multiple style properties at once.
             */
            function css (el, prop) {
                for (var n in prop) {
                    el.style[vendor(el, n) || n] = prop[n]
                }

                return el
            }

            /**
             * Fills in default values.
             */
            function merge (obj) {
                for (var i = 1; i < arguments.length; i++) {
                    var def = arguments[i]
                    for (var n in def) {
                        if (obj[n] === undefined) obj[n] = def[n]
                    }
                }
                return obj
            }

            /**
             * Returns the line color from the given string or array.
             */
            function getColor (color, idx) {
                return typeof color == 'string' ? color : color[idx % color.length]
            }

            // Built-in defaults

            var defaults = {
                lines: 12             // The number of lines to draw
                , length: 7             // The length of each line
                , width: 5              // The line thickness
                , radius: 10            // The radius of the inner circle
                , scale: 1.0            // Scales overall size of the spinner
                , corners: 1            // Roundness (0..1)
                , color: '#000'         // #rgb or #rrggbb
                , opacity: 1/4          // Opacity of the lines
                , rotate: 0             // Rotation offset
                , direction: 1          // 1: clockwise, -1: counterclockwise
                , speed: 1              // Rounds per second
                , trail: 100            // Afterglow percentage
                , fps: 20               // Frames per second when using setTimeout()
                , zIndex: 2e9           // Use a high z-index by default
                , className: 'spinner'  // CSS class to assign to the element
                , top: '50%'            // center vertically
                , left: '50%'           // center horizontally
                , shadow: false         // Whether to render a shadow
                , hwaccel: false        // Whether to use hardware acceleration (might be buggy)
                , position: 'absolute'  // Element positioning
            }

            /** The constructor */
            function Spinner (o) {
                this.opts = merge(o || {}, Spinner.defaults, defaults)
            }

            // Global defaults that override the built-ins:
            Spinner.defaults = {}

            merge(Spinner.prototype, {
                /**
                 * Adds the spinner to the given target element. If this instance is already
                 * spinning, it is automatically removed from its previous target b calling
                 * stop() internally.
                 */
                spin: function (target) {
                    this.stop()

                    var self = this
                        , o = self.opts
                        , el = self.el = createEl(null, {className: o.className})

                    css(el, {
                        position: o.position
                        , width: 0
                        , zIndex: o.zIndex
                        , left: o.left
                        , top: o.top
                    })

                    if (target) {
                        target.insertBefore(el, target.firstChild || null)
                    }

                    el.setAttribute('role', 'progressbar')
                    self.lines(el, self.opts)

                    if (!useCssAnimations) {
                        // No CSS animation support, use setTimeout() instead
                        var i = 0
                            , start = (o.lines - 1) * (1 - o.direction) / 2
                            , alpha
                            , fps = o.fps
                            , f = fps / o.speed
                            , ostep = (1 - o.opacity) / (f * o.trail / 100)
                            , astep = f / o.lines

                            ;(function anim () {
                            i++
                            for (var j = 0; j < o.lines; j++) {
                                alpha = Math.max(1 - (i + (o.lines - j) * astep) % f * ostep, o.opacity)

                                self.opacity(el, j * o.direction + start, alpha, o)
                            }
                            self.timeout = self.el && setTimeout(anim, ~~(1000 / fps))
                        })()
                    }
                    return self
                }

                /**
                 * Stops and removes the Spinner.
                 */
                , stop: function () {
                    var el = this.el
                    if (el) {
                        clearTimeout(this.timeout)
                        if (el.parentNode) el.parentNode.removeChild(el)
                        this.el = undefined
                    }
                    return this
                }

                /**
                 * Internal method that draws the individual lines. Will be overwritten
                 * in VML fallback mode below.
                 */
                , lines: function (el, o) {
                    var i = 0
                        , start = (o.lines - 1) * (1 - o.direction) / 2
                        , seg

                    function fill (color, shadow) {
                        return css(createEl(), {
                            position: 'absolute'
                            , width: o.scale * (o.length + o.width) + 'px'
                            , height: o.scale * o.width + 'px'
                            , background: color
                            , boxShadow: shadow
                            , transformOrigin: 'left'
                            , transform: 'rotate(' + ~~(360/o.lines*i + o.rotate) + 'deg) translate(' + o.scale*o.radius + 'px' + ',0)'
                            , borderRadius: (o.corners * o.scale * o.width >> 1) + 'px'
                        })
                    }

                    for (; i < o.lines; i++) {
                        seg = css(createEl(), {
                            position: 'absolute'
                            , top: 1 + ~(o.scale * o.width / 2) + 'px'
                            , transform: o.hwaccel ? 'translate3d(0,0,0)' : ''
                            , opacity: o.opacity
                            , animation: useCssAnimations && addAnimation(o.opacity, o.trail, start + i * o.direction, o.lines) + ' ' + 1 / o.speed + 's linear infinite'
                        })

                        if (o.shadow) ins(seg, css(fill('#000', '0 0 4px #000'), {top: '2px'}))
                        ins(el, ins(seg, fill(getColor(o.color, i), '0 0 1px rgba(0,0,0,.1)')))
                    }
                    return el
                }

                /**
                 * Internal method that adjusts the opacity of a single line.
                 * Will be overwritten in VML fallback mode below.
                 */
                , opacity: function (el, i, val) {
                    if (i < el.childNodes.length) el.childNodes[i].style.opacity = val
                }

            })


            function initVML () {

                /* Utility function to create a VML tag */
                function vml (tag, attr) {
                    return createEl('<' + tag + ' xmlns="urn:schemas-microsoft.com:vml" class="spin-vml">', attr)
                }

                // No CSS transforms but VML support, add a CSS rule for VML elements:
                sheet.addRule('.spin-vml', 'behavior:url(#default#VML)')

                Spinner.prototype.lines = function (el, o) {
                    var r = o.scale * (o.length + o.width)
                        , s = o.scale * 2 * r

                    function grp () {
                        return css(
                            vml('group', {
                                coordsize: s + ' ' + s
                                , coordorigin: -r + ' ' + -r
                            })
                            , { width: s, height: s }
                        )
                    }

                    var margin = -(o.width + o.length) * o.scale * 2 + 'px'
                        , g = css(grp(), {position: 'absolute', top: margin, left: margin})
                        , i

                    function seg (i, dx, filter) {
                        ins(
                            g
                            , ins(
                                css(grp(), {rotation: 360 / o.lines * i + 'deg', left: ~~dx})
                                , ins(
                                    css(
                                        vml('roundrect', {arcsize: o.corners})
                                        , { width: r
                                            , height: o.scale * o.width
                                            , left: o.scale * o.radius
                                            , top: -o.scale * o.width >> 1
                                            , filter: filter
                                        }
                                    )
                                    , vml('fill', {color: getColor(o.color, i), opacity: o.opacity})
                                    , vml('stroke', {opacity: 0}) // transparent stroke to fix color bleeding upon opacity change
                                )
                            )
                        )
                    }

                    if (o.shadow)
                        for (i = 1; i <= o.lines; i++) {
                            seg(i, -2, 'progid:DXImageTransform.Microsoft.Blur(pixelradius=2,makeshadow=1,shadowopacity=.3)')
                        }

                    for (i = 1; i <= o.lines; i++) seg(i)
                    return ins(el, g)
                }

                Spinner.prototype.opacity = function (el, i, val, o) {
                    var c = el.firstChild
                    o = o.shadow && o.lines || 0
                    if (c && i + o < c.childNodes.length) {
                        c = c.childNodes[i + o]; c = c && c.firstChild; c = c && c.firstChild
                        if (c) c.opacity = val
                    }
                }
            }

            if (typeof document !== 'undefined') {
                sheet = (function () {
                    var el = createEl('style', {type : 'text/css'})
                    ins(document.getElementsByTagName('head')[0], el)
                    return el.sheet || el.styleSheet
                }())

                var probe = css(createEl('group'), {behavior: 'url(#default#VML)'})

                if (!vendor(probe, 'transform') && probe.adj) initVML()
                else useCssAnimations = vendor(probe, 'animation')
            }

            return new Spinner

    };


    (function(window,document){
        "use strict";
        var w=window;

        var loadCSS = function( href, before, media ){
            // Arguments explained:
            // `href` [REQUIRED] is the URL for your CSS file.
            // `before` [OPTIONAL] is the element the script should use as a reference for injecting our stylesheet <link> before
            // By default, loadCSS attempts to inject the link after the last stylesheet or script in the DOM. However, you might desire a more specific location in your document.
            // `media` [OPTIONAL] is the media type or query of the stylesheet. By default it will be 'all'
            var doc = w.document;
            var ss = doc.createElement( "link" );
            var newMedia = media || "all";
            var ref;
            if( before ){
                ref = before;
            }
            else {
                var refs = ( doc.body || doc.getElementsByTagName( "head" )[ 0 ] ).childNodes;
                ref = refs[ refs.length - 1];
            }

            var sheets = doc.styleSheets;
            ss.rel = "stylesheet";
            ss.href = href;
            // temporarily set media to something inapplicable to ensure it'll fetch without blocking render
            ss.media = "only x";

            // wait until body is defined before injecting link. This ensures a non-blocking load in IE11.
            function ready( cb ){
                if( doc.body ){
                    return cb();
                }
                setTimeout(function(){
                    ready( cb );
                });
            }
            // Inject link
            // Note: the ternary preserves the existing behavior of "before" argument, but we could choose to change the argument to "after" in a later release and standardize on ref.nextSibling for all refs
            // Note: `insertBefore` is used instead of `appendChild`, for safety re: http://www.paulirish.com/2011/surefire-dom-element-insertion/
            ready( function(){
                ref.parentNode.insertBefore( ss, ( before ? ref : ref.nextSibling ) );
            });
            // A method (exposed on return object for external use) that mimics onload by polling until document.styleSheets until it includes the new sheet.
            var onloadcssdefined = function( cb ){
                var resolvedHref = ss.href;
                var i = sheets.length;
                while( i-- ){
                    if( sheets[ i ].href === resolvedHref ){
                        return cb();
                    }
                }
                setTimeout(function() {
                    onloadcssdefined( cb );
                });
            };

            // once loaded, set link's media back to `all` so that the stylesheet applies once it loads
            if( ss.addEventListener ){
                ss.addEventListener( "load", function(){
                    this.media = newMedia;
                });
            }
            ss.onloadcssdefined = onloadcssdefined;
            onloadcssdefined(function() {
                if( ss.media !== newMedia ){
                    ss.media = newMedia;
                }
            });
            return ss;
        };
        haloBear.loadCSS=loadCSS;
    }(window,document,undefined));

    /*
     * lib onloadCSS
     */

    (function(window,document){
        "use strict";
        /*! onloadCSS: adds onload support for asynchronous stylesheets loaded with loadCSS. [c]2016 @zachleat, Filament Group, Inc. Licensed MIT */
        /* https://github.com/filamentgroup/loadCSS */
        /* global navigator */
        /* exported onloadCSS */
        function onloadCSS( ss, callback ) {
            var called;
            function newcb(){
                if( !called && callback ){
                    called = true;
                    callback.call( ss );
                }
            }
            if( ss.addEventListener ){
                ss.addEventListener( "load", newcb );
            }
            if( ss.attachEvent ){
                ss.attachEvent( "onload", newcb );
            }

            // This code is for browsers that don’t support onload
            // No support for onload (it'll bind but never fire):
            //	* Android 4.3 (Samsung Galaxy S4, Browserstack)
            //	* Android 4.2 Browser (Samsung Galaxy SIII Mini GT-I8200L)
            //	* Android 2.3 (Pantech Burst P9070)

            // Weak inference targets Android < 4.4
            if( "isApplicationInstalled" in navigator && "onloadcssdefined" in ss ) {
                ss.onloadcssdefined( newcb );
            }
        }
        haloBear.onloadCSS=onloadCSS;
    }(window,document,undefined));

    /*
     * lib
     */
    (function(window,document){
        "use strict";
        var lib=(function(){
            return{};
        }());
        haloBear.lib=lib;
    }(window,document,undefined));


    /*
     * weiui
     */
    (function(window,document,undefined){
        "use strict";
        var weui=(function(){
            var _alert=function(options){
                var deferred = $.Deferred();
                var defaults = {
                    title:'提示',
                    content:'提示内容',
                    btn:'确定',
                };

                if(typeof options=="string"){
                    defaults = $.extend(defaults,{
                        content:options
                    });
                }else{

                }

                var settings = $.extend( {},defaults, options );

                var alertHtmlStr='' +
                    '<div class="weui_dialog_alert" >'+
                    '<div class="weui_mask"></div>'+
                    '<div class="weui_dialog" style="display: none;" >'+
                    '<div class="weui_dialog_hd"><strong class="weui_dialog_title">'+
                    settings.title+
                    '</strong></div>' +
                    '<div class="weui_dialog_bd">'+
                    settings.content +
                    '</div>' +
                    '<div class="weui_dialog_ft">' +
                    '<a href="javascript:;" class="weui_btn_dialog primary">'+settings.btn +'</a>' +
                    '</div>' +
                    ' </div>' +
                    ' </div>' +
                    '';
                var $alertHtml=$(alertHtmlStr);
                $("body").append($alertHtml);
                $alertHtml.find(".weui_dialog").fadeIn(200);
                var $confirmBt=$alertHtml.find(".weui_btn_dialog");
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



            var guideDownload,guideShare;
            (function(){
                var guide=function(url){
                    var imgUrl=url;
                    var loadingHtmlStr=`
                    <div style="position: fixed;z-index: 99999;width: 100%;height: 100%;top: 0;left: 0;background: rgba(0,0,0,0.8);">
                        <img style='position: absolute;width: 138px;top: 5px;right: 20px;' src="${imgUrl}">
                    </div>
                `;
                    var $loadingHtml=$(loadingHtmlStr);

                    var show=function(){
                        $("body").append($loadingHtml);
                        $loadingHtml.on('click touchmove',function(){
                            $loadingHtml.remove();
                        });
                    };

                    var hide=function(){
                        $loadingHtml.remove();
                    };

                    return{
                        show:show,
                        hide:hide
                    }
                };



                guideShare=(function(){
                    var _guide;
                    return{
                        show:function(){
                            _guide?_guide.show():_guide=guide('http://7jptwn.com2.z0.glb.qiniucdn.com/weixin-guide-share.png').show();
                        },
                        hide:function(){
                            _guide?_guide.hide():_guide=guide('http://7jptwn.com2.z0.glb.qiniucdn.com/weixin-guide-share.png').hide();
                        }
                    }
                }());


                guideDownload=(function(){
                    var _guide;
                    var os=haloBear.agent.os();
                    if(os=='ios'){
                        var imgUrl='http://7jptwn.com2.z0.glb.qiniucdn.com/weixin-guide-download-ios.png';
                    }else{
                        var imgUrl='http://7jptwn.com2.z0.glb.qiniucdn.com/weixin-guide-download-android.png';
                    }
                    return{
                        show:function(){
                            _guide?_guide.show():_guide=guide(imgUrl).show();
                        },
                        hide:function(){
                            _guide?_guide.show():_guide=guide(imgUrl).hide();
                        }
                    }
                }());





            }());




            var toast=function(msg){
                var toastHtmlStr='' +
                    '<div class="weui_msg_toast" >' +
                    '<div class="weui_mask_transparent"></div>' +
                    '<div class="weui_toast">' +
                    '<i class="weui_icon_toast"></i>' +
                    '<p class="weui_toast_content">'+msg+'</p>' +
                    '</div>' +
                    '</div>' +
                    '';
                var $toastHtml=$(toastHtmlStr);
                $("body").append($toastHtml);
                var $weui_toast=$toastHtml.find(".weui_toast");
                $weui_toast.fadeIn(200);
                var hideToast=function(){
                    $toastHtml.fadeOut(400).remove();
                };
                setTimeout(hideToast,600);
            };



            return{
                alert:_alert,
                confirm:_confirm,
                loading:loading,
                toast:toast,
                guideShare:guideShare,
                guideDownload:guideDownload,
            };
        }());

        haloBear.lib.weui=weui;
    }(window,document));



    /*
     *
     * color
     */

    (function(window,document){
        "use strict";
        var color=(function(){
            return{};
        }());
        haloBear.color=color;
    }(window,document,undefined));

    /*
     *
     * 16进制色值转rgb
     */
    (function(window,document){
        "use strict";
        function hexToRgb(hex,isString) {
            // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
            var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
            hex = hex.replace(shorthandRegex, function(m, r, g, b) {
                return r + r + g + g + b + b;
            });

            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            if(!isString){
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }else{
                return result ? `rgb(${parseInt(result[1], 16)},${parseInt(result[2], 16)},${parseInt(result[3], 16)})`
                    : null;
            }

        }

        haloBear.color.hexToRgb=hexToRgb;
    }(window,document,undefined));


    /*
     *
     * util
     */
    (function(window,document){
        "use strict";
        var util=(function(){
            return{};
        }());
        haloBear.util=util;
    }(window,document,undefined));

    /*
     *
     * 反序列化数据
     * a=1&b=2  =》 {a:1,b:2}
     *
     *
     */
    (function(window,document,undefined) {
        "use strict";
        var QueryStringToHash = function QueryStringToHash (query) {
            if(!query){
                return {};
            }
            var query_string = {};
            var vars = query.split("&");
            for (var i=0;i<vars.length;i++) {
                var pair = vars[i].split("=");
                pair[0] = decodeURIComponent(pair[0]);
                pair[1] = decodeURIComponent(pair[1]);
                // If first entry with this name
                if (typeof query_string[pair[0]] === "undefined") {
                    query_string[pair[0]] = pair[1];
                    // If second entry with this name
                } else if (typeof query_string[pair[0]] === "string") {
                    var arr = [ query_string[pair[0]], pair[1] ];
                    query_string[pair[0]] = arr;
                    // If third or later entry with this name
                } else {
                    query_string[pair[0]].push(pair[1]);
                }
            }
            return query_string;
        };

        haloBear.util.deParam=QueryStringToHash;
    })(window, document);


    /*
     *
     * 隐藏虚拟键盘
     */
    (function(window,document,undefined) {
        "use strict";
        var hideKeyboard = function() {
            document.activeElement.blur();
            $("input").blur();
        };
        haloBear.util.hideKeyboard=hideKeyboard;
    })(window, document);
    /*
     *
     * 数字加逗号
     */
    (function(window,document,undefined) {
        "use strict";
        /*
         *
         * 数字加逗号

         */
        var formatNumber = function(n) {
            var numberWithCommas,wanN;
            if(!n){
                return 0;
            }
            if(n>9999){
                wanN=n/10000
            }

            numberWithCommas=function(x) {
                var parts = x.toString().split(".");
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                parts[1]&&(parts[1]=parts[1].substr(0,1));
                return parts.join(".");
            };

            //if(n>9999){
            //    //console.log(numberWithCommas(wanN),'万');
            //
            //
            //    return numberWithCommas(wanN)+'万';
            //}

            return numberWithCommas(n)

        };
        haloBear.util.formatNumber=formatNumber;
    })(window, document);
    /*
     *
     * 数字前补0
     */
    (function(window,document,undefined) {
        "use strict";
        /*
         *
         *
         */
        function pad (str, max) {
            str = str.toString();
            return str.length < max ? pad("0" + str, max) : str;
        }
        haloBear.util.pad=pad;
    })(window, document);

    /*
     hb.util.loading
     require spin.js
     */
    (function(window,document,undefined) {
        "use strict";

        var loading=(function(){
            var loadingHtmlStr=`
            <div style="position: fixed;z-index: 9999999;width: 100%;height: 100%;left: 0;top: 0;background: rgba(0,0,0,0.2);"></div>
            `;
            var $loadingHtml=$(loadingHtmlStr);

            var target = $loadingHtml.get()[0];
            var spinner = haloBear.newSpinner().spin(target);

            //console.log(spinner);

            var show=function(){
                //console.log('er',$loadingHtml,$("body"))
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
        haloBear.util.loading=loading;
    })(window, document);


    /*
     hb.location
     */
    (function(window,document,undefined){
        "use strict";
        var location=(function(){
            return{};
        }());
        haloBear.location=location;
    }(window,document));

    /*
     hb.location.hash.get()
     */
    (function(window,document,undefined) {
        "use strict";
        var hash = (function() {

            var fromHash = function() {
                var params = window.location.hash ? window.location.hash.substr(1).split("&") : [],
                    paramsObject = {};

                for(var i = 0; i < params.length; i++) {
                    var a = params[i].split("=");
                    paramsObject[a[0]] =  decodeURIComponent(a[1]);
                }
                return paramsObject;
            };

            var toHash = function(params) {
                var str = [];
                for(var p in params) {
                    str.push(p + "=" + encodeURIComponent(params[p]));
                }
                window.location.hash = str.join("&");
            };

            return {
                get: function(param) {
                    var params = fromHash();
                    if (param) {
                        return params[param];
                    } else {
                        return params;
                    }
                },
                set: function(param) {
                    toHash(param);
                },
                add: function(newParams,removeParams) {
                    var removeParams=removeParams||[];
                    removeParams=[].concat(removeParams);
                    var params = fromHash();
                    var paramsAfterRemove = {};
                    removeParams.forEach(function(n,i){
                        for (var p in params) {
                            if(n!=p){
                                paramsAfterRemove[p] = params[p];
                            }
                        }

                    });


                    for (var p in newParams) {
                        //params[p] = newParams[p];
                        paramsAfterRemove[p] = newParams[p];
                    }
                    //toHash(params);
                    //console.log(paramsAfterRemove);
                    toHash(paramsAfterRemove);
                },
                remove: function(removeParams) {
                    removeParams = (typeof(removeParams)=='string') ? [removeParams] : removeParams;
                    var params = fromHash();
                    for (var i = 0; i < removeParams.length; i++) {
                        delete params[removeParams[i]];
                    }
                    toHash(params);
                },
                clear: function() {
                    toHash({});
                }
            };
        })();
        haloBear.location.hash=hash;
    })(window, document);


    /*
     修改自 js-url#2.3.0
     "homepage": "https://github.com/websanova/js-url",
     替换了全局变量
     url换成 hb.location.url
     */
    (function(window, undefined) {
        var url = (function() {

            function _t() {
                return new RegExp(/(.*?)\.?([^\.]*?)\.?(com|net|org|biz|ws|in|me|co\.uk|co|org\.uk|ltd\.uk|plc\.uk|me\.uk|edu|mil|br\.com|cn\.com|eu\.com|hu\.com|no\.com|qc\.com|sa\.com|se\.com|se\.net|us\.com|uy\.com|ac|co\.ac|gv\.ac|or\.ac|ac\.ac|af|am|as|at|ac\.at|co\.at|gv\.at|or\.at|asn\.au|com\.au|edu\.au|org\.au|net\.au|id\.au|be|ac\.be|adm\.br|adv\.br|am\.br|arq\.br|art\.br|bio\.br|cng\.br|cnt\.br|com\.br|ecn\.br|eng\.br|esp\.br|etc\.br|eti\.br|fm\.br|fot\.br|fst\.br|g12\.br|gov\.br|ind\.br|inf\.br|jor\.br|lel\.br|med\.br|mil\.br|net\.br|nom\.br|ntr\.br|odo\.br|org\.br|ppg\.br|pro\.br|psc\.br|psi\.br|rec\.br|slg\.br|tmp\.br|tur\.br|tv\.br|vet\.br|zlg\.br|br|ab\.ca|bc\.ca|mb\.ca|nb\.ca|nf\.ca|ns\.ca|nt\.ca|on\.ca|pe\.ca|qc\.ca|sk\.ca|yk\.ca|ca|cc|ac\.cn|com\.cn|edu\.cn|gov\.cn|org\.cn|bj\.cn|sh\.cn|tj\.cn|cq\.cn|he\.cn|nm\.cn|ln\.cn|jl\.cn|hl\.cn|js\.cn|zj\.cn|ah\.cn|gd\.cn|gx\.cn|hi\.cn|sc\.cn|gz\.cn|yn\.cn|xz\.cn|sn\.cn|gs\.cn|qh\.cn|nx\.cn|xj\.cn|tw\.cn|hk\.cn|mo\.cn|cn|cx|cz|de|dk|fo|com\.ec|tm\.fr|com\.fr|asso\.fr|presse\.fr|fr|gf|gs|co\.il|net\.il|ac\.il|k12\.il|gov\.il|muni\.il|ac\.in|co\.in|org\.in|ernet\.in|gov\.in|net\.in|res\.in|is|it|ac\.jp|co\.jp|go\.jp|or\.jp|ne\.jp|ac\.kr|co\.kr|go\.kr|ne\.kr|nm\.kr|or\.kr|li|lt|lu|asso\.mc|tm\.mc|com\.mm|org\.mm|net\.mm|edu\.mm|gov\.mm|ms|nl|no|nu|pl|ro|org\.ro|store\.ro|tm\.ro|firm\.ro|www\.ro|arts\.ro|rec\.ro|info\.ro|nom\.ro|nt\.ro|se|si|com\.sg|org\.sg|net\.sg|gov\.sg|sk|st|tf|ac\.th|co\.th|go\.th|mi\.th|net\.th|or\.th|tm|to|com\.tr|edu\.tr|gov\.tr|k12\.tr|net\.tr|org\.tr|com\.tw|org\.tw|net\.tw|ac\.uk|uk\.com|uk\.net|gb\.com|gb\.net|vg|sh|kz|ch|info|ua|gov|name|pro|ie|hk|com\.hk|org\.hk|net\.hk|edu\.hk|us|tk|cd|by|ad|lv|eu\.lv|bz|es|jp|cl|ag|mobi|eu|co\.nz|org\.nz|net\.nz|maori\.nz|iwi\.nz|io|la|md|sc|sg|vc|tw|travel|my|se|tv|pt|com\.pt|edu\.pt|asia|fi|com\.ve|net\.ve|fi|org\.ve|web\.ve|info\.ve|co\.ve|tel|im|gr|ru|net\.ru|org\.ru|hr|com\.hr|ly|xyz)$/);
            }

            function _d(s) {
                return decodeURIComponent(s.replace(/\+/g, ' '));
            }

            function _i(arg, str) {
                var sptr = arg.charAt(0),
                    split = str.split(sptr);

                if (sptr === arg) { return split; }

                arg = parseInt(arg.substring(1), 10);

                return split[arg < 0 ? split.length + arg : arg - 1];
            }

            function _f(arg, str) {
                var sptr = arg.charAt(0),
                    split = str.split('&'),
                    field = [],
                    params = {},
                    tmp = [],
                    arg2 = arg.substring(1);

                for (var i = 0, ii = split.length; i < ii; i++) {
                    field = split[i].match(/(.*?)=(.*)/);

                    // TODO: regex should be able to handle this.
                    if ( ! field) {
                        field = [split[i], split[i], ''];
                    }

                    if (field[1].replace(/\s/g, '') !== '') {
                        field[2] = _d(field[2] || '');

                        // If we have a match just return it right away.
                        if (arg2 === field[1]) { return field[2]; }

                        // Check for array pattern.
                        tmp = field[1].match(/(.*)\[([0-9]+)\]/);

                        if (tmp) {
                            params[tmp[1]] = params[tmp[1]] || [];

                            params[tmp[1]][tmp[2]] = field[2];
                        }
                        else {
                            params[field[1]] = field[2];
                        }
                    }
                }

                if (sptr === arg) { return params; }

                return params[arg2];
            }

            return function(arg, url) {
                var _l = {}, tmp, tmp2;

                if (arg === 'tld?') { return _t(); }

                url = url || window.location.toString();

                if ( ! arg) { return url; }

                arg = arg.toString();

                if (tmp = url.match(/^mailto:([^\/].+)/)) {
                    _l.protocol = 'mailto';
                    _l.email = tmp[1];
                }
                else {

                    // Ignore Hashbangs.
                    if (tmp = url.match(/(.*?)\/#\!(.*)/)) {
                        url = tmp[1] + tmp[2];
                    }

                    // Hash.
                    if (tmp = url.match(/(.*?)#(.*)/)) {
                        _l.hash = tmp[2];
                        url = tmp[1];
                    }

                    // Return hash parts.
                    if (_l.hash && arg.match(/^#/)) { return _f(arg, _l.hash); }

                    // Query
                    if (tmp = url.match(/(.*?)\?(.*)/)) {
                        _l.query = tmp[2];
                        url = tmp[1];
                    }

                    // Return query parts.
                    if (_l.query && arg.match(/^\?/)) { return _f(arg, _l.query); }

                    // Protocol.
                    if (tmp = url.match(/(.*?)\:?\/\/(.*)/)) {
                        _l.protocol = tmp[1].toLowerCase();
                        url = tmp[2];
                    }

                    // Path.
                    if (tmp = url.match(/(.*?)(\/.*)/)) {
                        _l.path = tmp[2];
                        url = tmp[1];
                    }

                    // Clean up path.
                    _l.path = (_l.path || '').replace(/^([^\/])/, '/$1').replace(/\/$/, '');

                    // Return path parts.
                    if (arg.match(/^[\-0-9]+$/)) { arg = arg.replace(/^([^\/])/, '/$1'); }
                    if (arg.match(/^\//)) { return _i(arg, _l.path.substring(1)); }

                    // File.
                    tmp = _i('/-1', _l.path.substring(1));

                    if (tmp && (tmp = tmp.match(/(.*?)\.(.*)/))) {
                        _l.file = tmp[0];
                        _l.filename = tmp[1];
                        _l.fileext = tmp[2];
                    }

                    // Port.
                    if (tmp = url.match(/(.*)\:([0-9]+)$/)) {
                        _l.port = tmp[2];
                        url = tmp[1];
                    }

                    // Auth.
                    if (tmp = url.match(/(.*?)@(.*)/)) {
                        _l.auth = tmp[1];
                        url = tmp[2];
                    }

                    // User and pass.
                    if (_l.auth) {
                        tmp = _l.auth.match(/(.*)\:(.*)/);

                        _l.user = tmp ? tmp[1] : _l.auth;
                        _l.pass = tmp ? tmp[2] : undefined;
                    }

                    // Hostname.
                    _l.hostname = url.toLowerCase();

                    // Return hostname parts.
                    if (arg.charAt(0) === '.') { return _i(arg, _l.hostname); }

                    // Domain, tld and sub domain.
                    if (_t()) {
                        tmp = _l.hostname.match(_t());

                        if (tmp) {
                            _l.tld = tmp[3];
                            _l.domain = tmp[2] ? tmp[2] + '.' + tmp[3] : undefined;
                            _l.sub = tmp[1] || undefined;
                        }
                    }

                    // Set port and protocol defaults if not set.
                    _l.port = _l.port || (_l.protocol === 'https' ? '443' : '80');
                    _l.protocol = _l.protocol || (_l.port === '443' ? 'https' : 'http');
                }

                // Return arg.
                if (arg in _l) { return _l[arg]; }

                // Return everything.
                if (arg === '{}') { return _l; }

                // Default to undefined for no match.
                return undefined;
            };
        })();
        haloBear.location.url=url;
    })(window);

    /*
     validation

     */
    (function(window, document,undefined){
        "use strict";
        var validation=(function(){
            return{};
        }());
        haloBear.validation=validation;
    }(window, document));

    /*
     hb.location.validation.checkPhone(string)
     return boolean
     */
    (function(window, document,undefined){
        "use strict";
        var checkPhone=function(num){
            //表示以1开头，第二位可能是3/4/5/7/8等的任意一个，在加上后面的\d表示数字[0-9]的9位，总共加起来11位结束。
            if(!(/^1[3|4|5|7|8]\d{9}$/.test(num))){
                return false;
            }else{
                return true;
            }
        };
        haloBear.validation.checkPhone=checkPhone;
    }(window, document));

    (function(window, document,undefined){
        "use strict";
        var isEmpty=function(str){
            if (/^\s*$/.test(str)) {
                return true;
            }else{
                return false;
            }
        };
        haloBear.validation.isEmpty=isEmpty;
    }(window, document));





    /*
     *  Cookies
     *  修改covert，window 改hb,默认编码改为 encodeURIComponent，
     *  hb.Cookies
     *
     *
     * JavaScript Cookie v2.1.0
     * https://github.com/js-cookie/js-cookie
     *
     * Copyright 2006, 2015 Klaus Hartl & Fagner Brack
     * Released under the MIT license
     */
    (function(window, document,undefined){
        "use strict";


        (function (factory) {
            if (typeof define === 'function' && define.amd) {
                define(factory);
            } else if (typeof exports === 'object') {
                module.exports = factory();
            } else {
                var _OldCookies = haloBear.Cookies;
                var api = haloBear.Cookies = factory();
                api.noConflict = function () {
                    haloBear.Cookies = _OldCookies;
                    return api;
                };
            }
        }(function () {
            function extend () {
                var i = 0;
                var result = {};
                for (; i < arguments.length; i++) {
                    var attributes = arguments[ i ];
                    for (var key in attributes) {
                        result[key] = attributes[key];
                    }
                }
                return result;
            }

            function init (converter) {
                function api (key, value, attributes) {
                    var result;

                    // Write

                    if (arguments.length > 1) {
                        attributes = extend({
                            path: '/'
                        }, api.defaults, attributes);

                        if (typeof attributes.expires === 'number') {
                            var expires = new Date();
                            expires.setMilliseconds(expires.getMilliseconds() + attributes.expires * 864e+5);
                            attributes.expires = expires;
                        }

                        try {
                            result = JSON.stringify(value);
                            if (/^[\{\[]/.test(result)) {
                                value = result;
                            }
                        } catch (e) {}

                        if (!converter.write) {
                            value = encodeURIComponent(String(value))
                                .replace(/%(23|24|26|2B|3A|3C|3E|3D|2F|3F|40|5B|5D|5E|60|7B|7D|7C)/g, decodeURIComponent);
                        } else {
                            value = converter.write(value, key);
                        }

                        key = encodeURIComponent(String(key));
                        key = key.replace(/%(23|24|26|2B|5E|60|7C)/g, decodeURIComponent);
                        key = key.replace(/[\(\)]/g, escape);

                        return (document.cookie = [
                            key, '=', value,
                            attributes.expires && '; expires=' + attributes.expires.toUTCString(), // use expires attribute, max-age is not supported by IE
                            attributes.path    && '; path=' + attributes.path,
                            attributes.domain  && '; domain=' + attributes.domain,
                            attributes.secure ? '; secure' : ''
                        ].join(''));
                    }

                    // Read

                    if (!key) {
                        result = {};
                    }

                    // To prevent the for loop in the first place assign an empty array
                    // in case there are no cookies at all. Also prevents odd result when
                    // calling "get()"
                    var cookies = document.cookie ? document.cookie.split('; ') : [];
                    var rdecode = /(%[0-9A-Z]{2})+/g;
                    var i = 0;

                    for (; i < cookies.length; i++) {
                        var parts = cookies[i].split('=');
                        var name = parts[0].replace(rdecode, decodeURIComponent);
                        var cookie = parts.slice(1).join('=');

                        if (cookie.charAt(0) === '"') {
                            cookie = cookie.slice(1, -1);
                        }

                        try {
                            cookie = converter.read ?
                                converter.read(cookie, name) : converter(cookie, name) ||
                            cookie.replace(rdecode, decodeURIComponent);

                            if (this.json) {
                                try {
                                    cookie = JSON.parse(cookie);
                                } catch (e) {}
                            }

                            if (key === name) {
                                result = cookie;
                                break;
                            }

                            if (!key) {
                                result[name] = cookie;
                            }
                        } catch (e) {}
                    }

                    return result;
                }

                api.get = api.set = api;
                api.getJSON = function () {
                    return api.apply({
                        json: true
                    }, [].slice.call(arguments));
                };
                api.defaults = {};

                api.remove = function (key, attributes) {
                    api(key, '', extend(attributes, {
                        expires: -1
                    }));
                };

                api.withConverter = init;

                return api;
            }

            return init(function () {});
        }));

        haloBear.Cookies.withConverter({
            write: function (value) {
                return encodeURIComponent(value);
            },
            read: function (value) {
                return decodeURIComponent(value);
            }
        });


    }(window, document));


    /*
     *  // Store.js  v1.3.20
     *
     *  hb.store
     *
     */
    (function(window, document,undefined){
        "use strict";

        ;(function (root, factory) {
            if (typeof define === 'function' && define.amd) {
                // AMD. Register as an anonymous module.
                define([], factory);
            } else if (typeof exports === 'object') {
                // Node. Does not work with strict CommonJS, but
                // only CommonJS-like environments that support module.exports,
                // like Node.
                module.exports = factory();
            } else {
                // Browser globals (root is window)
                haloBear.store = factory();
            }
        }(this, function () {

            // Store.js
            var store = {},
                win = (typeof window != 'undefined' ? window : global),
                doc = win.document,
                localStorageName = 'localStorage',
                scriptTag = 'script',
                storage

            store.disabled = false
            store.version = '1.3.20'
            store.set = function(key, value) {}
            store.get = function(key, defaultVal) {}
            store.has = function(key) { return store.get(key) !== undefined }
            store.remove = function(key) {}
            store.clear = function() {}
            store.transact = function(key, defaultVal, transactionFn) {
                if (transactionFn == null) {
                    transactionFn = defaultVal
                    defaultVal = null
                }
                if (defaultVal == null) {
                    defaultVal = {}
                }
                var val = store.get(key, defaultVal)
                transactionFn(val)
                store.set(key, val)
            }
            store.getAll = function() {}
            store.forEach = function() {}

            store.serialize = function(value) {
                return JSON.stringify(value)
            }
            store.deserialize = function(value) {
                if (typeof value != 'string') { return undefined }
                try { return JSON.parse(value) }
                catch(e) { return value || undefined }
            }

            // Functions to encapsulate questionable FireFox 3.6.13 behavior
            // when about.config::dom.storage.enabled === false
            // See https://github.com/marcuswestin/store.js/issues#issue/13
            function isLocalStorageNameSupported() {
                try { return (localStorageName in win && win[localStorageName]) }
                catch(err) { return false }
            }

            if (isLocalStorageNameSupported()) {
                storage = win[localStorageName]
                store.set = function(key, val) {
                    if (val === undefined) { return store.remove(key) }
                    storage.setItem(key, store.serialize(val))
                    return val
                }
                store.get = function(key, defaultVal) {
                    var val = store.deserialize(storage.getItem(key))
                    return (val === undefined ? defaultVal : val)
                }
                store.remove = function(key) { storage.removeItem(key) }
                store.clear = function() { storage.clear() }
                store.getAll = function() {
                    var ret = {}
                    store.forEach(function(key, val) {
                        ret[key] = val
                    })
                    return ret
                }
                store.forEach = function(callback) {
                    for (var i=0; i<storage.length; i++) {
                        var key = storage.key(i)
                        callback(key, store.get(key))
                    }
                }
            } else if (doc && doc.documentElement.addBehavior) {
                var storageOwner,
                    storageContainer
                // Since #userData storage applies only to specific paths, we need to
                // somehow link our data to a specific path.  We choose /favicon.ico
                // as a pretty safe option, since all browsers already make a request to
                // this URL anyway and being a 404 will not hurt us here.  We wrap an
                // iframe pointing to the favicon in an ActiveXObject(htmlfile) object
                // (see: http://msdn.microsoft.com/en-us/library/aa752574(v=VS.85).aspx)
                // since the iframe access rules appear to allow direct access and
                // manipulation of the document element, even for a 404 page.  This
                // document can be used instead of the current document (which would
                // have been limited to the current path) to perform #userData storage.
                try {
                    storageContainer = new ActiveXObject('htmlfile')
                    storageContainer.open()
                    storageContainer.write('<'+scriptTag+'>document.w=window</'+scriptTag+'><iframe src="/favicon.ico"></iframe>')
                    storageContainer.close()
                    storageOwner = storageContainer.w.frames[0].document
                    storage = storageOwner.createElement('div')
                } catch(e) {
                    // somehow ActiveXObject instantiation failed (perhaps some special
                    // security settings or otherwse), fall back to per-path storage
                    storage = doc.createElement('div')
                    storageOwner = doc.body
                }
                var withIEStorage = function(storeFunction) {
                    return function() {
                        var args = Array.prototype.slice.call(arguments, 0)
                        args.unshift(storage)
                        // See http://msdn.microsoft.com/en-us/library/ms531081(v=VS.85).aspx
                        // and http://msdn.microsoft.com/en-us/library/ms531424(v=VS.85).aspx
                        storageOwner.appendChild(storage)
                        storage.addBehavior('#default#userData')
                        storage.load(localStorageName)
                        var result = storeFunction.apply(store, args)
                        storageOwner.removeChild(storage)
                        return result
                    }
                }

                // In IE7, keys cannot start with a digit or contain certain chars.
                // See https://github.com/marcuswestin/store.js/issues/40
                // See https://github.com/marcuswestin/store.js/issues/83
                var forbiddenCharsRegex = new RegExp("[!\"#$%&'()*+,/\\\\:;<=>?@[\\]^`{|}~]", "g")
                var ieKeyFix = function(key) {
                    return key.replace(/^d/, '___$&').replace(forbiddenCharsRegex, '___')
                }
                store.set = withIEStorage(function(storage, key, val) {
                    key = ieKeyFix(key)
                    if (val === undefined) { return store.remove(key) }
                    storage.setAttribute(key, store.serialize(val))
                    storage.save(localStorageName)
                    return val
                })
                store.get = withIEStorage(function(storage, key, defaultVal) {
                    key = ieKeyFix(key)
                    var val = store.deserialize(storage.getAttribute(key))
                    return (val === undefined ? defaultVal : val)
                })
                store.remove = withIEStorage(function(storage, key) {
                    key = ieKeyFix(key)
                    storage.removeAttribute(key)
                    storage.save(localStorageName)
                })
                store.clear = withIEStorage(function(storage) {
                    var attributes = storage.XMLDocument.documentElement.attributes
                    storage.load(localStorageName)
                    for (var i=attributes.length-1; i>=0; i--) {
                        storage.removeAttribute(attributes[i].name)
                    }
                    storage.save(localStorageName)
                })
                store.getAll = function(storage) {
                    var ret = {}
                    store.forEach(function(key, val) {
                        ret[key] = val
                    })
                    return ret
                }
                store.forEach = withIEStorage(function(storage, callback) {
                    var attributes = storage.XMLDocument.documentElement.attributes
                    for (var i=0, attr; attr=attributes[i]; ++i) {
                        callback(attr.name, store.deserialize(storage.getAttribute(attr.name)))
                    }
                })
            }

            try {
                var testKey = '__storejs__'
                store.set(testKey, testKey)
                if (store.get(testKey) != testKey) { store.disabled = true }
                store.remove(testKey)
            } catch(e) {
                store.disabled = true
            }
            store.enabled = !store.disabled

            return store
        }));


    }(window, document));
    /*
     *  // Store.js  v1.3.20
     *  增加expire
     *  hb.storage
     *
     */
    (function(window, document,undefined){
        "use strict";
        var storeWithExpiration = {
            set: function(key, val, options) {
                var options=options||{};
                var defaults={
                    expires:1000,
                };
                var settings = $.extend( {}, defaults, options );
                var exp=settings.expires*24*60*60*1000;
                haloBear.store.set(key, {
                    val:val,
                    exp:exp,
                    time:new Date().getTime()
                })
            },
            get: function(key) {
                var info = haloBear.store.get(key);
                if (!info) { return null }
                if (new Date().getTime() - info.time > info.exp) { return null }
                return info.val
            }
        }

        haloBear.storage=storeWithExpiration;

    }(window, document));


    /*
     browser

     */
    (function(window, document,undefined){
        "use strict";
        var browser=(function(){
            return{};
        }());
        haloBear.browser=browser;
    }(window, document));


    /*
     hb.browser
     */
    (function(window, document,undefined){
        "use strict";
        var device = function(){
            var u = window.navigator.userAgent,app = $window.navigator.appVersion;
            switch (true){
                case (u.indexOf('Android') > -1 || u.indexOf('Linux') > -1):
                    return "android";
                    break;
                case (u.indexOf('iPhone') > -1):
                    return "iPhone";
                    break;
                case (u.indexOf('iPad') > -1):
                    return "iPad";
                    break;
                default:
                    return "unknown-device";
            }
        };
        var isMobile = function(){
            var u = window.navigator.userAgent;
            //console.log(u);
            if(u.match(/Android|iPhone|iPad|iPod|IEMobile|BlackBerry/i)){
                return true;
            }
            else {
                return false;
            }
        };

        haloBear.browser.device=device;
        haloBear.browser.isMobile=isMobile;
    }(window, document));




    /*
     setInterval

     */
    (function(window, document,undefined){
        "use strict";
        var interval=function(fun,delay,count,afterCount){

            if(count){
                var refreshIntervalId = setInterval(intervalFunWithCount, delay);
            }else{
                var refreshIntervalId = setInterval(intervalFun, delay);
            }

            var _clear = function() {
                clearInterval(refreshIntervalId);
            };

            function intervalFun(){
                count--;
                fun();
            }

            function intervalFunWithCount(){
                intervalFun();
                if(count<=0){
                    _clear();
                    afterCount();
                }
            }
        };
        haloBear.interval=interval;
    }(window, document));

    window.hb=window.haloBear=haloBear;
}(window,document,undefined));



