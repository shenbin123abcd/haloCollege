import ButtonGroup from '../components/Common.buttonGroup'
let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory;
import { fetchCourseStatusIfNeeded,receiveStatusPosts } from '../actions/buttonGroup'

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
            app.pay.callPay(name).callpay({
                data:data,
                onSuccess:function (res) {
                    dispatch(receiveStatusPosts(id,4));
                    app.modal.confirm({
                        pic:'able-seat',
                        content:'报名成功，是否前去选座？',
                        leftBtn:'稍等片刻',
                        rightBtn:'前去选座'
                    }).then(function(){
                        browserHistory.push(`/course/selectseat/${id}`);
                    },function(){

                    })
                },
                onFail:function (res) {
                    hb.lib.weui.alert(res);
                },
            });
        }
    },
    render(){
        let priceData=this.props.priceData;
        let numData=this.props.numData;
        let idData=this.props.idData;
        let {res,isFetching,dipatch}=this.props;
        return(
            <ButtonGroup priceData={priceData} idData={idData} status={res} handleClick={this.handleClick}></ButtonGroup>
        )

    }
})

function mapStateToProps(state) {
    const { courseStatus } = state
    return courseStatus

}

export default ReactRedux.connect(mapStateToProps)(CommonButtonGroup)
