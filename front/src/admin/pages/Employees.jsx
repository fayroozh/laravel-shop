import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Employees() {
  const [employees, setEmployees] = useState([]);
  const [roles, setRoles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    position: "",
    mobile: "",
    role_id: ""
  });
  const [editMode, setEditMode] = useState(false);
  const [currentEmployeeId, setCurrentEmployeeId] = useState(null);
  const [successMessage, setSuccessMessage] = useState("");
  const [userPermissions, setUserPermissions] = useState([]);

  useEffect(() => {
    fetchEmployees();
    fetchRoles();
    fetchUserPermissions();
  }, []);

  const fetchEmployees = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/employees`);
      setEmployees(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching employees:", error);
      setLoading(false);
    }
  };

  const fetchRoles = async () => {
    try {
      const response = await axios.get(`${API_URL}/roles`);
      setRoles(response.data);
    } catch (error) {
      console.error("Error fetching roles:", error);
    }
  };

  const fetchUserPermissions = async () => {
    try {
      const response = await axios.get(`${API_URL}/user/permissions`);
      setUserPermissions(response.data);
    } catch (error) {
      console.error("Error fetching user permissions:", error);
    }
  };

  const hasPermission = (permission) => {
    return userPermissions.includes(permission);
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
      if (editMode) {
        await axios.put(`${API_URL}/employees/${currentEmployeeId}`, formData);
        setSuccessMessage("تم تحديث الموظف بنجاح");
      } else {
        await axios.post(`${API_URL}/employees`, formData);
        setSuccessMessage("تم إضافة الموظف بنجاح");
      }
      resetForm();
      fetchEmployees();
      closeModal(editMode ? `editEmployeeModal${currentEmployeeId}` : "addEmployeeModal");
    } catch (error) {
      console.error("Error saving employee:", error);
    }
  };

  const handleEdit = (employee) => {
    setFormData({
      name: employee.name,
      email: employee.email,
      position: employee.position,
      mobile: employee.mobile,
      role_id: employee.user?.roles[0]?.id || ""
    });
    setEditMode(true);
    setCurrentEmployeeId(employee.id);
    openModal(`editEmployeeModal${employee.id}`);
  };

  const handleDelete = async (employeeId) => {
    if (window.confirm("هل أنت متأكد من حذف هذا الموظف؟")) {
      try {
        await axios.delete(`${API_URL}/employees/${employeeId}`);
        setSuccessMessage("تم حذف الموظف بنجاح");
        fetchEmployees();
      } catch (error) {
        console.error("Error deleting employee:", error);
      }
    }
  };

  const resetForm = () => {
    setFormData({
      name: "",
      email: "",
      position: "",
      mobile: "",
      role_id: ""
    });
    setEditMode(false);
    setCurrentEmployeeId(null);
  };

  const openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "block";
  };

  const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "none";
    resetForm();
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-6">
        <h1 className="text-2xl font-bold">👨‍💼 إدارة الموظفين</h1>
        <div className="dashboard-actions">
          {hasPermission('create_employees') && (
            <button onClick={() => openModal("addEmployeeModal")} className="btn-add px-4 py-2 bg-blue-600 text-white rounded">➕ إضافة موظف</button>
          )}
        </div>
      </div>

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
              <th>الاسم</th>
              <th>البريد الإلكتروني</th>
              <th>المنصب</th>
              <th>الدور</th>
              <th>الهاتف</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="7" className="text-center p-4">جاري التحميل...</td>
              </tr>
            ) : employees.length > 0 ? (
              employees.map((employee) => (
                <tr key={employee.id}>
                  <td>{employee.id}</td>
                  <td>{employee.name}</td>
                  <td>{employee.email}</td>
                  <td>{employee.position}</td>
                  <td>{employee.user?.roles?.map(role => role.display_name).join(", ") || "لا يوجد دور"}</td>
                  <td>{employee.mobile}</td>
                  <td>
                    {hasPermission('edit_employees') && (
                      <button onClick={() => handleEdit(employee)} className="btn-edit mx-1 px-2 py-1 bg-yellow-500 text-white rounded">✏️</button>
                    )}
                    {hasPermission('delete_employees') && (
                      <button onClick={() => handleDelete(employee.id)} className="btn-delete mx-1 px-2 py-1 bg-red-500 text-white rounded">🗑️</button>
                    )}
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="7" className="text-center p-4">لا يوجد موظفين</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Add Employee Modal */}
      <div id="addEmployeeModal" className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div className="modal-content bg-white p-6 rounded-lg w-1/2">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-bold">إضافة موظف جديد</h2>
            <span onClick={() => closeModal("addEmployeeModal")} className="close text-2xl cursor-pointer">&times;</span>
          </div>
          <form onSubmit={handleSubmit}>
            <div className="form-group mb-4">
              <label className="block mb-2">الاسم</label>
              <input 
                type="text" 
                name="name" 
                value={formData.name} 
                onChange={handleInputChange} 
                className="form-control w-full p-2 border rounded" 
                required 
              />
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2">البريد الإلكتروني</label>
              <input 
                type="email" 
                name="email" 
                value={formData.email} 
                onChange={handleInputChange} 
                className="form-control w-full p-2 border rounded" 
                required 
              />
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2">المنصب</label>
              <input 
                type="text" 
                name="position" 
                value={formData.position} 
                onChange={handleInputChange} 
                className="form-control w-full p-2 border rounded" 
                required 
              />
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2">الهاتف</label>
              <input 
                type="text" 
                name="mobile" 
                value={formData.mobile} 
                onChange={handleInputChange} 
                className="form-control w-full p-2 border rounded" 
                required 
              />
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2">الدور</label>
              <select 
                name="role_id" 
                value={formData.role_id} 
                onChange={handleInputChange} 
                className="form-control w-full p-2 border rounded"
              >
                <option value="">اختر دورًا</option>
                {roles.map(role => (
                  <option key={role.id} value={role.id}>{role.display_name}</option>
                ))}
              </select>
            </div>
            <div className="form-group text-right">
              <button type="submit" className="btn-submit px-4 py-2 bg-blue-600 text-white rounded">حفظ</button>
            </div>
          </form>
        </div>
      </div>

      {/* Edit Employee Modals */}
      {employees.map(employee => (
        <div key={`modal-${employee.id}`} id={`editEmployeeModal${employee.id}`} className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div className="modal-content bg-white p-6 rounded-lg w-1/2">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">تعديل الموظف</h2>
              <span onClick={() => closeModal(`editEmployeeModal${employee.id}`)} className="close text-2xl cursor-pointer">&times;</span>
            </div>
            <form onSubmit={handleSubmit}>
              <div className="form-group mb-4">
                <label className="block mb-2">الاسم</label>
                <input 
                  type="text" 
                  name="name" 
                  value={formData.name} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded" 
                  required 
                />
              </div>
              <div className="form-group mb-4">
                <label className="block mb-2">البريد الإلكتروني</label>
                <input 
                  type="email" 
                  name="email" 
                  value={formData.email} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded" 
                  required 
                />
              </div>
              <div className="form-group mb-4">
                <label className="block mb-2">المنصب</label>
                <input 
                  type="text" 
                  name="position" 
                  value={formData.position} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded" 
                  required 
                />
              </div>
              <div className="form-group mb-4">
                <label className="block mb-2">الهاتف</label>
                <input 
                  type="text" 
                  name="mobile" 
                  value={formData.mobile} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded" 
                  required 
                />
              </div>
              <div className="form-group mb-4">
                <label className="block mb-2">الدور</label>
                <select 
                  name="role_id" 
                  value={formData.role_id} 
                  onChange={handleInputChange} 
                  className="form-control w-full p-2 border rounded"
                >
                  <option value="">اختر دورًا</option>
                  {roles.map(role => (
                    <option key={role.id} value={role.id}>{role.display_name}</option>
                  ))}
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

export default Employees;
