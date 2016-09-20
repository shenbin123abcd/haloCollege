
import CommonHeader from './Common.Header'
const App = React.createClass({
    render: function() {
        // console.log(this.props)
        let { children,monthList,location,route,history }=this.props
        return (

            <div>
                <CommonHeader items={monthList} location={location} history={history}  />
                {
                    children
                }
            </div>
        )
    }
});




function mapStateToProps(state) {
    const { monthList} = state
    return {
        monthList,
    }
}

export default ReactRedux.connect(mapStateToProps)(App)

