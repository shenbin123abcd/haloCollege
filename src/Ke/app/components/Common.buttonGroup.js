let Link=ReactRouter.Link
let Modal=ReactBootstrap.Modal;

export default React.createClass({
    render:function(){
        const price=this.props.priceData;
        const handleClick=this.props.handleClick;
        const id=this.props.idData;
        const status=this.props.status;
        //const status=3;
        function renderEnterBtn(){
            if(status==1){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15">在线选座</div>
                        <div className='enter-btn f-15 able' data-type="appointment-now" onClick={handleClick}>预约课程（￥{price} /人）</div>
                    </div>
                )
            }else if(status==2){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15">在线选座</div>
                        <div className='enter-btn f-15 able'>已预约课程（￥{price} /人）</div>
                    </div>
                )
            }else if(status==3){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15" data-type="disable-choose-seat" onClick={handleClick}>在线选座</div>
                        <div className='enter-btn f-15 able' data-type="enroll-now" onClick={handleClick}><span id="change-text">立即</span>报名（￥{price} /人）</div>
                    </div>
                )
            }else if(status==4){
                return  (
                    <div className="flex-bottom-btn">
                        <Link to={`/course/selectseat/${id}`} className="choose-seat-btn f-15">在线选座</Link>
                        <div className='enter-btn f-15 able'>已报名（￥{price} /人）</div>
                    </div>
                )
            }else if(status==5){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15">在线选座</div>
                        <div className='enter-btn f-15 disable'>报名结束（￥{price} /人）</div>
                    </div>
                )
            }
        }
        return(
            <div>
                {renderEnterBtn()}
            </div>
        )
    }
})
