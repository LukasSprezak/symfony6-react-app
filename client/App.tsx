import MainStyle from "./styles/MainStyle";
import Main from "./component/core/Main";
import { BrowserRouter } from "react-router-dom";
import React from "react";
import store from "./store";
import { Provider } from "react-redux";
const App: React.FC = () => {
    return (
        <Provider store={store}>
            <BrowserRouter>
                <MainStyle />
                <Main />
            </BrowserRouter>
        </Provider>
    )
}
export  default App;
