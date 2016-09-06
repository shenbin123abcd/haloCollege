

var CommentBox = React.createClass({
  getInitialState: function() {
    return {
      users: []
    };
  },
  componentDidMount: function(dispatch) {
    dispatch({
      type: 'USER_LIST_SUCCESS',
      users: 'jhon doe'
    });
  },
  render: function() {
    if(this.props.active){
      return (
        <div className="commentBox">
          efewf Hello, world! I am a CommentBox.
        </div>
      );
    }
    return (
      <div className="commentBox">
        Hello, world! I am a CommentBox.
      </div>
    );
  }
});

CommentBox.propTypes = {
  active: React.PropTypes.bool.isRequired,

};



const getVisibleTodos = (todos, filter) => {
  switch (filter) {
    case 'SHOW_ALL':
      return todos
    case 'SHOW_COMPLETED':
      return todos.filter(t => t.completed)
    case 'SHOW_ACTIVE':
      return todos.filter(t => !t.completed)
  }
}

const mapStateToProps = (state) => {
  return {
    todos: getVisibleTodos(state.todos, state.visibilityFilter)
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
    onTodoClick: (id) => {
      dispatch(toggleTodo(id))
    }
  }
}


CommentBox = ReactRedux.connect(
    mapStateToProps,
    mapDispatchToProps
)(CommentBox)


export default CommentBox


