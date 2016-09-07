
export default (state = {
    items: null,
}, action) => {
    switch(action.type) {
        case 'INIT_SEATS':
            return Object.assign({}, state, {
                items: action.items,
            })
        default:
            return state
    }
}

