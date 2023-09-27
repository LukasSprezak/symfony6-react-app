import React, { useState } from 'react'
import { Link, NavigateFunction, useNavigate } from "react-router-dom"
import authFetch from '../../infrastructure/axios/axios'
import store from "../../store";
import RegisterInterface from "../../interface/user/RegisterInterface";

const Register: React.FC = () => {
    const navigate: NavigateFunction = useNavigate();
    const [username, setUsername] = useState("")
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")
    const [repeatPassword, setRepeatPassword] = useState("")
    const [validationErrors, setValidationErrors] = useState<object|any>({});
    const [isSubmitting, setIsSubmitting] = useState<boolean>(false);

    const registerAction = (event: any): void => {
        setValidationErrors({})
        event.preventDefault();
        setIsSubmitting(true)

        let payload = {
            username: username,
            email:email,
            password:password,
            repeatPassword: repeatPassword,
        }

        authFetch
            .post<RegisterInterface|any>('/api/users/create-account', payload)
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
                        <h5 className="card-title mb-4">Register</h5>
                        <form onSubmit={(event) => registerAction(event)}>
                            <div className="mb-3">
                                <label
                                    htmlFor="username"
                                    className="form-label">Username
                                </label>
                                <input
                                    type="text"
                                    className="form-control"
                                    id="username"
                                    name="username"
                                    value={username}
                                    onChange={(event): void => {
                                        setUsername(event.target.value)
                                    }}
                                />
                                {validationErrors.username !== undefined &&
                                    <div className="flex flex-col">
                                        <small  className="text-danger">
                                            {validationErrors.name[0]}
                                        </small >
                                    </div>
                                }

                            </div>
                            <div className="mb-3">
                                <label
                                    htmlFor="email"
                                    className="form-label">Email address
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
                                {validationErrors.email !== undefined &&
                                    <div className="flex flex-col">
                                        <small  className="text-danger">
                                            {validationErrors.email[0]}
                                        </small >
                                    </div>
                                }

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
                                    onChange={(event) => setPassword(event.target.value)}
                                />
                                {validationErrors.password !== undefined &&
                                    <div className="flex flex-col">
                                        <small  className="text-danger">
                                            {validationErrors.password[0]}
                                        </small >
                                    </div>
                                }
                            </div>
                            <div className="mb-3">
                                <label
                                    htmlFor="repeatPassword"
                                    className="form-label">
                                    Repeat Password
                                </label>
                                <input
                                    type="password"
                                    className="form-control"
                                    id="repeatPassword"
                                    name="repeatPassword"
                                    value={repeatPassword}
                                    onChange={(event) => setRepeatPassword(event.target.value)}
                                />
                            </div>
                            <div className="d-grid gap-2">
                                <button
                                    disabled={isSubmitting}
                                    type="submit"
                                    className="btn btn-primary btn-block">
                                    Register
                                </button>
                                <p
                                    className="text-center">You already have an account <Link to="/login">Login</Link>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default Register;
