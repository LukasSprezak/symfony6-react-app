import React, { useState } from "react";

export const Register: React.FC = () => {
    const [username, setUsername] = useState('');
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [repeatPassword, setRepeatPassword] = useState('');

    const handleSubmit = (event: { preventDefault: () => void; }): void => {
        event.preventDefault();
        console.log(email);
    }

    return (
        <div className="auth-form-container">
            <h2>Register</h2>
            <form className="register-form" onSubmit={handleSubmit}>
                <label htmlFor="username">Username</label>
                <input
                    value={username}
                    name="username"
                    onChange={(event) => setUsername(event.target.value)}
                    id="username" placeholder="Username"
                />
                <label htmlFor="email">Email</label>
                <input
                    value={email}
                    onChange={(event) => setEmail(event.target.value)}
                    type="email"
                    placeholder="example@gmail.com"
                    id="email"
                    name="email"
                />
                <label htmlFor="password">Password</label>
                <input
                    value={password}
                    onChange={(event) => setPassword(event.target.value)}
                    type="password"
                    placeholder="********"
                    id="password"
                    name="password"
                />
                <label htmlFor="repeatPassword">password</label>
                <input
                    value={repeatPassword}
                    onChange={(event) => setRepeatPassword(event.target.value)}
                    type="password"
                    placeholder="********"
                    id="repeatPassword"
                    name="repeatPassword"
                />
                <button type="submit">Register</button>
            </form>
        </div>
    )
}

export default Register;
