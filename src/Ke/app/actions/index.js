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

export function showCourseList(data){
  // console.log(data)
  return {
    type: 'showCourseList',
    data
  }
}

export function getCourseList(data){
  // console.log(data)
  return {
    type: 'getCourseList',
    data
  }
}

export function setCurrentMonth(data){
  // console.log(data)
  return {
    type: 'setCurrentMonth',
    data
  }
}





export const REQUEST_POSTS = 'REQUEST_POSTS'
export const RECEIVE_POSTS = 'RECEIVE_POSTS'

function requestPosts(data) {
  return {
    type: REQUEST_POSTS,
    data
  }
}

function receivePosts(data, json) {
  return {
    type: RECEIVE_POSTS,
    data,
    json,
    posts: 'pppppp',
    receivedAt: '12345'
  }
}

function fetchPosts(reddit) {
  // console.log(reddit)
  return dispatch => {
    // dispatch(requestPosts(reddit))
    return fetch(`/course`)
        .then(res => {
          // console.log(res,res.json())
          dispatch(receivePosts(reddit, res))
        });
        // .then(json => {
          // console.log(json)
          // dispatch(receivePosts(reddit, json))
        // })

    // return $.ajax(`/course`)
    //     .then(res => console.log(res))
    //     .then(json => {
    //       console.log(json)
    //         dispatch(receivePosts(reddit, json))
    //     })
  }
}


// function fetchPosts(reddit) {
//   return dispatch => {
//     dispatch(requestPosts(reddit))
//     return fetch(`https://www.reddit.com/r/${reddit}.json`)
//         .then(response => response.json())
//         .then(json => dispatch(receivePosts(reddit, json)))
//   }
// }


export function getCourseList(reddit) {
  // console.log(reddit)
  // return {
  //   type: REQUEST_POSTS,
  //   reddit
  // }

  return (dispatch, getState) => {
    console.log(reddit,getState())
    return dispatch(fetchPosts(reddit))
  }
}
