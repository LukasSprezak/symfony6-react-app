import React, { Suspense } from 'react';
import { Routes, Route } from "react-router-dom";
import ProductList from "../product/ProductList";
import NavBar from "./NavBar";
import PageNotFound from "./PageNotFound";
import { Register } from "../user/Register";

const Main: React.FC = () => {
    return (
        <>
            <NavBar />
            <Suspense fallback={<div className="container">Loading...</div>}>
                <Routes>
                    <Route path="product-list" element={<ProductList/>}/>
                    <Route path="create"/>
                    <Route path="edit/:id"/>
                    <Route path="show/:id"/>
                    <Route path="register" element={<Register/>}/>
                    <Route path="*" element={<PageNotFound />} />
                </Routes>
            </Suspense>
        </>
    );
}

export default Main;
