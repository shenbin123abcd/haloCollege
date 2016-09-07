import bgUser from '../images/bg-user.png'
import contentImg from '../images/content-img.png'
import {fetchUserItemsIfNeeded,showOpenClass,showTrainingCamps,receiveUserPosts} from '../actions/user'


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
    let {data,isFetching,dispatch,userClass}=this.props;
    var _this=this;
    function renderUserPage(){
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
            return(
                <div>
                    <Header data={data} handleClick={_this.handleClick}></Header>
                    <UserList data={data}></UserList>
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
    return(
        <div className="user-page-top">
            <img src={bgUser} alt=""/>
            <div className="top-content">
                <div className="avatar">
                    <img src={contentImg} alt=""/>
                </div>
                <div className="content f-14">
                    成都锦玉喜堂Amy
                </div>
            </div>
            <div className="tab-wrapper">
                <div className="top-tab">
                    <div className="tab-item f-15 active" data-type='SHOW_OPEN' onClick={data.handleClick}><i className="haloIcon haloIcon-"></i>公开课</div>
                    <div className="tab-tip"></div>
                    <div className="tab-item f-15" data-type='SHOW_TRAINING_CAMP' onClick={data.handleClick}><i className="haloIcon haloIcon-"></i>培训营</div>
                </div>
            </div>
        </div>
    )
}

const UserList=(data)=>{
   //console.log(data.data)
   return(
        <div className="content-list">
            {
                data.data.map((n,i)=>{
                    return(
                        <div className="content-item" key={i}>
                            <div className="avatar">
                                <img src={contentImg} alt=""/>
                            </div>
                            <div className="gap"></div>
                            <div className="content">
                                <div className="content-desc f-14">蔡上丨约见蔡上，走进婚礼人的世界</div>
                                <div className="content-info f-12">2016.09.16  幻熊上海总部  1天</div>
                                <div className="content-bottom clearfix">
                                    <div className="content-seat f-12">
                                        3排2座
                                    </div>
                                    <div className="content-isend f-10">
                                        已结束
                                    </div>
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
            return data.filter(n=>n.type=='public')
        case 'SHOW_TRAINING_CAMP':
            return data.filter(n=>n.type=='peixun')
    }
}

function mapStateToProps(state) {
    const { userItems} = state
    const {data}=userItems;
    return {
        data:showFilter(state.userItems.data,state.userItems.filter),
    }

}

export default ReactRedux.connect(mapStateToProps)(User)






