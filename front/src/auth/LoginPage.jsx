import React, { useEffect } from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { motion } from "framer-motion";
import { useNavigate, Link } from "react-router-dom";
import { toast } from "react-toastify";
import useAuthStore from "../app/authStore";  // افترض إنه Zustand أو Context
import useAuth from "../hooks/useAuth";       // هو كustom hook للتعامل مع الـ API
import { fadeIn, hoverScale, slideIn } from "../../utils/variants";
import { loginSchema } from "./schema";
import { InputField } from "../components/InputField";
import LoadingSpinner from "../components/LoadingSpinner";
import axios from "axios";
import { FaTachometerAlt } from "react-icons/fa";

const UserIcon = () => (
  <svg className="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
  </svg>
);

const LockIcon = () => (
  <svg className="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
  </svg>
);

const EmailIcon = () => (
  <svg className="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
  </svg>
);

const LoginPage = () => {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(loginSchema) });

  const { login, isLoading, isError, error } = useAuth();
  const { token, setUser } = useAuthStore(); // إضافة setUser هنا
  const navigate = useNavigate();

  // مهم: منع التوجيه (redirect) داخل render مباشرة
  useEffect(() => {
    if (token) {
      // إذا التوكن موجود، نحاول الحصول على معلومات المستخدم
      axios.get('/user', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      .then(response => {
        // تخزين معلومات المستخدم
        setUser(response.data);
        
        // التحقق إذا كان المستخدم مشرف أو موظف
        const isAdmin = response.data.is_admin === 1 || response.data.is_admin === true;
        const isEmployee = response.data.is_employee_role === 1 || response.data.is_employee_role === true;
        
        // إذا كان المستخدم مشرف أو موظف، توجيهه إلى لوحة التحكم
        if (isAdmin || isEmployee) {
          // Change from:
          // navigate("/admin/dashboard");
          // To:
          window.location.href = "http://localhost:8000/admin/dashboard";
        } else {
          // وإلا توجيهه إلى الصفحة الرئيسية
          navigate("/");
        }
      })
      .catch(error => {
        console.error('Error fetching user data:', error);
        navigate("/");
      });
    }
  }, [token, navigate, setUser]);

  const onSubmit = async (data) => {
    try {
      // ⬅️ أولاً: جلب توكن CSRF
      await axios.get("http://localhost:8000/sanctum/csrf-cookie", {
        withCredentials: true,
      });
  
      // ⬅️ ثانياً: إرسال بيانات تسجيل الدخول
      await login(data); // يفترض أن login يستخدم axios.post('/login', data)
  
      toast.success("تم تسجيل الدخول بنجاح!");
    } catch (err) {
      toast.error("فشل تسجيل الدخول. تأكد من البيانات.");
    }
  };
  

  return (
    <div className="min-h-screen bg-gradient-to-r from-blue-100 via-white to-purple-100 flex items-center justify-center">
      <div className="absolute inset-0 overflow-hidden z-0">
        <motion.div
          className="absolute top-20 left-20 w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"
          animate={{ x: [0, 30, 0], y: [0, 40, 0] }}
          transition={{ repeat: Infinity, duration: 8, ease: "easeInOut" }}
        />
        <motion.div
          className="absolute bottom-20 right-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70"
          animate={{ x: [0, -30, 0], y: [0, -40, 0] }}
          transition={{ repeat: Infinity, duration: 10, ease: "easeInOut" }}
        />
      </div>

      <div className="flex w-full max-w-5xl mx-auto rounded-xl shadow-lg overflow-hidden z-10">
        {/* Left side - Image */}
        <div className="hidden lg:block lg:w-1/2 relative">
          <img
            src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=1964&q=80"
            alt="Login"
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-blue-900/70 to-transparent flex flex-col justify-end p-8 text-white">
            <motion.h2 className="text-3xl font-bold mb-2" {...fadeIn} transition={{ delay: 0.2 }}>
              Welcome Back
            </motion.h2>
            <motion.p {...fadeIn} transition={{ delay: 0.4 }}>
              Sign in to continue your journey with us
            </motion.p>
          </div>
        </div>

        {/* Right side - Form */}
        <motion.div className="w-full lg:w-1/2 bg-white p-8 md:p-12" {...slideIn(20)}>
          <div className="flex justify-center mb-8">
            <motion.img
              className="h-12"
              src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
              alt="logo"
              {...hoverScale}
            />
          </div>

          <h1 className="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-8">
            Sign in to your account
          </h1>

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6">
            {isError && (
              <div className="p-3 bg-red-100 border border-red-200 text-red-700 rounded-md text-sm">
                {error?.response?.data?.message || "Login failed. Please check your credentials."}
              </div>
            )}

            <InputField
              label="Username"
              type="text"
              placeholder="Enter your username"
              icon={<UserIcon />}
              register={register}
              errors={errors}
              name="username"
            />

            <InputField
              label="Email"
              type="email"
              placeholder="Enter your email"
              icon={<EmailIcon />}
              register={register}
              errors={errors}
              name="email"
            />

            <InputField
              label="Password"
              type="password"
              placeholder="••••••••"
              icon={<LockIcon />}
              register={register}
              errors={errors}
              name="password"
            />

            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <input
                  id="remember-me"
                  name="remember-me"
                  type="checkbox"
                  className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label htmlFor="remember-me" className="ml-2 block text-sm text-gray-700">
                  Remember me
                </label>
              </div>
              <a href="#" className="text-sm font-medium text-blue-600 hover:text-blue-500">
                Forgot password?
              </a>
            </div>

            <motion.button
              {...hoverScale}
              type="submit"
              disabled={isLoading}
              className="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              {isLoading ? <LoadingSpinner /> : "Sign in"}
            </motion.button>

            // إضافة زر لوحة التحكم في نهاية النموذج
            <div className="mt-4 text-center">
              <p className="text-sm text-gray-600">
                Don't have an account?{" "}
                <Link to="/register" className="font-medium text-blue-600 hover:text-blue-500">
                  Sign up
                </Link>
              </p>
              
              {/* إضافة زر لوحة التحكم */}
              <div className="mt-4">
                <Link to="/admin/dashboard" className="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                  <FaTachometerAlt className="mr-2" />
                  الوصول إلى لوحة التحكم
                </Link>
              </div>
            </div>
          </form>
        </motion.div>
      </div>
    </div>
  );
};

export default LoginPage;
