const HeaderItem = ({item}) => (
    <div key={i}>
        <a onClick={e => {
            dispatch(getCourseList(22))
        }} >
            {item.month}
        </a>
    </div>
)

export default HeaderItem