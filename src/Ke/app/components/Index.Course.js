
const Course = ({isFetching,items, renderItem}) => {

    if(!items){
        var isNull=true
    }else{
        var isEmpty = items.length === 0
    }


    if (isFetching||isNull) {
        return <div>loading</div>
    }

    if (isEmpty) {
        return <div>no data</div>
    }

    return (
        <div>
            {items.map(renderItem)}
        </div>
    )}


export default Course