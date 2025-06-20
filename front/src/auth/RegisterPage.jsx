import React from "react";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { motion } from "framer-motion";
import { useNavigate, Link } from "react-router-dom";
import { toast } from "react-toastify";
import useAuth from "../hooks/useAuth";
import { fadeIn, hoverScale, slideIn } from "../../utils/variants";
import { registerSchema } from "./schema";
import { InputField } from "../components/InputField";
import LoadingSpinner from "../components/LoadingSpinner";

const UserIcon = () => (
  <svg
    className="h-5 w-5 text-gray-400"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20"
    fill="currentColor"
  >
    <path
      fillRule="evenodd"
      d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
      clipRule="evenodd"
    />
  </svg>
);

const EmailIcon = () => (
  <svg
    className="h-5 w-5 text-gray-400"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20"
    fill="currentColor"
  >
    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
  </svg>
);

const LockIcon = () => (
  <svg
    className="h-5 w-5 text-gray-400"
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 20 20"
    fill="currentColor"
  >
    <path
      fillRule="evenodd"
      d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
      clipRule="evenodd"
    />
  </svg>
);

const RegisterPage = () => {
  const {
    register,
    handleSubmit,
    formState: { errors },
  } = useForm({ resolver: zodResolver(registerSchema) });

  const { register: registerUser, isLoading, isError, error } = useAuth();
  const navigate = useNavigate();

  const onSubmit = async (data) => {
    try {
      await registerUser({
        name: data.name,
        email: data.email,
        password: data.password,
        password_confirmation: data.password_confirmation,
      });
      toast.success("Registration successful!");
      navigate("/login");
    } catch (err) {
      toast.error("Registration failed. Please try again.");
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-r from-blue-100 via-white to-purple-100 flex items-center justify-center">
      {/* Background Animated Blobs */}
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
        {/* Left side - Image & Welcome Text */}
        <div className="hidden lg:block lg:w-1/2 relative">
          <img
            src="https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?ixlib=rb-4.0.3&auto=format&fit=crop&w=1964&q=80"
            alt="Register"
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-blue-900/70 to-transparent flex flex-col justify-end p-8 text-white">
            <motion.h2
              className="text-3xl font-bold mb-2"
              {...fadeIn}
              transition={{ delay: 0.2 }}
            >
              Join Our Community
            </motion.h2>
            <motion.p {...fadeIn} transition={{ delay: 0.4 }}>
              Create an account to start shopping with us
            </motion.p>
          </div>
        </div>

        {/* Right side - Form */}
        <motion.div
          className="w-full lg:w-1/2 bg-white p-8 md:p-12"
          {...slideIn(20)}
        >
          {/* Logo */}
          <div className="flex justify-center mb-8">
            <motion.img
              className="h-12"
              src="https://flowbite.s3.amazonaws.com/blocks/marketing-ui/logo.svg"
              alt="logo"
              {...hoverScale}
            />
          </div>

          <h1 className="text-2xl md:text-3xl font-bold text-center text-gray-800 mb-8">
            Create your account
          </h1>

          <form onSubmit={handleSubmit(onSubmit)} className="space-y-6" noValidate>
            {isError && (
              <div className="p-3 bg-red-100 border border-red-200 text-red-700 rounded-md text-sm">
                {error?.response?.data?.message ||
                  "Registration failed. Please check your information."}
              </div>
            )}

            <InputField
              label="Name"
              type="text"
              placeholder="Enter your full name"
              icon={<UserIcon />}
              register={register}
              errors={errors}
              name="name"
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

            <InputField
              label="Confirm Password"
              type="password"
              placeholder="••••••••"
              icon={<LockIcon />}
              register={register}
              errors={errors}
              name="password_confirmation"
            />

            <motion.button
              {...hoverScale}
              type="submit"
              disabled={isLoading}
              className="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              {isLoading ? <LoadingSpinner /> : "Create Account"}
            </motion.button>

            <div className="mt-4 text-center">
              <p className="text-sm text-gray-600">
                Already have an account?{" "}
                <Link to="/login" className="font-medium text-blue-600 hover:text-blue-500">
                  Sign in
                </Link>
              </p>
            </div>
          </form>
        </motion.div>
      </div>
    </div>
  );
};

export default RegisterPage;
