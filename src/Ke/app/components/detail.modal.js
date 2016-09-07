let Link=ReactRouter.Link;
let Modal=ReactBootstrap.Modal;



export default React.createClass({
    getInitialState() {
        return { showModal: false };
    },

    close() {
        this.setState({ showModal: false });
    },

    open() {
        this.setState({ showModal: true });
    },

    render() {
        return (
            <div>
                <p onClick={this.open}>Click to get the full Modal experience!</p>
                <Modal show={this.state.showModal} onHide={this.close}>
                    <Modal.Body>
                        adasda
                    </Modal.Body>
                    <Modal.Footer>
                        <button onClick={this.close}>Close</button>
                    </Modal.Footer>
                </Modal>
            </div>
        );
    }
});
