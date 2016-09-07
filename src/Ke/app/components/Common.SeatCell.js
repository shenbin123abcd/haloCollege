

const SeatCell = ({item,index,itemsLength}) => {
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
        <div className={`seat-cell ${seatBoxClassSelected} ${seatBoxMiddle} ${seatBoxSize}`} ></div>
    )}

export default SeatCell