
const Item = ({item}) => {
    return(
        <img className="img" src={`${item}`} />
    )}



const Box = ({items,seats}) => {
    items=items||[];
    return(
        <div className='seatinfo-user-box' >
            <div className='title'><i className="haloIcon haloIcon-tag-2"></i><span className="text">已选座{seats?seats.length:''}人</span></div>
            <div className="avatar-box cf">
                {
                    items.map((n,i)=><Item key={i} item={n} />)
                }
            </div>

        </div>
    )}

export default Box