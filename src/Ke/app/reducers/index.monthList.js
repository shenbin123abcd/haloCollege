
function initState() {
    let CurrentMonth=new Date().getMonth()+1;
    let CurrentYear=new Date().getFullYear();
    if(CurrentYear==2016){
        if(CurrentMonth==9){
            CurrentMonth+=2;
        }
        if(CurrentMonth==10){
            CurrentMonth+=1;
        }
    }
    let monthList=[]
    for(let i=0;i<5;i++){
        monthList.push({
            month:CurrentMonth+i,
            year:CurrentYear,
            active:false,
        });
        // if(i==0){
        //     monthList[i].active=true
        // }
        if(monthList[i].month>12){
            monthList[i].month=monthList[i].month-12
            monthList[i].year=monthList[i].year+1
        }
        monthList[i].month=hb.util.pad(monthList[i].month,2)
    }

    return monthList;
}

export default (state = initState(), action) => {
    // console.log(state)
    switch(action.type) {
        case 'setCurrentMonth':
            // console.log(action)
            let newArr=[].concat(state);
            newArr.forEach(n=>{
                n.active=false
                if(n.month==action.data.month){
                    n.active=true
                }
            });
            return newArr;
        case 'resetMonth':
            // console.log(action)
            let newArr2=[].concat(state);
            newArr2.forEach(n=>{
                n.active=false
            });
            return newArr2;
        default:
            return state
    }
}

