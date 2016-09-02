export default (state = Immutable.List(['aaa','bbb']), action) => {
    switch(action.type) {
        case 'showCourseList':
            return state.concat(action.data)
        default:
            return state
    }
}

