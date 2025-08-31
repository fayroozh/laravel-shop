import { useMutation } from "@tanstack/react-query";
import useAuthStore from "../app/authStore";
import { useNavigate } from "react-router-dom";
import { toast } from "react-toastify";
import { authClient } from "../services/api-client";

export const useAuth = () => {
    const { setToken, setUser } = useAuthStore();
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
            setUser(data.user);

            // Always redirect the user to the homepage after login
            navigate("/");

            if (data.user.email === 'admin@example.com') {
                toast.success("Logged in successfully as Admin!");
            } else {
                toast.success("Logged in successfully!");
            }
        },
        onError: (error) => {
            toast.error("Login failed. Please check your credentials.");
            console.error("Login error:", error);
        },
    });

    // Register a new user
    const registerMutation = useMutation({
        mutationFn: async (userData) => {
            await authClient.get("/sanctum/csrf-cookie");
            const response = await authClient.post("/register", userData);
            return response.data;
        },
        onSuccess: (data) => {
            setToken(data.token);
            setUser(data.user);
            navigate("/");
            toast.success("Registration successful! Welcome to our platform.");
        },
        onError: (error) => {
            toast.error("Registration failed. Please check the provided data.");
        }
    });

    // Logout
    const logoutMutation = useMutation({
        mutationFn: () => {
            return authClient.post("/logout");
        },
        onSuccess: () => {
            setToken(null);
            navigate("/login");
            toast.success("Logged out successfully!");
        },
        onError: (error) => {
            console.error("Logout error:", error);
            setToken(null);
            navigate("/login");
            toast.success("Logged out successfully!");
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
