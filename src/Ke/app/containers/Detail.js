import playBtn from '../images/play-btn.png'
import weixinPic from '../images/weixin.jpg'
import BottomBtn from './Common.buttonGroup'
import PageLoading  from '../components/Common.Pageloading'
import {fetchCourseDetailIfNeeded,initial,timeOutStart,timeOutOver} from '../actions/detail'
import {fetchCourseStatusIfNeeded} from '../actions/buttonGroup'
import { fetchCourseIfNeeded,setCurrentMonth ,resetMonth} from '../actions'

let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory;
var CSSTransitionGroup = React.addons.CSSTransitionGroup;

var Detail= React.createClass({
  componentDidMount() {
     // document.title='幻熊课堂详情';
     //  if(Modernizr.weixin&&Modernizr.ios){
     //      hb.hack.setTitle(document.title);
     //  }
     const { dispatch,routeParams } = this.props
     dispatch(fetchCourseDetailIfNeeded(routeParams.id));
     dispatch(fetchCourseStatusIfNeeded(routeParams.id));
     let val=hb.location.url('?code');
      if(val){
          hb.Cookies.set('agents',val);
      }
  },
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(initial())
        this.timer && clearTimeout(this.timer);
        dispatch(timeOutOver());
    },
    formatContent(str){
        return str.replace(/\r\n|\r/g,'')
    },
    handleStart(time){
        let {dispatch}=this.props;
        this.timer=setTimeout(()=>dispatch(timeOutStart(time)),1000);
    },
    isWechatInit:false,
  render() {
    let {data,isFetching,dispatch,res,d,h,m,s,start_time}=this.props;
    let renderDetailPage=()=>{
        if(!data){
            var isNull=true
        }else if(data.length===0){
            var isEmpty =true
        }
        if (isFetching||isNull) {
            return <PageLoading key={1}/>
        }else if(isEmpty){
            return <div>no data</div>
        }else if(data.data){
            let fetchData=data.data;
            //console.log(res)
            let val=hb.Cookies.get('agents');

            let link='';
            let code=hb.location.url('?code');
            if(val && !code){
                link=window.location.href+'?code='+val;
            }else{
                link=window.location.href;
            }

            dispatch(setCurrentMonth({
                year:data.data.month.substring(0,4),
                month:data.data.month.substring(4,6),
            }));

            if(!this.isWechatInit){
                app.wechat.init({
                    title: `${app.util.formatTitle(data.data.title)}`,
                    content: `时间：${data.data.start_date}，地点：${data.data.place}，讲师介绍：${data.data.guest.content}`,
                    link : link,
                });
                this.isWechatInit=true;
            }


            const classData={
                price:fetchData.price,
                place:fetchData.place,
                start_date:fetchData.start_date,
                day:fetchData.day,
            };

            //倒计时
            this.handleStart(start_time);

            return(
                <div className="detail-page">
                    <DetailTop
                        topData={fetchData}
                        d={d}
                        h={h}
                        m={m}
                        s={s}
                    >
                    </DetailTop>
                    <DetailMiddle middleData={fetchData}></DetailMiddle>
                    <TuiJianBlock data={fetchData} res={res}></TuiJianBlock>
                    <ClassDesc classData={classData} cateData={fetchData.cate}></ClassDesc>
                    <TeacherDesc teacherData={fetchData.guest.content} ifShow={fetchData.cate_id}></TeacherDesc>
                    <DetailContent contentData={fetchData}></DetailContent>
                    <InterviewBlock interviewData={fetchData.video}></InterviewBlock>
                    <WeixinBlock cateId={fetchData.cate_id}></WeixinBlock>
                    <BottomBtn
                        priceData={fetchData.price}
                        idData={fetchData.id}
                        cate_id={fetchData.cate_id}

                        d={d}
                        h={h}
                        m={m}
                        s={s}
                        start_time={start_time}
                        original_price={fetchData.original_price}
                    >
                    </BottomBtn>
                    <Tel data={fetchData.tel}></Tel>
                </div>
            )
        }
    }
    return(

        <div className="detail-page-wrapper">
            <CSSTransitionGroup  transitionName="transition" component="div" transitionEnterTimeout={300} transitionLeaveTimeout={10}>
            {renderDetailPage()}
            </CSSTransitionGroup>
        </div>

    )
  }
})


