export const REQUEST_COURSE_STATUS='REQUEST_COURSE_STATUS'
export const RECEIVE_COURSE_STATUS='RECEIVE_COURSE_STATUS'

function requestStatusPosts(res){
    return {
        type:REQUEST_COURSE_STATUS,
        res
    }
}

export function receiveStatusPosts(id,res,showModal){
    return{
        type:RECEIVE_COURSE_STATUS,
        id,
        res,
        showModal
    }
}

function fetchCourseStatus(id){
    return dispatch => {
        dispatch(requestStatusPosts(id))
        return app.ajax(`/courses/applyStatus?course_id=${id}`)
            .then(res=>{
                dispatch(receiveStatusPosts(id,res.data,false))
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


