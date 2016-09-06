
export const REQUEST_SEAT = 'REQUEST_SEAT'
export const RECEIVE_SEAT = 'RECEIVE_SEAT'

function requestData(data) {
    return {
        type: REQUEST_SEAT,
        data
    }
}
function receiveData(req, res) {
    // console.log(res)
    return {
        type: RECEIVE_SEAT,
        index:req,
        items: res.data.seats,
    }
}

function fetchData(req) {
    return dispatch => {
        dispatch(requestData(req))
        return fetch(`http://localhost:1234`)
            .then(response=>{
                return response.json();
            }).then(json=>{
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

export function fetchSeatIfNeeded(req) {
    return (dispatch, getState) => {
        if (shouldFetchData(getState(), req)) {
            return dispatch(fetchData(req))
        }
    }
}