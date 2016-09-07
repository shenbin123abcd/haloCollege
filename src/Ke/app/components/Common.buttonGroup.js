let Link=ReactRouter.Link
let Modal=ReactBootstrap.Modal;

export default React.createClass({
    componentDidMount(){
        $('.enter-btn').on('click',function(e){
            if(!$(this).hasClass('able')){
                e.preventDefault();
            }
        })
    },
    render:function(){
        const num=this.props.numData;
        const price=this.props.priceData;
        const handleClick=this.props.handleClick;
        let classStyle='';
        function renderEnterBtn(){
            if(num<=0){
                return (classStyle="enter-btn f-15 disable")
            }else{
                return (classStyle="enter-btn f-15 able")
            }
        }
        return(
            <div className="flex-bottom-btn">
                <div className="choose-seat-btn f-15" data-type="choose-seat" onClick={handleClick}>在线选座</div>
                <div className={renderEnterBtn()}>立即报名（￥{price} /人）</div>
            </div>
        )
    }
})
