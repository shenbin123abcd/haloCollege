import {
    REQUEST_COURSE_STATUS, RECEIVE_COURSE_STATUS
} from '../actions/buttonGroup'


export default (state = {
    isFetching: false,
    res: null,
    showModal:false,

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
                showModal:action.showModal,
            })
        default:
            return state
    }
}
