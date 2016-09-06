import {
    RECEIVE_USER_ITEMS, REQUEST_USER_ITEMS
} from '../actions/user'


export default (state = {
    isFetching: false,
    data: null
}, action) => {
    switch(action.type) {
        case REQUEST_USER_ITEMS:
            return Object.assign({}, state, {
                isFetching: true,
            })
        case RECEIVE_USER_ITEMS:
            return Object.assign({}, state, {
                isFetching: false,
                data:[
                    {
                        name:'蔡上',
                        title:'adas',
                        date:'2016.09.16',
                        position:'上海',
                        seat:'3排2座',
                        type:'public',
                    },
                    {
                        name:'蔡上2',
                        title:'adas2',
                        date:'2016.09.16',
                        position:'上海',
                        seat:'3排2座',
                        type:'public',
                    },
                    {
                        name:'蔡上3',
                        title:'adas3',
                        date:'2016.09.16',
                        position:'上海',
                        seat:'3排2座',
                        type:'public',
                    },
                    {
                        name:'蔡上3',
                        title:'adas3',
                        date:'2016.09.16',
                        position:'上海',
                        seat:'3排2座',
                        type:'peixun',
                    },
                ]
            })
        default:
            return state
    }
}


