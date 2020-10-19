import React, { Component } from "react";
import axios from "axios";
import Delete from "./Delete";
import Setdate from "./Setdate";

export default class Home extends Component {
    render() {
        return (
            <div className="container">
                <div className="row py-3">
                    <div className="col-12">
                        <table className="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Turncate all for reset new import data</td>
                                    <td>
                                        <Delete />
                                    </td>
                                </tr>
                                <tr>
                                    <td>Set date (From date / To date)</td>
                                    <td><Setdate /></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        );
    }
}
