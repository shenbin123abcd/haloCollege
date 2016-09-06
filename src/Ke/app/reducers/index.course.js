import {
    REQUEST_COURSE, RECEIVE_COURSE
} from '../actions'


export default (state = {
    isFetching: false,
    items: null
}, action) => {
    switch(action.type) {
        case REQUEST_COURSE:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_COURSE:
            return Object.assign({}, state, {
                isFetching: false,
                items: action.items,
            })
        default:
            return state
    }
}

