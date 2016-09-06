import {
    SHOW_OPEN_CLASS, SHOW_TRAINING_CAMPS
} from '../actions/user'


export default (state = {
    class:[],
}, action) => {
    switch(action.type) {
        case SHOW_OPEN_CLASS:
            return Object.assign({}, state, {
                class: action.data,
            })
        case SHOW_TRAINING_CAMPS:
            return Object.assign({}, state, {
                class: action.data,
            })
        default:
            return state
    }
}


