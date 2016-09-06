

const SeatBox = ({items,renderItem,isFetching}) => {

    if(!items){
        var isNull=true
    }

    if (isFetching||isNull) {
        return <div><i className="haloIcon haloIcon-spinner haloIcon-spin"></i></div>
    }


    return(
        <div className='seatinfo-box ' data-my-drag >
            <div className="seat-canvas">投影幕布</div>

            <div className='seat-box' >
                {items.map(renderItem)}
            </div>
        </div>
    )}

export default SeatBox