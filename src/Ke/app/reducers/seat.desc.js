import {
    RECEIVE_SEAT_DESC
} from '../actions/buttonGroup'

export default (state = {
    data: null
}, action) => {
    switch(action.type) {
        case RECEIVE_SEAT_DESC:
            return Object.assign({}, state, {
                data: action.data,
            })
        default:
            return state
    }
}
