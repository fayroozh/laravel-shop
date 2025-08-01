import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Users() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    is_admin: false
  });
  const [editMode, setEditMode] = useState(false);
  const [currentUserId, setCurrentUserId] = useState(null);
  const [successMessage, setSuccessMessage] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  useEffect(() => {
    fetchUsers();
  }, []);

  const fetchUsers = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/users`);
      setUsers(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching users:", error);
      setLoading(false);
    }
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({
      ...formData,
      [name]: type === "checkbox" ? checked : value
    });
  };

  const resetForm = () => {
    setFormData({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      is_admin: false
    });
    setEditMode(false);
    setCurrentUserId(null);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editMode) {
        await axios.put(`${API_URL}/users/${currentUserId}`, formData);
        setSuccessMessage("تم تحديث المستخدم بنجاح");
      } else {
        await axios.post(`${API_URL}/users`, formData);
        setSuccessMessage("تم إضافة المستخدم بنجاح");
      }
      fetchUsers();
      resetForm();
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (error) {
      console.error("Error saving user:", error);
      setErrorMessage("حدث خطأ أثناء حفظ المستخدم");
      setTimeout(() => setErrorMessage(""), 3000);
    }
  };

  const handleEdit = (user) => {
    setFormData({
      name: user.name,
      email: user.email,
      password: "",
      password_confirmation: "",
      is_admin: user.is_admin
    });
    setEditMode(true);
    setCurrentUserId(user.id);
  };

  const handleDelete = async (id) => {
    if (window.confirm("هل أنت متأكد من حذف هذا المستخدم؟")) {
      try {
        await axios.delete(`${API_URL}/users/${id}`);
        setSuccessMessage("تم حذف المستخدم بنجاح");
        fetchUsers();
        setTimeout(() => setSuccessMessage(""), 3000);
      } catch (error) {
        console.error("Error deleting user:", error);
        setErrorMessage("حدث خطأ أثناء حذف المستخدم");
        setTimeout(() => setErrorMessage(""), 3000);
      }
    }
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">👥 إدارة المستخدمين</h1>
        <button 
          onClick={() => {
            resetForm();
            document.getElementById("userFormModal").classList.remove("hidden");
          }} 
          className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        >
          ➕ إضافة مستخدم
        </button>
      </div>

      {successMessage && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {successMessage}
        </div>
      )}

      {errorMessage && (
        <div className="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          {errorMessage}
        </div>
      )}

      <div className="bg-white rounded shadow overflow-x-auto">
        <table className="w-full table-auto">
          <thead className="bg-gray-100">
            <tr>
              <th className="px-4 py-2">#</th>
              <th className="px-4 py-2">الاسم</th>
              <th className="px-4 py-2">البريد الإلكتروني</th>
              <th className="px-4 py-2">نوع الحساب</th>
              <th className="px-4 py-2">تاريخ التسجيل</th>
              <th className="px-4 py-2">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="6" className="text-center py-4">جاري التحميل...</td>
              </tr>
            ) : users.length > 0 ? (
              users.map((user, index) => (
                <tr key={user.id} className="border-t">
                  <td className="px-4 py-2">{index + 1}</td>
                  <td className="px-4 py-2">{user.name}</td>
                  <td className="px-4 py-2">{user.email}</td>
                  <td className="px-4 py-2">
                    {user.is_admin ? "مدير" : user.isEmployee ? "موظف" : "مستخدم عادي"}
                  </td>
                  <td className="px-4 py-2">{new Date(user.created_at).toLocaleDateString()}</td>
                  <td className="px-4 py-2 flex space-x-2">
                    <button
                      onClick={() => {
                        handleEdit(user);
                        document.getElementById("userFormModal").classList.remove("hidden");
                      }}
                      className="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded"
                    >
                      ✏️ تعديل
                    </button>
                    <button
                      onClick={() => handleDelete(user.id)}
                      className="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                    >
                      🗑️ حذف
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="6" className="text-center py-4">لا يوجد مستخدمين</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* User Form Modal */}
      <div id="userFormModal" className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div className="bg-white p-6 rounded-lg w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-bold">{editMode ? "تعديل المستخدم" : "إضافة مستخدم جديد"}</h2>
            <button 
              onClick={() => document.getElementById("userFormModal").classList.add("hidden")} 
              className="text-gray-500 hover:text-gray-700"
            >
              &times;
            </button>
          </div>
          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">الاسم</label>
              <input
                type="text"
                name="name"
                value={formData.name}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                required
              />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">البريد الإلكتروني</label>
              <input
                type="email"
                name="email"
                value={formData.email}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                required
              />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">كلمة المرور {editMode && "(اتركها فارغة للاحتفاظ بنفس كلمة المرور)"}</label>
              <input
                type="password"
                name="password"
                value={formData.password}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                required={!editMode}
              />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">تأكيد كلمة المرور</label>
              <input
                type="password"
                name="password_confirmation"
                value={formData.password_confirmation}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                required={!editMode}
              />
            </div>
            <div className="mb-4">
              <label className="flex items-center">
                <input
                  type="checkbox"
                  name="is_admin"
                  checked={formData.is_admin}
                  onChange={handleInputChange}
                  className="mr-2"
                />
                <span className="text-gray-700">مدير النظام</span>
              </label>
            </div>
            <div className="flex justify-end">
              <button
                type="button"
                onClick={() => document.getElementById("userFormModal").classList.add("hidden")}
                className="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2"
              >
                إلغاء
              </button>
              <button
                type="submit"
                className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
              >
                {editMode ? "تحديث" : "إضافة"}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Users;
