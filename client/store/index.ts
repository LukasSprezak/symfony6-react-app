import { configureStore, createSlice } from '@reduxjs/toolkit';
import AuthReducer from "./auth/AuthReducer";

const reducerSlice = createSlice({
    name: 'store',
    initialState: {},
    reducers: {
        someAction: function(): void {

        }
    }
})

const store = configureStore({
    reducer: {
        auth: AuthReducer
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware({
            serializableCheck: false,
        }),
})

export default store;
