import Detail from './containers/detail'
import store from './store'
import User from './containers/User'
import Index from './containers/index'
import Seatinfo from './containers/seatinfo'
import Selectseat from './containers/selectseat'
import App from './containers/App'


var Router=ReactRouter.Router
var Route=ReactRouter.Route
var IndexRoute=ReactRouter.IndexRoute
var browserHistory=ReactRouter.browserHistory
var Provider=ReactRedux.Provider




let reactElement = document.getElementById('root')


let bodyClass=''
let htmlClass=''


const onUpdateRoute = () => {
    let path=hb.location.url('path')
    // console.log(path,path.indexOf('/user')>-1)
    switch (true){
        case path=='':
            htmlClass=`html-index`
            bodyClass=`body-index`
            break;
        case path.indexOf('/detail')>-1:
            htmlClass=`html-detail`
            bodyClass=`body-detail`
            break;
        case path.indexOf('/user')>-1:
            htmlClass=`html-user`
            bodyClass=`body-user`
            break;
        default:
            htmlClass=`html`
            bodyClass=`body`
    }
    $('html').addClass(htmlClass)
    $('body').addClass(bodyClass)
    
}
const onLeaveRoute = (prevState) => {
    $('html').removeClass(htmlClass)
    $('body').removeClass(bodyClass)
}


ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory} onUpdate={onUpdateRoute}>
        <Route path="/course" component={App}   >
            <IndexRoute component={Index}  onLeave={onLeaveRoute} />
            <Route path="/user" component={User}   onLeave={onLeaveRoute}  />
            <Route path="/detail_:id" component={Detail}    onLeave={onLeaveRoute}  />
        </Route>
        
        <Route path="/course/seatinfo_:id" component={Seatinfo}    onLeave={onLeaveRoute}  />
        <Route path="/course/selectseat_:id" component={Selectseat}    onLeave={onLeaveRoute}  />

    </Router>
  </Provider>,
    reactElement
);


