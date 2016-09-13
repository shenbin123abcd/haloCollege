
import CommonHeader from './Common.Header'


const App = ({ children,monthList,location,route }) => {
    // console.log(location)

    return (

    <div>
        <CommonHeader items={monthList} location={location}   />
        {children}
    </div>
)}

function mapStateToProps(state) {
    const { monthList} = state
    return {
        monthList,
    }
}

export default ReactRedux.connect(mapStateToProps)(App)