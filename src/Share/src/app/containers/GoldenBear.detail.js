import CommonHeader from './Common.header'
import CommonFinalMatch from './Common.finalMatch'
import CommonFirstMatch from './Common.firstMatch'
import CommonDownload from './Common.download'
import {fetchGoldenBearDetailIfNeeded,destroyGoldenBearDetailData} from '../actions/goldenBear.detail'
let Link=ReactRouter.Link;
class GoldenBear extends React.Component{

    constructor(props){
       super(props);
        this.isWechatInit=false
    }
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchGoldenBearDetailIfNeeded({
            vid:routeParams.id
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
        dispatch(destroyGoldenBearDetailData());
    }

    render(){
        const { dispatch,location,goldenBearDetailData } = this.props;
        if(goldenBearDetailData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(goldenBearDetailData.data){
            if(!this.isWechatInit){
                app.wechat.init({
                    title: `${goldenBearDetailData.data.gold_award.title}`,
                    content: `${goldenBearDetailData.data.gold_award.brief}`,
                    logo : `${goldenBearDetailData.data.gold_award.cover_url}?imageView2/1/w/200/h/200`,
                    link : window.location.href,
                });
                this.isWechatInit=true;
            }
            return(
                <div>
                    <CommonHeader title="金熊奖主页"></CommonHeader>
                    <div className="goldBear-desc-wrapper">
                        <div className="company-show">
                            <div className="show-left-box">
                                <img src={`${goldenBearDetailData.data.gold_award.cover_url}?imageView2/1/w/150/h/150`} alt="" className="show-left"/>
                            </div>
                            <div className="show-right">
                                <div className="company-title">{`${goldenBearDetailData.data.gold_award.title}`}</div>
                            </div>
                            <div className="myLine"></div>
                            <div className="company-profile">
                                <div className="company-myprofile" ref="profile">金熊奖简介：{`${goldenBearDetailData.data.gold_award.brief}`}</div>
                                <div className="company-more" ref="moreInfo">
                                    <div className="company-more-word" ref="moreBtn">查看更多</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {
                        goldenBearDetailData.data.video_feature?<div className="goldBear-video-wrapper">
                                <video src={`${goldenBearDetailData.data.video_feature.url}`} controls="controls" poster={`${goldenBearDetailData.data.video_feature.cover_url}?imageView2/1/w/690/h/388`} className="videos-url"></video>
                                <div className="video-title">{goldenBearDetailData.data.video_feature.title}</div>
                            </div>:''
                    }
                    {/*<div className="goldBear-video-wrapper">*/}
                        {/*<video src={`${goldenBearDetailData.data.video_feature.url}`} className="videos-url"></video>*/}
                        {/*<div className="video-title">{goldenBearDetailData.data.video_feature.title}</div>*/}
                    {/*</div>*/}
                    <CommonFinalMatch match_final={goldenBearDetailData.data.match_final} id={goldenBearDetailData.data.gold_award.id} type="3" level="2"></CommonFinalMatch>
                    <CommonFirstMatch match_first={goldenBearDetailData.data.match_first} id={goldenBearDetailData.data.gold_award.id} type="3" level="1"></CommonFirstMatch>
                    <CommonDownload></CommonDownload>
                </div>
            )
        }else{
            return null;
        }
    }
}

function mapStateToProps(state) {
    const {goldenBearDetailData}=state;
    return{
        goldenBearDetailData
    }

}
export default ReactRedux.connect(mapStateToProps)(GoldenBear)
