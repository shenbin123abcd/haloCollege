import todos from './todos'
import courseList from './index.course'
import monthList from './index.monthList'
import courseDetail from './detail.course'
import userItems from './user.items'
import userClass from './user.class'

export default Redux.combineReducers({
    todos,
    courseList,
    monthList,
    courseDetail,
    userItems,
    userClass
})