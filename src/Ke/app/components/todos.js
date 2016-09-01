import NewTodo from './NewTodo'
import { addTodo } from '../actions'
import store from '../store';
const Todos = ({todos, dispatch}) => {

    console.log(todos, dispatch)

    return (
    <div>
        <h1>Todos</h1>
        <NewTodo onChange={e => {
      if(e.keyCode == 13){
        dispatch(addTodo(e.target.value))
        e.target.value = ''
      }
    }}/>
        {todos.map(todo => <p key={todo}>{todo}</p>)}
    </div>
)};


// const Todos3 = () => (
//     <div>
//         <h1>Todos</h1>
//     </div>
// );
// console.log(Todos3())
// const Todos2 = React.createClass({
//     render: function() {
//         return (
//             <div>
//                 <h1>Todos</h1>
//             </div>
//         );
//     }
// });
// console.log(Todos2)


const Todos2 = React.createClass({
    componentDidMount: function() {
        $.ajax('/course').then(response => {

        });
    },
    render: function() {
        // console.log(this.props.dispatch);
        const {todos, dispatch} = this.props
        return (
            <div>
                <h1>Todos</h1>
                <NewTodo onChange={e => {
      if(e.keyCode == 13){
        dispatch(addTodo(e.target.value))
        e.target.value = ''
      }
    }}/>
                {todos.map(todo => <p key={todo}>{todo}</p>)}
            </div>
        );
    }
});


function mapStateToProps(todos) {
    return {
        todos
    }
}

export default ReactRedux.connect(mapStateToProps)(Todos)