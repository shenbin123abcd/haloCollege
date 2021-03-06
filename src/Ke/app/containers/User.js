import bgUser from '../images/bg-user.jpg'
import userNoData from '../images/user-no-data.png'
import {fetchUserItemsIfNeeded,showOpenClass,showTrainingCamps,receiveUserPosts} from '../actions/user'
import PageLoading  from '../components/Common.Pageloading'

var browserHistory=ReactRouter.browserHistory;
var CSSTransitionGroup = React.addons.CSSTransitionGroup;
var withRouter=ReactRouter.withRouter;
var User= React.createClass({
    unHandlerUrlChange(){

    },
    handlerUrlChange(ev){
        const { dispatch } = this.props;
        //console.log(ev);
        if(ev.pathname=='/course/user'){
            if(ev.query.cate_id==1){
                dispatch(receiveUserPosts('SHOW_OPEN'))
            }else{
                dispatch(receiveUserPosts('SHOW_TRAINING_CAMP'))
            }
        }
    },
    componentWillMount(){
        if(!Modernizr.weixin){

        }
    },
  componentDidMount() {
      document.title='我的个人中心';
      if(Modernizr.weixin&&Modernizr.ios){
          hb.hack.setTitle(document.title);
      }
      app.wechat.init();
      let { dispatch} = this.props;
      dispatch(fetchUserItemsIfNeeded());
      this.unHandlerUrlChange=this.props.router.listen(this.handlerUrlChange);

  },
  componentWillReceiveProps(nextProps){
      let {filter}=this.props;
      if(filter!=nextProps.filter){
          return true;
      }
  },
  handleClick(type){
    const {dispatch , data }=this.props;
    if(type=='SHOW_OPEN'){
        browserHistory.push(`/course/user?cate_id=1`);
        dispatch(receiveUserPosts('SHOW_OPEN'))
    }else{
        browserHistory.push(`/course/user?cate_id=2`);
        dispatch(receiveUserPosts('SHOW_TRAINING_CAMP'))
    }
  },
    componentWillUnmount(nextProps){
        this.unHandlerUrlChange()
    },

  render() {
    let {isFetching,list,user,monthList,location,filter}=this.props;
    var _this=this;
    function renderUserPage(){
        if(!user && !list){
            var isNull=true
        }else if(list.length===0){
            var isEmpty =true
        }
        if(!Modernizr.weixin){
            return (
                <div>
                    <div className="weui-msg">
                        <div className="weui-msg__icon-area">
                            <i className="weui-icon-info weui-icon_msg"></i>
                        </div>
                        <div className="weui-msg__text-area">
                            <h4 className="weui-msg_title">请在微信客户端打开链接</h4>
                        </div>
                    </div>
                </div>
            )
        }
        if (isFetching||isNull) {
            return <PageLoading key={1}/>
        }else if(isEmpty){
            return (
                <div className="height-wrapper">
                    <Header data={user} handleClick={_this.handleClick} showActive={filter}></Header>
                    <div className='content-list no-data-block'>
                        <div className="wrapper">
                            <img src={userNoData} alt=""/>
                        </div>
                    </div>
                </div>
            )
        }else{
            return(
                <div className="height-wrapper">
                    <Header data={user} handleClick={_this.handleClick} showActive={filter}></Header>
                    <UserList data={list}></UserList>
                </div>
            )
        }
    }
    return(
        <div className="user-page" >
            <CSSTransitionGroup  transitionName="transition" component="div" transitionEnterTimeout={300} transitionLeaveTimeout={10}>
            {renderUserPage()}
            </CSSTransitionGroup>
        </div>
    )
  }
})

const Header=(data)=>{
    let user=data.data;
    let active=data.showActive;
    function showActive(){
        if(active && active=='SHOW_OPEN'){
            return(
                <div className="top-tab">
                    <div className="tab-item f-15 active" onClick={e=>data.handleClick('SHOW_OPEN')}><span className="haloIcon haloIcon-open f-20" ></span>公开课</div>
                    <div className="tab-tip"></div>
                    <div className="tab-item f-15" onClick={e=>data.handleClick('SHOW_TRAINING_CAMP')}><span className="haloIcon haloIcon-training f-20" ></span>培训营</div>
                </div>
            )
        }else if(active && active=='SHOW_TRAINING_CAMP'){
            return(
                <div className="top-tab">
                    <div className="tab-item f-15" data-type='SHOW_OPEN' onClick={e=>data.handleClick('SHOW_OPEN')}><span className="haloIcon haloIcon-open f-20"></span>公开课</div>
                    <div className="tab-tip"></div>
                    <div className="tab-item f-15 active" data-type='SHOW_TRAINING_CAMP' onClick={e=>data.handleClick('SHOW_TRAINING_CAMP')}><span className="haloIcon haloIcon-training f-20"></span>培训营</div>
                </div>
            )
        }
    }
    return(
        <div className="user-page-top">
            <img src={bgUser} alt=""/>
            <div className="top-content">
                <div className="avatar">
                    <img src={user.avatar} alt=""/>
                </div>
                <div className="content f-14">
                    {user.username}
                </div>
            </div>
            <div className="tab-wrapper">
                {showActive()}
            </div>
        </div>
    )
}

const UserList=(data)=>{
    let list=data.data;
    const year=new Date().getFullYear();
   return(
        <div className="content-list">
            {
                data.data.map((n,i)=>{
                    function checkIfEnd(){
                        if(n.start_day && n.start_day>0){
                            return (
                                <div className="content-isend f-10">
                                    距离开课还有{n.start_day}天
                                </div>
                            )
                        }else{
                            return (
                                <div className="content-isend f-10">
                                    已结束
                                </div>
                            )
                        }
                    }
                    function chooseSeat(){
                        if(n.seat_no){
                            let addZero=(num)=>{
                                if(num<10){
                                    return ('0'+num)
                                }else{
                                    return num
                                }
                            };
                            let seatRow=addZero(n.seat_no.split(',')[0]);
                            let seatLine=addZero(n.seat_no.split(',')[1]);
                            return (seatRow+'排'+seatLine+'座');
                        }else{
                            return ('尚未选座');
                        }
                    }
                    return(
                        <div className="content-item" key={i}>
                            <div className="avatar">
                                <img src={n.avatar_url} alt=""/>
                            </div>
                            <div className="gap"></div>
                            <div className="content">
                                <div className="content-desc f-14">{n.title}</div>
                                <div className="content-info f-12">{n.start_date}  {n.place}  {n.day}天</div>
                                <div className="content-bottom clearfix">
                                    <div className="content-seat f-12">
                                        {chooseSeat()}
                                    </div>
                                    {checkIfEnd()}
                                </div>
                            </div>
                        </div>
                    )
                })
            }
        </div>
    )
}

const showFilter=(data,filter)=>{
    if(data!=undefined){
        switch(filter){
            case "SHOW_ALL":
                return data
            case 'SHOW_OPEN':
                return data.filter(n=>n.cate_id==1)
            case 'SHOW_TRAINING_CAMP':
                return data.filter(n=>n.cate_id==2)
        }
    }
}

function mapStateToProps(state) {
    const { userItems} = state;
    const {data}=userItems;
    return {
        list:showFilter(state.userItems.list,state.userItems.filter),
        user:state.userItems.user,
        isFetching:state.userItems.isFetching,
        filter:state.userItems.filter,
    }

}

export default ReactRedux.connect(mapStateToProps)(withRouter(User))






