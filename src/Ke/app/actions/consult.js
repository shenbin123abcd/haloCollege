export const REQUEST_CONSULT_ITEMS='REQUEST_CONSULT_ITEMS'
export const RECEIVE_CONSULT_ITEMS='RECEIVE_CONSULT_ITEMS'


var browserHistory=ReactRouter.browserHistory;

function requestConsultPosts(data){
    return{
        type:REQUEST_CONSULT_ITEMS,
        data,
    }
}

function receiveConsultPosts(data){
    return{
        type:RECEIVE_CONSULT_ITEMS,
        data,
    }
}

export function fetchPanel(id){
    return(dispatch,getState)=>{
        if(getState().consultData.isFetching==false){
            let data=getState().consultData.data;
            data.forEach((n,i)=>{
                if(id==n.id){
                    n.status=!n.status
                }
            })
            dispatch(receiveConsultPosts(data));
        };
    }
}

function fetchConsultItems(req){
    return (dispatch)=>{
        dispatch(requestConsultPosts(req))
        return app.ajax(`/courses/my`)
            .then((res)=>{
                if(res.iRet==1){
                    res.data=[
                            {
                                id:1,
                                q:'可以帮我预留名额吗',
                                a:'不可以',
                            },
                            {
                                id:2,
                                q:'潘珍玉老师的课程之后还会有吗，是什么时间',
                                a:'可以带走，但是器皿需要留下来哦~',
                            },
                            {
                                id:3,
                                q:'潘珍玉老师的课程之后还会有吗，是什么时间',
                                a:'可以带走，但是器皿需要留下来哦~',
                            },
                            {
                                id:4,
                                q:'潘珍玉老师的课程之后还会有吗，是什么时间',
                                a:'可以带走，但是器皿需要留下来哦~',
                            },
                        ];
                    res.data.forEach((n,i)=>{
                        _.assign({},n,{
                            status:false
                        })
                    })
                    dispatch(receiveConsultPosts(res.data));
                    dispatch(fetchPanel());
                }
            })
    }
}


function shouldFetching(state){
    const {consultData}=state;
    if(consultData.isFetching){
        return false
    }else{
        return true
    }
}

export function fetchConsiltItemsIfNeeded(req){
    return (dispatch,getState)=>{
        if(shouldFetching(getState())){
            dispatch(fetchConsultItems(req))
        }
    }
}




