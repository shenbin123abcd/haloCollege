import CommonHeader from './Common.header'
import CommonArticle from './Common.article'
import CommonVideos from './Common.videos'
import {fetchCompanyDetailIfNeeded} from '../actions/company.detail'
class Company extends React.Component{
    componentWillMount(){
        const { dispatch,location,routeParams} = this.props;
        dispatch(fetchCompanyDetailIfNeeded({
            company_id:routeParams.id
        }))
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }

    render(){
        const { dispatch,location,companyDetailData } = this.props;
        console.log(companyDetailData);
        if(companyDetailData.isFetching){
            return(
                <div>
                    <loading className="."></loading>
                </div>
            )
        }else if(companyDetailData.data){
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
                                <div className="company-myprofile">公司简介：{`${companyDetailData.data.company.description}`}</div>
                                <div className="company-more">
                                    <div className="company-more-word">查看更多</div>
                                    <div className="company-more-arrow">
                                        <svg className="haloIcon" aria-hidden="true">
                                            <use xlinkHref="#haloIcon-arrow"></use>
                                        </svg>
                                    </div>
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