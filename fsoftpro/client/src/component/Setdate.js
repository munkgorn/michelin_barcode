import React, { Component } from "react";
import axios from "axios";
import Moment from 'moment';

export default class Setdate extends Component {
  constructor(props) {
    super(props);

    Moment.locale('th');

    this.state = { status: null, message: "", datefrom: '', todate: '', dates:[] };
    this.handleClick = this.handleClick.bind(this);
    this.handleChange = this.handleChange.bind(this);
  }

  componentDidMount() {
    this.setState({ status: null, message: "", datefrom: '', todate: '', dates:[]});
    let url = 'http://localhost:5000/getAssociationDate';
    axios.post(url).then((res) => {
      this.setState({dates: res.data});
    }).catch((error) => {
      this.setState({ status: "error", message: 'Error get date'});
    });
  }

  handleChange(e) {
    this.setState({
      [e.target.name]: e.target.value
    });
  }

  handleClick() {
    this.setState({ status: null, message: "" });
    console.log(this.state);
    let url = "http://localhost:5000/setdate";
    let parameter = {datefrom: this.state.datefrom, todate: this.state.todate}
    axios
      .post(url, parameter)
      .then((res) => {
        console.log(res);
        this.setState({ status: "success", message: res.data });
      })
      .catch((error) => {
        this.setState({ status: "error", message: error.data });
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
        <div className="input-group mb-3">
          {/* <input type="date" className="form-control" name="datefrom" onChange={this.handleChange} /> */}
          
          <select name="datefrom" id="" className="form-control">
            {this.state.dates.map(value => <option value={value.date_wk}>{Moment(value.date_wk).format('D/M/Y')}</option>)}
          </select>

          <input type="date" className="form-control" name="todate" onChange={this.handleChange} />
          <div className="input-group-append">
            <button className="btn btn-outline-primary" type="button" onClick={this.handleClick}>
              Update
            </button>
          </div>
        </div>
      </div>
    );
  }
}
