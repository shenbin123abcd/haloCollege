import { fetchSeatInfoIfNeeded } from '../actions/seatinfo'
import  SeatBox  from '../components/Seatinfo.SeatBox'
import  SeatRow  from '../components/Common.SeatRow'
import BottomBtn from './Common.buttonGroup'
import UserBox from '../components/Seatinfo.UserBox'
import { fetchCourseStatusIfNeeded } from '../actions/buttonGroup'
var Seatinfo = React.createClass({
    componentDidMount() {
        document.title='座位表';
        const { dispatch ,routeParams} = this.props
        dispatch(fetchSeatInfoIfNeeded(routeParams.id))
        dispatch(fetchCourseStatusIfNeeded(routeParams.id));
    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps')
        // console.log('componentWillReceiveProps')
        // console.log(nextProps)
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
    renderSeatRow(item, i) {
        return (
            <SeatRow items={item} key={i}   />
        )
    },
    render() {
        let {items,isFetching,users}=this.props;

        return (
            <div ref="dragContainer" className="seatinfo-wrapper">
                <div className="seats-wrapper">
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />
                    <UserBox items={users} seats={items} />
                    <BottomBtn  priceData={1000} numData={10} />
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
        items:users
    } = seatInfo
    const {
        items
    } = seats
    return{
        isFetching,
        items,
        users,
    }
}

export default ReactRedux.connect(mapStateToProps)(Seatinfo)