

const SeatBox = ({items,renderItem,isFetching}) => {

    

    let seatBoxClass = classNames({
        'seat-box-1': items.length>=10,
        'seat-box-2': items.length==5,
    });
    let numSize = classNames({
        'num-20': items.length==20,
        'num-10': items.length==10,
        'num-5': items.length==5,
    });
    return(
        <div className='selectseat-box ' data-my-drag >
            <div className="seat-canvas">投影幕布</div>
            <div className={`seat-box ${seatBoxClass}`} >
                <div className="seat-ruler">
                    {items.map((n,i)=><div key={i} className={`num ${numSize}`}>{i+1}</div>)}
                </div>
                <div className="seat-line"></div>
                {items.map(renderItem)}
            </div>
        </div>
    )}

export default SeatBox