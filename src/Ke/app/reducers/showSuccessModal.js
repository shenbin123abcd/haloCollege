import {
    SHOW_MODAL_STATUS
} from '../actions/buttonGroup'

export default (state = {
    val: false
}, action) => {
    switch(action.type) {
        case SHOW_MODAL_STATUS:
            return Object.assign({}, state, {
                val: action.val,
            })
        default:
            return state
    }
}
