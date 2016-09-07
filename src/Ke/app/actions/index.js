export function addTodo(todo){
  return {
    type: 'addTodo',
    todo
  }
}

export function deleteTodo(index){
  return {
    type: 'deleteTodo',
    index
  }
}


export const REQUEST_COURSE = 'REQUEST_COURSE'
export const RECEIVE_COURSE = 'RECEIVE_COURSE'

function requestPosts(data) {
  return {
    type: REQUEST_COURSE,
    data
  }
}

function receiveCourse(req, res) {
  // console.log(res)
  return {
    type: RECEIVE_COURSE,
    index:req,
    items: res.data,
    receivedAt: '12345'
  }
}

function fetchCourse(req) {
  return dispatch => {
    dispatch(requestPosts(req))
    return fetch(`/courses?${$.param({
      month: req
    })}`)
      .then(response=>{
         return response.json();
      }).then(json=>{
         return dispatch(receiveCourse(req, json))
      });
  }
}
function shouldFetchCourse(state, req) {
  const { courseList } = state
  const {
      isFetching,
      items
  } = courseList

  if (isFetching){
    return false
  }
  return true
}

export function fetchCourseIfNeeded(req) {
  return (dispatch, getState) => {
    // console.log(shouldFetchCourse(getState(), req))
    if (shouldFetchCourse(getState(), req)) {
      return dispatch(fetchCourse(req))
    }
  }
}


export function setCurrentMonth(data){
  // console.log(data)
  return {
    type: 'setCurrentMonth',
    data
  }
}


