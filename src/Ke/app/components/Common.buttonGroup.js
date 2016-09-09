let Link=ReactRouter.Link
let Modal=ReactBootstrap.Modal;

export default React.createClass({
    render:function(){
        const num=this.props.numData;
        const price=this.props.priceData;
        const handleClick=this.props.handleClick;
        const status=this.props.status;
        let pathArr=hb.location.url('path').split('/');
        let id= pathArr[pathArr.length-1];
        function renderEnterBtn(){
            if(status==1 && num>0){
                return  (
                    <div className="flex-bottom-btn">
                        <Link to={`/course/selectseat/${id}`} className="choose-seat-btn f-15">在线选座</Link>
                        <div className='enter-btn f-15 able'>已报名（￥{price} /人）</div>
                    </div>
                )
            }else if(!status==1 && num>0){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15" data-type="choose-seat" onClick={handleClick}>在线选座</div>
                        <div className='enter-btn f-15 able'>立即报名（￥{price} /人）</div>
                    </div>
                )
            }else if(num<=0){
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
