

const SeatBox = ({items,renderItem,isFetching}) => {

    if(!items){
        var isNull=true
    }

    if (isFetching||isNull) {
        return <div><i className="haloIcon haloIcon-spinner haloIcon-spin"></i></div>
    }

    let seatBoxClass = classNames({
        'seat-box-1': items.length>=10,
        'seat-box-2': items.length==5,
    });

    return(
        <div className='seatinfo-box ' data-my-drag >
            <div className="seat-canvas">投影幕布</div>
            <div className={`seat-box ${seatBoxClass}`} >
                {items.map(renderItem)}
            </div>
        </div>
    )}

export default SeatBox