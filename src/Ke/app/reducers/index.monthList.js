
function initState() {
    let CurrentMonth=new Date().getMonth()+1;
    let state=[]
    for(let i=0;i<5;i++){
        state.push({
            month:`${CurrentMonth+i}`
        });
        if(i==0){
            state[i].active=true
        }
        if(state[i].month>12){
            state[i].month=state[i].month-12
        }
    }
    return state;
}

export default (state = initState(), action) => {
    // console.log(state)
    switch(action.type) {
        case 'setCurrentMonth':
            let newArr=[].concat(state);
            newArr.forEach(n=>{
                n.active=false
            });
            newArr[action.data].active=true
            console.log(newArr)
            return newArr;
        default:
            return state
    }
}

