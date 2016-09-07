import { fetchSeatInfoIfNeeded } from '../actions/seatinfo'
import  SeatBox  from '../components/Seatinfo.SeatBox'
import  SeatRow  from '../components/Common.SeatRow'


var Seatinfo = React.createClass({

    componentDidMount() {
        document.title='座位表';
        const { dispatch ,routeParams} = this.props
        // console.log(this.props)
        dispatch(fetchSeatInfoIfNeeded(routeParams.id))
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
        let dragDom=$(this.refs.dragContainer).find('[data-my-drag]')
        // console.log(prevState,dragDom)
        if(prevState.items){
            console.log(dragDom)
        }
    },
    renderSeatRow(item, i) {

        return (
            <SeatRow items={item} key={i}   />
        )
    },
    render() {

        let {items,isFetching}=this.props;
        // console.log(items)
        return (
            <div ref="dragContainer" className="seatinfo-wrapper">
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

export default ReactRedux.connect(mapStateToProps)(Seatinfo)