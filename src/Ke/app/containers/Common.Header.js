import Header from '../components/Common.Header'
import NavItem from '../components/Common.Header.NavItem'
import { fetchCourseIfNeeded,setCurrentMonth ,resetMonth} from '../actions'

var HeaderContainer = React.createClass({

    renderNavStatus(props){
        const { dispatch,monthList,location} = props
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
        this.renderNavStatus(this.props)

    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps',nextProps,this.props)
        this.renderNavStatus(nextProps)

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

export default ReactRedux.connect(mapStateToProps)(HeaderContainer)