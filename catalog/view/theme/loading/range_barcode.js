'use strict';

const e = React.createElement;



class RangeBarcode extends React.Component {
  constructor(props) {
    super(props);
    this.state = { liked: false };
  }

  render() {
    return e(
      <p>lorem</p>
    );
  }
}

const domContainer = document.querySelector('#like_button_container');
ReactDOM.render(e(RangeBarcode), domContainer);
