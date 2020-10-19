import React from "react";
import { BrowserRouter as Router, Switch, Route, Link } from "react-router-dom";
import "./App.css";

import Menu from './component/Menu';
import Home from './component/Home';

function App() {
  return (
    <div>
      <Router>
        <div>
          <Menu />

          <Switch>
            <Route path="/home">
              <Home />
            </Route>
            <Route path="/">
              <Home />
            </Route>
          </Switch>
        </div>
      </Router>
    </div>
  );
}

export default App;
