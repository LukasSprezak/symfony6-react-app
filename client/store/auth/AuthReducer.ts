import { createReducer, Draft } from "@reduxjs/toolkit";
import { auth } from "./AuthAction";
import LoginInterface from "../../interface/user/LoginInterface";

const originalState = { token: '', refresh_token: '' };

const AuthReducer = createReducer(originalState, builder => {
    builder
        .addCase(
            auth,
            (state: Draft<LoginInterface>,
             action): void => {

            const { token, refresh_token} = action.payload

            if (token && refresh_token) {
                state.token = token;
                state.refresh_token = refresh_token;
            }
        })
});

export default AuthReducer;