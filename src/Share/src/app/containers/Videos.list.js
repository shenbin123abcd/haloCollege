import {fetchVideosListIfNeeded} from '../actions/videos.list'
import CommonHeader from './Common.header'

class VideosList extends React.Component{
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchVideosListIfNeeded({
            home_id:routeParams.id,
            type:routeParams.type,
            // match_level:routeParams.match_level,
            page:1,
            per_page:99
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }

    render(){
        const { dispatch,location,videosListData } = this.props;
        // console.log(videosListData);
        if(videosListData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(videosListData.data){
            return(
                <div>
                    <CommonHeader title="所有视频"></CommonHeader>
                    {
                        videosListData.data.list.map((n,i)=>{
                            return (
                                <div className="videos-list-wrapper" key={n.id}>
                                    <a href={`http://college-api.halobear.com/video/detail?id=${n.id}`} className="article-detail">
                                        <section className="article-list-box" >
                                            <div className="list-images-box">
                                                <img src={`${n.cover_url}?imageView2/1/w/238/h/158`} alt="" className="list-images"/>
                                            </div>
                                            <div className="list-word">
                                                <div className="word">{n.title}</div>
                                                <div className="list-icon-box">
                                                    <span className="list-icon-first">
                                                        {
                                                            n.category.split(",").map((v,j)=>{
                                                                return(
                                                                    <span key={j}>
                                                                        <span className="list-icon">
                                                                            <svg className="haloIcon haloIcon-common" aria-hidden="true">
                                                                                <use xlinkHref=
                                                                                         {v==1? "#haloIcon-speech":(v==2?"#haloIcon-interview":(v==3?"#haloIcon-competition"
                                                                                             :(v==4?"#haloIcon-class":(v==5?"#haloIcon-charge":(v==6?"#haloIcon-plan":(v==7?"#haloIcon-marketing"
                                                                                             :(v==8?"#haloIcon-host":(v==12?"#haloIcon-train":(v==13?"#haloIcon-flower":"")))))))))}
                                                                                ></use>
                                                                            </svg>
                                                                            {n.cate_title.split(",")[j]}
                                                                        </span>
                                                                    </span>
                                                                )
                                                            })
                                                        }
                                                        {/*{n.cate_title.split(",").map(c=>(c))}*/}
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

export default ReactRedux.connect(mapStateToProps)(VideosList)