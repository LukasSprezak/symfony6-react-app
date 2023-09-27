import axios, { AxiosInstance } from 'axios';
import store from '../../store';

const authFetch: AxiosInstance = axios.create({
    baseURL: 'http://localhost:8080',
    headers: {
        "Content-Type": "application/json",
    },
});

const { auth: { token: tokenFromStore, refresh_token}} = store.getState();

const parseJwt = (tokenResponse?: string): any|null => {
    const token: string|undefined = tokenResponse || tokenFromStore;
    const base64Url = token?.split('.')[1];
    const base64 = base64Url?.replace(/-/g, '+').replace(/_/g, '/');

    const jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c: string): string {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
    }).join(''));

    return JSON.parse(jsonPayload);
}

const getToken = (): string|null => {
    return tokenFromStore;
}

const token: string|null = getToken();
console.log(token);

authFetch.interceptors.request.use(
    (request) => {

        if (undefined === request.headers['Content-Type']) {
            request.headers['Content-Type'] = 'application/json';
        }

        request.headers['X-Requested-With'] = 'XMLHttpRequest';

        if (token) {
            request.headers['Authorization'] = `Bearer ${token}` ?? '';
        }

        console.log('request sent');

        return request;
    },
    (error) => {
        return Promise.reject(error);
    }
);

authFetch.interceptors.response.use(
    (response) => {
        console.log('get response');
        return response;
    },
    (error) => {
        if (404 === error.response.status) {
            console.log('Not Found');
        }

        return Promise.reject(error);
    }
);

export default authFetch;
