import { createContext, useContext, useEffect, useState, useCallback } from "react";
import useAuthStore from "../../app/authStore";
import { apiClient, authClient } from "../../services/api-client";

const STORAGE_KEY = "theme";
const ThemeCtx = createContext(null);

export function ThemeProvider({ children }) {
    const { user, token, setUserTheme } = useAuthStore();

    const [isDark, setIsDark] = useState(() => {
        if (user?.theme) return user.theme === 'dark';
        const saved = localStorage.getItem(STORAGE_KEY);
        if (saved) return saved === 'dark';
        return window.matchMedia("(prefers-color-scheme: dark)").matches;
    });

    useEffect(() => {
        if (user?.theme && (user.theme === 'dark') !== isDark) {
            setIsDark(user.theme === 'dark');
        }
    }, [user, isDark]);

    useEffect(() => {
        document.documentElement.classList.toggle("dark", isDark);
    }, [isDark]);

    useEffect(() => {
        const mq = window.matchMedia("(prefers-color-scheme: dark)");
        const handler = (e) => {
            if (!token && !localStorage.getItem(STORAGE_KEY)) {
                setIsDark(e.matches);
            }
        };
        mq.addEventListener("change", handler);
        return () => mq.removeEventListener("change", handler);
    }, [token]);

    const setTheme = useCallback((newTheme) => {
        const newIsDark = newTheme === 'dark';
        setIsDark(newIsDark);
        localStorage.setItem(STORAGE_KEY, newTheme);

        // فقط إذا كان المستخدم مسجل دخول، نحاول حفظ التفضيل في الخادم
        if (token && setUserTheme) {
            setUserTheme(newTheme);
            try {
                authClient.post('/update-theme', { theme: newTheme })
                    .catch(err => {
                        console.error("Failed to update theme on server:", err);
                        // حتى لو فشل الحفظ في الخادم، التغيير المحلي سيظل يعمل
                    });
            } catch (err) {
                console.error("Error updating theme:", err);
            }
        }
    }, [token, setUserTheme]);

    const toggleTheme = useCallback(() => {
        setTheme(isDark ? 'light' : 'dark');
    }, [isDark, setTheme]);

    const value = {
        isDark,
        toggle: toggleTheme,
        setTheme: (mode) => setTheme(mode)
    };

    return <ThemeCtx.Provider value={value}>{children}</ThemeCtx.Provider>;
}

export const useTheme = () => useContext(ThemeCtx);