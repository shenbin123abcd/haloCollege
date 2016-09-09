export const REQUEST_COURSE_STATUS='REQUEST_COURSE_STATUS'
export const RECEIVE_COURSE_STATUS='RECEIVE_COURSE_STATUS'

function requestStatusPosts(res){
    return {
        type:REQUEST_COURSE_STATUS,
        res
    }
}

export function receiveStatusPosts(id,res){
    return{
        type:RECEIVE_COURSE_STATUS,
        id,
        res,
    }
}

function fetchCourseStatus(id){
    return dispatch => {
        dispatch(requestStatusPosts(id))
        return fetch(`/courses/applyStatus?course_id=${id}`)
            .then(response=>{
                return response.json();
            }).then(res=>{
                dispatch(receiveStatusPosts(id,res.data))
            });
    }
}

function shouldFetchCourseStatus(state) {
    const { courseStatus } = state
    if (courseStatus.isFetching){
        return false
    }
    return true
}

export function fetchCourseStatusIfNeeded(req) {
    return (dispatch, getState ) => {
        if (shouldFetchCourseStatus(getState())) {
            return dispatch(fetchCourseStatus(req))
        }
    }
}


