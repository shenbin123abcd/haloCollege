import Detail from './modules/detail'
import Index from './modules/index'
import Todos from './components/todos'
import store from './store'

var Router=ReactRouter.Router
var Route=ReactRouter.Route
// var hashHistory=ReactRouter.hashHistory
var browserHistory=ReactRouter.browserHistory
var Provider=ReactRedux.Provider

let reactElement = document.getElementById('root')


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





ReactDOM.render(
  <Provider store={store}>
    <Router history={browserHistory}>
      <Route path="/" component={Index} />
      <Route path="/todos" component={Todos} />
      <Route path="/detail" component={Detail}/>
    </Router>
  </Provider>,
    reactElement
);


