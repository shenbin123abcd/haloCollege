import courseList from './index.course'
import monthList from './index.monthList'
import courseDetail from './detail.course'
import userItems from './user.items'
import seats from './common.seat'
import seatInfo from './seatinfo'
import selectSeat from './selectseat'

export default Redux.combineReducers({
    courseList,
    monthList,
    courseDetail,
    userItems,
    seats,
    seatInfo,
    selectSeat,
})