import React, { useState } from 'react'
import { Link, NavigateFunction, useNavigate } from "react-router-dom"
import authFetch from "../../infrastructure/axios/axios";
import store from "../../store";
import AuthLoginInterface from "../../interface/user/AuthLoginInterface";

const Login: React.FC = () => {
    const navigate: NavigateFunction = useNavigate();
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")
    const [validationErrors, setValidationErrors] = useState<object>({});
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

    const loginAction = (event: any): void => {
        setValidationErrors({})
        event.preventDefault();
        setIsSubmitting(true)

    let payload = {
        username:email,
        password:password,
    }

    authFetch
        .post<AuthLoginInterface|any>('/api/login_check', payload)
        .then((response): void => {
            setIsSubmitting(false)

            store.dispatch({
                type: 'auth/user',
                payload: response,
            });
            localStorage.setItem('token', response.data.token)

            navigate("/dashboard");
        })
        .catch((error): void => {
            setIsSubmitting(false)

            if (undefined !== error.response.data.errors) {
                setValidationErrors(error.response.data.errors);
            }
        });
    }

    return (
        <div className="row justify-content-md-center mt-5">
            <div className="col-4">
                <div className="card">
                    <div className="card-body">
                        <h5 className="card-title mb-4">Sign In</h5>
                        <form onSubmit={(event): void => {
                            loginAction(event)
                        }}>
                            {Object.keys(validationErrors).length !== 0 &&
                                <p className='text-center '><small className='text-danger'>Incorrect Email or Password</small></p>
                            }
                            <div className="mb-3">
                                <label
                                    htmlFor="email"
                                    className="form-label">
                                    Email address
                                </label>
                                <input
                                    type="email"
                                    className="form-control"
                                    id="email"
                                    name="email"
                                    value={email}
                                    onChange={(event): void => {
                                        setEmail(event.target.value)
                                    }}
                                />
                            </div>
                            <div className="mb-3">
                                <label
                                    htmlFor="password"
                                    className="form-label">
                                    Password
                                </label>
                                <input
                                    type="password"
                                    className="form-control"
                                    id="password"
                                    name="password"
                                    value={password}
                                    onChange={(event): void => {
                                        setPassword(event.target.value)
                                    }}
                                />
                            </div>
                            <div className="d-grid gap-2">
                                <button
                                    disabled={isSubmitting}
                                    type="submit"
                                    className="btn btn-primary btn-block">Login</button>
                                <p className="text-center">Don't have account? <Link to="/register">Register</Link></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Login;
