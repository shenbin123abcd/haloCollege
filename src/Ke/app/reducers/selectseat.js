import {
    REQUEST_SELECT_SEAT, RECEIVE_SELECT_SEAT
} from '../actions/selectseat'



export default (state = {
    isFetching: false,
    course: null,
}, action) => {
    switch(action.type) {
        case REQUEST_SELECT_SEAT:
            return Object.assign({}, state, {
                isFetching: true,
                course: null,
            })
        case RECEIVE_SELECT_SEAT:
            return Object.assign({}, state, {
                isFetching: false,
                course: action.course,
            })
        default:
            return state
    }
}

