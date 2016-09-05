import Header from '../components/Index.Header'
import NavItem from '../components/Index.Header.NavItem'
import CourseItem from '../components/Index.CourseItem'
import Course from '../components/Index.Course'
import { fetchCourseIfNeeded,setCurrentMonth } from '../actions'


var Index = React.createClass({
    handleNavClick(item) {
        let {dispatch,monthList}=this.props;
        // console.log(item,monthList.filter(n=>n.active)[0])
        // console.log(item==monthList.filter(n=>n.active)[0])
        // console.log(item===monthList.filter(n=>n.active)[0])
        if(item!=monthList.filter(n=>n.active)[0]){
            dispatch(setCurrentMonth(item));
            dispatch(fetchCourseIfNeeded(item));
        }

    },
    renderNav:function (item, i) {
        return (
            <NavItem item={item}  key={i}  handleNavClick={e=>{this.handleNavClick(item)}} />
        )
    },
    handleCourseClick(index) {
        let {dispatch}=this.props;
    },
    renderCourse:function (item, i) {
        return (
            <CourseItem item={item}  key={i} handleCourseClick={e=>{this.handleCourseClick()}} />
        )
    },
    componentDidMount() {
        const { dispatch } = this.props
        dispatch(fetchCourseIfNeeded())
    },
    render: function() {
        let {items,isFetching,monthList}=this.props;
        return (
            <div>
                <Header items={monthList} renderItem={this.renderNav} />
                <div className="index-content-wrapper" >
                    <Course items={items} isFetching={isFetching} renderItem={this.renderCourse} />
                </div>
            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { monthList,courseList } = state
    const {
        isFetching,
        items
    } = courseList

    return {
        isFetching,
        items,
        monthList,
    }
}

export default ReactRedux.connect(mapStateToProps)(Index)