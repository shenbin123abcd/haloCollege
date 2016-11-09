
class CommonDownload extends React.Component{
    constructor(props) {
        super(props);
    }
    componentDidMount(){
        const { dispatch,location,routeParams} = this.props;
    }
    close(e){
        e.stopPropagation();
        ReactDOM.findDOMNode(this.refs.block).setAttribute("style","display:none");
    }
    download(e){
        e.preventDefault();
        window.location.href="http://a.app.qq.com/o/simple.jsp?pkgname=com.halobear.weddingvideo";
    }
    render(){
        return(
            <div className="dowload-block" ref="block" onClick={this.download.bind(this)}>
                <img src={require("../images/college-app-logo.png")}  alt="" className="logo"/>
                <div className="info">
                    <div className="title">幻熊学院</div>
                    <div className="desc">下载APP客户端，观看视频更流畅！</div>
                </div>
                <span className="close" onClick={this.close.bind(this)}>
                     <svg className="haloIcon haloIcon-times" aria-hidden="true">
                         <use xlinkHref="#haloIcon-times"></use>
                     </svg>
                </span>
                <a className="btn btn-primary" href="" >免费下载</a>
            </div>
        )
    }
}

function mapStateToProps(state) {
    return {

    }
}
export default ReactRedux.connect(mapStateToProps)(CommonDownload)