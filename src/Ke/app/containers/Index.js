import Header from '../components/Index.Header'
import NavItem from '../components/Index.Header.NavItem'
import CourseItem from '../components/Index.CourseItem'
import Course from '../components/Index.Course'
import { fetchCourseIfNeeded,setCurrentMonth } from '../actions'
var browserHistory=ReactRouter.browserHistory;


var Index = React.createClass({
    componentDidMount() {
        document.title='幻熊课堂';
        const { dispatch,monthList,location} = this.props
        // console.log(location.query)

        if(location.query.month){
            let item=monthList.filter(n=>(n.year.toString()+n.month.toString())==location.query.month)[0]
            dispatch(setCurrentMonth(item));
            dispatch(fetchCourseIfNeeded(`${location.query.month}`));
        }else{
            let item=monthList.filter(n=>n.active)[0]
            // browserHistory.push(`/?month=${item.year}${item.month}`);
            // console.log(item.active)
            // dispatch(setCurrentMonth(item));
            dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
        }
    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps',nextProps,this.props)
        const { dispatch,monthList,location} = nextProps
        var item;

        if(location.query.month){
            item=monthList.filter(n=>(n.year.toString()+n.month.toString())==location.query.month)[0]
        }else{
            item=monthList[0]
        }

        // console.log(item.month)

        if(item!=monthList.filter(n=>n.active)[0]){
            dispatch(setCurrentMonth(item));
            dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
        }


    },
    componentDidUpdate  : function(prevState,prevProps){
        // console.log('componentDidUpdate',prevState,prevProps,this.props)

    },
    handleNavClick(item) {
        let {dispatch,monthList}=this.props;
        // console.log(item,monthList.filter(n=>n.active)[0])
        // console.log(item==monthList.filter(n=>n.active)[0])
        // console.log(item===monthList.filter(n=>n.active)[0])
        if(item!=monthList.filter(n=>n.active)[0]){
            // dispatch(setCurrentMonth(item));
            // dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
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