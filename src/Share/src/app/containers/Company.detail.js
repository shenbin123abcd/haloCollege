import CommonHeader from './Common.header'
import CommonArticle from './Common.article'
import CommonVideos from './Common.videos'
import CommonDownload from './Common.download'
import {fetchCompanyDetailIfNeeded,destroyCompanyDetailData} from '../actions/company.detail'
class Company extends React.Component{
    constructor(props) {
        super(props);
        this.isWechatInit=false
    }
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchCompanyDetailIfNeeded({
            company_id:routeParams.id
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
        if(hh<40){
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
        dispatch(destroyCompanyDetailData());
    }
    render(){
        const { dispatch,location,companyDetailData } = this.props;
        // console.log(companyDetailData);
        if(companyDetailData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(companyDetailData.data){

            if(!this.isWechatInit){
                app.wechat.init({
                    title: `${companyDetailData.data.company.name}`,
                    content: `${companyDetailData.data.company.description}`,
                    logo : `${companyDetailData.data.company.logo}?imageView2/1/w/200/h/200`,
                    link : window.location.href,
                });
                this.isWechatInit=true;
            }

            return(
                <div>
                    <CommonHeader title="公司主页" ></CommonHeader>
                    <div className="company-desc-wrapper">
                        <div className="company-show">
                            <div className="show-left-box">
                                <img src={`${companyDetailData.data.company.logo}?imageView2/1/w/150/h/150`} alt="" className="show-left"/>
                            </div>
                            <div className="show-right">
                                <div className="company-title">{`${companyDetailData.data.company.name}`}</div>
                            </div>
                            <div className="myLine"></div>
                            <div className="company-profile">
                                <div className="company-myprofile" ref="profile">公司简介：{`${companyDetailData.data.company.description}`}</div>
                                <div className="company-more" ref="moreInfo">
                                    <div className="company-more-word" ref="moreBtn">查看更多</div>
                                    {/*<div className="company-more-arrow">*/}
                                        {/*<svg className="haloIcon" aria-hidden="true">*/}
                                            {/*<use xlinkHref="#haloIcon-arrow"></use>*/}
                                        {/*</svg>*/}
                                    {/*</div>*/}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="company-member-wrapper">
                        <div className="company-chengyuan">成员</div>
                        <div className="member-list-box">
                            {
                                companyDetailData.data.members.map((n,i)=>{
                                    return (
                                        <div className={`member-list ${companyDetailData.data.members.length==(i+1)?'member-list-no':''}`} key={n.id}>
                                            <div className="member-images-box">
                                                <img src={`${n.avatar_url}?imageView2/1/w/86/h/86`} alt="" className="member-images"/>
                                            </div>
                                            <div className="member-name">{n.title}</div>
                                            <div className="member-position">{n.position}</div>
                                        </div>
                                    )
                                })
                            }
                        </div>
                    </div>
                    <CommonArticle articles={companyDetailData.data.articles} id={companyDetailData.data.company.id} type="2"></CommonArticle>
                    <CommonVideos videos={companyDetailData.data.videos} id={companyDetailData.data.company.id} type="2"></CommonVideos>
                    <CommonDownload></CommonDownload>
                </div>
            )
        }else{
            return null;
        }
    }
}


function mapStateToProps(state) {
    const {companyDetailData}=state;
    return {
         companyDetailData,
    }
}
export default ReactRedux.connect(mapStateToProps)(Company)