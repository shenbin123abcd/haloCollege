import {
    REQUEST_SELECT_SEAT, RECEIVE_SELECT_SEAT
} from '../actions/selectseat'



export default (state = {
    isFetching: false,
    items: null,
}, action) => {
    switch(action.type) {
        case REQUEST_SELECT_SEAT:
            return Object.assign({}, state, {
                isFetching: true,
                items: null,
            })
        case RECEIVE_SELECT_SEAT:
            return Object.assign({}, state, {
                isFetching: false,
                items: action.users,
            })
        default:
            return state
    }
}

