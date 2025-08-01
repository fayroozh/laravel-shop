import axios from "axios";
import { API_URL } from "../constant/api";

// إنشاء نسخة من axios مع الإعدادات الافتراضية
const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    "Content-Type": "application/json",
    "Accept": "application/json"
  }
});

// إضافة معترض للطلبات لإرفاق رمز المصادقة إذا كان متاحاً
apiClient.interceptors.request.use(config => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// تصدير وظائف API المختلفة
export const api = {
  // المستخدمين
  getUsers: () => apiClient.get("/users"),
  getUser: (id) => apiClient.get(`/users/${id}`),
  createUser: (data) => apiClient.post("/users", data),
  updateUser: (id, data) => apiClient.put(`/users/${id}`, data),
  deleteUser: (id) => apiClient.delete(`/users/${id}`),
  
  // المنتجات
  getProducts: () => apiClient.get("/products"),
  getProduct: (id) => apiClient.get(`/products/${id}`),
  createProduct: (data) => apiClient.post("/products", data),
  updateProduct: (id, data) => apiClient.put(`/products/${id}`, data),
  deleteProduct: (id) => apiClient.delete(`/products/${id}`),
  
  // التصنيفات
  getCategories: () => apiClient.get("/categories"),
  getCategory: (id) => apiClient.get(`/categories/${id}`),
  createCategory: (data) => apiClient.post("/categories", data),
  updateCategory: (id, data) => apiClient.put(`/categories/${id}`, data),
  deleteCategory: (id) => apiClient.delete(`/categories/${id}`),
  
  // الطلبات
  getOrders: () => apiClient.get("/orders"),
  getOrder: (id) => apiClient.get(`/orders/${id}`),
  createOrder: (data) => apiClient.post("/orders", data),
  updateOrder: (id, data) => apiClient.put(`/orders/${id}`, data),
  deleteOrder: (id) => apiClient.delete(`/orders/${id}`),
  
  // التعليقات
  getFeedback: () => apiClient.get("/feedback"),
  createFeedback: (data) => apiClient.post("/feedback", data),
  deleteFeedback: (id) => apiClient.delete(`/feedback/${id}`),
  
  // الموظفين
  getEmployees: () => apiClient.get("/employees"),
  getEmployee: (id) => apiClient.get(`/employees/${id}`),
  createEmployee: (data) => apiClient.post("/employees", data),
  updateEmployee: (id, data) => apiClient.put(`/employees/${id}`, data),
  deleteEmployee: (id) => apiClient.delete(`/employees/${id}`),
  
  // الأدوار
  getRoles: () => apiClient.get("/roles"),
  getRole: (id) => apiClient.get(`/roles/${id}`),
  createRole: (data) => apiClient.post("/roles", data),
  updateRole: (id, data) => apiClient.put(`/roles/${id}`, data),
  deleteRole: (id) => apiClient.delete(`/roles/${id}`),
  
  // الصلاحيات
  getPermissions: () => apiClient.get("/permissions"),
  
  // النشاطات
  getActivities: () => apiClient.get("/activities"),
  
  // الإشعارات
  getNotifications: () => apiClient.get("/notifications"),
  markAsRead: (id) => apiClient.post(`/notifications/${id}/mark-as-read`),
  markAllAsRead: () => apiClient.post("/notifications/mark-all-as-read"),
  
  // المصادقة
  login: (credentials) => apiClient.post("/login", credentials),
  register: (data) => apiClient.post("/register", data),
  logout: () => apiClient.post("/logout"),
  getProfile: () => apiClient.get("/profile"),
  
  // التقارير
  getReports: (reportType) => apiClient.get(`/reports/${reportType}`),
};

export default apiClient;