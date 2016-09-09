
import  SeatCell  from '../containers/Common.SeatCell'



const SeatRow = ({items}) => {

    let seatBoxSize = classNames({
        'seat-row-20': items.length==20,
        'seat-row-16': items.length==16,
        'seat-row-6': items.length==6,
    });
    return(
        
        <div className={`seat-row cf ${seatBoxSize}`} >
            
           {items.map((item,i)=>{
               return (
                   <SeatCell item={item} index={i} key={i} itemsLength={items.length} />
               )
           })}
        </div>
    )}

export default SeatRow