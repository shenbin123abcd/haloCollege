import {
    REQUEST_COURSE_STATUS, RECEIVE_COURSE_STATUS,TIME_OUT_START,
    TIME_OUT_OVER
} from '../actions/buttonGroup'


export default (state = {
    isFetching: false,
    res: null,
    showModal:false,

    start_time:'',
    d:'no',
    h:'no',
    m:'no',
    s:'no',

}, action) => {
    switch(action.type) {
        case REQUEST_COURSE_STATUS:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_COURSE_STATUS:
            return Object.assign({}, state, {
                isFetching: false,
                res: action.res,
                showModal:action.showModal,
            })
        case TIME_OUT_START:
            //console.log(action);
            let start_time=action.time;
            const ts = (start_time-new Date().getTime())/1000;
            if(ts>0) {
                var d = parseInt(ts / 86400, 10);
                var h = parseInt(ts / 3600 % 24, 10);
                var m = parseInt(ts / 60 % 60, 10);
                var s = parseInt(ts % 60, 10);
                //console.log(d,h,m,s);
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
