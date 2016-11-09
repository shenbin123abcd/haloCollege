var browserHistory=ReactRouter.browserHistory;
var withRouter=ReactRouter.withRouter;

class Greeting extends React.Component {
    render() {
        return <h1>Hello, {this.props.name}</h1>;
    }
}

class GoBack extends React.Component{
    constructor(props) {
        super(props);
    }
    handlerClick(){
        browserHistory.goBack()
    }
    render(){
        if(history.length!=1){
            return(
                <div className="go-back" onClick={this.handlerClick}>
                    <svg className="haloIcon haloIcon-back" aria-hidden="true">
                        <use xlinkHref="#haloIcon-back"></use>
                    </svg>
                </div>
            )
        }else{
            return (
                <div className="go-back" onClick={this.handlerClick}>
                    <svg className="haloIcon haloIcon-back" aria-hidden="true">
                        <use xlinkHref="#haloIcon-back"></use>
                    </svg>
                </div>
            )

        }
    }

}


class CommonHeader extends React.Component{
    constructor(props) {
        super(props);
    }
    componentDidMount(){

    }
    render(){
        var {title,children,goBack=true}=this.props;
        let renderLeftBtn=()=>{
            if(goBack){
                return(
                    <GoBack goBack={goBack}/>
                )
            }else{
                return null
            }

        };
        return (
            <div className="common-header-wrapper">
                <div className="left-box">
                    {renderLeftBtn()}
                </div>
                <div className="title">{title}</div>
                <div className="right-box">
                    {children}
                </div>
            </div>
        );
    }
};



// export default Index
function mapStateToProps(state) {
    return {

    }
}


export default ReactRedux.connect(mapStateToProps)(CommonHeader)