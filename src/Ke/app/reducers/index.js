import todos from './todos'
import courseList from './course'
import monthList from './index.monthList'

import {
    REQUEST_POSTS, RECEIVE_POSTS
} from '../actions'




function postsByReddit(state ={
    isFetching: false,
    didInvalidate: false,
    items: ['12313']
}, action) {
    // console.log(state.get('items'))
    // state.get('items').push(554)
    switch (action.type) {
        case RECEIVE_POSTS:
            return Object.assign({}, state, {
                items: state.items.concat(234)
            })
        default:
            return state
    }
}

export default Redux.combineReducers({
    todos,
    courseList,
    postsByReddit,
    monthList,
})