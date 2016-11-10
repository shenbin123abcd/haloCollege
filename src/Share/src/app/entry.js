import Index from './containers/Index'
import Detail from './containers/Guest.detail'
import Company from './containers/Company.detail'
import ArticlesList from './containers/Articles.list'
import VideosList from './containers/Videos.list'
import GoldenBear from './containers/GoldenBear.detail'
import GoldenBearVideosList from './containers/GoldenBearVideos.list'
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
        <Route path="/"  component={Index}  onLeave={onLeaveRoute}  />
        <Route path="/share/guests/guest/:id"  component={Detail}  onLeave={onLeaveRoute}  />
        <Route path="/share/companies/company/:id"  component={Company}  onLeave={onLeaveRoute}  />
        <Route path="/share/articlesList/:id/:type"  component={ ArticlesList }  onLeave={onLeaveRoute}  />
        <Route path="/share/videosList/:id/:type"  component={VideosList}  onLeave={onLeaveRoute}  />
        <Route path="/share/goldenBears/goldenBear/:id"  component={GoldenBear}  onLeave={onLeaveRoute}  />
        <Route path="/share/goldBearsVideosList/:id/:type/:level"  component={GoldenBearVideosList}  onLeave={onLeaveRoute}  />
    </Router>
  </Provider>,
    reactElement
);


