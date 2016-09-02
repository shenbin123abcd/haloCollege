const Header = ({items,dispatch}) => (
    <div>
        {
            items.map((item,i) =>{
                return (
                    <div key={i}>
                        <a onClick={e => {
                            dispatch(getCourseList(22))
                        }} >
                            {item.month}
                        </a>
                    </div>
                )
            })
        }
    </div>
)

export default Header