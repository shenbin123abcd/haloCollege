import courseList from './index.course'
import monthList from './index.monthList'
import courseDetail from './detail.course'
import userItems from './user.items'
import seats from './common.seat'
import seatInfo from './seatinfo'
import courseStatus from './button.status'
import selectSeat from './selectseat'
import seatDesc from './seat.desc'

export default Redux.combineReducers({
    courseList,
    monthList,
    courseDetail,
    userItems,
    seats,
    seatInfo,
    courseStatus,
    selectSeat,
    seatDesc,
})