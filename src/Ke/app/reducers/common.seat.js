import {
    REQUEST_SEAT, RECEIVE_SEAT
} from '../actions/common.seat'


export default (state = {
    isFetching: false,
    items: null,
}, action) => {
    switch(action.type) {
        case REQUEST_SEAT:
            return Object.assign({}, state, {
                isFetching: true,
                items: null,
            })
        case RECEIVE_SEAT:
            return Object.assign({}, state, {
                isFetching: false,
                items: action.items,
            })
        default:
            return state
    }
}

