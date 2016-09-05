import Detail from './modules/detail'
import store from './store'
import User from './modules/User'
import Index from './modules/index'


var Router=ReactRouter.Router
var Route=ReactRouter.Route
var browserHistory=ReactRouter.browserHistory
var Provider=ReactRedux.Provider



let reactElement = document.getElementById('root')



ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory}>
      <Route path="/" component={Index}    />
      <Route path="/detail" component={Detail}      />
      <Route path="/user" component={User}    />
    </Router>
  </Provider>,
    reactElement
);


