function requestData(data) {
    return{
        type:'REQUEST_COMPANY_DETAIL_DATA',
        data
    }
}

function receiveData(data){
    return{
        type:'RECEIVE_COMPANY_DETAIL_DATA',
        data
    }
}

function receiveDataError(data) {
    return {
        type:'RECEIVE_COMPANY_DETAIL_DATA_ERROR',
        data
    }
}

function fetchData(req){
    return dispatch =>{
        dispatch(requestData());
        return $.ajax({
            // url:"http://collegeapi-test.weddingee.com/v1/public/personalHomePage",
            url:`${appConfig.apiBaseUrl}/v1/public/companyHomePage`,
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
    const{companyDetailData} = state;
    const{
        isFetching,
    } = companyDetailData;
    if(isFetching){
        return false;
    }
    return true;
}

export function fetchCompanyDetailIfNeeded(req){
    return (dispatch, getState)=> {
        if (shouldFetch(getState())) {
            return dispatch(fetchData(req))
        }
    }
}


export function destroyCompanyDetailData(data) {
    return{
        type:'DESTROY_COMPANY_DETAIL_DATA',
        data
    }
}