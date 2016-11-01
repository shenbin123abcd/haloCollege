
var { Row,Col } = antd;
var PageError=React.createClass({
    render(){
        return(
            <Row type="flex" justify="center" align="middle" className="error-message">
                <Col span={12} className="error-icon">
                    <div className="circle-wrapper">
                        <span className="haloIcon haloIcon-404"></span>
                    </div>
                </Col>
                <Col span={12} className="error-desc">
                    <div style={{fontSize:48,color:'#FF6600',marginBottom:10,}}>抱歉！404</div>
                    <div style={{fontSize:18,color:'#666666',marginBottom:10,}}>页面好像丢失了，也许...</div>
                    <div className="error-text">地址输入错了</div>
                    <div className="error-text">页面停用了</div>
                    <div className="error-text">系统君出去耍了</div>
                </Col>
            </Row>
        )
    }
});

export default PageError;
