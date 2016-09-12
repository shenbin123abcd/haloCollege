let Link=ReactRouter.Link
let Modal=ReactBootstrap.Modal;
import appointmentPic from '../images/appointment-pic.png'
//import ModalAppointment from './Modal.appointment'
import { fetchCourseStatusIfNeeded,receiveStatusPosts } from '../actions/buttonGroup'

export default React.createClass({
    componentDidUpdate  : function(prevState,prevProps){
        // console.log('componentDidUpdate')
        // console.log(prevState,prevProps)
        // let {items,isFetching}=this.props;
        //let dragDom=$(this.refs.dragContainer).find('[data-my-drag]').get()[0]
        //if(prevState.items&&!this.hbDrag){
        //    // console.log(dragDom)
        //    this.hbDrag=hb.drag(dragDom,{});
        //}
    },
    render:function(){
        const price=this.props.priceData;
        const handleClick=this.props.handleClick;
        const handleSubmit=this.props.handleSubmit;
        const handleOpen=this.props.handleOpen;
        const handleClose=this.props.handleClose;
        const showModal=this.props.showModal
        const id=this.props.idData;
        const status=this.props.status;
        //const status=1;
        let _this=this;
        function renderEnterBtn(){
            if(status==1){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15" data-type="disable-choose-seat" onClick={handleClick}>在线选座</div>
                        <div className='enter-btn f-15 able' data-type="appointment-now" onClick={handleOpen}><span id="appointment-text">预约课程</span>（￥{price} /人）</div>
                        <div className="appointment-now-modal">
                            <Modal show={showModal} onHide={handleClose}>
                                <Modal.Body>
                                    <div className='modal-body-content'>
                                        <div className="content-pic">
                                            <img src={appointmentPic} alt=""/>
                                        </div>
                                        <form>
                                            <div className="content-form-block">
                                                <input type="text" placeholder="阁下称呼" className="form-control input-style f-12" ref='name'/>
                                                <input type="text" placeholder="阁下手机号" className="form-control input-style f-12 last" ref='phone'/>
                                                <div className="desc-text">我们将在开课前一个月以短信形式通知你，<br/>请耐心等待。</div>
                                            </div>
                                            <div className='modal-dialog-footer f-15'>
                                                <div className="modal-dialog-send" onClick={e=>handleSubmit({
                                                name:$(_this.refs.name).val(),
                                                phone:$(_this.refs.phone).val()
                                                })}>提交</div>
                                            </div>
                                        </form>
                                    </div>
                                </Modal.Body>
                            </Modal>
                        </div>
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
                        <Link to={`/course/selectseat_${id}`} className="choose-seat-btn f-15">在线选座</Link>
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
