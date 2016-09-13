let Link=ReactRouter.Link


const Avatar = ({items,visibleNum,totalClass}) => {
    items=items||[];
    if(items.length<visibleNum){
        return (
            <div className="avatar-box">
                {items.map((n,i)=><img key={i} className="img" src={`${n}?imageView2/1/w/60/h/60`}/>)}
            </div>
        )
    }else{
        let newItems=items.slice(0,visibleNum)
        return (
            <div className="avatar-box">
                {newItems.map((n,i)=><img key={i} className="img" src={`${n}?imageView2/1/w/60/h/60`}/>)}
                <div className={totalClass}>+{items.length}</div>
            </div>
        )
    }
}

const CourseItem = ({item}) => {
    let tagClass = classNames({
        'tag tag-gkk': item.cate=='公开课',
        'tag tag-pxy': item.cate=='培训营',
    });
    let totalClass = classNames({
        'total total-gkk': item.cate=='公开课',
        'total total-pxy': item.cate=='培训营',
    });
    return (
        <Link to={`/course/detail_${item.id}`}  className="item">
            <div className="img-box">
                <img className="img" src={`${item.cover_url}?imageView2/1/w/710/h/380`}/>
                <div className="img-over-layer" ></div>
                <div className="guest-box">
                    <div className="name" >{item.guest.name}</div>
                    <div className="position" >{item.guest.position}</div>
                </div>
                
                <div className={tagClass}>{item.cate}</div>

            </div>
            <div className="content-box">
                <div className="des-box cf">
                    <div className="des-inner-1">
                        <div className="title">
                            {item.title}
                        </div>
                        <div className="info">
                            {item.start_date} · {item.city}
                        </div>
                    </div>
                    <div className="des-inner-2">
                        <div className="price">
                            ¥{item.price}/人
                        </div>
                        <div className="available">
                            名额仅剩:{item.last_num}
                        </div>
                    </div>
                </div>

                <div  className="info-box">
                    <Avatar items={item.user} visibleNum={8} totalClass={totalClass} />
                    <div className="more-box">
            
                    </div>
                </div>
            </div>

        </Link>
    )
}


export default CourseItem