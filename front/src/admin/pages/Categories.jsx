import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Categories() {
  const [categories, setCategories] = useState([]);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    name: "",
    description: ""
  });
  const [editMode, setEditMode] = useState(false);
  const [currentCategoryId, setCurrentCategoryId] = useState(null);
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    fetchCategories();
  }, []);

  const fetchCategories = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/frontend/categories`);
      setCategories(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching categories:", error);
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

  const resetForm = () => {
    setFormData({
      name: "",
      description: ""
    });
    setEditMode(false);
    setCurrentCategoryId(null);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editMode) {
        await axios.put(`${API_URL}/categories/${currentCategoryId}`, formData);
        setSuccessMessage("تم تحديث الفئة بنجاح");
      } else {
        await axios.post(`${API_URL}/categories`, formData);
        setSuccessMessage("تم إضافة الفئة بنجاح");
      }
      fetchCategories();
      resetForm();
      closeModal("addCategoryModal");
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (error) {
      console.error("Error saving category:", error);
    }
  };

  const handleEdit = (category) => {
    setFormData({
      name: category.name,
      description: category.description || ""
    });
    setEditMode(true);
    setCurrentCategoryId(category.id);
    openModal("addCategoryModal");
  };

  const handleDelete = async (id) => {
    if (window.confirm("هل أنت متأكد من حذف هذه الفئة؟")) {
      try {
        await axios.delete(`${API_URL}/categories/${id}`);
        setSuccessMessage("تم حذف الفئة بنجاح");
        fetchCategories();
        setTimeout(() => setSuccessMessage(""), 3000);
      } catch (error) {
        console.error("Error deleting category:", error);
      }
    }
  };

  const openModal = (modalId) => {
    document.getElementById(modalId).classList.remove("hidden");
  };

  const closeModal = (modalId) => {
    document.getElementById(modalId).classList.add("hidden");
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">🗂️ الفئات</h1>
        <button
          onClick={() => {
            resetForm();
            openModal("addCategoryModal");
          }}
          className="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
        >
          ➕ إضافة فئة
        </button>
      </div>

      {successMessage && (
        <div className="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
          {successMessage}
        </div>
      )}

      <div className="bg-white rounded shadow overflow-x-auto">
        <table className="w-full table-auto">
          <thead className="bg-gray-100">
            <tr>
              <th className="px-4 py-2">ID</th>
              <th className="px-4 py-2">الاسم</th>
              <th className="px-4 py-2">الوصف</th>
              <th className="px-4 py-2">الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="4" className="text-center py-4">جاري التحميل...</td>
              </tr>
            ) : categories.length > 0 ? (
              categories.map((category) => (
                <tr key={category.id} className="border-t">
                  <td className="px-4 py-2">{category.id}</td>
                  <td className="px-4 py-2">{category.name}</td>
                  <td className="px-4 py-2">{category.description}</td>
                  <td className="px-4 py-2 flex space-x-2">
                    <button
                      onClick={() => handleEdit(category)}
                      className="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded"
                    >
                      ✏️
                    </button>
                    <button
                      onClick={() => handleDelete(category.id)}
                      className="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded"
                    >
                      🗑️
                    </button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="4" className="text-center py-4">لا توجد فئات</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* Add/Edit Category Modal */}
      <div id="addCategoryModal" className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div className="bg-white p-6 rounded-lg w-full max-w-md">
          <div className="flex justify-between items-center mb-4">
            <h2 className="text-xl font-bold">{editMode ? "تعديل الفئة" : "إضافة فئة جديدة"}</h2>
            <button
              onClick={() => closeModal("addCategoryModal")}
              className="text-gray-500 hover:text-gray-700"
            >
              &times;
            </button>
          </div>
          <form onSubmit={handleSubmit}>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">اسم الفئة</label>
              <input
                type="text"
                id="name"
                name="name"
                value={formData.name}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                required
              />
            </div>
            <div className="mb-4">
              <label className="block text-gray-700 mb-2">الوصف</label>
              <textarea
                id="description"
                name="description"
                value={formData.description}
                onChange={handleInputChange}
                className="w-full px-3 py-2 border rounded"
                rows="4"
              ></textarea>
            </div>
            <div className="flex justify-end">
              <button
                type="button"
                onClick={() => closeModal("addCategoryModal")}
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

export default Categories;