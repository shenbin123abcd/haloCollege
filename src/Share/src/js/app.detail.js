;app.detail=(function( url , settings ,method ){
    function init() {
        initDom();
        getData();
        linstenUrl();

        // getPreData();
    }

    function getData() {
        hb.lib.iui.loading.show();
        app.service.getDetail({
            item_id:hb.location.url('?item_id'),
            shop_id:hb.location.url('?shop_id')||'',
            merchant_pid:hb.location.url('?merchant_pid')||'',
        }).then(res=>{
            hb.lib.iui.loading.hide();
            renderSwiper(res);
            renderTab(res);
            renderDetailOrderModal(res);
        },res=>{
            hb.lib.iui.loading.hide();
            alert(res)
        })
    }

    function getPreData(){
        hb.lib.iui.loading.show();
        app.service.getDetailPrefer({
            item_id:hb.location.url('?item_id'),
            shop_id:hb.location.url('?shop_id')||'',
            merchant_pid:hb.location.url('?merchant_pid')||'',
        }).then(res=>{
            hb.lib.iui.loading.hide();
            renderPrefer(res)
        },res=>{
            hb.lib.iui.loading.hide();
            alert(res);
        });
    }
    //获取服务内容 数据
    function getSerData(){
        app.service.getDetailService({
            item_id:hb.location.url('?item_id'),
            shop_id:hb.location.url('?shop_id')||'',
            merchant_pid:hb.location.url('?merchant_pid')||'',
        }).then(res=>{
            $('#tab-3-loading').remove();
            renderServiceContent(res);
        },res=>{

            alert(res);
        })
    }

    function renderSwiper(res) {
        // $('#root').empty();
        var swiperSlide=``;
        res.data.top_images.forEach(n=>{
            swiperSlide+=`<div class="swiper-slide">
             <img src="${n}@1e_1c_0o_0l_422h_750w" alt="" class="swiper-image">
            </div>`
        });
        var giftTotal = ``;
        res.data.gifts.forEach(n=>{
            giftTotal+=`<div class="gift-desc">
                            <p class="tag-wrap"><span class="tag">${n.tag}</span></p>  
                            <span class="remark">${n.remark}</span>
                        </div>`
        });

        $('#swiper-block').append(`
            <div class="my-swiper-wrapper">
                <div class="swiper-container" id="my-swiper-container">
                    <div class="swiper-wrapper">
                         ${swiperSlide}
                    </div>
                    <!-- Add Pagination --> 
                    <div class="swiper-pagination" id="my-swiper-pagination"></div>
                </div>
            </div>
            <div class="myDetail-desc">  
                <div class="desc-title">${res.data.subject}</div>
                <p class="desc-price">
                    <span class="desc-price-left"><strong class="strong">${res.data.price}</strong> <s class="under">${res.data.original_price}</s></span>
                    <span class="desc-title-right">可先支付${res.data.deposit}定金</span>
                </p>
            </div>
            <div class="myDetail-address">  
                <!--<img src="${res.data.shop.brand_logo}" alt="" class="brand-logo">-->
                <div class="address-desc">  
                  <span class="shop-name">${res.data.shop.main_shop_name}</span>
                  <p class="shop-address">公司地址：${res.data.shop.address}</p>
                </div>
                <i class="haloIcon haloIcon-more"></i>
            </div>
            <div class="myDetail-gift">
               ${giftTotal}
       
            </div>   
    `);
        $('#detail-footer-fixed').append(`
           <div class="detail-footer-fixed">
                  <a class="fixed-left" href="tel:${res.data.shop.contact_number&&res.data.shop.contact_number.split(',')[0]}">
                      <i class="haloIcon haloIcon-phone"></i>
                      <div class="phone">电话</div>
                  </a>
                  <span class="fixed-right" id="detail-order-bt">立即买单</span>
                  
           </div>
    `);


        var swiper = new Swiper('#my-swiper-container', {
            pagination: '#my-swiper-pagination',
            paginationClickable: true
        });




    }


    function renderDetailOrderModal(res) {
        $('#detail-order-modal').append(`
           <div class="modal fade" tabindex="-1" id="myOrderModal" role="dialog">
              <div class="modal-dialog my-modal-1" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                      <div class='modal-body-content'>
                        <div class="content-pic">
                            <div class="close-btn" data-dismiss="modal" >&times;</div>
                            <img src="/images/unable-seat.png" alt=""/>
                        </div>
                        <form id="detail-order-form">
                            <div class="content-form-block">
                                <input type="text" placeholder="阁下称呼" class="form-control input-style " />
                                <input type="text" placeholder="阁下手机号" class="form-control input-style  last" />
                                <div class="desc-text">为了能给你提供更好的服务，请留下你的联系方式</div>
                            </div>
                            <div class='modal-dialog-footer f-15'>
                                <div class="modal-dialog-send" >
                                    <div class="line"></div>
                                    去支付
                                </div>
                            </div>
                        </form>
                      </div>
                    </div>
                </div>
              </div>
            </div>
    `);

        $('#detail-order-bt').on('click',function () {
            $('#myOrderModal').modal('show')
        })

        $('#myOrderModal').on('submit',function (e) {
            e.preventDefault();

        })



    }

    function renderPrefer(res) {
        $('#detail-like-desc').append(`
            <div class="myDetail-like">
                <span class="like-title">猜你喜欢</span>
                <section class="like-desc">
                    <a class="like-figures">
                        <img src="http://dashboard-static.halobear.cn/cehua/20161020/5808ac469766b.jpg@1e_1c_0o_0l_422h_750w" alt="" class="like-images">
                        <div class="like-subject">新中式婚礼套餐</div>
                        <span class="like-price">￥20800</span>
                        <s class="like-before-price">￥28800</s>
                    </a>
                    <a class="like-figures">
                        <img src="http://dashboard-static.halobear.cn/cehua/20161020/5808ac469766b.jpg@1e_1c_0o_0l_422h_750w" alt="" class="like-images">
                        <div class="like-subject">新中式婚礼套餐</div>
                        <span class="like-price">￥20800</span>
                        <s class="like-before-price">￥28800</s>
                    </a>
                     <a class="like-figures">
                        <img src="http://dashboard-static.halobear.cn/cehua/20161020/5808ac469766b.jpg@1e_1c_0o_0l_422h_750w" alt="" class="like-images">
                        <div class="like-subject">新中式婚礼套餐</div>
                        <span class="like-price">￥20800</span>
                        <s class="like-before-price">￥28800</s>
                    </a>
                    <a class="like-figures">
                        <img src="http://dashboard-static.halobear.cn/cehua/20161020/5808ac469766b.jpg@1e_1c_0o_0l_422h_750w" alt="" class="like-images">
                        <div class="like-subject">新中式婚礼套餐</div>
                        <span class="like-price">￥20800</span>
                        <s class="like-before-price">￥28800</s>
                    </a>
                </section>  
            </div>
        `);
    }

    function initDom() {
        $('#root').append(`
            <div id="swiper-block"></div>
            <div id="detail-image-desc"></div>
            <div id="detail-like-desc"></div>
            <div id="detail-footer-fixed"></div>
            <div id="detail-order-modal"></div>
        `);
    }

    function renderTab(res) {

        let tab1Str=``;
        res.data.top_images.forEach(n=>{
            tab1Str+=`<div class="img-box">
                <img class="img" alt="" data-original="${n}@1e_1c_0o_0l_750w">    
            </div>`
        });



        $('#detail-image-desc').append(`
             <!-- Swiper -->
             <div class="my-tab-swiper-wrapper">
                <!-- Add Pagination -->
                <div id="my-tab-swiper-pagination" class="swiper-pagination"  ></div>
                <div class="tab-line"></div>
                 <div id="my-tab-swiper" class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide tab-1">${tab1Str}</div>
                        <div class="swiper-slide tab-2">${renderServiceDetail(res)}</div>
                        <div class="swiper-slide tab-3">
                            <div id="tab-3-loading" class="tab-3-loading">
                                <i class="haloIcon haloIcon-spinner haloIcon-spin"></i>
                            </div>
                            <div id="tab-3-content"></div>
                            
                      
                        </div>
                    </div>
                </div>
            </div>
        `);

        $(".img-box>.img").lazyload({
            placeholder:"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABAQMAAAAl21bKAAAABGdBTUEAALGPC/xhBQAAAAFzUkdCAK7OHOkAAAADUExURfDw8Lu/XasAAAAKSURBVAjXY2AAAAACAAHiIbwzAAAAAElFTkSuQmCC",
            effect:"fadeIn"
        });

        var isTab3Data=false;
        var tabSwiper = new Swiper('#my-tab-swiper', {
            pagination: '#my-tab-swiper-pagination',
            paginationClickable: true,
            paginationBulletRender:  (swiper, index, className)=> {
                if(index==0){
                    return `<span class=${className}><span class="title">图文详情</span></span>`;
                }else if(index==1){
                    return `<span class=${className}><span class="title">服务内容</span></span>`;
                }else{
                    return `<span class=${className}><span class="title">本店热卖</span></span>`;
                }

            },
            onSlideChangeEnd(swiper){
                if(swiper.activeIndex==2){
                    if (isTab3Data!=true){
                        getSerData();
                    }
                    isTab3Data=true;
                }
            }
        });


        
        function renderServiceDetail(res) {
            let htmlStr=``;
            htmlStr+=`
                <div class="item-p">
                    <div class="title-p">服务内容</div>
                    ${renderItemC1(res.data.zhuchi_desc,res.data.zhuchi_level,'主持人')}
                    ${renderItemC1(res.data.huazhuang_desc,res.data.huazhuang_level,'化妆师')}
                    ${renderItemC1(res.data.sheying_desc,res.data.sheying_level,'摄影师')}
                    ${renderItemC1(res.data.shexiang_desc,res.data.shexiang_level,'摄像师')}
                </div>
            `;

            if(res.data.yingbin&&res.data.yishi&&res.data.yanhui&&res.data.huayi){
                htmlStr+=`
                <div class="item-gap"></div>
                <div class="item-p">
                    <div class="title-p">婚礼布置</div>
                    ${renderItemC2(res.data.yingbin,'迎宾区')}
                    ${renderItemC2(res.data.yishi,'仪式区')}
                    ${renderItemC2(res.data.yanhui,'宴会区')}
                    ${renderItemC2(res.data.huayi,'花艺')}
                </div>
                `;
            }
            if(res.data.dengguang&&res.data.daoju){
                htmlStr+=`
                <div class="item-gap"></div>
                <div class="item-p">
                    <div class="title-p">灯光舞美</div>
                    ${renderItemC2(res.data.yingbin,'婚礼灯光')}
                    ${renderItemC2(res.data.yishi,'婚礼舞美')}
                </div>
                `;
            }
            if(res.data.buyer_notes){
                htmlStr+=`
                <div class="item-gap"></div>
                <div class="item-p">
                    <div class="title-p">补充说明</div>
                    ${renderItemC2(res.data.yingbin,'购买须知')}
                </div>
                `;
            }
            return htmlStr
        }


        function renderItemC1(des,level,title) {
            return `<div class="item-c">
                        <div class="title-c">${title}</div>
                        <div class="content-c">${level}${title}${des?`,${des}`:''}</div>
                    </div>`
        }


        function renderItemC2(data,title) {
            if(data){
                return `<div class="item-c">
                        <div class="title-c">${title}</div>
                        <div class="content-c">${data}</div>
                    </div>`
            }
            else{
                return ``;
            }
        }
    }

    function renderServiceContent(res){
        res.data.forEach(item=>{
            $("#tab-3-content").append(`
                 <div data-url="/app?item_id=${'2016102820076004000005945036'}&shop_id=${hb.location.url('?shop_id')||''}&merchant_pid=${hb.location.url('?merchant_pid')||''}" class="like-figures">
                    <img src="${item.cover}@1e_1c_0o_0l_180h_320w" alt="" class="like-images">
                    <div class="like-subject">${item.subject}</div>
                    <span class="like-price">${item.price}</span>
                    <s class="like-before-price">${item.original_price}</s>
                  </div>
            `);
        });

        $("#tab-3-content .like-figures").on("click",function () {
            // console.log(111)
            history.pushState({},'',$(this).data("url"));
            // console.log(history.state)
            destroyDetail();
            init();
        });
    }


    function destroyDetail(){
        $("#root").empty();
        unLinstenUrl();
    }

    function linstenUrl(){
        $(window).on('popstate',linstenUrlRender);
    }

    function unLinstenUrl(){
        $(window).off('popstate',linstenUrlRender);
    }

    function linstenUrlRender() {
        // console.log(111)

        destroyDetail();
        init();
    }


    return {
        init:init,
    };
}());


