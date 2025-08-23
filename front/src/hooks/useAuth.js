import { useMutation } from "@tanstack/react-query";
import useAuthStore from "../app/authStore";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { authClient } from "../services/api-client";

export const useAuth = () => {
    const { setToken, setUser } = useAuthStore();
    const navigate = useNavigate();

    // تسجيل الدخول
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
            setUser(data.user);

            const isAdmin =
                data.user.is_admin === 1 || data.user.is_admin === true;
            const isEmployee =
                data.user.is_employee_role === 1 || data.user.is_employee_role === true;

            if (isAdmin || isEmployee) {
                window.location.href = "http://localhost:8000/admin/dashboard";
                toast.success("تم تسجيل الدخول بنجاح! جاري توجيهك إلى لوحة التحكم");
            } else {
                navigate("/");
                toast.success("تم تسجيل الدخول بنجاح!");
            }
        },
        onError: (error) => {
            toast.error("فشل تسجيل الدخول. تأكد من البيانات.");
            console.error("Login error:", error);
        },
    });

    // تسجيل مستخدم جديد
    // تغيير مسار CSRF cookie
    const registerMutation = useMutation({
        mutationFn: async (userData) => {
            try {
                // جلب CSRF Token من المسار الصحيح
                await authClient.get("/sanctum/csrf-cookie");
                
                await new Promise(resolve => setTimeout(resolve, 2000));
                
                const response = await authClient.post("/register", userData);
                return response.data;
            } catch (error) {
                console.error("Registration error:", error);
                throw error;
            }
        },
        onSuccess: () => {
            toast.success("تم التسجيل بنجاح! يمكنك الآن تسجيل الدخول.");
            navigate("/login");
        },
        onError: (error) => {
            toast.error("فشل التسجيل. تأكد من صحة البيانات.");
        }
    });

    // تسجيل الخروج
    const logoutMutation = useMutation({
        mutationFn: () => {
            return authClient.post("/logout");
        },
        onSuccess: () => {
            setToken(null);
            navigate("/login");
            toast.success("تم تسجيل الخروج بنجاح!");
        },
        onError: (error) => {
            console.error("Logout error:", error);
            setToken(null);
            navigate("/login");
            toast.success("تم تسجيل الخروج بنجاح!");
        },
    });

    return {
        login: loginMutation.mutate,
        register: registerMutation.mutate,
        logout: logoutMutation.mutate,
        isLoading:
            loginMutation.isPending ||
            registerMutation.isPending ||
            logoutMutation.isPending,
        error:
            loginMutation.error ||
            registerMutation.error ||
            logoutMutation.error,
        isError:
            loginMutation.isError ||
            registerMutation.isError ||
            logoutMutation.isError,
    };
};

export default useAuth;
