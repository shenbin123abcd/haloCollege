
const Item = ({item}) => {
    return(
        <img className="img" src={`${item}`} />
    )}



const Box = ({items,course}) => {
    items=items||[];
    // var aSeatArr=[];

    // seats&&seats.forEach((n,i)=>{
    //     n.forEach((n2,i)=>{
    //         if(!n2.user){
    //             aSeatArr.push(n)
    //         }
    //     });
    // });
    return(
        <div className='seatinfo-user-box' >
            <div className='title'><i className="haloIcon haloIcon-tag-2"></i><span className="text">已选座{items.length}人</span></div>
            <div className="avatar-box cf">
                {
                    items.map((n,i)=><Item key={i} item={n} />)
                }
            </div>

        </div>
    )}

export default Box