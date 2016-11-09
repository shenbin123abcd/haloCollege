import CommonHeader from './Common.header'
import {fetchArticlesListIfNeeded} from '../actions/articles.list'

class ArticlesList extends React.Component{

    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchArticlesListIfNeeded({
            home_id:routeParams.id,
            type:routeParams.type,
            page:1,
            per_page:99
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }
    render(){
        const { dispatch,location,articlesListData } = this.props;
        if(articlesListData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(articlesListData.data){
                return (
                    <div>
                        <CommonHeader title="所有文章" ></CommonHeader>
                        {
                            articlesListData.data.list.map((n,i)=>{
                                return(
                                    <div className="articles-list-wrapper" key={n.id}>
                                        <a href={`http://college-api.halobear.com/toutiao/detail?wedding_id=${n.id}`} className="article-detail">
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
                                            <div className={`myLine ${(articlesListData.data.list.length==(i+1))?'lineLast':''}`} ></div>
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
    const {articlesListData}=state;
    return {
         articlesListData,
    }
}

export default ReactRedux.connect(mapStateToProps)(ArticlesList)