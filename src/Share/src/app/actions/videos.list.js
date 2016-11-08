function requestData(data) {
    return{
        type:'REQUEST_VIDEOS_LIST_DATA',
        data
    }
}

function receiveData(data){
    return{
        type:'RECEIVE_VIDEOS_LIST_DATA',
        data
    }
}

function receiveDataError(data) {
    return {
        type:'RECEIVE_VIDEOS_LIST_DATA_ERROR',
        data
    }
}

function fetchData(req){
    return dispatch =>{
        dispatch(requestData());
        return $.ajax({
            // url:"http://collegeapi-test.weddingee.com/v1/public/personalHomePage",
            url:`${appConfig.apiBaseUrl}/v1/public/homeVideosList`,
            data:req,
            dataType:"jsonp",
            success:function(res){
                if(res.iRect = 1){
                    dispatch(receiveData(res));
                }else{
                    alert(res)
                }
            },
            error:function(error){
                alert('网络繁忙请稍后再试')
            }
        });
    }
}

function shouldFetch(state) {
    const{videosListData} = state;
    const{
        isFetching,
    } = videosListData;
    if(isFetching){
        return false;
    }
    return true;
}

export function fetchVideosListIfNeeded(req){
    return (dispatch, getState)=> {
        if (shouldFetch(getState())) {
            return dispatch(fetchData(req))
        }
    }
}