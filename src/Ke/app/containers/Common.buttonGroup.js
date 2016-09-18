import ButtonGroup from '../components/Common.buttonGroup'
let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory;
import { fetchCourseStatusIfNeeded,receiveStatusPosts} from '../actions/buttonGroup'
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
            let data={
                course_id: id,
            };
            name='course'+id;
            let path=hb.location.url('path');

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
        }else if(btnType=='appointment-now'){

        }else if(btnType=='appointment-choose-seat'){
            app.modal.alert({
                pic:'disable-choose-seat',
                content:'还未开课，请耐心等待哦！',
                btn:'我知道了',
            })
        }else if(btnType=='disable-appointment-choose-seat'){
            app.modal.alert({
                pic:'disable-choose-seat',
                content:'不要着急，请先预约课程哦！',
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
            type:'POST',
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
    handleOpen(){
        const { dispatch,idData }=this.props;
        dispatch(receiveStatusPosts(idData,1,true))
    },
    handleClose(){
        const { dispatch,idData }=this.props;
        dispatch(receiveStatusPosts(idData,1,false))
    },
    render(){
        let priceData=this.props.priceData;
        let numData=this.props.numData;
        let idData=this.props.idData;
        let {res,isFetching,dipatch,showModal}=this.props;
        return(
            <ButtonGroup handleClose={this.handleClose} priceData={priceData} idData={idData} status={res} showModal={showModal} handleClick={this.handleClick} handleSubmit={this.handleSubmit} handleOpen={this.handleOpen}></ButtonGroup>
        )

    }
})

function mapStateToProps(state) {
    const { courseStatus } = state
    return courseStatus

}

export default ReactRedux.connect(mapStateToProps)(CommonButtonGroup)
