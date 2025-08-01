import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

export default function Suppliers() {
  const [suppliers, setSuppliers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [successMessage, setSuccessMessage] = useState("");
  const [formData, setFormData] = useState({
    name: "",
    company: "",
    phone: "",
    email: "",
    address: ""
  });
  const [editMode, setEditMode] = useState(false);
  const [currentSupplierId, setCurrentSupplierId] = useState(null);

  useEffect(() => {
    fetchSuppliers();
  }, []);

  const fetchSuppliers = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/suppliers`);
      setSuppliers(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching suppliers:", error);
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
      company: "",
      phone: "",
      email: "",
      address: ""
    });
    setEditMode(false);
    setCurrentSupplierId(null);
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (editMode) {
        await axios.put(`${API_URL}/suppliers/${currentSupplierId}`, formData);
        setSuccessMessage("تم تحديث المورد بنجاح");
      } else {
        await axios.post(`${API_URL}/suppliers`, formData);
        setSuccessMessage("تم إضافة المورد بنجاح");
      }
      fetchSuppliers();
      resetForm();
      closeModal(editMode ? `editSupplierModal${currentSupplierId}` : "addSupplierModal");
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (error) {
      console.error("Error saving supplier:", error);
    }
  };

  const handleEdit = (supplier) => {
    setFormData({
      name: supplier.name,
      company: supplier.company,
      phone: supplier.phone,
      email: supplier.email || "",
      address: supplier.address || ""
    });
    setEditMode(true);
    setCurrentSupplierId(supplier.id);
    openModal(`editSupplierModal${supplier.id}`);
  };

  const handleDelete = async (supplierId) => {
    if (window.confirm("هل أنت متأكد من حذف هذا المورد؟")) {
      try {
        await axios.delete(`${API_URL}/suppliers/${supplierId}`);
        setSuccessMessage("تم حذف المورد بنجاح");
        fetchSuppliers();
        closeModal(`deleteSupplierModal${supplierId}`);
        setTimeout(() => setSuccessMessage(""), 3000);
      } catch (error) {
        console.error("Error deleting supplier:", error);
      }
    }
  };

  const openModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "block";
  };

  const closeModal = (modalId) => {
    const modal = document.getElementById(modalId);
    if (modal) modal.style.display = "none";
    if (!editMode) resetForm();
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-4 flex justify-between items-center">
        <h1 className="text-2xl font-bold">🏭 Suppliers Management</h1>
        <div className="dashboard-actions">
          <button onClick={() => openModal('addSupplierModal')} className="btn-add px-4 py-2 bg-blue-600 text-white rounded">➕ Add Supplier</button>
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
              <th>ID</th><th>Name</th><th>Company</th><th>Phone</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="5" className="text-center p-4">Loading...</td>
              </tr>
            ) : suppliers.length > 0 ? (
              suppliers.map((supplier) => (
                <tr key={supplier.id}>
                  <td>{supplier.id}</td>
                  <td>{supplier.name}</td>
                  <td>{supplier.company}</td>
                  <td>{supplier.phone}</td>
                  <td>
                    <button className="btn-edit mx-1 px-2 py-1 bg-yellow-500 text-white rounded" onClick={() => handleEdit(supplier)} title="Edit">✏️</button>
                    <button className="btn-delete mx-1 px-2 py-1 bg-red-500 text-white rounded" onClick={() => openModal(`deleteSupplierModal${supplier.id}`)} title="Delete">🗑️</button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="5" className="text-center p-4">No suppliers found</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>
      
      {/* Add Supplier Modal */}
      <div id="addSupplierModal" className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
        <div className="modal-content bg-white p-6 rounded-lg w-1/2">
          <span className="close float-right text-xl cursor-pointer" onClick={() => closeModal('addSupplierModal')}>&times;</span>
          <h2 className="text-xl font-bold mb-4">Add New Supplier</h2>
          <form onSubmit={handleSubmit}>
            <div className="form-group mb-3">
              <label className="block mb-1">Name</label>
              <input type="text" name="name" value={formData.name} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
            </div>
            <div className="form-group mb-3">
              <label className="block mb-1">Company</label>
              <input type="text" name="company" value={formData.company} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
            </div>
            <div className="form-group mb-3">
              <label className="block mb-1">Phone</label>
              <input type="text" name="phone" value={formData.phone} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
            </div>
            <div className="form-group mb-3">
              <label className="block mb-1">Email</label>
              <input type="email" name="email" value={formData.email} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" />
            </div>
            <div className="form-group mb-3">
              <label className="block mb-1">Address</label>
              <textarea name="address" value={formData.address} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded"></textarea>
            </div>
            <div className="form-actions flex justify-end mt-4">
              <button type="button" onClick={() => closeModal('addSupplierModal')} className="btn-cancel bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Cancel</button>
              <button type="submit" className="btn-submit bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Add Supplier</button>
            </div>
          </form>
        </div>
      </div>

      {/* Edit Supplier Modals */}
      {suppliers.map((supplier) => (
        <div key={`edit-${supplier.id}`} id={`editSupplierModal${supplier.id}`} className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div className="modal-content bg-white p-6 rounded-lg w-1/2">
            <span className="close float-right text-xl cursor-pointer" onClick={() => closeModal(`editSupplierModal${supplier.id}`)}>&times;</span>
            <h2 className="text-xl font-bold mb-4">Edit Supplier</h2>
            <form onSubmit={handleSubmit}>
              <div className="form-group mb-3">
                <label className="block mb-1">Name</label>
                <input type="text" name="name" value={formData.name} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
              </div>
              <div className="form-group mb-3">
                <label className="block mb-1">Company</label>
                <input type="text" name="company" value={formData.company} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
              </div>
              <div className="form-group mb-3">
                <label className="block mb-1">Phone</label>
                <input type="text" name="phone" value={formData.phone} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" required />
              </div>
              <div className="form-group mb-3">
                <label className="block mb-1">Email</label>
                <input type="email" name="email" value={formData.email} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded" />
              </div>
              <div className="form-group mb-3">
                <label className="block mb-1">Address</label>
                <textarea name="address" value={formData.address} onChange={handleInputChange} className="form-control w-full px-3 py-2 border rounded"></textarea>
              </div>
              <div className="form-actions flex justify-end mt-4">
                <button type="button" onClick={() => closeModal(`editSupplierModal${supplier.id}`)} className="btn-cancel bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2">Cancel</button>
                <button type="submit" className="btn-submit bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Update Supplier</button>
              </div>
            </form>
          </div>
        </div>
      ))}
      
      {/* Delete Supplier Modals */}
      {suppliers.map((supplier) => (
        <div key={`delete-${supplier.id}`} id={`deleteSupplierModal${supplier.id}`} className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div className="modal-content bg-white p-6 rounded-lg w-1/2">
            <span className="close float-right text-xl cursor-pointer" onClick={() => closeModal(`deleteSupplierModal${supplier.id}`)}>&times;</span>
            <h2 className="text-xl font-bold mb-4">Delete Supplier</h2>
            <p className="mb-2">Are you sure you want to delete the supplier "{supplier.name}"?</p>
            <p className="mb-4">This action cannot be undone.</p>
            <div className="form-actions flex justify-end mt-4">
              <button type="button" className="btn-cancel bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded mr-2" onClick={() => closeModal(`deleteSupplierModal${supplier.id}`)}>Cancel</button>
              <button type="button" onClick={() => handleDelete(supplier.id)} className="btn-delete bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Delete Supplier</button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
