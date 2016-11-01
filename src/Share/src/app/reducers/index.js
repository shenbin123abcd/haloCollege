

export default (state = {
    isFetching: false,
    data: null,
    errorInfo: null,
}, action) => {
    switch(action.type) {
        case 'REQUEST_INDEX_DATA':
            return _.assign({}, state, {
                isFetching: true,
            })
        case 'RECEIVE_INDEX_DATA':
            // console.log(action)
            return _.assign({}, state, {
                isFetching: false,
                data: action.data.data,
            })
        case 'RECEIVE_INDEX_DATA_ERROR':
            // console.log(action)
            return _.assign({}, state, {
                isFetching: false,
                errorInfo: action.data,
            })
        default:
            return state
    }
}


