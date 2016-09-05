import reducers from './reducers/index'

var thunkMiddleware =ReduxThunk.default


export default Redux.createStore(
    reducers,
    undefined,
    Redux.applyMiddleware(thunkMiddleware)
)