

const SeatRow = ({items,renderItem}) => {

    return(
        <div  >
           {items.map(renderItem)}
        </div>
    )}

export default SeatRow