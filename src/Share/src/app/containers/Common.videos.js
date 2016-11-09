let Link=ReactRouter.Link;
class CommonVideos extends React.Component{
    constructor(props) {
        super(props);
    }
    renderVideos(){
        var {videos,id,type}=this.props;
        if(videos){
            return (
                <div className="common-article-wrapper">
                    <div className="article-top">
                        <div className="article-title">视频</div>
                        <div className="article-arrow">
                            <svg className="haloIcon" aria-hidden="true">
                                <use xlinkHref="#haloIcon-arrow2"></use>
                            </svg>
                        </div>
                        <Link to={`/share/videosList/${id}/${type}`} className="article-watch">查看所有{videos.total}个视频</Link>
                    </div>
                    {
                        videos.list.map((n,i)=>{
                            return(
                                <a href={`http://college-api.halobear.com/video/detail?id=${n.id}`} className="article-detail"  key={n.id}>
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
                                    <div className={`myLine ${(videos.list.length==(i+1))?'lineLast':''}`} ></div>
                                </a>
                            )
                        })
                    }
                </div>
            )
        }
    }

    render(){
        var {videos,id,type}=this.props;
        return(
           <span>
                {this.renderVideos()}
           </span>
        )
    }
}
// export default Index
function mapStateToProps(state) {
    return {

    }
}
export default ReactRedux.connect(mapStateToProps)(CommonVideos)