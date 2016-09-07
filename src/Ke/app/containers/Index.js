import Header from '../components/Index.Header'
import NavItem from '../components/Index.Header.NavItem'
import CourseItem from '../components/Index.CourseItem'
import Course from '../components/Index.Course'
import { fetchCourseIfNeeded,setCurrentMonth } from '../actions'


var Index = React.createClass({
    componentDidMount() {
        document.title='幻熊课堂';
        const { dispatch,monthList} = this.props
        let item=monthList.filter(n=>n.active)[0]
        dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
    },
    handleNavClick(item) {
        let {dispatch,monthList}=this.props;
        // console.log(item,monthList.filter(n=>n.active)[0])
        // console.log(item==monthList.filter(n=>n.active)[0])
        // console.log(item===monthList.filter(n=>n.active)[0])
        if(item!=monthList.filter(n=>n.active)[0]){
            dispatch(setCurrentMonth(item));
            dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
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

    render: function() {
        let {items,isFetching,monthList}=this.props;
        return (
            <div className="index-wrapper">
                <Header items={monthList} renderItem={this.renderNav} />
                <Course items={items} isFetching={isFetching} renderItem={this.renderCourse} />
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