let Link=ReactRouter.Link
const Header = ({items,renderItem}) => (
    <div  className="index-header-nav-wrapper">
        <div className="nav-box">
            {items.map(renderItem)}
        </div>
        <span className="bar-box"></span>
        <div className="user-box">
            <Link to="/course/user?cate_id=1"  className="haloIcon haloIcon-user-single"></Link>
        </div>
    </div>
)

export default Header