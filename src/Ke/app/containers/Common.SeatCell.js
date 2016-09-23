import { selectSeat } from '../actions/common.seat'

var SeatCell= React.createClass({
    componentDidMount(){

    },
    handleClick(item){
        // console.log(item)
        // console.log(this.props)
        const {clickable,dispatch} = this.props
        if(!clickable||item.user!=0){
            return
        }else{
            dispatch(selectSeat(item))
        }



    },
    render(){
        let {item,index,itemsLength,selectedItem}=this.props;
        selectedItem=selectedItem||{}
        let seatBoxClassBooked = classNames({
            'booked': item.user!=0,
        });

        let seatBoxClassSelected = classNames({
            'selected': item.seat_no==selectedItem.seat_no,
        });

        if(itemsLength==8){
            var seatBoxMiddle = classNames({
                'middle-242-0': index==0,
                'middle-242-1': index==1,
                'middle-242-2': index==2,
                'middle-242-3': index==5,
                'middle-242-4': index==6,
                'middle-242-7': index==7,
            });
        }else{
            var seatBoxMiddle = classNames({
                'middle-1': Math.floor(itemsLength/2)==index+1,
                'middle-2': Math.floor(itemsLength/2)==index,
            });
        }



        let seatBoxSize = classNames({
            'seat-cell-20': itemsLength==20,
            'seat-cell-16': itemsLength==16,
            'seat-cell-8': itemsLength==8,
            'seat-cell-6': itemsLength==6,
        });
        return(
            <div className={`seat-cell ${seatBoxClassSelected} ${seatBoxClassBooked} ${seatBoxMiddle} ${seatBoxSize}`}
                 onClick={e=>this.handleClick(item)}
            ></div>
        )

    }
})

function mapStateToProps(state) {
    // console.log(state)
    const { seats } = state
    const { clickable,selectedItem } = seats
    return{
        clickable,
        selectedItem,
    }
}

export default ReactRedux.connect(mapStateToProps)(SeatCell)