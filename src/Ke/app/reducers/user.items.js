import {
    RECEIVE_USER_ITEMS, REQUEST_USER_ITEMS
} from '../actions/user'


export default (state = {
    isFetching: false,
    list: [],
    user:{},
    filter:'SHOW_OPEN'
}, action) => {
    switch(action.type) {
        case REQUEST_USER_ITEMS:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_USER_ITEMS:
            return Object.assign({}, state, {
                isFetching: false,
                list:action.data.list,
                user:action.data.user,
                filter:action.filter
            })
        default:
            return state
    }
}


