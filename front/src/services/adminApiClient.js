import axios from "axios";
import useAuthStore from "../app/authStore";

// إنشاء نسخة خاصة بـ API لوحة التحكم
const adminApiClient = axios.create({
  baseURL: "/api/admin", // سيتم توجيهه إلى http://localhost:8000/api/admin
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json",
  },
  withCredentials: true,
});

// إضافة التوكن إلى الطلبات
adminApiClient.interceptors.request.use((config) => {
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
});

// معالجة الأخطاء وإعادة التوجيه إذا انتهت صلاحية التوكن
adminApiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      // تسجيل الخروج وإعادة التوجيه إلى صفحة تسجيل الدخول
      const { logout } = useAuthStore.getState();
      logout();
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default adminApiClient;