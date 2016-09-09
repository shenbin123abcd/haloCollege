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
        return <div>no data</div>
    }

    return (
        <div className="index-course-wrapper">
            {items.map(renderItem)}
        </div>
    )}


export default Course