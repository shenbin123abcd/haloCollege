import playBtn from '../images/play-btn.png'
import BottomBtn from './Common.buttonGroup'
import PageLoading  from '../components/Common.Pageloading'
import {fetchCourseDetailIfNeeded} from '../actions/detail'
import {fetchCourseStatusIfNeeded} from '../actions/buttonGroup'

let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory

var Detail= React.createClass({
  componentDidMount() {
     document.title='幻熊课堂详情';
     const { dispatch,routeParams } = this.props
     dispatch(fetchCourseDetailIfNeeded(routeParams.id));
      dispatch(fetchCourseStatusIfNeeded(routeParams.id));
  },

  render() {
    let {data,isFetching,dipatch}=this.props;
    let _this=this;
    function renderDetailPage(){
        if(!data){
            var isNull=true
        }else if(data.length===0){
            var isEmpty =true
        }

        if (isFetching||isNull) {
            return <PageLoading />
        }else if(isEmpty){
            return <div>no data</div>
        }else{
            let fetchData=data.data;
            const classData={
                price:fetchData.price,
                place:fetchData.place,
                start_date:fetchData.start_date,
                day:fetchData.day,
            };
            return(
                <div className="detail-page">
                    <DetailTop topData={fetchData}></DetailTop>
                    <DetailMiddle middleData={fetchData}></DetailMiddle>
                    <ClassDesc classData={classData}></ClassDesc>
                    <TeacherDesc teacherData={fetchData.guest.content}></TeacherDesc>
                    <DetailContent contentData={fetchData}></DetailContent>
                    <InterviewBlock interviewData={fetchData.video}></InterviewBlock>
                    <div className="bg-gap"></div>
                    <BottomBtn priceData={fetchData.price} idData={fetchData.id}></BottomBtn>
                </div>
            )
        }
    }
    return(
        <div className="detail-page">
            {renderDetailPage()}
        </div>
    )
  }
})


var DetailTop=React.createClass({
  render(){
    const data=this.props.topData;
    let styleCss=()=>{
         let style='';
         if(data.cate=='公开课'){
             style='desc-tag f-10 open'
         }else{
             style='desc-tag f-10 training-camp'
         }
         return style
    }
    return(
      <div className="detail-top">
        <div className="detail-bg">
          <img  className='bg-img' src={`${data.cover_url}?imageView2/1/w/750/h/380`} alt=""/>
            {/*
             <div className="sign-block">
             <div className="sign-person f-9">西米 报名了！</div>
             </div>
            */}
          <div className="teacher-desc">
            <div className="name f-17">{data.guest.name}</div>
            <div className="position f-13">{data.guest.position}</div>
          </div>
        </div>

        <div className="detail-desc">
          <div className="desc-content f-15">{data.title}</div>
          <div className={styleCss()}>
              {data.cate}
          </div>
        </div>

      </div>
    )
  }
})

var DetailMiddle=React.createClass({
  render(){
    const data=this.props.middleData;
    return (
      <Link to={`/course/seatinfo/${data.id}`} className="deatil-middle">
        <div className="sign-num-block clearfix">
          <div className="sign-num-block-left f-14"><span className="haloIcon haloIcon-user f-20"></span>已报名{data.num}人</div>
          <div className="sign-num-block-right f-14">名额仅剩 {data.last_num}个<i className="haloIcon haloIcon-right"></i></div>
        </div>
      </Link>
    )
  }
})

var ClassDesc=React.createClass({
  render(){
      let descList=Object.values(this.props.classData);
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
      data[0].desc='￥'+data[0].desc+'/人';
      data[3].desc=data[3].desc+'天课时';

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
        if(contentHeight<=57){
            $("#desc-content").removeClass('text-hidden');
            $("#see-more-btn").hide();
            //$(".teacher-desc-block").css('padding-bottom',30);
        }else{
            $("#desc-content").addClass('text-hidden');
            $("#see-more-btn").show();
            $("#see-more-btn").on('click',function(e){
                e.preventDefault();
                $("#desc-content").animate({
                    height:contentHeight,
                },400);
                $("#desc-content").removeClass('text-hidden');
                $(".teacher-desc-block").css('padding-bottom',30);
                $("#see-more-btn").hide();
            })
        }
    },
    render(){
        const data=this.props.teacherData;
        return(
            <div className="teacher-desc-block">
                <div className="desc-title f-13">
                    <span className="line"></span>讲师介绍
                </div>
                <div className="desc-content">
                    <div className="content f-13" id="desc-content">
                        {data}
                    </div>
                    <div className="btn see-more-btn" id="see-more-btn">查看更多<i className="haloIcon haloIcon-arrowdown"></i></div>
                </div>
            </div>
        )
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
                <div className="desc-cintent-text" dangerouslySetInnerHTML={renderHtml()} />
            </div>
        </div>
    )
  }
})

var InterviewBlock=React.createClass({
    render(){
        const data=this.props.interviewData;
        return (
            <div className="interview-block">
                <div className="desc-title f-13">
                    <span className="line"></span>讲师专访
                </div>
                <div className="desc-list-block">
                    {
                        data.map((n,i)=>{
                            return(
                                <a className="list-block-item" key={i} href={`http://college.halobear.com/lectureDetail/${n.id}`}>
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
    }
})

function mapStateToProps(state) {
    const { courseDetail } = state
    return courseDetail

}

export default ReactRedux.connect(mapStateToProps)(Detail)





