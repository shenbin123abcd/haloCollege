import bgUser from '../images/bg-user.png'
import contentImg from '../images/content-img.png'
import fetchUserItemsIfNeeded from '../actions/user'


var User= React.createClass({

  componentDidMount() {
     document.title='我的个人中心';
     const { dispatch } = this.props
     dispatch(fetchUserItemsIfNeeded(22))
  },

  render() {
    let {data,isFetching,dipatch}=this.props;
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
            let fetchData=data
            return(
                <div>
                    <Header data={data}></Header>
                    <UserList data={data}></UserList>
                </div>
            )
        }
    }
    return(
        <div className="user-page">
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
                    <div className="tab-item f-15 active"><i className="haloIcon haloIcon-"></i>公开课</div>
                    <div className="tab-tip"></div>
                    <div className="tab-item f-15"><i className="haloIcon haloIcon-"></i>培训营</div>
                </div>
            </div>
        </div>
    )
}

const UserList=(data)=>{
    console.log(data);
    function filterArr(arr){
        
    }
    return(
        <div className="content-list">
            asd
        </div>
    )
}


function mapStateToProps(state) {
    const { userItems } = state
    return userItems
}

export default ReactRedux.connect(mapStateToProps)(User)






