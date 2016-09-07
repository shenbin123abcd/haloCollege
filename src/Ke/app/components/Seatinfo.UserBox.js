
const Item = ({items}) => {
    return(
        <div>aa</div>
    )}



const Box = ({items}) => {
    items=items||[];
    return(
        <div className='aaa' >
            {
                items.map((n,i)=><Item key={i} />)
            }
        </div>
    )}

export default Box