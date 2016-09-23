
var SeatLine=React.createClass({
    renderLines(){

    },
    render(){
        let {items}=this.props;
        if(items[0].length==8){
            return(
                <div className="seat-line-box">
                    <div className="seat-line seat-line-242-1"></div>
                    <div className="seat-line seat-line-242-2"></div>
                </div>
            )
        }else{
            return(
                <div className="seat-line-box">
                    <div className="seat-line"></div>
                </div>
            )
        }




    }
})

export default SeatLine