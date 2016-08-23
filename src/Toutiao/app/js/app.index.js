;app.index=(function(){
	'use strict';
	function init(){
		renderContent();
		renderComment();
		dowloadClick();
		//getComment();
	}

	function contentService(data){
		var deferred=$.Deferred();
		$.ajax({
            method: "GET",
            url: "http://college-api.halobear.com/v1/wedding/weddingDetail",
            data: data,
			dataType:'jsonp',
            success: function(res, textStatus, errorThrown) {
                if(res.iRet==1){
                    deferred.resolve(res.data.detail);
                }else{
                    deferred.reject(res.info);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                deferred.reject('网络繁忙请稍候再试');
            }
        });
        return deferred.promise();
	}

	function commentService(data){
		var deferred=$.Deferred();
		//deferred.reject(-1);
		$.ajax({
            method: "GET",
            url: "http://college-api.halobear.com/v1/wedding/weddingComment",
            data: data,
			dataType:'jsonp',
            success: function(res, textStatus, errorThrown) {
                if(res.iRet==1){
                    deferred.resolve(res.data);
                }else{
                    deferred.reject(res.info);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                deferred.reject('网络繁忙请稍候再试');
            }
        });
        return deferred.promise();
	}

	function renderContent(){
		var id=window.location.href.split('=')[1];
		contentService({
			wedding_id:id || 1
		}).then(function(res){
			//console.log(res);
			res.create_time=parseInt(res.create_time)*1000;
			var Y=getdate(res.create_time).Y;
			var M=getdate(res.create_time).M;
			var D=getdate(res.create_time).D;
			var headingStr="";
			var picHtml="";

			headingStr=`
				<div class="title f-19" >${res.headline}</div>
				<div class="date f-15" >${Y}年${M}月${D}日</div>
			`;
			if(res.imgs){
				res.imgs.forEach(function(n,i){
					picHtml+=`
						<img class="lazy" data-original="${n.url}" alt="">
					`;
				})
			}
			
			
			$("#toutiao-header").empty().html(headingStr);
			$("#toutiao-content").empty().html(res.content);
			$("#toutiao-body-pic").empty().html(picHtml);
			$("#toutiao-brief").empty().text(res.brief);
			lazyLoad();

		},function(error){
			hb.lib.weui.alert({
                title:'温馨提示',
                content:error,
                btn:'确定',
            })
		})
	}

	function renderComment(){
		var id=window.location.href.split('=')[1];
		commentService({
			page:1,
			per_page:15,
			wedding_id:id || 1
		}).then(function(res){
			//console.log(res);
			var htmlStr='';
			var htmlAll='';
			res.comment.forEach(function(n,i){
				n.create_time=parseInt(n.create_time)*1000;
				var momentDate=getdate(n.create_time).momentDate;
				console.log(new Date(n.create_time))
				if(!n.parent_reply){
					htmlStr=`
						<div class="item">
							<div class="comment-item">
								<div class="avatar"><img src="${n.headimg}" alt=""></div>
								<div class="item-content">
									<div class="name f-16">${n.username}</div>
									<div class="text f-14">${n.content}</div>
								</div>
							</div>
							<div class="comment-bottom f-13 clearfix">
								<div class="date">${moment(n.create_time).fromNow()}</div>
								<div class="message f-13"><span class="haloIcon haloIcon-message f-18" style="position:relative;top:3px;"></span>回复</div>
								<div class="remark f-13"><span class="haloIcon haloIcon-great f-18"></span>${n.count_praise}</div>
							</div>
						</div>
					</div>
					`;
				}else{
					var htmlComment='';
					n.parent_reply.forEach(function(n2,i2){
						n2.create_time=parseInt(n2.create_time)*1000;
						var M=getdate(n2.create_time).M;
						var D=getdate(n2.create_time).D;
						htmlComment+=`
						<div class="dialog-content">
							<div class="content-top clearfix">
								<div class="name f-13">${n2.username}</div>
								<div class="date f-13">${moment(n2.create_time).fromNow()}</div>
							</div>
							<div class="content f-13">${n2.content}</div>
							<img src="images/arrow.png" alt="" class="img">
						</div>
						`;					
					});
					htmlStr=`
					<div class="item">
						<div class="comment-item">
							<div class="avatar"><img src="${n.headimg}" alt=""></div>
							<div class="item-content">
								<div class="name f-16">${n.username}</div>
								<div class="text f-14">${n.content}</div>
							</div>
						</div>
						${htmlComment}
						<div class="comment-bottom f-13 clearfix">
							<div class="date">${moment(n.create_time).fromNow()}</div>
							<div class="message f-13"><span class="haloIcon haloIcon-message f-18" style="position:relative;top:3px;"></span>回复</div>
							<div class="remark f-13"><span class="haloIcon haloIcon-great f-18"></span>${n.count_praise}</div>
						</div>
					</div>
					`;
				}
				htmlAll+=htmlStr;
				$("#comment-list").empty().html(htmlAll);
			})
		},function(error){
			$(".comment-block").hide();
			hb.lib.weui.alert({
                title:'温馨提示',
                content:error,
                btn:'确定',
            })
		})
	}

	function getComment(){
		var id=window.location.href.split('=')[1];
		commentService({
			page:1,
			per_page:15,
			wedding_id:id || 1
		}).then(function(res){
			console.log(res);
			var htmlStr='';
			var htmlAll='';
			res.comment.forEach(function(n,i){
				n.create_time=parseInt(n.create_time)*1000;
				console.log(new Date(n.create_time))
				var momentDate=getdate(n.create_time).momentDate;
				if(n.type=="comment"){
					htmlStr+=`
						<div class="item" data-id="${n.id}">
							<div class="comment-item">
								<div class="avatar"><img src="${n.headimg}" alt=""></div>
								<div class="item-content">
									<div class="name f-16">${n.username}</div>
									<div class="text f-14">${n.content}</div>
								</div>
							</div>
							<div class="comment-replay"></div>		
							<div class="comment-bottom f-13 clearfix">
								<div class="date">${moment(n.create_time).fromNow()}</div>
								<div class="message f-13"><span class="haloIcon haloIcon-message f-18" style="position:relative;top:3px;"></span>回复</div>
								<div class="remark f-13"><span class="haloIcon haloIcon-great f-18"></span>${n.count_praise}</div>
							</div>
						</div>
					`;
				}
			});
			$("#comment-list").empty().html(htmlStr);
			res.comment.forEach(function(n2,i2){
				n.create_time=parseInt(n.create_time)*1000;
				var momentDate=getdate(n.create_time).momentDate;
				if(n2.type=="reply"){
					htmlStr+=`
						<div class="comment-replay">
							<div class="dialog-content">
								<div class="content-top clearfix">
									<div class="name f-13">${n2.username}</div>
									<div class="date f-13">${M}-${D}</div>
								</div>
								<div class="content f-13">${n2.content}</div>
								<img src="images/arrow.png" alt="" class="img">
							</div>
						</div>
					`;
				}
			})
		},function(error){
			hb.lib.weui.alert({
                title:'温馨提示',
                content:error,
                btn:'确定',
            })
		})
	}

	function changeDateStyle(i){
        if (i < 10) {
            i = "0" + i;
        }
        return i;
	}
	
	function getdate(n){
		var date=new Date(parseInt(n));
		var Y=changeDateStyle(date.getFullYear());
		var M=changeDateStyle(date.getMonth()+1);
		var D=changeDateStyle(date.getDate());
		return{
			Y:Y,
			M:M,
			D:D,
			momentDate:Y+M+D,
		}
	}

	function lazyLoad(){
		$("img.lazy").lazyload({
            placeholder: "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==",
            effect: "fadeIn",
            failure_limit : 100,
            threshold: 200,
        });
	}

	function dowloadClick(){
		$("#dowload-block").on('click',function(e){
			e.preventDefault();
			window.location.href="http://a.app.qq.com/o/simple.jsp?pkgname=com.halobear.weddingvideo";
		})

		$("[close-btn]").on('click',function(e){
			e.preventDefault();
			$("#dowload-block").remove();
		})
	}

	return{
		init:init
	}
}());