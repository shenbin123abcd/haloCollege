export const REQUEST_USER_ITEMS='REQUEST_USER_ITEMS'
export const RECEIVE_USER_ITEMS='RECEIVE_USER_ITEMS'
export const SHOW_OPEN_CLASS='SHOW_OPEN_CLASS'
export const SHOW_TRAINING_CAMPS='SHOW_TRANING_CAMPS'

var receiveDate='';

function requestUserPosts(data){
    return{
        type:REQUEST_USER_ITEMS,
        data,
    }
}

export function receiveUserPosts(filter="SHOW_OPEN"){
    return{
        type:RECEIVE_USER_ITEMS,
        data:receiveDate,
        filter
    }
}

function fetchUserItems(req){
    return dispatch=>{
        dispatch(requestUserPosts(req))

        return fetch(`/courses`,{
            method:"POST",
            body:JSON.stringify({
                id:req
            })
        }).then(res=>{
            return res.json()
        }).then(data=>{
            data=[
                {
                    name:'蔡上',
                    title:'adas',
                    date:'2016.09.16',
                    position:'上海',
                    seat:'3排2座',
                    type:'public',
                },
                {
                    name:'蔡上2',
                    title:'adas2',
                    date:'2016.09.16',
                    position:'上海',
                    seat:'3排2座',
                    type:'public',
                },
                {
                    name:'蔡上3',
                    title:'adas3',
                    date:'2016.09.16',
                    position:'上海',
                    seat:'3排2座',
                    type:'public',
                },
                {
                    name:'蔡上3',
                    title:'adas3',
                    date:'2016.09.16',
                    position:'上海',
                    seat:'3排2座',
                    type:'peixun',
                },
            ];

            receiveDate=data
            dispatch(receiveUserPosts());
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


