import App from './components/App'
import Detail from './modules/detail'
import store from './store'
import Todos from './components/todos'
import User from './modules/User'
import Index from './modules/index'



// The Reducer Function
// var userReducer = function(state = [], action) {
//   if (action.type === 'ADD_USER') {
//     var newState = state.concat([action.user]);
//     return newState;
//   }
//   return state;
// };


// Create a store by passing in the reducer
// var store = Redux.createStore(userReducer);


//ReactDOM.render(
//  <App />,
//  document.getElementById('root')
//);

var Router=ReactRouter.Router
var Route=ReactRouter.Route
var hashHistory=ReactRouter.hashHistory
var browserHistory=ReactRouter.browserHistory
var Provider=ReactRedux.Provider


let reactElement = document.getElementById('root')

ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory}>
      <Route path="/" component={Index} />
      <Route path="/detail" component={Detail}/>
      <Route path="/user" component={User}/>
    </Router>
  </Provider>,
    reactElement
);


