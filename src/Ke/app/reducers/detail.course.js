import {
    REQUEST_COURSE_DETAIL,
    RECEIVE_COURSE_DETAIL,
    GET_AGENTS,
    DESTROY_DETAIL_DATA,
    TIME_OUT_START,
    TIME_OUT_OVER
} from '../actions/detail'


export default (state = {
    isFetching: false,
    data: null,
    res:null,

    start_time:'',
    d:'no',
    h:'no',
    m:'no',
    s:'no',

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
        case TIME_OUT_START:
            let start_time=action.time;
            const ts = (start_time-new Date().getTime())/1000;
            if(ts>0) {
                var d = parseInt(ts / 86400, 10);
                var h = parseInt(ts / 3600 % 24, 10);
                var m = parseInt(ts / 60 % 60, 10);
                var s = parseInt(ts % 60, 10);
                return Object.assign({}, state, {
                    d,
                    h,
                    m,
                    s,
                    start_time:action.time
                })
            }else{
                return Object.assign({}, state, {
                    d:'no',
                    h:'no',
                    m:'no',
                    s:'no',
                    start_time:''
                })
            }
        case TIME_OUT_OVER:
            return _.assign({
                start_time:'',
                d:'no',
                h:'no',
                m:'no',
                s:'no',
            })

        default:
            return state
    }
}


