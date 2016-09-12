export function initSeats(data) {
    // console.log(data)
    return {
        type: 'INIT_SEATS',
        items: data,
    }
}
export function setSeatsStatus(clickable=false) {
    // console.log(data)
    return {
        type: 'SET_SEATS_STATUS',
        clickable: clickable,
    }
}
export function destroySeats() {
    // console.log(data)
    return {
        type: 'DESTROY_SEATS',
        items: null,
    }
}

export function selectSeat(seat) {
    // console.log(seat)
    return {
        type: 'SELECT_SEAT',
        selectedItem: seat,
    }
}

export function selectRandomSeat(seat) {
    // console.log(seat)
    return {
        type: 'SELECT_RANDOM_SEAT',
    }
}


export const BOOK_SEAT_REQUEST = 'BOOK_SEAT_REQUEST'
export const BOOK_SEAT_SUCCESS = 'BOOK_SEAT_SUCCESS'
export const BOOK_SEAT_FAILURE = 'BOOK_SEAT_FAILURE'

function requestData(data) {
    return {
        type: BOOK_SEAT_REQUEST,
        data
    }
}
function receiveData(req, res) {
    // console.log(res)
    if(res.iRet==1){
        return {
            type: BOOK_SEAT_SUCCESS,
            info:res.info,
        }
    }else{
        return {
            type: BOOK_SEAT_FAILURE,
            info:res.info,
        }
    }
}



function fetchData(req) {
    return dispatch => {
        dispatch(requestData(req))
        return app.ajax('/courses/selectSeat',{
            data:req
        }).then(res=> {
            return dispatch(receiveData(req, res))
        });
        // return fetch(`/courses/selectSeat?${$.param(req)}`)
        //     .then(response=>{
        //         return response.json();
        //     }).then(json=>{
        //         return dispatch(receiveData(req, json))
        //     });
    }
}

function shouldFetchData(state, req) {
    const { seats } = state
    const {
        isBooking,
    } = seats

    if (isBooking){
        return false
    }
    return true
}

export function bookSeatIfNeeded(req) {
    // console.log(req)
    return (dispatch, getState) => {
        if (shouldFetchData(getState(), req)) {
            return dispatch(fetchData(req))
        }
    }
}