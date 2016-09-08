
const Box = ({data}) => {
    if(!data){
        return (<div></div>)
    }
    return(
        <div className='selectseat-course-box' >
            <div className="course-inner">
                <img className="img" src={`${data.guest.avatar_url}?imageView2/1/w/74/h/74`} />
                <div className="info-box">
                    <div className="title">{data.title}</div>
                    <div className="info">{data.city} · {data.start_date} ·{data.day}天</div>
                </div>

            </div>
            <div className="line"></div>
            <div className="guide-inner">
                <div className="lab">
                    <i className="dot dot-available"></i>
                    <span className="text">可选</span>
                </div>
                <div className="lab lab-booked">
                    <i className="dot dot-booked"></i>
                    <span className="text">已定</span>
                </div>
                <div className="lab">
                    <i className="dot dot-selected"></i>
                    <span className="text">已选</span>
                </div>
            </div>

        </div>
    )}

export default Box