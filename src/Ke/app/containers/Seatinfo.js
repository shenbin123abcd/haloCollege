import { fetchSeatInfoIfNeeded } from '../actions/seatinfo'
import  SeatBox  from '../components/Seatinfo.SeatBox'
import  SeatRow  from '../components/Common.SeatRow'
import BottomBtn from './Common.buttonGroup'
import UserBox from '../components/Seatinfo.UserBox'
import { fetchCourseStatusIfNeeded } from '../actions/buttonGroup'
import  PageLoading  from '../components/Common.Pageloading'
import { destroySeats } from '../actions/common.seat'
var Seatinfo = React.createClass({
    componentDidMount() {
        document.title='座位表';
        if(Modernizr.weixin&&Modernizr.ios){
            hb.hack.setTitle(document.title);
        }
        app.wechat.init();
        const { dispatch ,routeParams} = this.props
        dispatch(fetchSeatInfoIfNeeded(routeParams.id))
        dispatch(fetchCourseStatusIfNeeded(routeParams.id));
    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps')
        // console.log('componentWillReceiveProps')
        // console.log(nextProps)
        // console.log(1,this.props.course,nextProps.course);
        // if(nextProps.course){
        //     if(!this.props.course){
        //         app.wechat.init({
        //             title: `幻熊课堂-座位表`,
        //             content: `${nextProps.course.title}`,
        //             link : window.location.href,
        //         });
        //     }else if(this.props.data.id!=nextProps.data.id){
        //         app.wechat.init({
        //             title: `幻熊课堂-座位表`,
        //             content: `${nextProps.course.title}`,
        //             link : window.location.href,
        //         });
        //     }else if(!_.isEqual(_.omit(app.wechat.getShareDate(),['logo']),{
        //             title: `幻熊课堂-座位表`,
        //             content: `${nextProps.course.title}`,
        //             link : window.location.href,
        //         })){
        //         app.wechat.init({
        //             title: `幻熊课堂-座位表`,
        //             content: `${nextProps.course.title}`,
        //             link : window.location.href,
        //         });
        //     }
        // }
    },
    hbDrag:null,
    componentDidUpdate  : function(prevState,prevProps){
        // console.log('componentDidUpdate')
        // console.log(prevState,prevProps)
        // let {items,isFetching}=this.props;
        let dragDom=$(this.refs.dragContainer).find('[data-my-drag]').get()[0]
        if(prevState.items&&!this.hbDrag){
            // console.log(dragDom)
            this.hbDrag=hb.drag(dragDom,{});
        }
    },
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(destroySeats())
    },
    renderSeatRow(item, i) {
        return (
            <SeatRow items={item} key={i}   />
        )
    },
    render() {
        let {items,isFetching,users,routeParams,course}=this.props;
        if(!items){
            var isNull=true
        }

        if (isFetching||isNull) {
            return <PageLoading />
        }
        return (
            <div ref="dragContainer" className="seatinfo-wrapper">
                <div className="seats-wrapper">
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />
                    <UserBox items={users}  course={course} />
                    <BottomBtn  priceData={course.price} idData={routeParams.id} />
                </div>

            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { seats,seatInfo } = state
    const {
        isFetching,
        course,
        items:users
    } = seatInfo
    const {
        items
    } = seats
    return{
        isFetching,
        items,
        users,
        course,
    }
}

export default ReactRedux.connect(mapStateToProps)(Seatinfo)