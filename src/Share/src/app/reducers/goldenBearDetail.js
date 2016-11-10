export default (state = {
    isFetching: false,
    data: null,
}, action) => {
    switch(action.type) {
        case 'REQUEST_GOLDENBEAR_DETAIL_DATA':
            return _.assign({}, state, {
                isFetching: true,
            });
        case 'RECEIVE_GOLDENBEAR_DETAIL_DATA':
            // console.log(action)
            return _.assign({}, state, {
                isFetching: false,
                data: action.data.data,
            });
        case 'RECEIVE_GOLDENBEAR_DETAIL_ERROR':
            // console.log(action)
            return _.assign({}, state, {
                isFetching: false,
                errorInfo: action.data,
            });
        case 'DESTROY_GOLDENBEAR_DETAIL_DATA':
            // console.log(action)
            return _.assign({}, state, {
                isFetching: false,
                data: null,
            });
        default:
            return state
    }
}


