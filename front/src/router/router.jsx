import { createBrowserRouter } from "react-router-dom";
import App from "../App";
import FetchProject from "../Products/FetchProduct";
import ProductDetailes from "../Products/ProductDetails/ProductDetailes";
import Checkout from "../Checkout/Checkout";
import ProfilePage from "../auth/ProfilePage";
import LoginPage from "../auth/LoginPage";
import RegisterPage from "../auth/RegisterPage";
import Slider from "../slider/Slider";
import Home from "../Home/Home";
import EditProfilePage from "../auth/EditProfilePage";


// استيراد مكونات لوحة التحكم
import AdminLayout from "../admin/layouts/AdminLayout";
import Dashboard from "../admin/pages/Dashboard";
import AdminProducts from "../admin/pages/Products";
import AdminOrders from "../admin/pages/Orders";
import AdminFeedback from "../admin/pages/Feedback";
import AdminUsers from "../admin/pages/Users";
import AdminEmployees from "../admin/pages/Employees";
import AdminReports from "../admin/pages/Reports";

// استيراد المكونات الجديدة
import AdminCategories from "../admin/pages/Categories";
import AdminRoles from "../admin/pages/Roles";
import AdminActivities from "../admin/pages/Activities";
import AdminNotifications from "../admin/pages/Notifications";

const router = createBrowserRouter([
  {
    path: "/",
    element: <App />,
    children: [
      {
        index: true,
        element: <Home />,
      },
      {
        path: "profile",
        element: <ProfilePage />,
      },
       {
        path: "edit-profile", // إضافة المسار هنا
        element: <EditProfilePage />,
      },
      {
        path: "login",
        element: <LoginPage />,
      },
      {
        path: "register",
        element: <RegisterPage />,
      },
      {
        path: "products",
        element: <FetchProject />,
      },
      {
        path: "product/:id",
        element: <ProductDetailes />,
      },
      {
        path: "checkout",
        element: <Checkout />,
      },
      {
        path: "slider",
        element: <Slider />,
      },
    ],
  },
  // إضافة مسارات لوحة التحكم
  {
    path: "/admin",
    element: <AdminLayout />,
    children: [
      {
        path: "dashboard",
        element: <Dashboard />,
      },
      {
        path: "products",
        element: <AdminProducts />,
      },
      {
        path: "categories",
        element: <AdminCategories />,
      },
      {
        path: "orders",
        element: <AdminOrders />,
      },
      {
        path: "feedback",
        element: <AdminFeedback />,
      },
      {
        path: "notifications",
        element: <AdminNotifications />,
      },
      {
        path: "activities",
        element: <AdminActivities />,
      },
      {
        path: "users",
        element: <AdminUsers />,
      },
      {
        path: "employees",
        element: <AdminEmployees />,
      },
      {
        path: "roles",
        element: <AdminRoles />,
      },
      {
        path: "reports",
        element: <AdminReports />,
      },
    ],
  },
]);

export default router;
