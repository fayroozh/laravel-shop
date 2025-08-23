import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import adminApiClient from "../../services/adminApiClient";

function Dashboard() {
  const [stats, setStats] = useState({
    products: 0,
    orders: 0,
    employees: 0,
    suppliers: 0,
    productsThisMonth: 0,
    ordersThisMonth: 0,
    employeesThisMonth: 0,
    suppliersThisMonth: 0
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDashboardStats();
  }, []);

  const fetchDashboardStats = async () => {
    try {
      setLoading(true);
      const response = await adminApiClient.get(`/dashboard/stats`);
      setStats(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching dashboard stats:", error);
      setLoading(false);
    }
  };

  const exportReport = (format) => {
    // تنفيذ تصدير التقرير
    console.log(`Exporting dashboard report as ${format}`);
    // يمكن تنفيذ طلب API لتصدير التقرير
    // window.open(`${API_URL}/dashboard/export?format=${format}`, '_blank');
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">📊 إحصائيات لوحة التحكم</h1>
        <div className="dashboard-actions space-x-2 rtl:space-x-reverse">
          <button 
            onClick={() => exportReport('pdf')} 
            className="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700"
          >
            📄 تصدير PDF
          </button>
          <button 
            onClick={() => exportReport('excel')} 
            className="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            📊 تصدير Excel
          </button>
        </div>
      </div>

      {loading ? (
        <div className="text-center py-10">جاري تحميل الإحصائيات...</div>
      ) : (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {/* بطاقة المنتجات */}
          <div className="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
            <div className="flex items-center">
              <div className="text-3xl mr-4">📦</div>
              <div>
                <div className="text-lg font-semibold">إجمالي المنتجات</div>
                <div className="text-3xl font-bold">{stats.products}</div>
                <div className="text-sm text-green-600">+{stats.productsThisMonth} هذا الشهر</div>
              </div>
            </div>
            <Link to="/admin/products" className="block mt-4 text-blue-600 hover:underline text-right">
              عرض التفاصيل →
            </Link>
          </div>

          {/* بطاقة الطلبات */}
          <div className="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
            <div className="flex items-center">
              <div className="text-3xl mr-4">🛒</div>
              <div>
                <div className="text-lg font-semibold">إجمالي الطلبات</div>
                <div className="text-3xl font-bold">{stats.orders}</div>
                <div className="text-sm text-green-600">+{stats.ordersThisMonth} هذا الشهر</div>
              </div>
            </div>
            <Link to="/admin/orders" className="block mt-4 text-blue-600 hover:underline text-right">
              عرض التفاصيل →
            </Link>
          </div>

          {/* بطاقة الموظفين */}
          <div className="bg-white rounded-lg shadow p-6 border-t-4 border-purple-500">
            <div className="flex items-center">
              <div className="text-3xl mr-4">👥</div>
              <div>
                <div className="text-lg font-semibold">إجمالي الموظفين</div>
                <div className="text-3xl font-bold">{stats.employees}</div>
                <div className="text-sm text-green-600">+{stats.employeesThisMonth} هذا الشهر</div>
              </div>
            </div>
            <Link to="/admin/employees" className="block mt-4 text-blue-600 hover:underline text-right">
              عرض التفاصيل →
            </Link>
          </div>

          {/* بطاقة الموردين */}
          <div className="bg-white rounded-lg shadow p-6 border-t-4 border-yellow-500">
            <div className="flex items-center">
              <div className="text-3xl mr-4">🏭</div>
              <div>
                <div className="text-lg font-semibold">إجمالي الموردين</div>
                <div className="text-3xl font-bold">{stats.suppliers}</div>
                <div className="text-sm text-green-600">+{stats.suppliersThisMonth} هذا الشهر</div>
              </div>
            </div>
            <Link to="/admin/suppliers" className="block mt-4 text-blue-600 hover:underline text-right">
              عرض التفاصيل →
            </Link>
          </div>
        </div>
      )}
    </div>
  );
}

export default Dashboard;
