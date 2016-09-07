let Link=ReactRouter.Link
const CourseItem = ({item}) => (
    <div className="item">
        <div className="img-box">
            <img className="img" src={require('../images/sample.png')} />
            <div className="tag tag-gkk">公开课</div>
        </div>
        <div className="content-box">
            <div className="des-box cf">
                <div className="des-inner-1">
                    <div className="title">
                        {item.title}
                    </div>
                    <div className="info">
                        09月16日 · 上海
                    </div>
                </div>
                <div className="des-inner-2">
                    <div className="price">
                        ¥2000/人
                    </div>
                    <div className="available">
                        名额仅剩:14
                    </div>
                </div>
            </div>
            <div className="line-box"></div>
            <Link to="/course/detail/10" className="info-box">
                <div className="avatar-box">
                    <img className="img" src={require('../images/sample-head.png')} />
                    <img className="img" src={require('../images/sample-head.png')} />
                    <img className="img" src={require('../images/sample-head.png')} />
                    <img className="img" src={require('../images/sample-head.png')} />
                    <img className="img" src={require('../images/sample-head.png')} />
                </div>
                <div className="more-box">
                    课程详情 &gt;
                </div>
                
            </Link>
        </div>

    </div>
)


export default CourseItem