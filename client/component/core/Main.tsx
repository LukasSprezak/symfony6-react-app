import React from 'react';
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";

const Main: React.FC = () => {
    return (
        <Router>
            <Routes>
                {/*<Route exact path="/"   />*/}
                <Route path="create"   />
                <Route path="edit/:id" />
                <Route path="show/:id"  />
            </Routes>
        </Router>
    );
}

export default Main;
