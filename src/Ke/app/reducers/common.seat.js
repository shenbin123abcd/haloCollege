
export default (state = {
    items: null,
    clickable: false,
    selectedItem: null,
}, action) => {
    switch(action.type) {
        case 'INIT_SEATS':
            return Object.assign({}, state, {
                items: action.items,
            })
        case 'SET_SEATS_STATUS':
            return Object.assign({}, state, {
                clickable: action.clickable,
            })
        case 'DESTROY_SEATS':
            return Object.assign({}, state, {
                items: null,
            })
        case 'SELECT_SEAT':
            return Object.assign({}, state, {
                selectedItem: action.selectedItem,
            })
        default:
            return state
    }
}

