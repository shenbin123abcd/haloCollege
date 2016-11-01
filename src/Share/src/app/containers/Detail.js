

var hashHistory=ReactRouter.hashHistory;
var browserHistory=ReactRouter.browserHistory;
let Link=ReactRouter.Link;



var CSSTransitionGroup = React.addons.CSSTransitionGroup;

var Index = React.createClass({

    componentDidMount:function(){
        const { dispatch,location } = this.props;
    },

    render: function() {

        return (
            <div>
                dddd
            </div>
        )
    }
});

function mapStateToProps(state) {
    const {}=state;
    return {

    }
}

export default ReactRedux.connect(mapStateToProps)(Index)


