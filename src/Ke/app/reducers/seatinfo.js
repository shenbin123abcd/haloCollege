import {
    REQUEST_SEAT_INFO, RECEIVE_SEAT_INFO
} from '../actions/seatinfo'



export default (state = {
    isFetching: false,
    items: null,
    course: null,
}, action) => {
    switch(action.type) {
        case REQUEST_SEAT_INFO:
            return Object.assign({}, state, {
                isFetching: true,
                items: null,
                course: null,
            })
        case RECEIVE_SEAT_INFO:
            return Object.assign({}, state, {
                isFetching: false,
                items: action.users,
                course: action.course,
            })
        default:
            return state
    }
}

