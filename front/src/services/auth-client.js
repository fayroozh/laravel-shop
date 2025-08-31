import axios from "axios";
import useAuthStore from "../app/authStore";

const authClient = axios.create({
    baseURL: "http://localhost:8000/api",
});

authClient.interceptors.request.use(
    (config) => {
        const token = useAuthStore.getState().token;
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => {
        return Promise.reject(error);
    }
);

export default authClient;