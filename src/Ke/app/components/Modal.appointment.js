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
                    <Modal.Header closeButton>
                        <Modal.Title>Modal heading</Modal.Title>
                    </Modal.Header>
                    <Modal.Body>
                        <h4>Text in a modal</h4>
                        <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>

                        <h4>Popover in a modal</h4>
                        <p>there is a here</p>

                        <h4>Tooltips in a modal</h4>
                        <p>there is a here</p>

                        <hr />

                        <h4>Overflowing text to show scroll behavior</h4>
                        <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                    </Modal.Body>
                    <Modal.Footer>
                        <p onClick={this.close}>Close</p>
                    </Modal.Footer>
                </Modal>
            </div>
        );
    }
});
