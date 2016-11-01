import reducers from './reducers/app'
var thunkMiddleware =ReduxThunk.default


export default Redux.createStore(
    reducers,
    undefined,
    Redux.applyMiddleware(thunkMiddleware)
)