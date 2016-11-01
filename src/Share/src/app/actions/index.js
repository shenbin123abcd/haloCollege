

function requestData(data) {
    return {
        type: 'REQUEST_INDEX_DATA',
        data
    }
}

function receiveData(data) {
    return {
        type: 'RECEIVE_INDEX_DATA',
        data
    }
}
function receiveDataError(data) {
    return {
        type: 'RECEIVE_INDEX_DATA_ERROR',
        data
    }
}

function fetchData(req) {
    return dispatch => {
        dispatch(requestData('receive'));
        return  $.ajax({
            type:'GET',
            url:`/fake1?d=0`,
            data:req,
            dataType:'json',
            success: function(res) {
                if(res.iRet==1){
                    dispatch(receiveData(res));
                }else{
                    dispatch(receiveDataError(res.info));
                }
            },
            error: function(error) {
                dispatch(receiveDataError('网络繁忙请稍候再试'));
            }
        });
    }
}

function shouldFetch(state) {

    const { indexData } = state;
    const {
        isFetching,
        } = indexData;

    if (isFetching){
        return false
    }
    return true
}

export function fetchIndexIfNeeded(req) {
    return (dispatch, getState)=> {
        if (shouldFetch(getState())) {
            return dispatch(fetchData(req))
        }
    }
}

