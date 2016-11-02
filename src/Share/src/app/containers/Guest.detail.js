import CommonHeader from './Common.header'


var Detail = React.createClass({
    componentDidMount:function(){
        const { dispatch,location } = this.props;
    },


    render: function() {

        return (
            <div>
                <CommonHeader title="主页" />
                guest detail
            </div>
        )
    }
});

function mapStateToProps(state) {
    const {}=state;
    return {

    }
}

export default ReactRedux.connect(mapStateToProps)(Detail)


