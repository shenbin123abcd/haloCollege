import Index from './containers/Index'
import Detail from './containers/Detail'

import store from './store'

var Router=ReactRouter.Router;
var Route=ReactRouter.Route;
var Redirect=ReactRouter.Redirect;
var IndexRoute=ReactRouter.IndexRoute;
var browserHistory=ReactRouter.browserHistory;
var hashHistory=ReactRouter.hashHistory;
var Provider=ReactRedux.Provider;


// console.log(hashHistory)

let reactElement = document.getElementById('root');


let bodyClass='';
let htmlClass='';


const onUpdateRoute = () => {
    let path=hb.location.url('path');
    switch (true){
        case path=='/':
        case path=='':
            htmlClass=`index-html`;
            bodyClass=`index-body`;
            break;
        case path=='/login':
            htmlClass=`login-index`;
            bodyClass=`login-index`;
            break;
        //case path=='/user/collect':
        //    htmlClass=`collect-html`;
        //    bodyClass=`collect-body header-bar-body`;
        //    break;
        default:
            htmlClass=`user-html`;
            bodyClass=`user-body header-bar-body`;
            break;
    }
    $('html').addClass(htmlClass);
    $('body').addClass(bodyClass);
    
};
const onLeaveRoute = (prevState) => {
    $('html').removeClass(htmlClass);
    $('body').removeClass(bodyClass);
};


ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory} onUpdate={onUpdateRoute}>

        <Route path="/app/index"  component={Index}  onLeave={onLeaveRoute}  />
        <Route path="/app/detail"  component={Detail}  onLeave={onLeaveRoute}  />

    </Router>
  </Provider>,
    reactElement
);


