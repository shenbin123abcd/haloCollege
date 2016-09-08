import { selectSeat } from '../actions/common.seat'

var SeatCell= React.createClass({
    componentDidMount(){

    },
    handleClick(item){
        // console.log(item)
        // console.log(this.props)
        const {clickable,dispatch} = this.props
        if(!clickable){
            return
        }else{
            dispatch(selectSeat(item))
        }



    },
    render(){
        let {item,index,itemsLength}=this.props;

        let seatBoxClassSelected = classNames({
            'selected': item.user!=0,
        });
        let seatBoxMiddle = classNames({
            'middle-1': Math.floor(itemsLength/2)==index+1,
            'middle-2': Math.floor(itemsLength/2)==index,
        });
        let seatBoxSize = classNames({
            'seat-cell-20': itemsLength==20,
            'seat-cell-16': itemsLength==16,
            'seat-cell-6': itemsLength==6,
        });
        return(
            <div className={`seat-cell ${seatBoxClassSelected} ${seatBoxMiddle} ${seatBoxSize}`}
                 onClick={e=>this.handleClick(item)}
            ></div>
        )

    }
})

function mapStateToProps(state) {
    // console.log(state)
    const { seats } = state
    const { clickable } = seats
    return{
        clickable,
    }
}

export default ReactRedux.connect(mapStateToProps)(SeatCell)