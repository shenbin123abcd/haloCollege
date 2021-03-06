import CommonHeader from './Common.header'
import CommonArticle from './Common.article'
import CommonVideos from './Common.videos'
import CommonDownload from './Common.download'
import {fetchGuestDetailIfNeeded,destroyGuestDetailData} from '../actions/guest.detail'
let Link=ReactRouter.Link;
class Detail extends React.Component{
    constructor(props) {
        super(props);
        this.isWechatInit=false
    }
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchGuestDetailIfNeeded({
            guest_id:routeParams.id
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }
    componentDidUpdate(){
        var profile = this.refs.profile;
        var hh = $(profile).outerHeight();
        var moreInfo = this.refs.moreInfo;
        var moreBtn = this.refs.moreBtn;
        // console.log(hh)
        if(hh<55){
            $(moreInfo).hide();
            $(profile).css({
                "display":"block",
                "height":hh,
            });
        }else{
            $(profile).css("display","-webkit-box");
            var hh2 = $(profile).outerHeight();
            $(profile).css({
                "height":hh2,
            });
            $(moreInfo).show();
            $(moreInfo).on("click",function () {
                if($(moreInfo).text()=="查看更多"){
                    $(moreInfo).text("收起");
                    $(profile).css({
                        "display":"block",
                        "height":hh,
                    });
                }else if($(moreInfo).text()=="收起"){
                    // var hh2 = $(profile).outerHeight();
                    $(moreInfo).text("查看更多");
                    $(profile).css({
                        "display":"-webkit-box",
                        "height":hh2,
                    });
                }
            });
        }
    }
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(destroyGuestDetailData());
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
                // console.log(this.isWechatInit)
                // console.log(guestDetailData.data.guest.title,guestDetailData.data.guest.content,guestDetailData.data.guest.avatar_url)
                if(!this.isWechatInit){
                    app.wechat.init({
                        title: `${guestDetailData.data.guest.title}`,
                        content: `${guestDetailData.data.guest.content}`,
                        logo : `${guestDetailData.data.guest.avatar_url}?imageView2/1/w/200/h/200`,
                        link : window.location.href,
                    });
                    this.isWechatInit=true;
                }

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
                                <div className="guest-myprofile" ref="profile">个人简介：{`${guestDetailData.data.guest.content}`}</div>
                                <div className="guest-more" ref="moreInfo">
                                    <div className="guest-more-word" ref="moreBtn">查看更多</div>
                                    {/*<div className="guest-more-arrow">*/}
                                        {/*<svg className="haloIcon" aria-hidden="true">*/}
                                            {/*<use xlinkHref="#haloIcon-arrow"></use>*/}
                                        {/*</svg>*/}
                                    {/*</div>*/}
                                </div>
                            </div>
                        </div>
                        { this.renderCompany()}
                        <CommonArticle articles={guestDetailData.data.articles} id={guestDetailData.data.guest.id} type="1"></CommonArticle>
                        <CommonVideos videos={guestDetailData.data.videos} id={guestDetailData.data.guest.id} type="1"></CommonVideos>
                        <CommonDownload></CommonDownload>
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


