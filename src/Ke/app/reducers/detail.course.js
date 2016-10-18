import {
    REQUEST_COURSE_DETAIL, RECEIVE_COURSE_DETAIL,GET_AGENTS,DESTROY_DETAIL_DATA
} from '../actions/detail'


export default (state = {
    isFetching: false,
    data: null,
    res:null
}, action) => {
    switch(action.type) {
        case REQUEST_COURSE_DETAIL:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_COURSE_DETAIL:
            return Object.assign({}, state, {
                isFetching: false,
                data: action.data,
            })
        case GET_AGENTS:
            return Object.assign({}, state, {
                res:action.res
            })
        case DESTROY_DETAIL_DATA:
            return Object.assign({}, state, {
                isFetching: false,
                data: null,
                res:null
            })

        default:
            return state
    }
}


