var { Spin } = antd;
var PageLoading=React.createClass({
    render(){
        return(
            <div className="loading-container">
                <Spin  size="large"/>
            </div>
        )
    }
});

export default PageLoading
