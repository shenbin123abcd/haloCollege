import {
    REQUEST_COURSE_DETAIL, RECEIVE_COURSE_DETAIL
} from '../actions/detail'


export default (state = {
    isFetching: false,
    data: null
}, action) => {
    switch(action.type) {
        case REQUEST_COURSE_DETAIL:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_COURSE_DETAIL:
            return Object.assign({}, state, {
                isFetching: false,
                data: action.data,
            })
        default:
            return state
    }
}


