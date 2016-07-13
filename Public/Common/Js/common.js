/*
 * 公共操作
 * @author wtwei
 */
var userAgent = navigator.userAgent.toLowerCase();
var COMMON = (function() {
	return {
		/**
		 * 判断浏览器类型
		 */
		browser: {
			/**
			 * 获取版本号
			 */

			version: (userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/) || [0, '0'])[1],
			/**
			 * 是否webkit浏览器
			 */
			webkit: /webkit/.test(userAgent),
			/**
			 * 是否opera浏览器
			 */
			opera: /opera/.test(userAgent),
			/**
			 * 是否IE浏览器
			 */
			msie: /msie/.test(userAgent) && !/opera/.test(userAgent),
			/**
			 * 是否mozilla浏览器
			 */
			mozilla: /mozilla/.test(userAgent) && !/(compatible|webkit)/.test(userAgent),
			/**
			 * 是否TT浏览器
			 */
			tt: /tencenttraveler/.test(userAgent),
			/**
			 * 是否chrome浏览器
			 */
			chrome: /chrome/.test(userAgent),
			/**
			 * 是否firefox浏览器
			 */
			firefox: /firefox/.test(userAgent),
			/**
			 * 是否safari浏览器
			 */
			safari: /safari/.test(userAgent),
			/**
			 * 是否gecko浏览器
			 */
			gecko: /gecko/.test(userAgent),
			/**
			 * 是否IE6
			 */
			ie6: this.msie && this.version.substr(0, 1) == '6',
			isIeNew: function() {
				return this.msie && parseInt(this.version.substr(0, 3)) > 8;
			}
		},
		// AJAX
		ajax: function(conf, callback) {
			$.ajax({
				url: conf.url,
				data: conf.data || '',
				type: conf.type || 'post',
				dataType: conf.dataType || 'json',
				success: function(res) {
					$.dialog.get('loading') && $.dialog.get('loading').close();
					if ($.isFunction(callback)) {
						callback(res);
					} else {
						if (res.status) {
							$.dialog.success(res.info);
						} else {
							$.dialog.error(res.info);
						};
					};
				},
				error: function() {
					$.dialog.get('loading') && $.dialog.get('loading').close();
					$.dialog.error('服务器繁忙，请稍后再试！');
				}
			});
		},
		/**
		 * 获取url中的参数值
		 * @param {string} pa 参数名称
		 * @return {string} 参数值
		 */
		request: function(pa) {
			var url = window.location.href.replace(/#+.*$/, ''),
				params = url.substring(url.indexOf("?") + 1, url.length).split("&"),
				param = {};
			for (var i = 0; i < params.length; i++) {
				var t = params[i].split("=");
				param[t[0]] = t[1];
			}
			return (typeof(param[pa]) == "undefined") ? "" : param[pa];
		},
		/**
		 * cookie操作
		 * cookie.set("abc","345",3600,"www.qq.com","/",false);
		 * cookie.get("abc")
		 * cookie.clear("abc","www.qq.com","/")，删除后值为null
		 * @type {Object}
		 */
		cookie: {
			/**
			 * 设置cookie
			 * @param {string} sName cookie名
			 * @param {string} sValue cookie值
			 * @param {int} iExpireSec 失效时间（秒）
			 * @param {string} sDomain 作用域
			 * @param {string} sPath 作用路径
			 * @param {bool} bSecure 是否加密
			 * @return {void}
			 */
			set: function(sName, sValue, iExpireSec, sDomain, sPath, bSecure) {
				if (sName == undefined) {
					return;
				}
				if (sValue == undefined) {
					sValue = "";
				}
				var oCookieArray = [sName + "=" + escape(sValue)];
				if (!isNaN(iExpireSec)) {
					var oDate = new Date();
					oDate.setTime(oDate.getTime() + iExpireSec * 1000);
					iExpireSec == 0 ? '' : oCookieArray.push("expires=" + oDate.toGMTString());
				}
				if (sDomain != undefined) {
					oCookieArray.push("domain=" + sDomain);
				}
				if (sPath != undefined) {
					oCookieArray.push("path=" + sPath);
				}
				if (bSecure) {
					oCookieArray.push("secure");
				}
				document.cookie = oCookieArray.join("; ");
			},
			/**
			 * 获取cookie
			 * @param {string} sName cookie名
			 * @param {string} sValue 默认值
			 * @return {string} cookie值
			 */
			get: function(sName, sDefaultValue) {
				var sRE = "(?:; |^)" + sName + "=([^;]*);?";
				var oRE = new RegExp(sRE);

				if (oRE.test(document.cookie)) {
					return unescape(RegExp["$1"]);
				} else {
					return sDefaultValue || null;
				}
			},
			/**
			 * 获取cookie
			 * @param {string} sName cookie名
			 * @param {string} sDomain 作用域
			 * @param {sPath} sPath 作用路径
			 * @return {void}
			 */
			clear: function(sName, sDomain, sPath) {
				var oDate = new Date();
				cookie.set(sName, "", -oDate.getTime() / 1000, sDomain, sPath);
			}
		},
		string: {
			/**
			 * 校验邮箱地址
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isMail: function(str) {
				return /^(?:[\w-]+\.?)*[\w-]+@(?:[\w-]+\.)+[\w]{2,3}$/.test(str);
			},
			length: function(str) {
				return str.replace(/[^\x00-\xff]/g, "**").length;
			},
			removeHtml: function(str) {
				return str.replace(/<[^>].*?>/g, "");
			},
			/**
			 * 校验普通电话、传真号码：可以“+”开头，除数字外，可含有“-”
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isTel : function(str){
				return /^[+]{0,1}(\d){1,3}[ ]?([-]?((\d)|[ ]){1,12})+$/.test(str);
			},
			/**
			 * 校验手机号码：必须以数字开头
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isMobile : function(str){
				return /^1[3458]\d{9}$/.test(str);
			},
			/**
			 * 校验邮政编码
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isZipCode : function(str){
				return /^(\d){6}$/.test(str);
			},
			/**
			 * 是否身份证号码
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isIDCard : function(str){
				var C15ToC18 = function(c15) {
					var cId=c15.substring(0,6)+"19"+c15.substring(6,15);
					var strJiaoYan  =[  "1", "0", "X", "9", "8", "7", "6", "5", "4", "3", "2"];
					var intQuan =[7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
					var intTemp=0;
					for(i = 0; i < cId.length ; i++)
					intTemp +=  cId.substring(i, i + 1)  * intQuan[i];  
					intTemp %= 11;
					cId+=strJiaoYan[intTemp];
					return cId;
				}
				var Is18IDCard = function(IDNum) {
					var aCity={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"};
				
					var iSum=0, info="", sID=IDNum;
					if(!/^\d{17}(\d|x)$/i.test(sID)) {
						return false;
					}
					sID=sID.replace(/x$/i,"a");
				
					if(aCity[parseInt(sID.substr(0,2))]==null) {
						return false;
					}
					
					var sBirthday=sID.substr(6,4)+"-"+Number(sID.substr(10,2))+"-"+Number(sID.substr(12,2));
					var d=new Date(sBirthday.replace(/-/g,"/"))
					
					if(sBirthday!=(d.getFullYear()+"-"+ (d.getMonth()+1) + "-" + d.getDate()))return false;
					
					for(var i = 17;i>=0;i --) iSum += (Math.pow(2,i) % 11) * parseInt(sID.charAt(17 - i),11)
					
					if(iSum%11!=1)return false;
					return true;
				}
				
				return str.length==15 ? Is18IDCard(C15ToC18(str)) : Is18IDCard(str);
			},	
			/**
			 * 是否全部是中文
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isChinese : function(str){
				return milo.getChineseNum(str)==str.length ? true : false;
			},
			/**
			 * 是否全部是英文
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isEnglish : function(str){
				return /^[A-Za-z]+$/.test(str);
			},
			/**
			 * 是否链接地址
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isURL : function(str){
				return /^http:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(str);
			},
			/**
			 * 是否数字字符串
			 * @param {string} str 字符串
			 * @return {bool}
			 */
			isNumberString : function(str){
				return /^\d+$/.test(str);
			}
		},
		// placeholder: function() {
		// 	$('input, textarea').placeholder();
		// },
		top: function() {
			var pageToper = $('.page-toper');
			$(window).scroll(function() {
				if ($(this).scrollTop() > 240) {
					pageToper.fadeIn(300);
				} else {
					pageToper.fadeOut(300);
				}
			}).scroll();
			pageToper.click(function() {
				$('html,body').animate({
					scrollTop: 0
				}, 400);
			});
		},
		sidebar: {
			init: function() {
				var _this = this;
				// $(window).scroll(function() {
				// 	if ($(this).scrollTop() > 250) {
				// 		$('.wed-sidebar .top').css('display', 'block');
				// 	} else {
				// 		$('.wed-sidebar .top').css('display', 'none');
				// 	}
				// });

				$('#top').click(function() {
					_this.scrollTop('body');
				});

				$('.footer-sns .wx').click(function() {
					COMMON.weixin();
				});
			},
			scrollTop: function(id) {
				$('html,body').animate({
					scrollTop: $('#' + id).offset().top
				}, 500);
				return;
			}
		},
		feedback: function(){
			$('.feedback-btn').click(function() {
				var data = {};
				data.name = $.trim($('#feedback-name').val());
				data.tel = $.trim($('#feedback-tel').val());
				data.content = $.trim($('#feedback-content').val());

				if (data.name == '') {
					$.dialog.error('抱歉，请填写您的称呼！');
					return true;
				}
				if (data.tel == '') {
					$.dialog.error('抱歉，请填写您的联系方式！');
					return true;
				}
				if (data.content == '') {
					$.dialog.error('抱歉，请填写您的留言内容！');
					return true;
				}
				$.dialog.loading('留言提交中...');
				COMMON.ajax({url:_URL_ + '/about/feedback',data:data},function(res){
					if (res.status == 1) {
						$.dialog.success(res.info,function(){
							$('form[name=feedback]')[0].reset();
							data = {};
						});
					}else{
						$.dialog.error(res.info);
					}
				});
			});
		},
		weixin:function(){
			$.dialog.show('<div class="pop-wechat" style="padding:10px;"><img src="/Public/Home/Images/halos.jpg" alt="" /></div>',{title:'关注幻熊科技公众账号'});
		}
	};

})();

// 调用
$(function() {
	// COMMON.placeholder();

	COMMON.sidebar.init();
	COMMON.feedback();
});