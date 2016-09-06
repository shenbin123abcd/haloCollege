import { fetchSeatIfNeeded } from '../actions/common.seat'
import  SeatBox  from '../components/Seatinfo.SeatBox'
import  SeatRow  from '../components/Common.SeatRow'
import  SeatCell  from '../components/Common.SeatCell'


var Seatinfo = React.createClass({

    componentDidMount() {
        document.title='座位表';
        const { dispatch } = this.props
        // console.log('componentDidMount')
        dispatch(fetchSeatIfNeeded())
    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps')
        // console.log('componentWillReceiveProps')
        // console.log(nextProps)
    },
    componentDidUpdate  : function(prevState,prevProps){
        // console.log('componentDidUpdate')
        // console.log(prevState,prevProps)
        // let {items,isFetching}=this.props;
        let {items}=this.props;
        let dragDom=$(this.refs.dragContainer).find('[data-my-drag]')
        console.log(items,dragDom)
    },
    renderSeatRow(item, i) {
        return (
            <SeatRow items={item} key={i} renderItem={this.renderSeatCell} />
        )
    },
    renderSeatCell(item, i) {
        return (
            <SeatCell item={item} key={i}   />
        )
    },
    render() {

        let {items,isFetching}=this.props;
        // console.log(items)
        return (
            <div  ref="dragContainer" className="seatinfo-wrapper">
                <div className="seats-wrapper">
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />
                </div>

            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { seats } = state
    const {
        isFetching,
        items
    } = seats
    return{
        isFetching,
        items,
    }
}

export default ReactRedux.connect(mapStateToProps)(Seatinfo)