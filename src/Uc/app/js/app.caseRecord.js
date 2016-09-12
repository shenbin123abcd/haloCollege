;app.caseRecord=(function(){
    "use strict";
    function init(){
        renderList();
    }

    function renderList() {
        var appData=window.appData;
        appData.recordList=appData.recordList||[];
        // appData.recordList=[];
        var htmlStr=``;
        var listStr=``;
        if(appData.recordList.length>0){
            appData.recordList.forEach((n,i)=>{
                let priceArr=n.price.split('.');
                n.price_int=priceArr[0];
                n.price_decimal=priceArr[1];




                let typeHtmlStr=``;
                if(!n.type){
                    typeHtmlStr=''
                }else{
                    let typeArr=n.type.split(',');
                    typeArr.forEach(function(n2,i2){
                        typeHtmlStr+=`
                    <div class="version">
                        ${n2}
                    </div>`
                    })
                }


                let deliverTimeHtmlStr=``;
                if(!n.send_date){
                    deliverTimeHtmlStr=''
                }else{
                    deliverTimeHtmlStr=`<div class="deliver-time">预计发货：${n.send_date}</div>`;
                }



                let subtitleHtmlStr=``;
                if(n.module=='case'){
                    subtitleHtmlStr=`<div class="subtitle">${n.goods_subtitle}</div>`
                }else{
                    subtitleHtmlStr=`<div class="subtitle">数量 × ${n.num}</div>`;
                }




                listStr+=`
                <div class="item-box">
                    <div class="des">
                        <div class="order">订单编号：${n.order_no}</div>
                        ${deliverTimeHtmlStr}
                    </div>
                    <a href="${n.goods_url}" class="info">
                        <div class="img-box">
                            <img class="img" src="${n.goods_cover}?imageView2/1/w/290/h/200" alt="" class="img">
                        </div>
                        <div class="property-box">
                            <div class="name">${n.goods_name}</div>
                            <div class="subtitle">
                                ${subtitleHtmlStr}
                            </div>
                            <div class="version-box cf">
                                ${typeHtmlStr}
                            </div>
                            <div class="price"><span class=t">¥</span><span class="em">${n.price_int}.</span><span class="t">${n.price_decimal}</span></div>
                        </div>
                    </a>
                </div>
            `;
            });

            $("#list-wrapper").append(listStr);
        }else{

            let emptyStr=`
                <div class="no-data-box">您还未购买婚礼策划样品哦,<br>请到五楼VR体验区参观自助购买哦。</div> 
            `;

            $("#list-wrapper").append(emptyStr);
        }
        
    }
    


    return{
        init:init,
    }
}());


