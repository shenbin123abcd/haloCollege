;(function(){
	//切换类实例化入口方法
	function Switcher(element,options){
		return new Switcher[options ? $.inArray(options.type,Switcher.type) >= 0 ? options.type : Switcher.options.type : Switcher.options.type](element,options);
	}
	//返回或设置对象私有成员
	Switcher.private = (function(){
		var _private = [];
		return function(object,private){
			if(arguments.length){
				var _current = [], _index;
				for(var i = 0; i < _private.length; i++){
					if(object === _private[i][0]){
						_current = _private[i];
						_index = i;
						break;
					}
				}
				if(private === true){
					return _index;
				}else{
					if(_current.length && arguments.length == 1){
						return _current[1];
					}else{
						if(_current.length){
							$.extend(_current[1], private);
						}else{
							_current[0] = object;
							_current[1] = private || {};
							_private.push(_current);
						}
						return _current[1];
					}
				}
			}else{
				return _private;
			}
		}
	}());
	//扩展切换对象
	Switcher.extend = (function(){
		Switcher.type = [];
		return function(name,object){
			$.inArray(name,Switcher.type) < 0 && Switcher.type.push(name);
			Switcher[name] = function(){
				this._initialize && this._initialize.apply(this,arguments) && (delete this._initialize);
				this.initialize && this.initialize.apply(this,arguments)&& (delete this.initialize);
			}
			$.extend(Switcher[name].prototype,Switcher.baseClass);
			Switcher[name].prototype.parentClass = Switcher.baseClass;
			for(var i in object){
				Switcher[name].prototype[i] = object[i];
			}
		}
	})();
	//获取元素宽度尺寸和
	Switcher.getElementWidthSum = function(element){
		var _widthSum = 0;
		element.each(function(){
			_widthSum += $(this).outerWidth() + parseInt($(this).css('margin-left')) + parseInt($(this).css('margin-right'));
		});
		return _widthSum;
	}
	//默认配置项
	Switcher.options = {
		type : 'leval', //元素切换方式
		current : 0, //当前初始显示元素索引
		step : 1, //单步切换数量
		auto : true, //是否自动切换
		navg : jQuery(), //指定切换索引导航
		rotation : true, //是否回转切换
		pause : 3000, //暂停时间
		speed : 600, //切换速度
		prev : $('<span class="prev">&nbsp;</span>'), //向前播放元素
		next : $('<span class="next">&nbsp;</span>'), //向后播放元素
		easing : null, //动画效果，基于easing文件
		onPauseProgress : null, //相应每次自动切换时暂停时间的进度
		onInitializeAfter : null //初始化完成时触发回调函数
	}
	//切换对象基类
	Switcher.baseClass = {
		//初始化操作
		_initialize : function(element,options){
			//设置私有成员对象
			var _private = Switcher.private(this), _self = this;
			//合并配置
			_private.options = $.extend({},Switcher.options,options);
			//设置切换列表
			this.switchList = element;
			//设置播放元素
			this.switchItem = this.switchList.children();
			//向前切换按钮
			this.prevElement = $.isFunction(_private.options.prev) ? _private.options.prev.call(this) : _private.options.prev;
			//向后切换按钮
			this.nextElement = $.isFunction(_private.options.next) ? _private.options.next.call(this) : _private.options.next;
			//设置暂停进度元素
			this.progress = $('<b/>');
			//克隆原始数据
			_private.clone = this.switchList.clone();
			//检测当前显示参数数据类型
			$.isFunction(_private.options.current) && (_private.options.current = _private.options.current.call(this));
			//更新切换列表
			this.update();
			//向前切换
			this.prevElement.bind('click',$.proxy(this,'prev'));
			//向后切换
			this.nextElement.bind('click',$.proxy(this,'next'));
			//自动播放
			_private.options.auto && this.auto();
		},
		//切换到指定分组索引
		target : function(index){
			var _private = Switcher.private(this);
			_private.pointer = index;
			_private.progress = 0;
			this.check();
			this.stop();
		},
		//向前切换
		prev : function(){
			var _private = Switcher.private(this);
			return this.prevStatus && this.target(--(_private.pointer));
		},
		//向后切换
		next : function(){
			var _private = Switcher.private(this);
			return this.nextStatus && this.target(++(_private.pointer));
		},
		//自动切换
		auto : function(){
			var _private = Switcher.private(this), _self = this;
			this.stop();
			_private.options.auto = true;
			_private.progress = _private.progress ? _private.progress : 0;
			_private.autoTimer = setInterval(function(){
				_private.progress += 50;
				if(_private.progress > _private.options.pause){
					_private.options.auto && _self.next();
					_private.progress = 0;
				}
				_self.progress.css('width', _private.progress / _private.options.pause * 100 + '%');
				$.isFunction(_private.options.onPauseProgress) && _private.options.onPauseProgress.call(_self,_private.progress);
			}, 50);
		},
		//单步切换
		step : function(){
			var _private = Switcher.private(this);
			_private.options.auto = false;
			this.stop(true,true);
		},
		//停止自动切换
		stop : function(){
			var _private = Switcher.private(this);
			clearInterval(_private.autoTimer);
		},
		//暂停切换
		pause : function(){
			var _private = Switcher.private(this);
			this.stop();
		},
		//启动切换
		enable : function(){
			var _private = Switcher.private(this);
			this.stop();
			_private.options.auto && this.auto();
		},
		//禁用切换
		disabled : function(){
			var _private = Switcher.private(this);
			this.pause();
			this.check();
		},
		//获取配置参数
		getOptions : function(name){
			return Switcher.private(this).options[name];
		},
		//检测切换状态
		check : function(){
			var _private = Switcher.private(this);
			this.prevElement.removeClass('disabled');
			this.prevStatus = true;
			this.nextElement.removeClass('disabled');
			this.nextStatus = true;
			_private.switchStatus = true;
			if(_private.switchStatus == 'disabled'){
				this.prevElement.addClass('disabled');
				this.prevStatus = false;
				this.nextElement.addClass('disabled');
				this.nextStatus = false;
			}else{
				if(_private.itemLeng <= _private.options.step){
					this.prevElement.addClass('disabled');
					this.prevStatus = false;
					this.nextElement.addClass('disabled');
					this.nextStatus = false;
				}else{
					if(_private.pointer == 0 && !_private.options.rotation){
						this.prevElement.addClass('disabled');
						this.prevStatus = false;
						_private.pointer = 0;
					}else if(_private.pointer < 0 && _private.options.rotation){
						_private.pointer = _private.groupNum - 1;
					}
					if(_private.pointer == _private.groupNum - 1 && !_private.options.rotation){
						this.nextStatus = false;
						this.nextElement.addClass('disabled');
						_private.pointer = _private.groupNum - 1;
					}else if(_private.pointer > _private.groupNum - 1 && _private.options.rotation){
						_private.pointer = 0;
					}
				}
			}
			if(!this.prevStatus && !this.nextStatus){
				_private.switchStatus = false;
			}
			this.navgElement.eq(_private.pointer).addClass('selected').siblings().removeClass('selected');
			return _private.switchStatus;
		},
		//更新播放列表
		update : function(){
			var _private = Switcher.private(this), _self = this;
			//更新切换元素
			this.switchItem = _private.wrap ? _private.wrap.children() : this.switchList.children();
			//设置切换指针
			var _max = Math.ceil(this.switchItem.length / _private.options.step) - 1;
			var _cur = Math.floor(_private.options.current / _private.options.step);
			_private.pointer = _cur > _max ? _max : _cur;
			//保存切换元素个数
			_private.itemLeng = this.switchItem.length;
			//保存切换元素分组数
			_private.groupNum = Math.ceil(_private.itemLeng / _private.options.step);
			//切换元素分组
			_private.group = [];
			for(var i = 0; i < _private.groupNum; i++){
				var _start = i * _private.options.step, _end = (i + 1) * _private.options.step;
				if(_end > _private.itemLeng){
					_end = _private.itemLeng;
					_start = _private.itemLeng - _private.options.step;
				}
				_private.group.push(this.switchItem.slice(_start, _end));
			}
			//切换导航
			if(_private.options.navg.length){
				this.navgElement = _private.options.navg;
			}else{
				this.navgElement = $('<div/>');
				for(var i = 0; i < _private.groupNum; i++){
					this.navgElement.append($('<span class="index">'+(i+1)+'</span>'));
				}
				this.navgElement = this.navgElement.children();
			}
			this.navgElement.bind('click',function(){
				_self.target(_self.navgElement.index($(this)));
			});
			//检测切换对象
			this.check();
		},
		//销毁切换对象
		destroy : function(){
			//获取克隆元素
			var _clone = Switcher.private(this).clone;
			//停止切换
			this.stop();
			//恢复默认结构
			this.switchList.replaceWith(_clone);
			//移除私有成员
			Switcher.private().splice(Switcher.private(this,true),1);
			//移除元素数据
			this.switchList.removeData('switcher');
			//移除实例成员
			for(var i in this){
				if(this[i][0] && this[i][0].nodeType == 1){
					this[i].remove();
				}else{
					this[i] = undefined;
				}
			}
		}
	}
	//可见性切换
	Switcher.extend('visibility',{
		//初始化
		initialize : function(elements,options){
			var _private = Switcher.private(this);
			if(_private.switchStatus){
				//定位初始选中元素
				this.switchItem.hide();
				$.each(_private.group[_private.pointer],function(){
					$(this).show();
				});
			}
			//初始化完成回调函数
			$.isFunction(_private.options.onInitializeAfter) && _private.options.onInitializeAfter.call(this);
		},
		//切换到目标元素
		target : function(){
			var _private = Switcher.private(this), _self = this;
			this.parentClass.target.apply(this,arguments);
			this.switchItem.hide();
			$.each(_private.group[_private.pointer],function(){
				$(this).show();
			});
			_private.options.auto && _self.auto();
		}
	});
	//水平切换
	Switcher.extend('leval',{
		//初始化
		initialize : function(elements,options){
			var _private = Switcher.private(this);
			if(_private.switchStatus){
				//定位初始选中元素
				_private.wrap.css('margin-left',-(Switcher.getElementWidthSum(_private.group[_private.pointer].eq(0).prevAll())));
			}
			//初始化完成回调函数
			$.isFunction(_private.options.onInitializeAfter) && _private.options.onInitializeAfter.call(this);
		},
		//更新播放列表
		update : function(){
			var _private = Switcher.private(this);
			//调用父类更新方法
			this.parentClass.update.apply(this,arguments);
			//创建外层集合元素
			_private.wrap = $('<div/>');
			//更新必须切换结构
			this.switchList.empty().append(_private.wrap.append(this.switchItem));
			//计算并设置宽度
			_private.wrap.width(Switcher.getElementWidthSum(this.switchItem));
		},
		//切换到目标元素
		target : function(index){
			var _private = Switcher.private(this), _self = this;
			this.parentClass.target.apply(this,arguments);
			_private.wrap.animate({'margin-left' : -(Switcher.getElementWidthSum(_private.group[_private.pointer].eq(0).prevAll()))},_private.options.speed,_private.options.easing,function(){
				_private.options.auto && _self.auto();
			});
		},
		//停止动画
		stop : function(clearQueue,gotoEnd){
			var _private = Switcher.private(this);
			this.parentClass.stop.apply(this,arguments);
			_private.wrap.stop(clearQueue,gotoEnd);
		},
		//暂停播放
		pause : function(){
			var _private = Switcher.private(this);
			this.parentClass.pause.apply(this,arguments);
			_private.wrap.pause();
		},
		//启用播放
		enable : function(){
			var _private = Switcher.private(this);
			this.parentClass.enable.apply(this,arguments);
			_private.wrap.resume();
		}
	});
	//垂直切换
	Switcher.extend('vertical',{
		//初始化
		initialize : function(elements,options){
			var _private = Switcher.private(this);
			if(_private.switchStatus){
				//定位初始选中元素
				_private.wrap.css('margin-top',_private.wrap.offset().top - _private.group[_private.pointer].eq(0).offset().top);
			}
			//初始化完成回调函数
			$.isFunction(_private.options.onInitializeAfter) && _private.options.onInitializeAfter.call(this);
		},
		//更新播放列表
		update : function(){
			var _private = Switcher.private(this);
			//调用父类更新方法
			this.parentClass.update.apply(this,arguments);
			//创建外层集合元素
			_private.wrap = $('<div/>');
			//更新必须切换结构
			this.switchList.empty().append(_private.wrap.append(this.switchItem));
		},
		//切换到目标元素
		target : function(index){
			var _private = Switcher.private(this), _self = this;
			this.parentClass.target.apply(this,arguments);
			_private.wrap.animate({'margin-top' : _private.wrap.offset().top - _private.group[_private.pointer].eq(0).offset().top},_private.options.speed,_private.options.easing,function(){
				_private.options.auto && _self.auto();
			});
		},
		//停止动画
		stop : function(clearQueue,gotoEnd){
			var _private = Switcher.private(this);
			this.parentClass.stop.apply(this,arguments);
			_private.wrap.stop(clearQueue,gotoEnd);
		},
		//暂停播放
		pause : function(){
			var _private = Switcher.private(this);
			this.parentClass.pause.apply(this,arguments);
			_private.wrap.pause();
		},
		//启用播放
		enable : function(){
			var _private = Switcher.private(this);
			this.parentClass.enable.apply(this,arguments);
			_private.wrap.resume();
		}
	});
	//淡入淡出
	Switcher.extend('fade',{
		//初始化
		initialize : function(elements,options){
			var _private = Switcher.private(this);
			if(_private.switchStatus){
				//定位初始选中元素
				this.switchItem.hide();
				$.each(_private.group[_private.pointer],function(){
					$(this).show();
				});
				//记录上次切换指针
				_private.prevPointer = _private.pointer;
			}
			//初始化完成回调函数
			$.isFunction(_private.options.onInitializeAfter) && _private.options.onInitializeAfter.call(this);
		},
		//切换到目标元素
		target : function(){
			var _private = Switcher.private(this), _self = this;
			this.parentClass.target.apply(this,arguments);
			$.each(_private.group[_private.prevPointer],function(i,v){
				$(this).hide();
			});
			$.each(_private.group[_private.pointer],function(i,v){
				$(this).fadeTo(_private.options.speed, 1).show();
				i === _private.options.step - 1 && _private.options.auto && _self.auto();
			});
			_private.prevPointer = _private.pointer;
		}
	});
	//扩展DOM元素切换方法
	$.fn.switcher = function(options){
		var _selector = this.selector;
		return this.each(function(){
			var _switcher = $(this).data('switcher');
			if(_switcher){
				if($.type(options) == 'string' && options in _switcher){
					return _switcher[Array.prototype.shift.call(arguments)].apply(_switcher,arguments);
				}else{
					_switcher.destroy();
					$(_selector).switcher(options);
				}
			}else{
				$(this).data('switcher', Switcher($(this),options));
			}
		});
	}
})();
//动画暂停
;(function(){function s(){return(new Date).getTime()}var e=jQuery,t="jQuery.pause",n=1,r=e.fn.animate,i={};e.fn.animate=function(o,u,a,f){var l=e.speed(u,a,f);return l.complete=l.old,this.each(function(){this[t]||(this[t]=n++);var u=e.extend({},l);r.apply(e(this),[o,e.extend({},u)]),i[this[t]]={run:!0,prop:o,opt:u,start:s(),done:0}})},e.fn.pause=function(){return this.each(function(){this[t]||(this[t]=n++);var r=i[this[t]];r&&r.run&&(r.done+=s()-r.start,r.done>r.opt.duration?delete i[this[t]]:(e(this).stop(),r.run=!1))})},e.fn.resume=function(){return this.each(function(){this[t]||(this[t]=n++);var o=i[this[t]];o&&!o.run&&(o.opt.duration-=o.done,o.done=0,o.run=!0,o.start=s(),r.apply(e(this),[o.prop,e.extend({},o.opt)]))})}})();