var DetailTop=React.createClass({
  render(){
    const data=this.props.topData;
    let d=this.props.d;
    let h=this.props.h;
    let s=this.props.s;

    let styleCss=()=>{
         let style='';
         if(data.cate_id==1){
             style='desc-tag f-10 open'
         }else if(data.cate_id==2){
             style='desc-tag f-10 training-camp'
         }else if(data.cate_id==3){
             style='desc-tag f-10 partner'
         }
         return style
    }

    let renderDescHtml=()=>{
        if(data.cate_id!=1){
            return(
                <div className="teacher-desc">
                    <div className="name f-17">{data.guest.name}</div>
                    <div className="position f-13">{data.guest.position}</div>
                </div>
            )
        }
    }

    let renderIsv=()=>{
        if(data.cate_id==3){
            return(
                <div className="isv">{data.isv_name}</div>
            )
        }else{
            return(
                <div></div>
            )
        }
    }

    let ifShowOrPrice=()=>{
        if(data.next_date && d!='no' && h!='no' && s!='no'){
            return(
                <div className="price">
                    <span className="icon">￥</span>{data.price}/人
                    <span style={{
                        color:'#aaaaaa',
                        fontSize:'.26rem',
                        marginLeft:'10px',
                        textDecoration:'line-through',
                    }}>
                        ￥{data.original_price}/人
                    </span>
                </div>
            )
        }else{
            return(
                <div className="price">
                    <span className="icon">￥</span>{data.original_price}/人
                </div>
            )
        }
    }

    let ifShowText=()=>{
        if(data.next_date && d!='no' && h!='no' && s!='no'){
            return(
                <div className="bottom-text" style={{color:'#666666',fontSize:'.22rem'}}>
                    08月26日恢复原价 ¥880/人
                </div>
            )
        }else{
            return(
                <div></div>
            )
        }
    }



    return(
      <div className="detail-top">
        <div className="detail-bg">
          <img  className='bg-img' src={`${data.cover_url}?imageView2/1/w/750/h/380`} alt=""/>
          <div className="img-over-layer"></div>
            {renderDescHtml()}
        </div>

        <div className="detail-desc">
            <div className="desc-top">
                <div className="desc-content f-15">{data.title}</div>
                <div className={styleCss()}>
                    {data.cate}
                </div>
            </div>
            <div className="desc-bottom">
                {ifShowOrPrice()}
                {renderIsv()}
            </div>
            {ifShowText()}
        </div>
      </div>
    )
  }
})

var DetailMiddle=React.createClass({
  render(){
    const data=this.props.middleData;
    return (
      <Link to={`/course/seatinfo_${data.id}`} className="deatil-middle">
        <div className="sign-num-block clearfix">
          <div className="sign-num-block-left f-14"><span className="haloIcon haloIcon-user f-20"></span>已报名{data.num}人</div>
          <div className="sign-num-block-right f-13">名额仅剩 {data.last_num}个<i className="haloIcon haloIcon-right"></i></div>
        </div>
      </Link>
    )
  }
})



var TuiJianBlock=React.createClass({
    render(){
        let cate_id=this.props.data.cate_id;
        let res=this.props.res;
        //console.log(cate_id);
        if(res.iRet==1 && cate_id!=3){
            return(
                <Link className="tel-block clearfix" to={`/course/branding`}>
                    <div className="sign-num-block clearfix">
                        <div className="tel-line"></div>
                        <div className="sign-num-block-left f-14">
                            <span className="tuijianren">
                                <span className="circle"><img src={res.data.headimgurl} alt=""/></span>
                            </span>
                            <span>{res.data.nickname}</span>
                        </div>
                        <div className='sign-num-block-right f-13 active'>
                            申请成为推荐人
                            <i className="haloIcon haloIcon-right"></i>
                        </div>
                    </div>
                </Link>
            )
        }else{
            return(
                <div></div>
            )
        }

    }
})

var ClassDesc=React.createClass({
  render(){
      let descList=Object.values(this.props.classData);
      let cate=this.props.cateData;
      let data=[
          {
              type:'item1',
              icon:'haloIcon haloIcon-tag',
              desc:'',
              iconClass:'desc-item-icon item1',
          },
          {
              type:'item2',
              icon:'haloIcon haloIcon-position',
              desc:'',
              iconClass:'desc-item-icon item2',
          },
          {
              type:'item3',
              icon:'haloIcon haloIcon-calender',
              desc:'',
              iconClass:'desc-item-icon item3',
          },
          {
              type:'item4',
              icon:'haloIcon haloIcon-time',
              desc:'',
              iconClass:'desc-item-icon item4',
          }
      ];
      data.forEach(function(n,i){
          n.desc=descList[i];
      });
      //data[2].desc='11月01-12月02日';

      data[0].desc='￥'+data[0].desc+'/人';
      if(cate=='公开课'){
          data[3].desc=data[3].desc+'天课时';
      }else{
          data[3].desc=data[3].desc+'天课时';
      }


    return (
      <div className="class-desc-block">
          {data.map((n,i)=>{
          return(
              <div className="class-desc-item" key={i}>
                <div className="item-icon-wrapper">
                    <div className="border-right"></div>
                    <div className={n.iconClass}>
                        <i className={n.icon}></i>
                    </div>
                </div>
                <div className="desc-item-content f-11">
                  <div className={n.type}>{n.desc}</div>
                </div>
              </div>
          )
          })}
      </div>
    )
  }
})

