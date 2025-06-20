import { useMutation } from "@tanstack/react-query";
import useAuthStore from "../app/authStore";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { authClient } from "../services/api-client";

export const useAuth = () => {
  const { setToken, setUser } = useAuthStore(); // إضافة setUser هنا
  const navigate = useNavigate();

  // Login
  const loginMutation = useMutation({
    mutationFn: (credentials) =>
      authClient
        .post("/login", {
          email: credentials.email,
          password: credentials.password,
        })
        .then((res) => res.data),
    onSuccess: (data) => {
      setToken(data.token);
      setUser(data.user); // تخزين معلومات المستخدم
      
      // التحقق إذا كان المستخدم مشرف أو موظف
      const isAdmin = data.user.is_admin === 1 || data.user.is_admin === true;
      const isEmployee = data.user.is_employee_role === 1 || data.user.is_employee_role === true;
      
      // إذا كان المستخدم مشرف أو موظف، توجيهه إلى لوحة التحكم
      if (isAdmin || isEmployee) {
        navigate("/admin/dashboard");
        toast.success('تم تسجيل الدخول بنجاح! جاري توجيهك إلى لوحة التحكم');
      } else {
        // وإلا توجيهه إلى الصفحة الرئيسية
        navigate("/");
        toast.success('تم تسجيل الدخول بنجاح!');
      }
    },
  });

  // Register new user
  const registerMutation = useMutation({
    mutationFn: (userData) =>
      authClient
        .post("/register", userData)
        .then((res) => res.data),
    onSuccess: (data) => {
      setToken(data.token);
      setUser(data.user); // إضافة هذا السطر لتخزين معلومات المستخدم
      navigate("/");
      toast.success('Account created successfully!');
    },
  });

  // Logout
  const logoutMutation = useMutation({
    mutationFn: () => {
      console.log("Sending logout request");
      return authClient.post("/logout");
    },
    onSuccess: () => {
      console.log("Logout successful");
      // تنظيف التوكن من المتصفح
      setToken(null);
      // توجيه المستخدم إلى صفحة تسجيل الدخول
      navigate("/login");
      toast.success('Logged out successfully!');
    },
    onError: (error) => {
      console.error("Logout error:", error);
      console.error("Error details:", error.response ? error.response.data : 'No response');
      // حتى لو فشل الطلب، نقوم بتنظيف التوكن محلياً
      setToken(null);
      navigate("/login");
      toast.success('Logged out successfully!');
    }
  });

  return {
    login: loginMutation.mutate,
    register: registerMutation.mutate,
    logout: logoutMutation.mutate,
    isLoading: loginMutation.isPending || registerMutation.isPending || logoutMutation.isPending,
    error: loginMutation.error || registerMutation.error || logoutMutation.error,
    isError: loginMutation.isError || registerMutation.isError || logoutMutation.isError,
  };
};

export default useAuth;
