import { fetchSelectSeatIfNeeded } from '../actions/selectseat'
import { setSeatsStatus } from '../actions/common.seat'
import  SeatRow  from '../components/Common.SeatRow'
import  PageLoading  from '../components/Common.Pageloading'
import  SeatBox  from '../components/Selectseat.SeatBox'
import  CourseBox  from '../components/Selectseat.CourseBox'
import { destroySeats,selectRandomSeat ,bookSeatIfNeeded} from '../actions/common.seat'
var browserHistory=ReactRouter.browserHistory



var SeatBtn =({selectedItem,selectRandomSeat,bookSeat})=>{
    selectedItem=selectedItem||{}


    let renderLeftBt=()=>{

        if(selectedItem.seat_no){
            let seat_no_arr=selectedItem.seat_no.split(',')
            return(
                <div className="bt-box-1">
                    <span className="done-text">已选</span>
                    <span className="done-tag">{seat_no_arr[0]}排{seat_no_arr[1]}座</span>
                </div>
            )
        }else{
            return(
                <div onClick={selectRandomSeat}  className="bt-box-1">
                    系统选座
                </div>
            )
        }
    }

    let itemClass = classNames({
        'active': selectedItem.seat_no,
    });


    return(
        <div className="selectseat-bt-box" >
            {renderLeftBt()}
            <button className={`bt-box-2 ${itemClass}`}
                    disabled={!selectedItem.seat_no}
                    onClick={bookSeat}
            >
                确认选座
            </button>
        </div>
    )

}


var SelectSeat = React.createClass({
    componentDidMount() {
        document.title='座位表-在线选座';
        if(Modernizr.weixin&&Modernizr.ios){
            hb.hack.setTitle(document.title);
        }
        app.wechat.init();
        const { dispatch ,routeParams} = this.props
        // console.log(this.props)
        dispatch(fetchSelectSeatIfNeeded(routeParams.id))
        dispatch(setSeatsStatus(true))
    },
    componentWillReceiveProps : function(nextProps) {
        // console.log('componentWillReceiveProps')
        // console.log('componentWillReceiveProps')
        // console.log(nextProps)
    },
    hbDrag:null,
    componentDidUpdate  : function(prevState){
        // console.log('componentDidUpdate')
        // console.log(prevState,prevProps)
        let {isBooking,isBookSuccess,isBookFailure,info,course}=this.props;

        // console.log(course)
        // if(course){
        //     console.log(`/course/user?cate_id=${course.cate_id}`)
        // }

        let dragDom=$(this.refs.dragContainer).find('[data-my-drag]').get()[0]
        // console.log(prevState,this.props)
        if(prevState.items&&!this.hbDrag){
            // console.log(dragDom)
            this.hbDrag=hb.drag(dragDom,{});
        }
        if(isBooking){
            // console.log(dragDom)
            hb.lib.weui.loading.show()
        }else{
            hb.lib.weui.loading.hide()
        }

        if(isBookFailure){
            hb.lib.weui.alert(info)
        }

        if(isBookSuccess){
            hb.lib.weui.alert(info).then(res=>browserHistory.push(`/course/user?cate_id=${course.cate_id}`))
        }

    },
    componentWillUnmount(){
        const { dispatch ,routeParams} = this.props
        dispatch(destroySeats())
    },
    renderSeatRow(item, i) {
        return (
            <SeatRow items={item} key={i}   />
        )
    },
    selectRandomSeat(){

        // console.log(this.props)
        const {dispatch} = this.props
        dispatch(selectRandomSeat());
    },
    bookSeat(){
        // console.log(this.props)
        const {dispatch,selectedItem ,routeParams} = this.props
        dispatch(bookSeatIfNeeded({
            seat_no:selectedItem.seat_no,
            course_id:routeParams.id
        }));
    },
    render() {
        let {items,isFetching,course,selectedItem}=this.props;

        if(!items){
            var isNull=true
        }

        if (isFetching||isNull) {
            return <PageLoading />
        }

        return (
            <div ref="dragContainer" className="selectseat-wrapper">
                <div className="seats-wrapper">
                    <CourseBox data={course}  />
                    <SeatBox items={items} isFetching={isFetching}
                             renderItem={this.renderSeatRow} />
                    <SeatBtn selectedItem={selectedItem} selectRandomSeat={this.selectRandomSeat} bookSeat={this.bookSeat} />

                </div>

            </div>
        );
    }
});

// export default Index
function mapStateToProps(state) {
    const { seats,selectSeat } = state
    const {
        isFetching,
        course,
    } = selectSeat
    const {
        items,
        selectedItem,
        isBooking,
        info,
        isBookSuccess,
        isBookFailure,
    } = seats
    return{
        course,
        isFetching,
        items,
        selectedItem,
        isBooking,
        info,
        isBookSuccess,
        isBookFailure,
    }
}

export default ReactRedux.connect(mapStateToProps)(SelectSeat)