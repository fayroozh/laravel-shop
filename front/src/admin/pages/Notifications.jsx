import { useState, useEffect } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import { API_URL } from "../../constant/api";

function Notifications() {
  const [notifications, setNotifications] = useState([]);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState("all");

  useEffect(() => {
    fetchNotifications();
  }, []);

  const fetchNotifications = async () => {
    try {
      setLoading(true);
      // استخدام API_URL بدلاً من المسار المباشر
      const response = await axios.get(`${API_URL}/notifications`);
      setNotifications(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching notifications:", error);
      setLoading(false);
    }
  };

  const markAsRead = async (id) => {
    try {
      // استخدام API_URL بدلاً من المسار المباشر
      await axios.post(`${API_URL}/notifications/${id}/mark-as-read`);
      fetchNotifications();
    } catch (error) {
      console.error("Error marking notification as read:", error);
    }
  };

  const markAllAsRead = async () => {
    try {
      // استخدام API_URL بدلاً من المسار المباشر
      await axios.post(`${API_URL}/notifications/mark-all-as-read`);
      fetchNotifications();
    } catch (error) {
      console.error("Error marking all notifications as read:", error);
    }
  };

  // دالة لتنسيق الوقت
  const formatTimeAgo = (dateString) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
      return "منذ أقل من دقيقة";
    }
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
      return `منذ ${diffInMinutes} دقيقة`;
    }
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
      return `منذ ${diffInHours} ساعة`;
    }
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) {
      return `منذ ${diffInDays} يوم`;
    }
    
    const diffInMonths = Math.floor(diffInDays / 30);
    return `منذ ${diffInMonths} شهر`;
  };

  // تصفية الإشعارات حسب النوع النشط
  const filteredNotifications = notifications.filter(notification => {
    if (activeTab === "all") return true;
    if (activeTab === "unread") return !notification.read_at;
    if (activeTab === "inventory") return notification.type.includes("LowStock");
    if (activeTab === "orders") return notification.type.includes("Order");
    if (activeTab === "system") return notification.type.includes("System");
    return true;
  });

  return (
    <div className="p-4">
      <div className="mb-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">🔔 الإشعارات</h1>
        <button
          onClick={markAllAsRead}
          className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          تعيين الكل كمقروء
        </button>
      </div>

      {/* تبويبات تصفية الإشعارات */}
      <div className="mb-6 border-b">
        <ul className="flex flex-wrap -mb-px text-sm font-medium text-center">
          <li className="mr-2">
            <button
              onClick={() => setActiveTab("all")}
              className={`inline-block p-4 rounded-t-lg ${activeTab === "all" ? "border-b-2 border-blue-600 text-blue-600" : "border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"}`}
            >
              الكل
            </button>
          </li>
          <li className="mr-2">
            <button
              onClick={() => setActiveTab("unread")}
              className={`inline-block p-4 rounded-t-lg ${activeTab === "unread" ? "border-b-2 border-blue-600 text-blue-600" : "border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"}`}
            >
              غير مقروء
            </button>
          </li>
          <li className="mr-2">
            <button
              onClick={() => setActiveTab("inventory")}
              className={`inline-block p-4 rounded-t-lg ${activeTab === "inventory" ? "border-b-2 border-blue-600 text-blue-600" : "border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"}`}
            >
              المخزون
            </button>
          </li>
          <li className="mr-2">
            <button
              onClick={() => setActiveTab("orders")}
              className={`inline-block p-4 rounded-t-lg ${activeTab === "orders" ? "border-b-2 border-blue-600 text-blue-600" : "border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"}`}
            >
              الطلبات
            </button>
          </li>
          <li>
            <button
              onClick={() => setActiveTab("system")}
              className={`inline-block p-4 rounded-t-lg ${activeTab === "system" ? "border-b-2 border-blue-600 text-blue-600" : "border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300"}`}
            >
              النظام
            </button>
          </li>
        </ul>
      </div>

      {/* قائمة الإشعارات */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg">
        {loading ? (
          <p className="text-center text-gray-500 p-4">جاري تحميل الإشعارات...</p>
        ) : filteredNotifications.length > 0 ? (
          <ul className="divide-y divide-gray-200">
            {filteredNotifications.map((notification) => {
              // تحديد نوع الإشعار وأيقونته ولونه
              let icon = "🔔";
              let bgColor = "bg-gray-100";
              let link = "#";
              
              if (notification.type.includes("LowStock")) {
                icon = "📦";
                bgColor = "bg-yellow-100";
                link = `/admin/products`;
              } else if (notification.type.includes("NewOrder")) {
                icon = "🛒";
                bgColor = "bg-green-100";
                link = `/admin/orders`;
              } else if (notification.type.includes("System")) {
                icon = "⚙️";
                bgColor = "bg-blue-100";
              }
              
              return (
                <li key={notification.id} className={`p-4 ${!notification.read_at ? bgColor : ""}`}>
                  <div className="flex items-start space-x-3 rtl:space-x-reverse">
                    <div className="text-2xl">{icon}</div>
                    <div className="flex-1">
                      <div className="flex justify-between">
                        <Link to={link} className="text-gray-900 font-medium hover:underline">
                          {notification.data.title || "إشعار جديد"}
                        </Link>
                        <span className="text-sm text-gray-500">{formatTimeAgo(notification.created_at)}</span>
                      </div>
                      <p className="text-gray-600">{notification.data.message}</p>
                      {!notification.read_at && (
                        <button
                          onClick={() => markAsRead(notification.id)}
                          className="mt-2 text-sm text-blue-600 hover:underline"
                        >
                          تعيين كمقروء
                        </button>
                      )}
                    </div>
                  </div>
                </li>
              );
            })}
          </ul>
        ) : (
          <p className="text-center text-gray-500 p-4">لا توجد إشعارات {activeTab !== "all" ? "في هذا القسم" : ""}</p>
        )}
      </div>
    </div>
  );
}

export default Notifications;