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
        return app.ajax(`/courses/my`)
            .then(data=>{
            receiveDate=data.data;
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


