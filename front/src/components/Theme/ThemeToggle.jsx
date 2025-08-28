import { FaSun, FaMoon } from "react-icons/fa";
import { useTheme } from "./ThemeProvider";

export default function ThemeToggle() {
  const { isDark, toggle } = useTheme();
  return (
    <button
      onClick={toggle}
      className="p-2 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-300"
      aria-label="Toggle theme"
      title={isDark ? "Light mode" : "Dark mode"}
    >
      {isDark ? <FaSun className="text-yellow-400 text-xl" /> : <FaMoon className="text-gray-700 text-xl" />}
    </button>
  );
}