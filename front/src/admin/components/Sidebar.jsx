import React from "react";
import { NavLink } from "react-router-dom";
import '../styles/app.css'; // تعديل المسار ليشير إلى المجلد الأب

const links = [
  { to: "/admin/dashboard", label: "Dashboard" },
  { to: "/admin/products", label: "المنتجات" },
  { to: "/admin/categories", label: "الفئات" },
  { to: "/admin/orders", label: "الطلبات" },
  { to: "/admin/users", label: "المستخدمون" },
  { to: "/admin/employees", label: "الموظفون" },
  { to: "/admin/feedback", label: "الملاحظات" },
  { to: "/admin/activities", label: "النشاطات" },
  { to: "/admin/notifications", label: "الإشعارات" },
  { to: "/admin/roles", label: "الصلاحيات" },
  { to: "/admin/reports", label: "التقارير" }
];

export default function Sidebar() {
  return (
    <aside className="w-64 bg-gray-800 text-white p-4 space-y-2">
      <h2 className="text-xl font-bold mb-4">لوحة التحكم</h2>
      <nav className="flex flex-col space-y-1">
        {links.map(link => (
          <NavLink
            key={link.to}
            to={link.to}
            className={({ isActive }) =>
              "p-2 rounded " + (isActive ? "bg-gray-700" : "hover:bg-gray-700")
            }
          >
            {link.label}
          </NavLink>
        ))}
      </nav>
    </aside>
  );
}
