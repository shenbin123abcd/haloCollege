import { fetchSelectSeatIfNeeded } from '../actions/selectseat'
import { setSeatsStatus } from '../actions/common.seat'
import  SeatRow  from '../components/Common.SeatRow'
import  SeatBox  from '../components/Selectseat.SeatBox'
import  CourseBox  from '../components/Selectseat.CourseBox'
import { destroySeats,selectRandomSeat } from '../actions/common.seat'




var SeatBtn =({selectedItem,selectRandomSeat})=>{
    selectedItem=selectedItem||{}
    

    let renderLeftBt=()=>{
        if(selectedItem.seat_no){
            return(
                <div className="bt-box-1">
                     {selectedItem.seat_no}
                </div>
            )
        }else{
            return(
                <div onClick={selectRandomSeat}  className="bt-box-1">
                    系统选座
                </div>
            )
        }
    }


    return(
        <div className="selectseat-bt-box">
            {renderLeftBt()}
            <button className="bt-box-2">
                确认选座
            </button>
        </div>
    )

}


var SelectSeat = React.createClass({
    componentDidMount() {
        document.title='座位表';
        const { dispatch ,routeParams} = this.props
        // console.log(this.props)
        dispatch(fetchSelectSeatIfNeeded(routeParams.id))
        dispatch(setSeatsStatus(true))
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
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(destroySeats())
    },
    renderSeatRow(item, i) {
        return (
            <SeatRow items={item} key={i}   />
        )
    },
    selectRandomSeat(){

        // console.log(this.props)
        const {dispatch} = this.props
        dispatch(selectRandomSeat());
    },
    render() {
        let {items,isFetching,course,selectedItem}=this.props;

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
                    <SeatBtn selectedItem={selectedItem} selectRandomSeat={this.selectRandomSeat} />

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
        items,
        selectedItem,
    } = seats
    return{
        course,
        isFetching,
        items,
        selectedItem,
    }
}

export default ReactRedux.connect(mapStateToProps)(SelectSeat)