

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

CommentBox = ReactRedux.connect()(CommentBox)

export default CommentBox


