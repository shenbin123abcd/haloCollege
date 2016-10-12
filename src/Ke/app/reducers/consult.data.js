import {
    REQUEST_CONSULT_ITEMS, RECEIVE_CONSULT_ITEMS
} from '../actions/consult'

export default(state = {
    isFetching: false,
    data: null
}, action)=>{
    switch(action.type) {
        case REQUEST_CONSULT_ITEMS:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_CONSULT_ITEMS:
            return Object.assign({}, state, {
                isFetching: false,
                data: action.data,
            })
        default:
            return state
    }
}