var TeacherDesc=React.createClass({
    componentDidMount(){
        let contentHeight=$('#desc-content').outerHeight();
        let scrollHeight=''
        if(contentHeight<=56){
            $("#desc-content").removeClass('text-hidden');
            $("#see-more-btn").hide();
        }else{
            $("#desc-content").addClass('text-hidden');
            $("#see-more-btn").show();

            $("#see-more-btn").on('click',function(e){
                e.preventDefault();
                scrollHeight=$(window).scrollTop();
                $("#desc-content").animate({
                    height:contentHeight,
                },400);
                $("#desc-content").removeClass('text-hidden');
                $("#see-more-btn").hide();
                $("#slide-up-btn").show();
            })

            $("#slide-up-btn").on('click',function(e){
                e.preventDefault();
                $("#desc-content").animate({
                    height:56,
                },400,function(){
                    $(window).scrollTop(scrollHeight);
                });
                $("#desc-content").addClass('text-hidden');
                $("#see-more-btn").show();
                $("#slide-up-btn").hide();
            })
        }
    },
    render(){
        let data=this.props.teacherData;
        let ifShow=this.props.ifShow;
        //data='撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算打算撒打算撒打算撒打算撒打算撒打算撒打算打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒打算撒撒打算撒打算撒打算撒打算撒打算撒撒打算撒打算撒打算撒打算撒打算撒撒打算撒打算撒打算撒打算撒打算撒撒打算撒打算撒打算撒打算撒打算撒撒打算撒打算'
        if(ifShow!=1){
            data=data.split('\r');
            return(
                <div className="teacher-desc-block">
                    <div className="desc-title f-13">
                        <span className="line"></span>讲师介绍
                    </div>
                    <div className="desc-content">
                        <div className="content f-13" id="desc-content">
                            {
                                data.map((n,i)=>{
                                    return(
                                        <p className="desc-style" key={i}>{n}</p>
                                    )
                                })
                            }
                        </div>
                        <div className="btn btn-link see-more-btn" id="see-more-btn">查看更多<i className="haloIcon haloIcon-arrowdown"></i></div>
                        <div className="btn btn-link see-more-btn arrow-up" id="slide-up-btn">收起介绍<i className="haloIcon haloIcon-arrowup"></i></div>
                    </div>
                </div>
            )
        }else{
            return false
        }

    }
})

var DetailContent=React.createClass({
  render(){
    const data=this.props.contentData;
    let renderHtml=()=>{
        return {__html:data.content}
    }
    return (
        <div className="content-block">
            <div className="desc-title f-13">
                <span className="line"></span>课程介绍
            </div>
            <div className="desc-content">
                <div className="desc-content-text" dangerouslySetInnerHTML={renderHtml()} />
            </div>
        </div>
    )
  }
})

var InterviewBlock=React.createClass({
    render(){
        let data=this.props.interviewData;
        if(data.length){
            return (
                <div className="interview-block">
                    <div className="desc-title f-13">
                        <span className="line"></span>讲师专访
                    </div>
                    <div className="desc-list-block">
                        {
                            data.map((n,i)=>{
                                return(
                                    <a className="list-block-item" key={i} href={`http://college-api.halobear.com/video/detail?id=${n.id}`}>
                                        <div className="item-cover-wrapper">
                                            <img className="item-cover" src={`${n.cover_url}?imageView2/1/w/220/h/144`} alt=""/>
                                            <img className='play-btn' src={playBtn} alt=""/>
                                        </div>
                                        <div className="item-desc f-10">{n.title}</div>
                                    </a>
                                )
                            })
                        }
                    </div>
                </div>
            )
        }else{
            return false;
        }
    }
})

var WeixinBlock=React.createClass({
  render(){
    let cateId=this.props.cateId;

    let renderWx=()=>{
      if(cateId!=1){
        return(
          <div className="bg-gap">
            <div className="weixin-block">
                <div className="wrapper">
                    <img src={weixinPic} alt=""/>
                </div>
                <div className="text">长按二维码添加「 幻熊研习社 」客服微信号<br/>咨询课程报名相关事项</div>
                {/*<div className="consult">
                    <Link to={`/course/consult`}><span>报名须知 》</span></Link>
                </div>*/}
            </div>
          </div>
        )
      }
    }

    return(
      <div>
        {renderWx()}
      </div>
    )
  }  
});

var Tel=React.createClass({
    render(){
        let tel=this.props.data;
        return(
            <a className="tel-fixed" href={`tel:${tel}`}>
                <span className="haloIcon haloIcon-kefu"></span>
            </a>
        )
    }
})

function mapStateToProps(state) {
    const { courseDetail } = state
    return courseDetail

}

export default ReactRedux.connect(mapStateToProps)(Detail)






