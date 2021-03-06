import Detail from './containers/detail'
import store from './store'
import User from './containers/User'
import Index from './containers/index'
import Seatinfo from './containers/seatinfo'
import Selectseat from './containers/selectseat'
import Consult from './containers/Consult'
import App from './containers/App'
import Branding from './containers/Branding'


var Router=ReactRouter.Router
var Route=ReactRouter.Route
var Redirect=ReactRouter.Redirect
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
        case path=='/course/index':
            htmlClass=`html-index`
            bodyClass=`body-index`
            break;
        case path.indexOf('/course/detail')>-1:
            htmlClass=`html-detail`
            bodyClass=`body-detail`
            break;
        case path.indexOf('/course/user')>-1:
            htmlClass=`html-user`
            bodyClass=`body-user`
            break;
        case path=='/course/consult':
            htmlClass=`html-consult`
            bodyClass=`body-consult`
            break;
        case path=='/course/branding':
            htmlClass=`html-branding`
            bodyClass=`body-branding`
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


browserHistory.listen(function (ev) {
    // console.log(ev)
    var partUrl=`${hb.location.url('path')}${hb.location.url('query')||''}${hb.location.url('hash')||''}`;
    // console.log(partUrl);
    _hmt.push(['_trackPageview', partUrl]);
})
ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory} onUpdate={onUpdateRoute}>
        <Redirect from="/" to="/course/rIndex" />
        <Redirect from="/course/" to="/course/index" />
        <Route path="/course/" component={App}   >
            <Route path="/"  component={Index}  onLeave={onLeaveRoute}  />
            <Route path="index"  component={Index}   onLeave={onLeaveRoute}  />
            <Route path="user" component={User}   onLeave={onLeaveRoute}  />
            <Route path="detail_:id" component={Detail}    onLeave={onLeaveRoute}  />
            <Route path="rIndex"  onEnter={e=>{window.location.href='/course/index'}}  />
            <Route path="consult" component={Consult}   onLeave={onLeaveRoute}  />
        </Route>

        <Route path="/course/seatinfo_:id" component={Seatinfo}     onLeave={onLeaveRoute}  />
        <Route path="/course/selectseat_:id" component={Selectseat}    onLeave={onLeaveRoute}  />

        <Route path="/course/branding" component={Branding}     onLeave={onLeaveRoute}  />

        <Redirect from="/course/*" to="/course/index" />
        <Redirect from="*" to="/course/rIndex" />
    </Router>
  </Provider>,
    reactElement
);


