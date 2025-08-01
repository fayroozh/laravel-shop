import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Roles() {
  const [roles, setRoles] = useState([]);
  const [permissions, setPermissions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    name: "",
    permissions: []
  });
  const [editMode, setEditMode] = useState(false);
  const [currentRoleId, setCurrentRoleId] = useState(null);

  useEffect(() => {
    fetchRoles();
    fetchPermissions();
  }, []);

  const fetchRoles = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/roles`);
      setRoles(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching roles:", error);
      setLoading(false);
    }
  };

  const fetchPermissions = async () => {
    try {
      const response = await axios.get(`${API_URL}/permissions`);
      setPermissions(response.data);
    } catch (error) {
      console.error("Error fetching permissions:", error);
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editMode) {
        await axios.put(`${API_URL}/roles/${currentRoleId}`, formData);
      } else {
        await axios.post(`${API_URL}/roles`, formData);
      }
      resetForm();
      fetchRoles();
    } catch (error) {
      console.error("Error saving role:", error);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm("هل أنت متأكد من حذف هذا الدور؟")) {
      try {
        await axios.delete(`${API_URL}/roles/${id}`);
        fetchRoles();
      } catch (error) {
        console.error("Error deleting role:", error);
      }
    }
  };

  const handleEdit = (role) => {
    setFormData({
      name: role.name,
      permissions: role.permissions.map(p => p.id)
    });
    setEditMode(true);
    setCurrentRoleId(role.id);
  };

  const resetForm = () => {
    setFormData({ name: "", permissions: [] });
    setEditMode(false);
    setCurrentRoleId(null);
  };

  const handleCheckboxChange = (permissionId) => {
    const updatedPermissions = formData.permissions.includes(permissionId)
      ? formData.permissions.filter(id => id !== permissionId)
      : [...formData.permissions, permissionId];
    
    setFormData({ ...formData, permissions: updatedPermissions });
  };

  return (
    <div className="p-4">
      <div className="mb-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">🔐 إدارة الصلاحيات</h1>
      </div>

      {/* نموذج إضافة/تعديل دور */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4 mb-6">
        <h2 className="text-xl font-semibold mb-4">{editMode ? "تعديل دور" : "إضافة دور جديد"}</h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-700 dark:text-gray-300 mb-2">اسم الدور</label>
            <input
              type="text"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
              className="w-full p-2 border rounded"
              required
            />
          </div>
          
          <div className="mb-4">
            <label className="block text-gray-700 dark:text-gray-300 mb-2">الصلاحيات</label>
            <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
              {permissions.map(permission => (
                <div key={permission.id} className="flex items-center">
                  <input
                    type="checkbox"
                    id={`permission-${permission.id}`}
                    checked={formData.permissions.includes(permission.id)}
                    onChange={() => handleCheckboxChange(permission.id)}
                    className="mr-2"
                  />
                  <label htmlFor={`permission-${permission.id}`}>{permission.name}</label>
                </div>
              ))}
            </div>
          </div>
          
          <div className="flex space-x-2 rtl:space-x-reverse">
            <button
              type="submit"
              className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
            >
              {editMode ? "تحديث" : "إضافة"}
            </button>
            {editMode && (
              <button
                type="button"
                onClick={resetForm}
                className="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
              >
                إلغاء
              </button>
            )}
          </div>
        </form>
      </div>

      {/* جدول الأدوار */}
      <div className="bg-white dark:bg-gray-800 shadow rounded-lg p-4">
        <h2 className="text-xl font-semibold mb-4">الأدوار الحالية</h2>
        {loading ? (
          <p className="text-center text-gray-500">جاري تحميل الأدوار...</p>
        ) : roles.length > 0 ? (
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الصلاحيات</th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {roles.map(role => (
                  <tr key={role.id}>
                    <td className="px-6 py-4 whitespace-nowrap">{role.name}</td>
                    <td className="px-6 py-4">
                      <div className="flex flex-wrap gap-1">
                        {role.permissions.map(permission => (
                          <span key={permission.id} className="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            {permission.name}
                          </span>
                        ))}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <button
                        onClick={() => handleEdit(role)}
                        className="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        تعديل
                      </button>
                      <button
                        onClick={() => handleDelete(role.id)}
                        className="text-red-600 hover:text-red-900"
                      >
                        حذف
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <p className="text-center text-gray-500">لا توجد أدوار مضافة حتى الآن</p>
        )}
      </div>
    </div>
  );
}

export default Roles;