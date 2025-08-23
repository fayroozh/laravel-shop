import axios from "axios";

// Create an instance for API routes
// تغيير baseURL ليشمل /api
const apiClient = axios.create({
  baseURL: "http://localhost:8000/api", // إضافة /api
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  withCredentials: true,
});

const authClient = axios.create({
  baseURL: "http://localhost:8000/api", // إضافة /api
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
    "X-Requested-With": "XMLHttpRequest"
  },
  withCredentials: true
});

// Add token to requests safely
// Add token to requests safely
const addAuthHeader = (config) => {
  try {
    const tokenString = localStorage.getItem("auth-storage");
    if (tokenString) {
      const getToken = JSON.parse(tokenString);
      if (getToken && getToken.state && getToken.state.token) {
        config.headers.Authorization = `Bearer ${getToken.state.token}`;
      }
    }
  } catch (e) {
    console.error("Error parsing token:", e);
  }
  return config;
};

apiClient.interceptors.request.use(addAuthHeader);
authClient.interceptors.request.use(addAuthHeader);

export { apiClient, authClient };
export default apiClient;
