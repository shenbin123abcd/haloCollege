import bgUser from '../images/bg-user.png'
import userNoData from '../images/user-no-data.png'
import {fetchUserItemsIfNeeded,showOpenClass,showTrainingCamps,receiveUserPosts} from '../actions/user'
import PageLoading  from '../components/Common.Pageloading'


var User= React.createClass({
  componentDidMount() {
     document.title='我的个人中心';
     let { dispatch,data} = this.props;
     dispatch(fetchUserItemsIfNeeded(22));
  },
  handleClick(e){
    const {dispatch , data }=this.props;
    let type=$(e.target).data('type');
    $(".top-tab .tab-item").removeClass('active');
    $(e.target).addClass('active');
    if(type=='SHOW_OPEN'){
        dispatch(receiveUserPosts('SHOW_OPEN'))
    }else{
        dispatch(receiveUserPosts('SHOW_TRAINING_CAMP'))
    }
  },

  render() {
    let {isFetching,list,user,monthList,location}=this.props;
    var _this=this;
    function renderUserPage(){
        if(!list){
            var isNull=true
        }else if(list.length===0){
            var isEmpty =true
        }

        if (isFetching||isNull) {
            return <PageLoading/>
        }else if(isEmpty){
            return (
                <div className="height-wrapper">
                    <Header data={user} handleClick={_this.handleClick}></Header>
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
                    <Header data={user} handleClick={_this.handleClick}></Header>
                    <UserList data={list}></UserList>
                </div>
            )
        }
    }
    return(
        <div className="user-page" >
            {renderUserPage()}
        </div>
    )
  }
})

const Header=(data)=>{
    let user=data.data;
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
                <div className="top-tab">
                    <div className="tab-item f-15 active" data-type='SHOW_OPEN' onClick={data.handleClick}><i className="haloIcon haloIcon-open f-20"></i>公开课</div>
                    <div className="tab-tip"></div>
                    <div className="tab-item f-15" data-type='SHOW_TRAINING_CAMP' onClick={data.handleClick}><i className="haloIcon haloIcon-training f-20"></i>培训营</div>
                </div>
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
                        if(n.start_day>0){
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
                            let seatRow=n.seat_no.split(',')[0];
                            let seatLine=n.seat_no.split(',')[1];
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
    switch(filter){
        case "SHOW_ALL":
            return data
        case 'SHOW_OPEN':
            return data.filter(n=>n.cate=='公开课')
        case 'SHOW_TRAINING_CAMP':
            return data.filter(n=>n.cate=='培训营')
    }
}

function mapStateToProps(state) {
    const { userItems} = state
    const {data}=userItems;
    return {
        list:showFilter(state.userItems.list,state.userItems.filter),
        user:state.userItems.user,
        isFetching:state.userItems.isFetching,
    }

}

export default ReactRedux.connect(mapStateToProps)(User)






