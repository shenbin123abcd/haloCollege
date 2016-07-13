$.fn.subBanner = function(imglist,btns){
	var imgId=0;
	var timeoutId=null;
	var speed=6000;
	var imgs=imglist;
	var btns=btns;

	function player(){
		clearTimeout(timeoutId);
		$(btns).each(function(e){
			$(btns.get(imgs.length-1-e)).removeClass("active");
			if(e==imgId){
				$(imgs.get(e)).css("display","block");
				$(imgs.get(e)).animate({opacity:1},500);
			}else{
				$(imgs.get(e)).animate({opacity:0},500,function(){
					$(imgs.get(e)).css("display","none");
				});
			}
		});
		$(btns.get(imgs.length-1-imgId)).addClass("active");
		imgId < imgs.length-1 ? imgId++ : imgId=0;
		timeoutId=setTimeout(player,speed); 
	}
	$(btns).each(function(e){
		$(this).click(function(){
			imgId=(imgs.length-1-e);
			clearTimeout(timeoutId);
			player();
		});
	});
	player();
};