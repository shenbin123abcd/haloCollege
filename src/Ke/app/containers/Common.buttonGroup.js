import ButtonGroup from '../components/Common.buttonGroup'
let Link=ReactRouter.Link;
var browserHistory=ReactRouter.browserHistory;
import { fetchCourseStatusIfNeeded } from '../actions/buttonGroup'

var CommonButtonGroup= React.createClass({
    componentDidMount(){
        const { dispatch ,routeParams}=this.props;

        //dispatch(fetchCourseStatusIfNeeded(routeParams.id))
    },
    handleClick(e){
        let btnType=$(e.target).data('type');
        let pathArr=hb.location.url('path').split('/');
        let id= pathArr[pathArr.length-1];
        if(btnType=="choose-seat"){
            app.modal.alert({
                pic:'unable-seat',
                content:'不要着急，请先报名课程哦!',
                btn:'我知道了',
            })
        }else if(btnType=="enroll-now"){
            /*app.modal.confirm({
             pic:'able-seat',
             content:'报名成功，是否前去选座？',
             leftBtn:'稍等片刻',
             rightBtn:'前去选座'
             }).then(()=>browserHistory.push(`/course/selectseat/${id}`))*/
        }
    },
    render(){
        let priceData=this.props.priceData;
        let numData=this.props.numData;
        let {res,isFetching,dipatch}=this.props;
        return(
            <ButtonGroup priceData={priceData} numData={numData} status={res} handleClick={this.handleClick}></ButtonGroup>
        )

    }
})

function mapStateToProps(state) {
    const { courseStatus } = state
    return courseStatus

}

export default ReactRedux.connect(mapStateToProps)(CommonButtonGroup)
