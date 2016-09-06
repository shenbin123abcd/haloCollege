import {
    SHOW_OPEN_CLASS, SHOW_TRAINING_CAMPS
} from '../actions/user'

function makeOpenArr(n,i){
    if(n.type=='peixun'){
        return false
    }
    return true
}

function makeTrainingArr(n,i){
    if(n.type=='public'){
        return false
    }
    return true
}

export default (state = {
    class:[],
}, action) => {
    let dataArr='';
    switch(action.type) {
        case SHOW_OPEN_CLASS:
            dataArr=action.data.filter(n=>n.type=='peixun')
            return Object.assign({}, state, {
                class: dataArr,
            })
        case SHOW_TRAINING_CAMPS:
            dataArr=action.data.filter(n=>n.type=='public')
            return Object.assign({}, state, {
                class: dataArr,
            })
        default:
            return state
    }
}


