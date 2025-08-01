import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Orders() {
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [successMessage, setSuccessMessage] = useState("");
  const [formData, setFormData] = useState({
    status: ""
  });
  const [currentOrderId, setCurrentOrderId] = useState(null);

  useEffect(() => {
    fetchOrders();
  }, []);

  const fetchOrders = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/orders`);
      setOrders(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching orders:", error);
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.put(`${API_URL}/orders/${currentOrderId}`, formData);
      setSuccessMessage("تم تحديث حالة الطلب بنجاح");
      fetchOrders();
      closeModal(`editOrderModal${currentOrderId}`);
    } catch (error) {
      console.error("Error updating order:", error);
    }
  };

  const handleEdit = (order) => {
    setFormData({
      status: order.status
    });
    setCurrentOrderId(order.id);
    openModal(`editOrderModal${order.id}`);
  };

  const openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "block";
  };

  const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "none";
  };

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-6">🛒 إدارة الطلبات</h1>

      {successMessage && (
        <div className="alert alert-success bg-green-100 border-green-400 text-green-700 p-4 mb-4 rounded">
          {successMessage}
        </div>
      )}

      <div className="card bg-white p-4 rounded shadow">
        <table className="styled-table w-full">
          <thead>
            <tr>
              <th>ID</th>
              <th>العميل</th>
              <th>المنتج</th>
              <th>الكمية</th>
              <th>الحالة</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="6" className="text-center p-4">جاري التحميل...</td>
              </tr>
            ) : orders.length > 0 ? (
              orders.map((order) => (
                <tr key={order.id}>
                  <td>{order.id}</td>
                  <td>{order.user?.name || "زائر"}</td>
                  <td>{order.product_name || "-"}</td>
                  <td>{order.quantity}</td>
                  <td>
                    <span className={`px-2 py-1 rounded ${getStatusColor(order.status)}`}>
                      {getStatusText(order.status)}
                    </span>
                  </td>
                  <td>
                    <button onClick={() => handleEdit(order)} className="btn-edit px-3 py-1 bg-yellow-500 text-white rounded">✏️ تعديل الحالة</button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="6" className="text-center p-4">لا توجد طلبات</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Edit Order Status Modals */}
      {orders.map(order => (
        <div key={`modal-${order.id}`} id={`editOrderModal${order.id}`} className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div className="modal-content bg-white p-6 rounded-lg w-1/2">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">تعديل حالة الطلب</h2>
              <span onClick={() => closeModal(`editOrderModal${order.id}`)} className="close text-2xl cursor-pointer">&times;</span>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="form-group mb-4">
                <label className="block mb-2">حالة الطلب</label>
                <select 
                  id="status" 
                  name="status" 
                  value={formData.status} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded" 
                  required
                >
                  <option value="pending">قيد الانتظار</option>
                  <option value="processing">قيد المعالجة</option>
                  <option value="shipped">تم الشحن</option>
                  <option value="delivered">تم التسليم</option>
                  <option value="cancelled">ملغي</option>
                </select>
              </div>
              <div className="form-group text-right">
                <button type="submit" className="btn-submit px-4 py-2 bg-blue-600 text-white rounded">تحديث</button>
              </div>
            </form>
          </div>
        </div>
      ))}
    </div>
  );
}

// Helper functions for order status
function getStatusColor(status) {
  switch (status) {
    case "pending": return "bg-yellow-200 text-yellow-800";
    case "processing": return "bg-blue-200 text-blue-800";
    case "shipped": return "bg-purple-200 text-purple-800";
    case "delivered": return "bg-green-200 text-green-800";
    case "cancelled": return "bg-red-200 text-red-800";
    default: return "bg-gray-200 text-gray-800";
  }
}

function getStatusText(status) {
  switch (status) {
    case "pending": return "قيد الانتظار";
    case "processing": return "قيد المعالجة";
    case "shipped": return "تم الشحن";
    case "delivered": return "تم التسليم";
    case "cancelled": return "ملغي";
    default: return status;
  }
}

export default Orders;
