import SeatLine from './Common.SeatLine'


const SeatBox = ({items,renderItem,isFetching}) => {


    let numSize = classNames({
        'num-20': items[0].length==20,
        'num-10': items[0].length==10,
        'num-8': items[0].length==8,
        'num-6': items[0].length==6,
    });
    
    
    return(
        <div className='selectseat-box ' data-my-drag >
            <div className="seat-canvas">投影幕布</div>
            <div className={`seat-box`} >
                <div className="seat-ruler">
                    {items.map((n,i)=><div key={i} className={`num ${numSize}`}>{i+1}</div>)}
                </div>
                <SeatLine items={items} />
                {items.map(renderItem)}
            </div>
        </div>
    )}

export default SeatBox