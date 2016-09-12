'use strict';

;app.index = function () {
	'use strict';

	function init() {
		dowloadClick();
		renderContent();
		renderRecommend();
	}

	function dowloadClick() {
		$("#dowload-block").on('click', function (e) {
			e.preventDefault();
			window.location.href = "http://a.app.qq.com/o/simple.jsp?pkgname=com.halobear.weddingvideo";
		});

		$("[close-btn]").on('click', function (e) {
			e.preventDefault();
			$("#dowload-block").remove();
		});
	}

	function contentService(data) {
		var deferred = $.Deferred();
		$.ajax({
			url: "http://collegeapi-test.weddingee.com/v1/api/videoDetailV2",
			data: data,
			dataType: 'jsonp',
			success: function success(res, textStatus, errorThrown) {
				if (res.iRet == 1) {
					deferred.resolve(res.data);
				} else {
					deferred.reject(res.info);
				}
			},
			error: function error(jqXHR, textStatus, errorThrown) {
				deferred.reject('网络繁忙请稍候再试');
			}
		});
		return deferred.promise();
	}

	function recommendService(data) {
		var deferred = $.Deferred();
		//deferred.reject(-1);
		$.ajax({
			method: "GET",
			url: "http://collegeapi-test.weddingee.com/v1/api/videoRecommend",
			data: data,
			dataType: 'jsonp',
			success: function success(res, textStatus, errorThrown) {
				if (res.iRet == 1) {
					deferred.resolve(res.data);
				} else {
					deferred.reject(res.info);
				}
			},
			error: function error(jqXHR, textStatus, errorThrown) {
				deferred.reject('网络繁忙请稍候再试');
			}
		});
		return deferred.promise();
	}

	function renderContent() {
		var id = window.location.href.split('=')[1];
		contentService({
			id: id
		}).then(function (res) {
			var guestHtml = '';
			var videoHtml = '';
			var videoWrapperHtml = '';
			var guests = res.guests;
			var video = res.video;
			//console.log(video);
			if (video.is_vip == 1) {
				videoWrapperHtml = '\n\t\t\t\t\t<div class="video-top-block no-access">\n\t\t\t\t\t\t<div class="top-block-wrapper">\n\t\t\t\t\t\t\t<div class="top-block-desc">开通黄金会员后可观看完整视频</div>\n\t\t\t\t\t\t\t<div class="top-block-btn btn f-12" id="play-btn">开通会员</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t';
			} else {
				videoWrapperHtml = '\n\n\t\t\t\t';
			};

			guestHtml = '\n\t\t\t\t<div class="video-info">\n\t\t\t\t\t<div class="info-title f-16">' + video.title + '</div>\n\t\t\t\t\t<div class="info-desc clearfix">\n\t\t\t\t\t\t<div class="desc-left f-15">' + guests.name + '丨' + guests.position + '</div>\n\t\t\t\t\t\t<div class="desc-right f-15">播放：4359</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t\t<div class="content-desc">\n\t\t\t\t\t<div class="avatar-block">\n\t\t\t\t\t\t<img src=' + guests.avatar_url + ' alt="">\n\t\t\t\t\t</div>\n\t\t\t\t\t<div class="desc-info-block">\n\t\t\t\t\t\t<div class="name f-16">' + guests.name + '</div>\n\t\t\t\t\t\t<div class="text f-14">' + guests.content + '</div>\n\t\t\t\t\t</div>\n\t\t\t\t</div>\n\t\t\t';
			$("#video-block").empty().html(videoWrapperHtml);
			$("#guest-block").empty().html(guestHtml);
			if ($("#play-btn")) {
				$("#play-btn").on('click', function (e) {
					alert(1);
				});
			}
		}, function () {
			hb.lib.weui.alert({
				title: '温馨提示',
				content: error,
				btn: '确定'
			});
		});
	}

	function renderRecommend() {
		var id = window.location.href.split('=')[1];
		recommendService({
			page: 1,
			per_page: 1,
			vid: id || 1
		}).then(function (res) {
			console.log(res);
			var recommendStr = '';
			res.forEach(function (n, i) {
				recommendStr += '\n\t\t\t\t\t<div class="recommend-item">\n\t\t\t\t\t\t<a class="cover-block" href=\'\'>\n\t\t\t\t\t\t\t<img src=\'\' alt="">\n\t\t\t\t\t\t</a>\n\t\t\t\t\t\t<div class="desc-block">\n\t\t\t\t\t\t\t<div class="title f-15">幻熊婚礼人专访</div>\n\t\t\t\t\t\t\t<div class="info f-13">蔡上丨重庆蔡上工作室创始人</div>\n\t\t\t\t\t\t\t<div class="play-num f-13">播放 ：' + n.views + '</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>\n\t\t\t\t';
			});
			$("#recommend-block").empty().html(recommendStr);
		}, function (error) {});
	}

	return {
		init: init
	};
}();