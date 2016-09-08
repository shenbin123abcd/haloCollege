import { fetchSelectSeatIfNeeded } from '../actions/selectseat'
import  SeatRow  from '../components/Common.SeatRow'
import  SeatBox  from '../components/Selectseat.SeatBox'
import  CourseBox  from '../components/Selectseat.CourseBox'


var SelectSeat = React.createClass({
    componentDidMount() {
        document.title='座位表';
        const { dispatch ,routeParams} = this.props
        // console.log(this.props)
        dispatch(fetchSelectSeatIfNeeded(routeParams.id))
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
            <div ref="dragContainer" className="selectseat-wrapper">
                <div className="seats-wrapper">
                    <CourseBox data={users}  />
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />

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
    } = seatInfo
    const {
        items
    } = seats
    return{
        isFetching,
        items,
    }
}

export default ReactRedux.connect(mapStateToProps)(SelectSeat)