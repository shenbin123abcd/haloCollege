export const REQUEST_USER_ITEMS='REQUEST_USER_ITEMS'
export const RECEIVE_USER_ITEMS='RECEIVE_USER_ITEMS'

function requestUserPosts(data){
    return{
        type:REQUEST_USER_ITEMS,
        data
    }
}

function receiveUserPosts(req,data){
    return{
        type:RECEIVE_USER_ITEMS,
        req,
        data,
    }
}

function fetchUserItems(req){
    return dispatch=>{
        dispatch(requestUserPosts(req))

        return fetch(`/course`,{
            method:"POST",
            body:JSON.stringify({
                id:req
            })
        }).then(res=>{
            return res.json()
        }).then(data=>{
            return dispatch(receiveUserPosts(req,data))
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

export default function(req){
    return (dispatch,getState)=>{
        if(shouldFetching(getState())){
            dispatch(fetchUserItems(req))
        }
    }
}


