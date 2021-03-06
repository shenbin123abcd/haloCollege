export const REQUEST_USER_ITEMS='REQUEST_USER_ITEMS'
export const RECEIVE_USER_ITEMS='RECEIVE_USER_ITEMS'
export const SHOW_OPEN_CLASS='SHOW_OPEN_CLASS'
export const SHOW_TRAINING_CAMPS='SHOW_TRANING_CAMPS'

var receiveDate='';
var browserHistory=ReactRouter.browserHistory;

function requestUserPosts(data){
    return{
        type:REQUEST_USER_ITEMS,
        data,
    }
}

export function receiveUserPosts(filter){
    return{
        type:RECEIVE_USER_ITEMS,
        data:receiveDate,
        filter
    }
}

function fetchUserItems(req){
    return dispatch=>{
        dispatch(requestUserPosts(req))
        let params=hb.location.url("?wechat_code");
        if(params){
            return $.ajax({
                url:'/courses/setLogin',
                data:{
                    wechat_code:params
                }
            }).then(res=>{
                return getMyInfo()
            })

        }else{
            return getMyInfo()
        }
        
        function getMyInfo() {
            return $.ajax(`/courses/my`)
                .then(data=>{
                    receiveDate=data.data;
                    // console.log(receiveDate)
                    if(data.iRet==-1&&Modernizr.weixin){
                        window.location.href=data.data;
                    }
                    let id = hb.location.url('?cate_id');
                    if(id&&id==2){
                        return dispatch(receiveUserPosts('SHOW_TRAINING_CAMP'));
                    }else if(id&&id==1){
                        return dispatch(receiveUserPosts('SHOW_OPEN'));
                    }else{
                        browserHistory.push(`/course/user?cate_id=1`);
                        return dispatch(receiveUserPosts('SHOW_OPEN'));
                    }

                })
        }

    }
}


function shouldFetching(state){
    const {userItems}=state
    if(userItems.isFetching){
        return false
    }else{
        return true
    }
}

export function fetchUserItemsIfNeeded(req){
    return (dispatch,getState)=>{
        //console.log(getState())
        if(shouldFetching(getState())){
            dispatch(fetchUserItems(req))
        }
    }
}


