import React, {Component} from 'react';
import TextField from '@material-ui/core/TextField';
import Button from '@material-ui/core/Button';

class Login extends Component {
    render() {
        return (
            <div>
                <h1>Login</h1>
                <form noValidate autoComplete="off">
                    <TextField
                        margin="normal"
                        id="email-input"
                        label="Email"
                        variant="outlined"
                        fullWidth
                    />
                    <TextField
                        margin="normal"
                        id="password-input"
                        label="Password"
                        variant="outlined"
                        type="password"
                        fullWidth
                    />
                    <br/>
                    <Button
                        variant="contained"
                        color="primary"
                        style={{ width:1000 }}
                    >
                        Login
                    </Button>
                </form>
            </div>
        );
    }
}

export default Login;