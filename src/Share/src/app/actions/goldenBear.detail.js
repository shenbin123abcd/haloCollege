function requestData(data) {
    return {
        type: 'REQUEST_GOLDENBEAR_DETAIL_DATA',
        data
    }
}

function receiveData(data) {
    return {
        type: 'RECEIVE_GOLDENBEAR_DETAIL_DATA',
        data
    }
}
function receiveDataError(data) {
    return {
        type: 'RECEIVE_GOLDENBEAR_DETAIL_ERROR',
        data
    }
}

function fetchData(req) {
    return dispatch => {
        dispatch(requestData('receive'));
        return  $.ajax({
            // url:`http://collegeapi-test.weddingee.com/v1/public/personalHomePage`,
            url:`${appConfig.apiBaseUrl}/v1/public/awardsHomePage`,
            data:req,
            dataType:'jsonp',
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
    const{goldenBearDetailData} = state;
    const{
        isFetching,
    } = goldenBearDetailData;
    if(isFetching){
        return false;
    }
    return true;
}

export function fetchGoldenBearDetailIfNeeded(req) {
    return (dispatch, getState)=> {
        if (shouldFetch(getState())) {
            return dispatch(fetchData(req))
        }
    }
}

export function destroyGoldenBearDetailData(data) {
    return{
        type:'DESTROY_GOLDENBEAR_DETAIL_DATA',
        data
    }
}
