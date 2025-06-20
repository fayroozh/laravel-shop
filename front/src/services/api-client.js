import axios from "axios";

// Create an instance for API routes
const apiClient = axios.create({
  baseURL: "http://localhost:8000", // Will be proxied to http://localhost:8000/api
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  withCredentials: true, // Important for CSRF cookies
});

// Create an instance for auth routes
const authClient = axios.create({
  baseURL: "/", // Will be proxied to http://localhost:8000
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  withCredentials: true,
});

// Add token to requests safely
const addAuthHeader = (config) => {
  const tokenString = localStorage.getItem("auth-storage");
  let getToken = null;
  try {
    getToken = JSON.parse(tokenString);
  } catch (e) {
    getToken = null;
  }

  if (getToken && getToken.state && getToken.state.token) {
    config.headers.Authorization = `Bearer ${getToken.state.token}`;
  }

  return config;
};

apiClient.interceptors.request.use(addAuthHeader);
authClient.interceptors.request.use(addAuthHeader);

export { apiClient, authClient };
export default apiClient;
