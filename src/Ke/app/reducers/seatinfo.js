import {
    REQUEST_SEAT_INFO, RECEIVE_SEAT_INFO
} from '../actions/seatinfo'



export default (state = {
    isFetching: false,
    // items: null,
}, action) => {
    switch(action.type) {
        case REQUEST_SEAT_INFO:
            return Object.assign({}, state, {
                isFetching: true,
                // items: null,
            })
        case RECEIVE_SEAT_INFO:
            return Object.assign({}, state, {
                isFetching: false,
                // items: action.items,
            })
        default:
            return state
    }
}

