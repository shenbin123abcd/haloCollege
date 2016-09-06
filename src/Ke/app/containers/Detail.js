import bgImg from '../images/detail-bg.png'
import contentImg from '../images/content-img.png'
import BottomBtn from '../components/detail.bottom'
import fetchCourseDetailIfNeeded from '../actions/detail'
let Link=ReactRouter.Link

var Detail= React.createClass({

  componentDidMount() {
      document.title='幻熊课堂详情';
     const { dispatch } = this.props
     dispatch(fetchCourseDetailIfNeeded(22))
  },

  render() {
    let {data,isFetching,dipatch}=this.props;
    function renderDetailPage(){
        if(!data){
            var isNull=true
        }else if(data.length===0){
            var isEmpty =true
        }

        if (isFetching||isNull) {
            return <div>loading</div>
        }else if(isEmpty){
            return <div>no data</div>
        }else{
            let fetchData=data.data
            //console.log(fetchData);
            let staticData={
                price:'¥2000/人',
                position:'上海总部',
                date:'2016.09.27',
                desc:'两天课时',
                num:2,
                interview:[
                    {
                        desc:'2016幻熊婚礼人专访',
                        cover:bgImg,
                    },
                    {
                        desc:'2016幻熊婚礼人专访',
                        cover:bgImg,
                    },
                    {
                        desc:'2016幻熊婚礼人专访',
                        cover:bgImg,
                    }
                ],
            }
            const classData={
                price:staticData.price,
                position:staticData.position,
                date:staticData.date,
                desc:staticData.desc,
            };
            return(
                <div className="detail-page">
                    <DetailTop></DetailTop>
                    <DetailMiddle></DetailMiddle>
                    <ClassDesc classData={classData}></ClassDesc>
                    <TeacherDesc></TeacherDesc>
                    <DetailContent></DetailContent>
                    <InterviewBlock interviewData={staticData.interview}></InterviewBlock>
                    <div className="bg-gap"></div>
                    <BottomBtn priceData={staticData.price} numData={staticData.num}></BottomBtn>
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
    return(
      <div className="detail-top">
        <div className="detail-bg">
          <img  className='bg-img' src={bgImg} alt=""/>
          <div className="sign-block">
            <div className="sign-person f-9">西米 报名了！</div>
          </div>
          <div className="teacher-desc">
            <div className="name f-17">蔡上</div>
            <div className="position f-13">蔡上作品工作室 创始人</div>
          </div>
        </div>

        <div className="detail-desc">
          <div className="desc-content f-15">蔡上丨约见蔡上，走进婚礼人的世界</div>
          <div className="desc-tag f-10">
            培训营
          </div>
        </div>

      </div>
    )
  }
})

var DetailMiddle=React.createClass({
  render(){
    return (
      <Link to="/seatinfo/10" className="deatil-middle">
        <div className="sign-num-block clearfix">
          <div className="sign-num-block-left f-14"><span className="haloIcon haloIcon-user f-20"></span>已报名28人</div>
          <div className="sign-num-block-right f-14">名额仅剩14个<i className="haloIcon haloIcon-right"></i></div>
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
        return(
            <div className="teacher-desc-block">
                <div className="desc-title f-13">
                    <span className="line"></span>讲师介绍
                </div>
                <div className="desc-content">
                    <div className="content f-13" id="desc-content">
                        宴会设计师/高端婚礼统筹师/蔡上作品工作室创始人
                        蔡上是婚庆行业知名主持人兼统筹，业内人士只要谈到婚庆
                        主持与策划，总会不时提到他的名字。蔡上的主持费用是全
                        宴会设计师/高端婚礼统筹师/蔡上作品工作室创始人
                        蔡上是婚庆行业知名主持人兼统筹，业内人士只要谈到婚庆
                        主持与策划，总会不时提到他的名字。蔡上的主持费用是全
                    </div>
                    <div className="btn see-more-btn" id="see-more-btn">查看更多<i className="haloIcon haloIcon-arrowdown"></i></div>
                </div>
            </div>
        )
    }
})

var DetailContent=React.createClass({
  render(){
    return (
        <div className="content-block">
            <div className="desc-title f-13">
                <span className="line"></span>课程介绍
            </div>
            <div className="desc-content">
                <img className="desc-content-img" src={contentImg} alt=""/>
                <img className="desc-content-img" src={contentImg} alt=""/>
                <div className="desc-cintent-text">宴会设计师/高端婚礼统筹师/蔡上作品工作室创始人
                    蔡上是婚庆行业知名主持人兼统筹，业内人士只要谈到婚庆
                    主持与策划，总会不时提到他的名字。蔡上的主持费用是全</div>
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
                                <div className="list-block-item" key={i}>
                                    <img className="item-cover" src={n.cover} alt=""/>
                                    <div className="item-desc f-10">{n.desc}</div>
                                </div>
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






