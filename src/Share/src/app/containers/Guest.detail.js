import CommonHeader from './Common.header'

class Detail extends React.Component{
    componentDidMount(){
        const { dispatch,location } = this.props;
    }

    render() {

        return (
            <div>
                <CommonHeader title="主页" />
                guest detail
            </div>
        )
    }
}

function mapStateToProps(state) {
    const {}=state;
    return {

    }
}

export default ReactRedux.connect(mapStateToProps)(Detail)


