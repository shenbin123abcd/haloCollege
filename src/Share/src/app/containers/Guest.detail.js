import CommonHeader from './Common.header'
import CommonArticle from './Common.article'
import CommonVideos from './Common.videos'
import {fetchGuestDetailIfNeeded} from '../actions/guest.detail'
let Link=ReactRouter.Link;
class Detail extends React.Component{
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchGuestDetailIfNeeded({
            guest_id:routeParams.id
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;


    }
    renderCompany(){
        const { dispatch,location,guestDetailData } = this.props;
        if(guestDetailData.data.company){
            return(
                <Link to={`/share/companies/company/${guestDetailData.data.company.id}`} className="guest-company-wrapper">
                    <svg className="haloIcon haloIcon-company" aria-hidden="true">
                        <use xlinkHref="#haloIcon-company"></use>
                    </svg>
                    <div className="company-name">
                        {`${guestDetailData.data.company.name}`}
                    </div>
                    <div className="company-more">
                        <svg className="haloIcon " aria-hidden="true">
                            <use xlinkHref="#haloIcon-arrow2"></use>
                        </svg>
                    </div>
                </Link>
            )
        }
    }
    render() {
            const { dispatch,location,guestDetailData } = this.props;
            // console.log(guestDetailData);
            if(guestDetailData.isFetching){
                return (
                    <div>
                        <loading className="."></loading>
                    </div>
                )
            }else if(guestDetailData.data){
                return (
                    <div>
                        <CommonHeader title="个人主页" ></CommonHeader>
                        <div className="guest-desc-wrapper">
                            <div className="guest-show">
                                <div className="show-left-box">
                                    <img src={`${guestDetailData.data.guest.avatar_url}?imageView2/1/w/150/h/150`} alt="" className="show-left"/>
                                </div>
                                <div className="show-right">
                                    <div className="guess-title">{`${guestDetailData.data.guest.title}`}</div>
                                    <div className="guess-position">{`${guestDetailData.data.guest.position}`}</div>
                                </div>
                            </div>
                            <div className="myLine"></div>
                            <div className="guest-profile">
                                <div className="guest-myprofile">个人简介：{`${guestDetailData.data.guest.content}`}</div>
                                <div className="guest-more">
                                    <div className="guest-more-word">查看更多</div>
                                    <div className="guest-more-arrow">
                                        <svg className="haloIcon" aria-hidden="true">
                                            <use xlinkHref="#haloIcon-arrow"></use>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        { this.renderCompany()}
                        <CommonArticle articles={guestDetailData.data.articles} id={guestDetailData.data.guest.id} type="1"></CommonArticle>
                        <CommonVideos videos={guestDetailData.data.videos} id={guestDetailData.data.guest.id} type="1"></CommonVideos>
                    </div>
                )
            }else{
                return null
            }
    }
}

function mapStateToProps(state) {
    const {guestDetailData}=state;
    return {
        guestDetailData,
    }
}

export default ReactRedux.connect(mapStateToProps)(Detail)


