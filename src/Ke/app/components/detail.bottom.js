let Link=ReactRouter.Link

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
                <Link to="/course/selectseat/10" className="choose-seat-btn f-15">在线选座</Link>
                <div className={renderEnterBtn()} >立即报名（{price}）</div>
            </div>
        )
    }
})
