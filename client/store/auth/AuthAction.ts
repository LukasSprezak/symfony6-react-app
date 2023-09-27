import { ActionCreatorWithPayload, createAction } from "@reduxjs/toolkit";
import LoginInterface from "../../interface/user/LoginInterface";

export const auth: ActionCreatorWithPayload<LoginInterface> = createAction<LoginInterface>('auth/user')
