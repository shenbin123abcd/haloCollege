import indexData from './index'
import guestDetailData from './guestDetail'
import companyDetailData from './companyDetail'
import articlesListData from './articlesList'
import videosListData from './videosList'
import goldenBearDetailData from './goldenBearDetail'
// var reduxFormReducer=ReduxForm.reducer

export default Redux.combineReducers({
 	// form: reduxFormReducer,
    indexData,
    guestDetailData,
    companyDetailData,
    articlesListData,
    videosListData,
    goldenBearDetailData
})