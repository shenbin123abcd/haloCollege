import {fetchVideosListIfNeeded,destroyVideosListData} from '../actions/videos.list'
import CommonHeader from './Common.header'
import CommonDownload from './Common.download'

class GoldenBearVideosList extends React.Component{
    constructor(props) {
        super(props);
        this.isWechatInit=false
    }
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchVideosListIfNeeded({
            home_id:routeParams.id,
            type:routeParams.type,
            match_level:routeParams.level,
            page:1,
            per_page:99
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(destroyVideosListData());
    }
    render(){
        const { dispatch,location,videosListData } = this.props;
        // console.log(videosListData.data.list);
        if(videosListData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(videosListData.data){
            if(!this.isWechatInit){
                app.wechat.init({
                    title: `视频列表`,
                    content: `全国大咖现身演讲`,
                    link : window.location.href,
                });
                this.isWechatInit=true;
            }
            return(
                <div>
                    <CommonHeader title="所有视频"></CommonHeader>
                    {
                        videosListData.data.list.map((n,i)=>{
                            return (
                                <div className="videos-list-wrapper" key={n.id}>
                                    <a href={`http://college-api.halobear.com/video/detail?id=${n.id}`} className="article-detail"  key={n.id}>
                                        <section className="article-list-box" >
                                            <div className="list-images-box">
                                                <img src={`${n.cover_url}?imageView2/1/w/238/h/158`} alt="" className="list-images"/>
                                            </div>
                                            <div className="list-word">
                                                <div className="goldBearVideo-title">{n.title}</div>
                                                {
                                                    n.company?<div className="goldBearVideo-company-name">{n.company.name}</div>:<div className="goldBearVideo-company-name"></div>
                                                }
                                                <div className="list-icon-box goldBear-list-icon-box">
                                                <span className="list-icon-first">
                                                    <span className="list-icon">
                                                        {(n.times.split(":")[0]).replace(/\b(0+)/gi,"")}分钟
                                                    </span>
                                                    <span className="list-icon">
                                                        {n.views}次观看
                                                    </span>
                                                </span>
                                                </div>
                                            </div>
                                        </section>
                                        <div className={`myLine ${(videosListData.data.list.length==(i+1))?'lineLast':''}`} ></div>
                                    </a>
                                </div>
                            )
                        })
                    }
                    <CommonDownload></CommonDownload>
                </div>
            )
        }else{
            return null;
        }

    }
}

function mapStateToProps(state) {
    const {videosListData}=state;
    return {
        videosListData,
    }
}

export default ReactRedux.connect(mapStateToProps)(GoldenBearVideosList)