import React, { Component } from "react";
import axios from "axios";

export default class Delete extends Component {
  constructor(props) {
    super(props);
    this.state = { status: null, message: "" };
    this.handleClick = this.handleClick.bind(this);
  }

  componentDidMount() {
    this.setState({ status: null, message: "" });
  }

  handleClick() {
    this.setState({ status: null, message: "" });
    let url = "http://localhost:5000/truncate";
    axios
      .post(url)
      .then((res) => {
        console.log(res);
        this.setState({ status: "success", message: "TRUNCATE all success" });
      })
      .catch((error) => {
        this.setState({ status: "error", message: "Fail truncate." });
      });
  }

  render() {
    return (
      <div>
        {this.state.status == "success" ? (
          <div className="alert alert-success" role="alert">
            {this.state.message}
          </div>
        ) : (
          ""
        )}
        {this.state.status == "error" ? (
          <div className="alert alert-danger" role="alert">
            {this.state.message}
          </div>
        ) : (
          ""
        )}

        <button
          type="button"
          className="btn btn-outline-primary"
          onClick={this.handleClick}
        >
          Reset (TRUNCATE)
        </button>
      </div>
    );
  }
}
