let Link=ReactRouter.Link
let Modal=ReactBootstrap.Modal;
import appointmentPic from '../images/appointment-pic.png'
import buySuccessPic from '../images/unable-seat.png'
//import ModalAppointment from './Modal.appointment'
import { fetchCourseStatusIfNeeded,receiveStatusPosts } from '../actions/buttonGroup'

export default React.createClass({
    render:function(){
        const price=this.props.priceData;
        const handleClick=this.props.handleClick;
        const handleSubmit=this.props.handleSubmit;
        const handleOpen=this.props.handleOpen;
        const handleClose=this.props.handleClose;
        const toBuySubmit=this.props.toBuySubmit;
        const showModal=this.props.showModal;
        const showSuccessModal=this.props.showSuccessModal;
        const id=this.props.idData;
        const status=this.props.status;
        const chooseSeat=this.props.chooseSeat;
        const cate_id=this.props.cate_id
        console.log(cate_id);

        let d=this.props.d;
        let h=this.props.h;
        let m=this.props.m;
        let s=this.props.s;

        h=h<10?'0'+h:h;
        m=m<10?'0'+m:m;
        s=s<10?'0'+s:s;

        let _this=this;
        function renderBottomBtnGroup(){
            if(status==1){
                const appointData=hb.store.get('ke-appoint-info');
                let styleClass=''
                let appointInfo=()=>{
                    if(appointData){
                        return(
                            <div className="content-form-block">
                                <input type="text" placeholder="阁下称呼" className="form-control input-style f-12" defaultValue={appointData.name} ref='name'/>
                                <input type="text" placeholder="阁下手机号" className="form-control input-style f-12 last" defaultValue={appointData.phone} ref='phone'/>
                                <div className="desc-text">我们将在开课前一个月以短信形式通知你，<br/>请耐心等待。</div>
                            </div>
                        )
                    }else{
                        return(
                            <div className="content-form-block">
                                <input type="text" placeholder="阁下称呼" className="form-control input-style f-12" ref='name'/>
                                <input type="text" placeholder="阁下手机号" className="form-control input-style f-12 last" ref='phone'/>
                                <div className="desc-text">我们将在开课前一个月以短信形式通知你，<br/>请耐心等待。</div>
                            </div>
                        )
                    }
                }
                let render=()=>{
                    if(cate_id==3){
                        return(
                            <div></div>
                        )
                    }else{
                        return(
                            <div className="choose-seat-btn f-15" onClick={e=>handleClick({type:'disable-appointment-choose-seat'})}>在线选座</div>
                        )
                    }
                }
                if(cate_id==3){
                    styleClass='enter-btn f-15 able sp width'
                }else{
                    styleClass='enter-btn f-15 able sp'
                }

                return(
                    <div className="flex-bottom-btn">
                        {render()}
                        <div className={styleClass}  onClick={handleOpen}><span id="appointment-text">预约课程</span>（￥{price} /人）</div>
                        <div className="appointment-now-modal">
                            <Modal show={showModal}>
                                <Modal.Body>
                                    <div className='modal-body-content'>
                                        <div className="content-pic">
                                            <div className="close-btn" onClick={e=>handleClose({type:'appointment-close'})}>&times;</div>
                                            <img src={appointmentPic} alt=""/>
                                        </div>
                                        <form>
                                            {appointInfo()}
                                            <div className='modal-dialog-footer f-15'>
                                                <div className="modal-dialog-send" onClick={e=>handleSubmit({
                                                name:$(_this.refs.name).val(),
                                                phone:$(_this.refs.phone).val()
                                                })}>
                                                    <div className="line"></div>
                                                    提交
                                                </div>
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
                        <div className="choose-seat-btn f-15" onClick={e=>handleClick({type:"appointment-choose-seat"})}>在线选座</div>
                        <div className='enter-btn f-15 disable'>已预约课程（￥{price} /人）</div>
                    </div>
                )
            }else if(status==3){
                const storeData=hb.store.get('ke-buy-info');
                let buyInfo=()=>{
                    if(storeData){
                        return(
                            <div className="content-form-block">
                                <input type="text" placeholder="阁下称呼" className="form-control input-style f-12" defaultValue={storeData.name} ref='name'/>
                                <input type="text" placeholder="阁下手机号" className="form-control input-style f-12 last" defaultValue={storeData.phone} ref='phone'/>
                                <input type="text" placeholder="阁下公司" className="form-control input-style f-12 last" defaultValue={storeData.company} ref='company'/>
                                <div className="desc-text">请留下以上信息，支付成功后我们会给您发送确认短信</div>
                            </div>
                        )
                    }else{
                        return(
                            <div className="content-form-block">
                                <input type="text" placeholder="阁下称呼" className="form-control input-style f-12" ref='name'/>
                                <input type="text" placeholder="阁下手机号" className="form-control input-style f-12 last" ref='phone'/>
                                <input type="text" placeholder="阁下公司" className="form-control input-style f-12 last" ref='company'/>
                                <div className="desc-text">请留下以上信息，支付成功后我们会给您发送确认短信</div>
                            </div>
                        )
                    }
                }
                let ifTimeoutShow=()=>{
                    if(d!='no' && m!='no' && h!='no' &&s!='no'){
                        $(".flex-bottom-btn").removeClass('sp');
                        return(
                            <div className='enter-btn f-15 able' onClick={e=>handleClick({type:'enroll-now'})}>
                                <div className="btn-wrapper">
                                    <div><span id="change-text">立即</span>报名（￥{price} /人）</div>
                                    <div style={{letterSpacing:'1px'}}>{d}天{h}:{m}:{s}</div>
                                </div>
                            </div>
                        )
                    }else{
                        $(".flex-bottom-btn").addClass('sp');
                        return(
                            <div className='enter-btn f-15 able sp' onClick={e=>handleClick({type:'enroll-now'})}>
                                <span id="change-text">立即</span>报名（￥{price} /人）
                            </div>
                        )
                    }
                }
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15" onClick={e=>handleClick({type:"disable-choose-seat"})}>在线选座</div>
                        {ifTimeoutShow()}
                        <div className="appointment-now-modal">
                            <Modal show={showSuccessModal}>
                                <Modal.Body>
                                    <div className='modal-body-content'>
                                        <div className="content-pic">
                                            <div className="close-btn" onClick={e=>handleClose({type:'buy-success-close'})}>&times;</div>
                                            <img src={buySuccessPic} alt=""/>
                                        </div>
                                        <form>
                                            {buyInfo()}
                                            <div className='modal-dialog-footer f-15'>
                                                <div className="modal-dialog-send" onClick={e=>toBuySubmit({
                                                name:$(_this.refs.name).val(),
                                                phone:$(_this.refs.phone).val(),
                                                company:$(_this.refs.company).val(),
                                                })}>
                                                    <div className="line"></div>
                                                    去支付
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </Modal.Body>
                            </Modal>
                        </div>
                    </div>
                )
            }else if(status==40){
                return  (
                    <div className="flex-bottom-btn">
                        <Link to={`/course/selectseat_${id}`} className="choose-seat-btn f-15">在线选座</Link>
                        <div className='enter-btn f-15 disable'>已报名（￥{price} /人）</div>
                    </div>
                )
            }else if(status==41){
                let line='',seat='';

                let addZero=(num)=>{
                    let number=parseInt(num);

                    if(number<10){
                        return ('0'+number)
                    }else{
                        return number
                    }
                }

                if(chooseSeat!=null){
                    let descArray=chooseSeat.split(',');
                    let lastPosition=parseInt(descArray.length)-1;
                    line=addZero(descArray[0]);
                    seat=addZero(descArray[lastPosition]);
                }

                return  (
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15">
                            <div className="seat-choosed">
                                <div className='f-14'>已选座</div>
                                <div className='f-14' id="choose-seat-info">{line}排{seat}座</div>
                            </div>
                        </div>
                        <div className='enter-btn f-15 disable'>已报名（￥{price} /人）</div>
                    </div>
                )
            }else if(status==5){
                return(
                    <div className="flex-bottom-btn">
                        <div className="choose-seat-btn f-15 grey">选座结束</div>
                        <div className='enter-btn f-15 disable'>报名结束（￥{price} /人）</div>
                    </div>
                )
            }
        }
        return(
            <div>
                {renderBottomBtnGroup()}
            </div>
        )
    }
})
