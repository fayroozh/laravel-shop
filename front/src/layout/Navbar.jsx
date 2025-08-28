// src/components/Navbar.jsx
import React, { useState, useEffect } from "react";
import { Link, NavLink } from "react-router-dom";
import {
  FaBars,
  FaTimes,
  FaHome,
  FaBox,
  FaUser,
  FaSignInAlt,
  FaSignOutAlt,
  FaCog,
  FaSearch,
} from "react-icons/fa";
import useAuthStore from "../app/authStore";
import useAuth from "../hooks/useAuth";
import Favorite from "../Products/Favorite";
import DrawerProduct from "../Products/DrawerProduct";
import ThemeToggle from "../components/Theme/ThemeToggle";
import { apiClient } from "../services/api-client";
import AdvancedSearch from "../components/Search/AdvancedSearch";

const navLinkClasses = ({ isActive }) =>
  [
    "inline-flex items-center gap-2 rounded-lg px-3 py-2",
    "transition-colors duration-200",
    "text-gray-900 dark:!text-white visited:text-gray-900 dark:visited:!text-white",
    "dark:opacity-100",
    "hover:bg-black/5 dark:hover:bg-white/5",
    "focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50",
    "active:scale-[.98] active:shadow active:shadow-blue-900/30",
    "[-webkit-tap-highlight-color:transparent]",
    isActive
      ? "border border-blue-400/40 shadow-[0_0_0_2px_rgba(59,130,246,.25)] bg-transparent text-blue-700 dark:!text-white"
      : "border border-transparent bg-transparent",
  ].join(" ");

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [isAdvancedSearchOpen, setIsAdvancedSearchOpen] = useState(false);
  const [userRole, setUserRole] = useState(null);
  const { token, user } = useAuthStore();
  const { logout } = useAuth();

  useEffect(() => {
    if (!token) return;
    apiClient
      .get("/frontend/users", {
        headers: { Authorization: `Bearer ${token}` }, // ✅ صححتها
      })
      .then((response) => {
        const userData = response.data?.[0];
        const isAdmin = userData?.is_admin === 1;
        const isEmployee = userData?.is_employee_role === 1;
        setUserRole({ isAdmin, isEmployee });
      })
      .catch((error) => console.error("Error fetching user data:", error));
  }, [token]);

  const toggleMenu = () => setIsMenuOpen((v) => !v);
  const handleLogout = () => logout();
  const openAdvancedSearch = () => setIsAdvancedSearchOpen(true);
  const closeAdvancedSearch = () => setIsAdvancedSearchOpen(false);

  return (
    <div className="fixed top-0 left-0 right-0 z-50">
      <nav className="force-nav-white bg-white dark:bg-gray-800 shadow-md transition-colors duration-300">
        <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <div className="flex h-16 items-center justify-between">
            {/* Logo */}
            <Link to="/" className="flex items-center gap-2">
              <img
                src="/logo.png"
                className="h-8"
                alt="Briktha Fashion Logo"
              />
              <span className="text-xl font-semibold text-gray-800 dark:text-white">
                BRIFKTHAR
              </span>
            </Link>

            {/* Mobile toggle */}
            <button
              onClick={toggleMenu}
              type="button"
              className="inline-flex items-center justify-center md:hidden p-2 w-10 h-10 rounded-lg text-gray-600 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
              aria-controls="navbar-default"
              aria-expanded={isMenuOpen}
            >
              <span className="sr-only">Open main menu</span>
              {isMenuOpen ? <FaTimes className="w-5 h-5" /> : <FaBars className="w-5 h-5" />}
            </button>

            {/* Links */}
            <div
              id="navbar-default"
              className={`w-full md:block md:w-auto ${isMenuOpen ? "block" : "hidden"}`} // ✅ backticks
            >
              <ul className="font-medium flex flex-col md:flex-row md:items-center gap-2 md:gap-4 p-4 md:p-0 mt-4 md:mt-0 rounded-lg border md:border-0 bg-gray-50 md:bg-transparent dark:bg-gray-800 md:dark:bg-transparent border-gray-100 dark:border-gray-700">
                <li>
                  <NavLink to="/" className={navLinkClasses}>
                    <FaHome className="w-4 h-4 " />
                    <span>Home</span>
                  </NavLink>
                </li>
                <li>
                  <NavLink to="/products" className={navLinkClasses}>
                    <FaBox className="w-4 h-4" />
                    <span>Products</span>
                  </NavLink>
                </li>
                <li>
                  <NavLink to="/profile" className={navLinkClasses}>
                    <FaUser className="w-4 h-4" />
                    <span>Profile</span>
                  </NavLink>
                </li>

                {/* Mobile actions */}
                <li className="md:hidden mt-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                  <div className="flex flex-col gap-2">
                    <Favorite />
                    <DrawerProduct />
                    {token ? (
                      <button
                        onClick={handleLogout}
                        className="inline-flex items-center gap-2 p-2 rounded-lg text-red-600 dark:text-white hover:text-red-700 dark:hover:text-red-300 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
                      >
                        <FaSignOutAlt className="w-4 h-4" />
                        <span>Logout</span>
                      </button>
                    ) : (
                      <Link
                        to="/login"
                        className="inline-flex items-center gap-2 p-2 rounded-lg text-green-700 dark:text-white hover:text-green-800 dark:hover:text-green-300 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
                      >
                        <FaSignInAlt className="w-4 h-4" />
                        <span>Login</span>
                      </Link>
                    )}
                  </div>
                </li>
              </ul>
            </div>

            {/* Desktop actions */}
            <div className="hidden md:flex items-center gap-3">
              <Favorite />
              <DrawerProduct />

              {token ? (
                <button
                  onClick={handleLogout}
                  className="inline-flex items-center gap-2 p-2 rounded-lg text-red-600 dark:text-white hover:text-red-700 dark:hover:text-red-300 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
                >
                  <FaSignOutAlt className="w-5 h-5" />
                  <span>Logout</span>
                </button>
              ) : (
                <Link
                  to="/login"
                  className="inline-flex items-center gap-2 p-2 rounded-lg text-green-700 dark:text-white hover:text-green-800 dark:hover:text-green-300 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
                >
                  <FaSignInAlt className="w-5 h-5" />
                  <span>Login</span>
                </Link>
              )}

              <ThemeToggle />

              {(user?.email === "admin@example.com" ||
                localStorage.getItem("adminMode") === "true") && (
                  <a
                    href="http://localhost:8000/admin/dashboard"
                    className="p-2 rounded-lg text-gray-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
                    title="لوحة تحكم الأدمن"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    <FaCog className="w-5 h-5" />
                  </a>
                )}

              <button
                onClick={openAdvancedSearch}
                className="p-2 rounded-lg text-gray-700 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 hover:bg-black/5 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/50"
              >
                <FaSearch className="w-5 h-5" />
              </button>
            </div>
          </div>
        </div>
      </nav>

      {/* Advanced Search Modal */}
      {isAdvancedSearchOpen && (
        <AdvancedSearch isOpen={isAdvancedSearchOpen} onClose={closeAdvancedSearch} />
      )}
    </div>
  );
};

export default Navbar;