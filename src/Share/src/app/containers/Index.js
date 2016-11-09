import {fetchIndexIfNeeded} from '../actions/index'
var hashHistory=ReactRouter.hashHistory;
var browserHistory=ReactRouter.browserHistory;
let Link=ReactRouter.Link;

class Index extends React.Component {
    constructor(props) {
        super(props);
        // this.state = {date: new Date()};
    }
    componentWillMount(){
        const { dispatch,location } = this.props;
        dispatch(fetchIndexIfNeeded({
            guest_id:148
        }));
    }
    componentDidMount(){
        // const { dispatch,location } = this.props;
        // dispatch(fetchIndexIfNeeded({
        //     id:location.query.id
        // }));
    }
    componentDidUpdate(){
        const { dispatch,location } = this.props;
        // console.log('componentDidUpdate')
    }
    render() {
        const { dispatch,location,indexData } = this.props;
        console.log(indexData)
        if(indexData.isFetching){
            return (
                <div>
                    loading..
                </div>
            )
        }else if(indexData.errorInfo){
            return (
                <div>
                    {indexData.errorInfo}
                </div>
            )
        }else if(indexData.data){
            return (
                <div>
                  index
                </div>
            )
        }else{
            return null
        }
    }
}


function mapStateToProps(state) {
    const {indexData}=state;
    return {
        indexData,
    }
}

export default ReactRedux.connect(mapStateToProps)(Index)


