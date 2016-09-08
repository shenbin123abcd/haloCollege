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
        // console.log(dragDom,prevState.items)
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
        let {items,isFetching,course}=this.props;

        if(!items){
            var isNull=true
        }

        if (isFetching||isNull) {
            return <div><i className="haloIcon haloIcon-spinner haloIcon-spin"></i></div>
        }

        return (
            <div ref="dragContainer" className="selectseat-wrapper">
                <div className="seats-wrapper">
                    <CourseBox data={course}  />
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />

                </div>

            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { seats,selectSeat } = state
    const {
        isFetching,
        course,
    } = selectSeat
    const {
        items
    } = seats
    return{
        course,
        isFetching,
        items,
    }
}

export default ReactRedux.connect(mapStateToProps)(SelectSeat)