const Header = ({items,renderItem}) => (
    <div  className="index-header-nav-wrapper">
        {items.map(renderItem)}
    </div>
)

export default Header