let Link=ReactRouter.Link;
class CommonArticle extends React.Component{
    constructor(props) {
        super(props);
    }
    render(){
        var {articles ,id,type}=this.props;
        return (
            <div className="common-article-wrapper">
                <div className="article-top">
                    <div className="article-title">文章</div>
                    <div className="article-arrow">
                        <svg className="haloIcon" aria-hidden="true">
                            <use xlinkHref="#haloIcon-arrow2"></use>
                        </svg>
                    </div>
                    <Link to={`/share/articlesList/${id}/${type}`} className="article-watch">查看所有{articles.total}篇文章</Link>
                </div>
                    {articles.list.map((n,i)=>{
                    return(
                        <a href={`http://college-api.halobear.com/toutiao/detail?wedding_id=${n.id}`} className="article-detail"  key={n.id}>
                            <section className="article-list-box" >
                                <div className="list-images-box">
                                    <img src={`${n.imgs[0].url}?imageView2/1/w/238/h/158`} alt="" className="list-images"/>
                                </div>
                                <div className="list-word">
                                    <div className="word">{n.headline}</div>
                                    <div className="list-icon-box">
                                        <span className="list-icon-first">
                                            <span className="list-icon">
                                                { moment(n.create_time*1000).fromNow()}
                                            </span>
                                            <span className="list-icon">
                                                <svg className="haloIcon haloIcon-common" aria-hidden="true">
                                                    <use xlinkHref="#haloIcon-read"></use>
                                                </svg>
                                                {n.visitCount}
                                            </span>
                                            <span className="list-icon">
                                                <svg className="haloIcon haloIcon-common" aria-hidden="true">
                                                    <use xlinkHref="#haloIcon-like"></use>
                                                </svg>
                                                {n.praiseCount}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </section>
                            <div className={`myLine ${(articles.list.length==(i+1))?'lineLast':''}`} ></div>
                        </a>
                    )
                })}
            </div>
        )
    }
}
// export default Index
function mapStateToProps(state) {
    return {

    }
}
export default ReactRedux.connect(mapStateToProps)(CommonArticle)