import React from "react";
import {Switch, Route} from "react-router-dom"
import Header from "../layout/home/header";
import Login from "./home/login";
import Register from "./home/register";

const home = (props) => {
    //console.log(props);
    return (
        <div>
            <Header props={props} />
            <Switch>
                <Route exact path={props.match.path} component={Login} />
                <Route path={`${props.match.path}/Register`} component={Register} />
            </Switch>
        </div>
    );
};

export default home;