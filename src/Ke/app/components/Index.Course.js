import  PageLoading  from './Common.Pageloading'
const Course = ({isFetching,items, renderItem}) => {

    if(!items){
        var isNull=true
    }else{
        var isEmpty = items.length === 0
    }


    if (isFetching||isNull) {
        return <PageLoading />
    }

    if (isEmpty) {
        return (
            <div className="index-no-data-wrapper">
                <div className="bg-ico"></div>
                <div className="main-msg">正在筹备中，时刻关注!</div>
                <div className="second-msg">精选课程马上上线，请耐心等候哦</div>
            </div>
        )
    }

    return (
        <div className="index-course-wrapper">
            {items.map(renderItem)}
        </div>
    )}


export default Course