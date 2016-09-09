import {initSeats} from './common.seat'

export const REQUEST_SELECT_SEAT = 'REQUEST_SELECT_SEAT'
export const RECEIVE_SELECT_SEAT = 'RECEIVE_SELECT_SEAT'

function requestData(data) {
    return {
        type: REQUEST_SELECT_SEAT,
        data
    }
}
function receiveData(req, res) {
    // console.log(res)
    return {
        type: RECEIVE_SELECT_SEAT,
        index:req,
        items: res.data.seat,
        course: res.data.course,
    }
}


function fetchData(req) {
    return dispatch => {
        dispatch(requestData(req))

        return app.ajax('/courses/seat',{
            data:{
                course_id: req
            }
        }).then(res=> {
            dispatch(initSeats(res.data.seat))
            return dispatch(receiveData(req, res))
        });


        // return fetch(`/courses/seat?${$.param({
        //     course_id: req
        // })}`)
        // .then(response=>{
        //     return response.json();
        // }).then(json=>{
        //     dispatch(initSeats(json.data.seat))
        //     return dispatch(receiveData(req, json))
        // });
    }
}

function shouldFetchData(state, req) {
    const { seats } = state
    const {
        isFetching,
    } = seats

    if (isFetching){
        return false
    }
    return true
}

export function fetchSelectSeatIfNeeded(req) {
    return (dispatch, getState) => {
        if (shouldFetchData(getState(), req)) {
            return dispatch(fetchData(req))
        }
    }
}