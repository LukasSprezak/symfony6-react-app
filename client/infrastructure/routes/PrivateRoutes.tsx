import { Outlet, Navigate } from 'react-router-dom'
import React from "react";

const PrivateRoutes: React.FC = () => {
    const token: string|null = localStorage.getItem('token');
    let auth: { token: (string|null) } = { token: token };

    return (
        auth.token ? <Outlet/> : <Navigate to="/dashboard"/>
    )
}

export default PrivateRoutes
