import Header from '../components/Common.Header'
import NavItem from '../components/Common.Header.NavItem'
import { fetchCourseIfNeeded,setCurrentMonth ,resetMonth} from '../actions'


var withRouter=ReactRouter.withRouter
var HeaderContainer = React.createClass({
    unHandlerUrlChange(){

    },
    handlerUrlChange(ev){
        const { dispatch,monthList,location} = this.props
        // console.log(ev)
        var item;
        if(ev.pathname=='/'){
            if(ev.query.month){
                dispatch(fetchCourseIfNeeded(`${ev.query.month}`));
                item=monthList.filter(n=>(n.year.toString()+n.month.toString())==ev.query.month)[0]
                if(!item){
                    item={
                        year:ev.query.month.substring(0,4),
                        month:ev.query.month.substring(4,6),
                    }
                }
                dispatch(setCurrentMonth(item));
            }else{
                item=monthList[0]
                dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
                dispatch(setCurrentMonth(item));
            }
            // console.log('item',`${item.year}${item.month}`)
        }else if(ev.pathname.indexOf('/course/detail_')>-1){


        }else{
            if(monthList.filter(n=>n.active).length>0){
                dispatch(resetMonth())
            }
        }
    },
    renderNavStatus(props){
        const { dispatch,monthList,location,history} = props
        // console.log(location)
        var item;
        if(location.pathname=='/'){
            if(location.query.month){
                item=monthList.filter(n=>(n.year.toString()+n.month.toString())==location.query.month)[0]
            }else{
                item=monthList[0]
            }

            if(item!=monthList.filter(n=>n.active)[0]){
                dispatch(setCurrentMonth(item));
                dispatch(fetchCourseIfNeeded(`${item.year}${item.month}`));
            }

        }else{
            if(monthList.filter(n=>n.active).length>0){
                dispatch(resetMonth())
            }
        }
    },

    componentDidMount() {
        // this.renderNavStatus(this.props)
        const { dispatch,monthList,location} = this.props

        // console.log(this.props)

        this.unHandlerUrlChange=this.props.router.listen(this.handlerUrlChange)

    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps',nextProps,this.props)
        // this.renderNavStatus(nextProps)

    },
    componentWillUnmount(nextProps){
        this.unHandlerUrlChange()
    },
    renderNav:function (item, i) {
        return (
            <NavItem item={item}  key={i}  />
        )
    },

    render: function() {
        let {monthList}=this.props;
        return (
            <Header items={monthList} renderItem={this.renderNav} />
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { monthList } = state

    return {
        monthList,
    }
}



export default ReactRedux.connect(mapStateToProps)(withRouter(HeaderContainer))