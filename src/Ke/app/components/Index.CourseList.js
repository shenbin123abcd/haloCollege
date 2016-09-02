
const CourseList = ({items,onClick}) => (
    <div>
        {
            items.map((item ,i)=>{
                return (
                    <p key={i}>
                        <span>{item}</span>
                        <b onClick={onClick}>{item}</b>
                    </p>
                )
            })
        }
    </div>
)


export default CourseList