
export default (state = {
    items: null,
    clickable: false,
    selectedItem: null,
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
                items: null,
            })
        case 'SELECT_SEAT':
            return Object.assign({}, state, {
                selectedItem: action.selectedItem,
            })
        case 'SELECT_RANDOM_SEAT':
            // console.log(state.items)
            var aSeatArr=[];
            state.items.forEach((n,i)=>{
                n.forEach((n,i)=>{
                    if(!n.user){
                        aSeatArr.push(n)
                    }
                });
            });
            var num=hb.util.getRandomInt(0,aSeatArr.length);
            return Object.assign({}, state, {
                selectedItem: aSeatArr[num],
            })
        default:
            return state
    }
}

