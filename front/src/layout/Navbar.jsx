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
  FaTachometerAlt,
} from "react-icons/fa";
import useAuthStore from "../app/authStore";
import useAuth from "../hooks/useAuth";
import Favorite from "../Products/Favorite";
import DrawerProduct from "../Products/DrawerProduct";
import ThemeToggle from "../components/Theme/ThemeToggle";
import axios from "axios";

const Navbar = () => {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [userRole, setUserRole] = useState(null);
  const { token, user } = useAuthStore();
  const { logout } = useAuth();

  useEffect(() => {
    console.log("Current token:", token);
    if (token) {
      console.log("Token exists, fetching user data");
      axios
        .get("/user", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          console.log("User data received:", response.data);
          const isAdmin =
            response.data.is_admin === 1 || response.data.is_admin === true;
          const isEmployee =
            response.data.is_employee_role === 1 ||
            response.data.is_employee_role === true;

          console.log("isAdmin:", isAdmin, "isEmployee:", isEmployee);
          setUserRole({
            isAdmin,
            isEmployee,
          });
        })
        .catch((error) => {
          console.error("Error fetching user data:", error);
          console.error(
            "Error details:",
            error.response ? error.response.data : "No response"
          );
        });
    }
  }, [token]);

  const toggleMenu = () => {
    setIsMenuOpen(!isMenuOpen);
  };

  const handleLogout = () => {
    console.log("Logout button clicked");
    logout();
  };

  const canAccessDashboard = userRole?.isAdmin || userRole?.isEmployee;

  console.log("User role:", userRole);
  console.log("Can access dashboard:", canAccessDashboard);

  return (
    <div className="fixed top-0 left-0 right-0 z-50">
      <nav className="navbar-container ">
        <div className="navbar-wrapper">
          <Link to="/" className="navbar-brand">
            <img
              src="https://flowbite.com/docs/images/logo.svg"
              className="h-8"
              alt="Flowbite Logo"
            />
            <span className="navbar-brand-text">Project</span>
          </Link>
          <button
            onClick={toggleMenu}
            type="button"
            className="navbar-menu-button"
            aria-controls="navbar-default"
            aria-expanded={isMenuOpen}
          >
            <span className="sr-only">Open main menu</span>
            {isMenuOpen ? (
              <FaTimes className="navbar-menu-icon" />
            ) : (
              <FaBars className="navbar-menu-icon" />
            )}
          </button>
          <div
            className={`navbar-menu ${isMenuOpen ? "open" : ""}`}
            id="navbar-default"
          >
            <ul className="navbar-menu-list">
              <li className="navbar-menu-item">
                <NavLink
                  to="/"
                  className={({ isActive }) =>
                    `navbar-link ${isActive ? "active" : ""}`
                  }
                >
                  <FaHome className="navbar-link-icon" />
                  Home
                </NavLink>
              </li>
              <li className="navbar-menu-item">
                <NavLink
                  to="/products"
                  className={({ isActive }) =>
                    `navbar-link ${isActive ? "active" : ""}`
                  }
                >
                  <FaBox className="navbar-link-icon" />
                  Products
                </NavLink>
              </li>
              <li className="navbar-menu-item">
                <NavLink
                  to="/profile"
                  className={({ isActive }) =>
                    `navbar-link ${isActive ? "active" : ""}`
                  }
                >
                  <FaUser className="navbar-link-icon" />
                  Profile
                </NavLink>
              </li>

              {/* Dashboard - only for admins and employees */}
              // في قسم الـ navigation items، أضف هذا الكود بجانب البروفايل:
              {token && canAccessDashboard && (
              <li className="navbar-menu-item">
                <a
                  href="http://localhost:8000/admin/dashboard"
                  className="navbar-link"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  <FaTachometerAlt className="navbar-link-icon" />
                  لوحة التحكم
                </a>
              </li>
              )}

              <div className="navbar-actions">
                <Favorite />
                <DrawerProduct />
                {token ? (
                  <button
                    className="navbar-logout-button"
                    onClick={handleLogout}
                  >
                    <FaSignOutAlt className="navbar-logout-icon" />
                    Logout
                  </button>
                ) : (
                  <Link className="navbar-login-button" to={"/login"}>
                    <FaSignInAlt className="navbar-login-icon" />
                    Login
                  </Link>
                )}
              </div>
            </ul>
          </div>
          <div className="flex items-center justify-center md:justify-end">
            <ThemeToggle />
          </div>
        </div>
      </nav>
    </div>
  );
};

export default Navbar;
