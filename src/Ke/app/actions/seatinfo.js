import {initSeats} from './common.seat'

export const REQUEST_SEAT_INFO = 'REQUEST_SEAT_INFO'
export const RECEIVE_SEAT_INFO = 'RECEIVE_SEAT_INFO'

function requestData(data) {
    return {
        type: REQUEST_SEAT_INFO,
        data
    }
}
function receiveData(req, res) {
    // console.log(res)
    return {
        type: RECEIVE_SEAT_INFO,
        index:req,
        items: res.data.seat,
        users: res.data.user,
    }
}



function fetchData(req) {
    return dispatch => {
        dispatch(requestData(req))
        return fetch(`/courses/seatInfo?${$.param({
            course_id: req
        })}`)
        .then(response=>{
            return response.json();
        }).then(json=>{
            dispatch(initSeats(json.data.seat))
            return dispatch(receiveData(req, json))
        });
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

export function fetchSeatInfoIfNeeded(req) {
    return (dispatch, getState) => {
        if (shouldFetchData(getState(), req)) {
            return dispatch(fetchData(req))
        }
    }
}