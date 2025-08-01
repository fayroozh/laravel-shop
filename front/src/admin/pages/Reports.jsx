import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Reports() {
  const [reports, setReports] = useState([]);
  const [loading, setLoading] = useState(true);
  const [reportType, setReportType] = useState("sales"); // sales, inventory, employees

  useEffect(() => {
    fetchReports();
  }, [reportType]);

  const fetchReports = async () => {
    try {
      setLoading(true);
      // استخدام API_URL بدلاً من المسار المباشر
      const response = await axios.get(`${API_URL}/reports/${reportType}`);
      setReports(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching reports:", error);
      setLoading(false);
    }
  };

  return (
    <div className="p-4">
      <div className="mb-6 text-center">
        <h1 className="text-2xl font-bold">📊 التقارير</h1>
        <p className="text-gray-600">عرض وتحليل تقارير المبيعات والمخزون وأداء الموظفين</p>
      </div>

      {/* أزرار اختيار نوع التقرير */}
      <div className="mb-6 flex justify-center space-x-4 rtl:space-x-reverse">
        <button
          onClick={() => setReportType("sales")}
          className={`px-4 py-2 rounded ${reportType === "sales" ? "bg-blue-600 text-white" : "bg-gray-200 text-gray-800"}`}
        >
          تقارير المبيعات
        </button>
        <button
          onClick={() => setReportType("inventory")}
          className={`px-4 py-2 rounded ${reportType === "inventory" ? "bg-blue-600 text-white" : "bg-gray-200 text-gray-800"}`}
        >
          تقارير المخزون
        </button>
        <button
          onClick={() => setReportType("employees")}
          className={`px-4 py-2 rounded ${reportType === "employees" ? "bg-blue-600 text-white" : "bg-gray-200 text-gray-800"}`}
        >
          تقارير الموظفين
        </button>
      </div>

      {/* عرض التقارير */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        {loading ? (
          <p className="text-center text-gray-500">جاري تحميل التقارير...</p>
        ) : reports.length > 0 ? (
          <div>
            {reportType === "sales" && (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفترة</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجمالي المبيعات</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد الطلبات</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">متوسط قيمة الطلب</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {reports.map((report, index) => (
                      <tr key={index}>
                        <td className="px-6 py-4 whitespace-nowrap">{report.period}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.total_sales} ر.س</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.order_count}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.average_order} ر.س</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            {reportType === "inventory" && (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنتج</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكمية الحالية</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحد الأدنى</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تحديث</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {reports.map((report, index) => (
                      <tr key={index}>
                        <td className="px-6 py-4 whitespace-nowrap">{report.product_name}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.current_quantity}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.min_quantity}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.last_updated}</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}

            {reportType === "employees" && (
              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموظف</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطلبات المعالجة</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبيعات</th>
                      <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {reports.map((report, index) => (
                      <tr key={index}>
                        <td className="px-6 py-4 whitespace-nowrap">{report.employee_name}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.processed_orders}</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.sales_amount} ر.س</td>
                        <td className="px-6 py-4 whitespace-nowrap">{report.rating}/5</td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </div>
        ) : (
          <p className="text-center text-gray-500">لا توجد تقارير متاحة للفترة المحددة</p>
        )}
      </div>
    </div>
  );
}

export default Reports;