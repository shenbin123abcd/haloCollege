export const REQUEST_COURSE_DETAIL='REQUEST_COURSE_DETAIL'
export const RECEIVE_COURSE_DETAIL='RECEIVE_COURSE_DETAIL'
export const GET_AGENTS='GET_AGENTS'
export const DESTROY_DETAIL_DATA='DESTROY_DETAIL_DATA'
function requestDetailPosts(data){
    return {
        type:REQUEST_COURSE_DETAIL,
        data
    }
}

function receiveDetailPosts(req,data){
    return{
        type:RECEIVE_COURSE_DETAIL,
        req,
        data
    }
}

function getAgents(res){
    return{
        type:GET_AGENTS,
        res,
    }
}

export function initial(){
    return{
        type:DESTROY_DETAIL_DATA,
        items: null,
    }
}

function fetchCourseDetail(req) {
    return dispatch => {
        dispatch(requestDetailPosts(req))
        return app.ajax(`/courses/detail?id=${req}`)
            .then(res=>{
                app.ajax(`/courses/getAgents`)
                    .then(res2=>{
                        dispatch(getAgents(res2))
                        dispatch(receiveDetailPosts(req,res))
                    })

            });
    }
}

function shouldFetchCourseDetail(state) {
    const { courseDetail } = state
    if (courseDetail.isFetching){
        return false
    }
    return true
}

export function fetchCourseDetailIfNeeded(req) {
    return (dispatch, getState ) => {
        if (shouldFetchCourseDetail(getState())) {
            return dispatch(fetchCourseDetail(req))
        }
    }
}

