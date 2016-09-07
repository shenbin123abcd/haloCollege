
import  SeatCell  from '../components/Common.SeatCell'





const SeatRow = ({items}) => {
    
    
    return(
        
        <div className="seat-row cf" >
            
           {items.map((item,i)=>{
               return (
                   <SeatCell item={item} index={i} key={i} itemsLength={items.length} />
               )
           })}
        </div>
    )}

export default SeatRow