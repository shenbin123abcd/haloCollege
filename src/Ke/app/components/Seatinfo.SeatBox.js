import SeatLine from './Common.SeatLine'

var SeatBox=React.createClass({
    render(){
        let {items,renderItem}=this.props;
        let seatBoxClass = classNames({
            'seat-box-10': items[0].length==10,
            'seat-box-8': items[0].length==8,
            'seat-box-6': items[0].length==6,
        });
        return(
            <div className='seatinfo-box ' data-my-drag >
                <div className="seat-canvas">投影幕布</div>
                <div className={`seat-box ${seatBoxClass}`} >
                    <SeatLine items={items} />
                    {items.map(renderItem)}
                </div>
            </div>
        )
    }
})

export default SeatBox