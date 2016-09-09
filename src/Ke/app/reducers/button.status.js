import {
    REQUEST_COURSE_STATUS, RECEIVE_COURSE_STATUS,
} from '../actions/buttonGroup'


export default (state = {
    isFetching: false,
    res: null,
}, action) => {
    switch(action.type) {
        case REQUEST_COURSE_STATUS:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_COURSE_STATUS:
            return Object.assign({}, state, {
                isFetching: false,
                res: action.res,
            })
        default:
            return state
    }
}
