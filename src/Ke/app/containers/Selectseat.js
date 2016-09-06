

var Selectseat = React.createClass({
    componentDidMount() {
        document.title='座位表';

    },

    render() {

        return (
            <div>
                Selectseat
            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    return{
        
    }
}

export default ReactRedux.connect(mapStateToProps)(Selectseat)