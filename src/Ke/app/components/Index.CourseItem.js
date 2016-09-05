const CourseItem = ({item}) => (
    <div className="item">
        <div className="img-box">
            <img className="img" src={require('../images/sample.png')} />
            <div className="tag tag-gkk">公开课</div>
        </div>
        <div className="content-box">
            <div className="des-box cf">
                <div className="des-inner">
                    <div className="title">
                        云南站 |{item.title}
                    </div>
                    <div className="info">
                        09月16日 · 上海
                    </div>
                </div>
            </div>

        </div>

    </div>
)


export default CourseItem