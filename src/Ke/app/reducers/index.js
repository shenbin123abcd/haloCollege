import todos from './todos'
import courseList from './index.course'
import monthList from './index.monthList'


export default Redux.combineReducers({
    todos,
    courseList,
    monthList,
})