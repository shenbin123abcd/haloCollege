import CourseItem from '../components/Index.CourseItem'
import Course from '../components/Index.Course'
// import { fetchCourseIfNeeded,setCurrentMonth } from '../actions'
var ReactCSSTransitionGroup = React.addons.CSSTransitionGroup


var Index = React.createClass({
    componentDidMount() {
        document.title='幻熊研习社';
        if(Modernizr.weixin&&Modernizr.ios){
            hb.hack.setTitle(document.title);
        }
        const { dispatch,monthList,location,isFetching} = this.props;
    },
    formatContent(arr){
        let str='';
        arr.forEach((n,i)=>{
            str+=`,${app.util.formatTitle(n.title)}`;
        });
        return str.substring(1);
    },
    componentWillReceiveProps : function(nextProps) {
        const { monthList} = this.props;
        // console.log(1,this.props.isFetching)
        // console.log(2,nextProps.isFetching)
        // console.log('componentWillReceiveProps',nextProps,this.props)
        // const { dispatch,monthList,location} = nextProps
        var shareMonth=''

        if(nextProps.items){
            if(!nextProps.location.query.month){
                shareMonth=monthList[0].month
            }else{
                shareMonth=nextProps.location.query.month.substring(4,6)
            }
            if(!this.props.items){
                if(nextProps.items.length==0){
                    app.wechat.init();
                }else{
                    app.wechat.init({
                        title: `婚礼行业全新课程体系 幻熊研习社独家授权`,
                        content: `${Number(shareMonth)}月份课程大纲：${this.formatContent(nextProps.items)}`,
                        link : window.location.href,
                    });
                }
            }else if(this.props.receivedAt!=nextProps.receivedAt){
                if(nextProps.items.length==0){
                    app.wechat.init();
                }else{
                    app.wechat.init({
                        title: `婚礼行业全新课程体系 幻熊研习社独家授权`,
                        content: `${Number(shareMonth)}月份课程大纲：${this.formatContent(nextProps.items)}`,
                        link : window.location.href,
                    });
                }
            }
        }
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
        items,
        receivedAt,
    } = courseList

    return {
        isFetching,
        items,
        monthList,
        receivedAt,
    }
}

export default ReactRedux.connect(mapStateToProps)(Index)