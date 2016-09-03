import Header from '../components/Index.Header'
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
    handleNavClick(index) {
        let {dispatch}=this.props;
        dispatch(setCurrentMonth(index));
    },
    renderNav:function (item, i) {
        return (
            <div key={i}>
                <a onClick={e=>{this.handleNavClick(i)}} >
                    {item.month}
                </a>
            </div>
        )
    },
    render: function() {
        let {courseList,dispatch,monthList}=this.props;
        return (
            <div>
                <Header items={monthList} renderItem={this.renderNav} />
                <div className="index-content-wrapper">
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