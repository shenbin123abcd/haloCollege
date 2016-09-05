import todos from './todos'
import courseList from './index.course'
import monthList from './index.monthList'
import courseDetail from './detail.course'


export default Redux.combineReducers({
    todos,
    courseList,
    monthList,
    courseDetail,
})