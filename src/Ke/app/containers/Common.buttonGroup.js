import ButtonGroup from '../components/Common.buttonGroup'
let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory;
import { fetchCourseStatusIfNeeded,receiveStatusPosts,buySuccessModal} from '../actions/buttonGroup'
import {fetchCourseDetailIfNeeded} from '../actions/detail'

var CommonButtonGroup= React.createClass({
    handleClick(e){
        const { dispatch ,idData}=this.props;
        let btnType=$(e.target).data('type');
        let id= idData;
        let name='';
        if(btnType=="disable-choose-seat"){
            app.modal.alert({
                pic:'disable-choose-seat',
                content:'不要着急，请先报名课程哦!',
                btn:'我知道了',
            })
        }else if(btnType=="enroll-now"){
            dispatch(buySuccessModal(true))

        }else if(btnType=='appointment-now'){

        }else if(btnType=='appointment-choose-seat' || btnType=='disable-appointment-choose-seat'){
            app.modal.alert({
                pic:'disable-choose-seat',
                content:'还未开课，请耐心等待哦！',
                btn:'我知道了',
            })
        }
    },
    handleSubmit(e){
        const { dispatch,idData }=this.props;
        let data={
            course_id:idData,
            phone:e.phone,
            name:e.name,
        }
        if(data.name==''){
            hb.lib.weui.alert('请填写姓名');
            return false;
        }else if(!hb.validation.checkPhone(data.phone)){
            hb.lib.weui.alert('请填写正确的手机号码');
            return false;
        }else if(data.phone==''){
            hb.lib.weui.alert('请填写手机号码');
            return false;
        }
        $.ajax({
            url:'/courses/reserve',
            data:data,
            success:function(res){
                //console.log(res);
                dispatch(receiveStatusPosts(idData,2,false))
            },
            error:function(error){
                hb.lib.weui.alert(error);
            }
        })
    },
    toBuySubmit(e){
        const { dispatch,idData }=this.props;
        let id= idData;
        let name='';
        let data={
            course_id:idData,
            phone:e.phone,
            name:e.name,
            company:e.company,
        }
        if(data.name==''){
            hb.lib.weui.alert('请填写姓名');
            return false;
        }else if(!hb.validation.checkPhone(data.phone)){
            hb.lib.weui.alert('请填写正确的手机号码');
            return false;
        }else if(data.phone==''){
            hb.lib.weui.alert('请填写手机号码');
            return false;
        }else if(data.company==''){
            hb.lib.weui.alert('请填写您的公司名');
            return false;
        }
        $.ajax({
            url:'/courses/apply',
            data:data,
            success:function(res){
                dispatch(buySuccessModal(false));
                let data={
                    course_id: id,
                };
                name='course'+id;
                let path=hb.location.url('path');
                setTimeout(function(){
                    app.pay.callPay(name).callpay({
                        url: '/pay/course',
                        data:data,
                        onSuccess:function (res) {
                            dispatch(receiveStatusPosts(id,40,false));
                            app.modal.confirm({
                                pic:'able-seat',
                                content:'报名成功，是否前去选座？',
                                leftBtn:'稍等片刻',
                                rightBtn:'前去选座'
                            }).then(function(){
                                browserHistory.push(`/course/selectseat_${id}`);
                            },function(){
                                if(path.indexOf('/course/detail_')>-1){
                                    dispatch(fetchCourseDetailIfNeeded(id));
                                }
                            })
                        },
                        onFail:function (res) {
                            hb.lib.weui.alert(res);
                        },
                    });
                },500);
            },
            error:function(error){
                hb.lib.weui.alert(error);
            }
        })
    },
    handleOpen(){
        const { dispatch,idData }=this.props;
        dispatch(receiveStatusPosts(idData,1,true))
    },
    handleClose(e){
        let btnType=$(e.target).data('type');
        const { dispatch,idData }=this.props;
        if(btnType=='appointment-close'){
            dispatch(receiveStatusPosts(idData,1,false));
        }else if(btnType=='buy-success-close'){
            dispatch(buySuccessModal(false));
        }
    },
    render(){
        let priceData=this.props.priceData;
        let idData=this.props.idData;
        let {res,showModal,data,val}=this.props;
        return(
            <ButtonGroup
                chooseSeat={data}
                handleClose={this.handleClose}
                priceData={priceData}
                idData={idData}
                status={res}
                showModal={showModal}
                handleClick={this.handleClick}
                handleSubmit={this.handleSubmit}
                handleOpen={this.handleOpen}
                showSuccessModal={val}
                toBuySubmit={this.toBuySubmit}
            >
            </ButtonGroup>
        )

    }
})

function mapStateToProps(state) {
    const { courseStatus,seatDesc,showSuccessModal } = state;
    const {
        res,
        isFetching,
        showModal
    }=courseStatus;
    const {
        data
    }=seatDesc
    const {
        val
     }=showSuccessModal
    return {
        res,
        isFetching,
        showModal,
        data,
        val
    }

}

export default ReactRedux.connect(mapStateToProps)(CommonButtonGroup)
