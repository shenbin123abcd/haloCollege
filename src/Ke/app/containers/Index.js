// import Header from '../components/Index.Header'
import List from '../components/Index.CourseList'
import { showCourseList,getCourseList,setCurrentMonth } from '../actions'


// const Index = ({courseList,items,dispatch}) => (
//     <div>
//         <Header items={monthList}  onClick={e => {
//         dispatch(showCourseList(11))
//     }}
//
//         />
//
//         <div className="ddd">
//             <List items={courseList} onClick={e => {
//         dispatch(getCourseList(22))
//     }}   />
//         </div>
//     </div>
// );

var Index = React.createClass({
    render: function() {
        let {courseList,items,dispatch,monthList}=this.props;
        return (
            <div>
                <div className="header-nav-wrapper">
                    {monthList.map((item,i) =>{
                        return (
                            <div key={i}>
                                <a onClick={e => {
                                dispatch(setCurrentMonth(i));
                                dispatch(showCourseList(item));
                                }} >
                                    {item.month}
                                </a>
                            </div>
                        )
                    })}
                </div>
                <div className="ddd">
                    <List items={courseList} onClick={e => {
                    dispatch(getCourseList(22))
                }}   />
                </div>
            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { postsByReddit,monthList,courseList } = state
    const {
        isFetching,
        items
    } = postsByReddit
    // console.log(state.postsByReddit,postsByReddit,isFetching,items)

    return {
        courseList,
        isFetching,
        items,
        monthList,
    }
}

export default ReactRedux.connect(mapStateToProps)(Index)