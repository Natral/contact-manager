import React from 'react';
import logo from './logo.svg';
import './App.css';
import {BrowserRouter, Switch, Route} from "react-router-dom";
import home from "./components/home";

function App() {
  return (

      <BrowserRouter>
          <div className="App">
              <Switch>
                  <Route exact path="/" component={home} />
                  <Route path="/home" component={home} />
              </Switch>
          </div>
      </BrowserRouter>

  );
}

export default App;
