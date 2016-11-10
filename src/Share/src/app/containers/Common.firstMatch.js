let Link=ReactRouter.Link;
class CommonFirstMatch extends React.Component{

    constructor(props) {
        super(props);
    }

    renderGoldenBearVideos(){
        const { dispatch,location,match_first,id,type,level} = this.props;
        if(match_first){
            return(
                <div className="common-article-wrapper">
                    <div className="article-top">
                        <div className="article-title">初赛</div>
                        <div className="article-arrow">
                            <svg className="haloIcon" aria-hidden="true">
                                <use xlinkHref="#haloIcon-arrow2"></use>
                            </svg>
                        </div>
                        <Link to={`/share/goldBearsVideosList/${id}/${type}/${level}`} className="article-watch">查看所有{match_first.total}个视频</Link>
                    </div>
                    {
                        match_first.list.map((n,i)=>{
                            return(
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
                                    <div className={`myLine ${(match_first.list.length==(i+1))?'lineLast':''}`} ></div>
                                </a>
                            )
                        })
                    }
                </div>
            )
        }
    }

    render(){
        const { dispatch,location,match_first} = this.props;
        return(
            <span>
                {this.renderGoldenBearVideos()}
            </span>
        )
    }
}

function mapStateToProps(state) {
    return {

    }
}
export default ReactRedux.connect(mapStateToProps)(CommonFirstMatch)