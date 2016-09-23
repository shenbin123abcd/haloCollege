
let Link=ReactRouter.Link
const NavItem = ({item}) => {
    let itemClass = classNames({
        'item ': !item.active,
        'item active': item.active,
    });
    return(

    <div className={itemClass} >
        <Link to={`/course/index?month=${item.year}${item.month}`} className="link"  >
            {item.month}月
        </Link>
    </div>
)}

export default NavItem