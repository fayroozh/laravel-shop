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

// استيراد صفحة لوحة التحكم (يجب إنشاؤها إذا لم تكن موجودة)
// import AdminDashboard from "../admin/Dashboard";
// {
//   path: "admin/dashboard",
//   element: <AdminDashboard />,
// },

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
      // // إضافة مسار لوحة التحكم
      // {
      //   path: "admin/dashboard",
      //   element: <AdminDashboard />,
      // },
    ],
  },
]);
export default router;
