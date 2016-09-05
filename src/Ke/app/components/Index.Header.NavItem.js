

const NavItem = ({item,handleNavClick}) => {
    let itemClass = classNames({
        'item ': !item.active,
        'item active': item.active,
    });
    return(

    <div className={itemClass} >
        <a className="link" onClick={handleNavClick} >
            {item.month}
        </a>
    </div>
)}

export default NavItem