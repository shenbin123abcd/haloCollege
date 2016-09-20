import CourseItem from '../components/Index.CourseItem'
import Course from '../components/Index.Course'
// import { fetchCourseIfNeeded,setCurrentMonth } from '../actions'
var ReactCSSTransitionGroup = React.addons.CSSTransitionGroup


var Index = React.createClass({
    componentDidMount() {
        document.title='幻熊课堂';
        const { dispatch,monthList,location,isFetching} = this.props;
        // console.log(location.query)

        // if(location.query.month){
        //     let item=monthList.filter(n=>(n.year.toString()+n.month.toString())==location.query.month)[0]
        //     dispatch(setCurrentMonth(item));
        //     dispatch(fetchCourseIfNeeded(`${location.query.month}`));
        // }else{
        //     let item=monthList.filter(n=>n.active)[0]
        //     // browserHistory.push(`/?month=${item.year}${item.month}`);
        //     // console.log(item.active)
        //     // dispatch(setCurrentMonth(item));
        //     dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
        // }
    },
    componentWillReceiveProps : function(nextProps) {
        // const { dispatch,monthList,location,isFetching} = this.props;
        // console.log(1,this.props.isFetching)
        // console.log(2,nextProps.isFetching)
        // console.log('componentWillReceiveProps',nextProps,this.props)
        // const { dispatch,monthList,location} = nextProps
        // var item;
        //
        // if(location.query.month){
        //     item=monthList.filter(n=>(n.year.toString()+n.month.toString())==location.query.month)[0]
        // }else{
        //     item=monthList[0]
        // }
        //
        // // console.log(item.month)
        //
        // if(item!=monthList.filter(n=>n.active)[0]){
        //     dispatch(setCurrentMonth(item));
        //     dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
        // }

    },
    componentDidUpdate  : function(prevState,prevProps){
        // console.log(11,this.props.isFetching)
        // console.log('componentDidUpdate',prevState,prevProps,this.props)
        // if(this.props.isFetching){
        //     app.wechat.init({
        //         link : window.location.href,
        //     });
        // }


    },
    handleCourseClick(index) {
        let {dispatch}=this.props;
    },
    renderCourse:function (item, i) {
        return (
            <CourseItem item={item}  key={i} handleCourseClick={e=>{this.handleCourseClick()}} />
        )
    },
    // getInitialState: function() {
    //     return {items2: ['hello', 'world', 'click', 'me']};
    // },
    // handleAdd: function() {
    //     var newItems =
    //         this.state.items2.concat([prompt('Enter some text')]);
    //     this.setState({items2: newItems});
    // },
    // handleRemove: function(i) {
    //     var newItems = this.state.items2.slice();
    //     newItems.splice(i, 1);
    //     this.setState({items2: newItems});
    // },
    render: function() {
        var _this=this
        let {items,isFetching,monthList,location}=this.props;
        // var items2 = this.state.items2.map(function(item, i) {
        //     return (
        //         <div key={item} onClick={this.handleRemove.bind(this, i)}>
        //             {item}
        //         </div>
        //     );
        // }.bind(this));
        // var Items3 = ()=>{
        //     if(this.state.items2.length==4){
        //         return (
        //             <div >
        //                 is
        //             </div>
        //         );
        //     }else{
        //         return (
        //             <div >
        //                 er
        //             </div>
        //         );
        //     }
        //
        // };
        return (


            <div className="index-wrapper">
                {/*
                 <button onClick={this.handleAdd}>Add Item</button>
                 */}
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