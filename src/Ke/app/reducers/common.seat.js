
export default (state = {
    items: null,
    clickable: false,
    selectedItem: null,
    isBooking: false,
    isBookSuccess: false,
    isBookFailure: false,
}, action) => {
    // console.log(action)
    switch(action.type) {
        case 'INIT_SEATS':
            return Object.assign({}, state, {
                items: action.items,
            })
        case 'SET_SEATS_STATUS':
            return Object.assign({}, state, {
                clickable: action.clickable,
            })
        case 'DESTROY_SEATS':
            return Object.assign({}, state, {
                clickable: false,
                selectedItem: null,
                items: null,
                isBooking: false,
                isBookSuccess: false,
                isBookFailure: false,
            })
        case 'SELECT_SEAT':
            return Object.assign({}, state, {
                selectedItem: action.selectedItem,
                isBookSuccess: false,
                isBookFailure: false,
            })
        case 'SELECT_RANDOM_SEAT':
            // console.log(state.items)
            var aSeatArr=[];
            state.items.forEach((n,i)=>{
                n.forEach((n2,i2)=>{
                    if(!n2.user){
                        aSeatArr.push(n2)
                    }
                });
            });
            var num=hb.util.getRandomInt(0,aSeatArr.length);
            return Object.assign({}, state, {
                selectedItem: aSeatArr[num],
            })
        case 'BOOK_SEAT_REQUEST':
            return Object.assign({}, state, {
                isBooking: true,
                isBookSuccess: false,
                isBookFailure: false,
            })
        case 'BOOK_SEAT_SUCCESS':
            return Object.assign({}, state, {
                isBooking: false,
                info:action.info,
                isBookSuccess:true,
            })
        case 'BOOK_SEAT_FAILURE':
            return Object.assign({}, state, {
                isBooking: false,
                info:action.info,
                isBookFailure:true,
            })
        default:
            return state
    }
}

