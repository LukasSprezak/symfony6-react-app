import React, { Suspense } from 'react';
import { Navigate, Route, Routes } from "react-router-dom";
import ProductList from "../product/ProductList";
import PageNotFound from "./PageNotFound";
import Login from "../user/Login";
import Register from "../user/Register";
import Dashboard from "./Dashboard";
import PrivateRoutes from "../../infrastructure/routes/PrivateRoutes";

const Main: React.FC = () => {
    return (
        <>
            <Suspense fallback={<div className="container">Loading...</div>}>
                <Routes>
                    <Route path="/" element={<Navigate to="/login" replace={true} />}/>
                    <Route path="login" element={<Login/>}/>
                    <Route path="register" element={<Register/>}/>
                    <Route element={<PrivateRoutes />}>
                        <Route path="dashboard" element={<Dashboard/>}/>
                        <Route path="product-list" element={<ProductList/>}/>
                    </Route>
                    <Route path="*" element={<PageNotFound />} />
                </Routes>
            </Suspense>
        </>
    );
}

export default Main;
