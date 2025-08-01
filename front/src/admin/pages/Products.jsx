import React, { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

export default function Products() {
  const [products, setProducts] = useState([]);
  const [categories, setCategories] = useState([]);
  const [showAddModal, setShowAddModal] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [successMessage, setSuccessMessage] = useState("");
  const [errorMessage, setErrorMessage] = useState("");

  useEffect(() => {
    fetchProducts();
    fetchCategories();
  }, []);

  const fetchProducts = async () => {
    try {
      setLoading(true);
      console.log('Fetching products from:', `${API_URL}/products`);
      const response = await axios.get(`${API_URL}/products`);
      console.log('Products response:', response.data);
      setProducts(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching products:", error);
      setErrorMessage("حدث خطأ أثناء جلب المنتجات");
      setLoading(false);
      setTimeout(() => setErrorMessage(""), 3000);
    }
  };

  const fetchCategories = async () => {
    try {
      const response = await axios.get(`${API_URL}/categories`);
      setCategories(Array.isArray(response.data) ? response.data : []);
    } catch (error) {
      console.error("Error fetching categories:", error);
    }
  };

  const openEditModal = (product) => setSelectedProduct(product);
  const closeModals = () => {
    setShowAddModal(false);
    setSelectedProduct(null);
  };

  const handleDeleteProduct = async (id) => {
    if (window.confirm("هل أنت متأكد من حذف هذا المنتج؟")) {
      try {
        await axios.delete(`${API_URL}/products/${id}`);
        setSuccessMessage("تم حذف المنتج بنجاح");
        fetchProducts();
        setTimeout(() => setSuccessMessage(""), 3000);
      } catch (error) {
        console.error("Error deleting product:", error);
        setErrorMessage("حدث خطأ أثناء حذف المنتج");
        setTimeout(() => setErrorMessage(""), 3000);
      }
    }
  };

  return (
    <div className="p-4">
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">📦 Products Management</h1>
        <button onClick={() => setShowAddModal(true)} className="btn-add">
          ➕ Add Product
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

      <div className="card">
        <table className="styled-table w-full">
          <thead>
            <tr>
              <th>ID</th><th>Title</th><th>Price</th><th>Category</th><th>Stock</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan="6" className="text-center p-4">جاري التحميل...</td></tr>
            ) : products.length === 0 ? (
              <tr><td colSpan="6" className="text-center p-4">No products found.</td></tr>
            ) : (
              products.map((p) => (
                <tr key={p.id}>
                  <td>{p.id}</td>
                  <td>{p.title}</td>
                  <td>{p.price}</td>
                  <td>{p.category?.name || "-"}</td>
                  <td>{p.stock}</td>
                  <td>
                    <button onClick={() => openEditModal(p)} className="btn-edit">✏️ Edit</button>
                    <button onClick={() => handleDeleteProduct(p.id)} className="btn-delete">🗑️ Delete</button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {showAddModal && (
        <ProductModal
          title="Add New Product"
          categories={categories}
          onClose={closeModals}
          onSubmit={async (data) => {
            try {
              const formData = new FormData();
              formData.append('title', data.title);
              formData.append('description', data.description);
              formData.append('price', data.price);
              formData.append('stock', data.stock);
              formData.append('category_id', data.category_id);
              if (data.images && data.images.length > 0) {
                data.images.forEach(image => {
                  formData.append('images[]', image);
                });
              }
              await axios.post(`${API_URL}/products`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
              });
              setSuccessMessage("تم إضافة المنتج بنجاح");
              fetchProducts();
              closeModals();
              setTimeout(() => setSuccessMessage(""), 3000);
            } catch (error) {
              console.error("Error adding product:", error);
              setErrorMessage("حدث خطأ أثناء إضافة المنتج");
              setTimeout(() => setErrorMessage(""), 3000);
            }
          }}
        />
      )}

      {selectedProduct && (
        <ProductModal
          title="Edit Product"
          categories={categories}
          product={selectedProduct}
          onClose={closeModals}
          onSubmit={async (data) => {
            try {
              const formData = new FormData();
              formData.append('_method', 'PUT');
              formData.append('title', data.title);
              formData.append('description', data.description);
              formData.append('price', data.price);
              formData.append('stock', data.stock);
              formData.append('category_id', data.category_id);
              if (data.images && data.images.length > 0) {
                data.images.forEach(image => {
                  formData.append('images[]', image);
                });
              }
              await axios.post(`${API_URL}/products/${selectedProduct.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
              });
              setSuccessMessage("تم تحديث المنتج بنجاح");
              fetchProducts();
              closeModals();
              setTimeout(() => setSuccessMessage(""), 3000);
            } catch (error) {
              console.error("Error updating product:", error);
              setErrorMessage("حدث خطأ أثناء تحديث المنتج");
              setTimeout(() => setErrorMessage(""), 3000);
            }
          }}
        />
      )}
    </div>
  );
}

function ProductModal({ title, product = {}, categories = [], onClose, onSubmit }) {
  const [form, setForm] = useState({
    title: product.title || "",
    description: product.description || "",
    price: product.price || 0,
    stock: product.stock || 1,
    category_id: product.category_id || "",
    images: [],
  });

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    setForm((prev) => ({
      ...prev,
      [name]: files ? Array.from(files) : value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(form);
  };

  return (
    <div className="modal fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
      <div className="modal-content bg-white p-6 rounded w-[90%] max-w-2xl max-h-[90vh] overflow-y-auto">
        <button className="float-right text-xl" onClick={onClose}>&times;</button>
        <h2 className="text-xl font-bold mb-4">{title}</h2>
        <form onSubmit={handleSubmit}>
          <div className="form-group mb-2">
            <label>Product Title</label>
            <input type="text" name="title" value={form.title} onChange={handleChange} className="form-control w-full" required />
          </div>
          <div className="form-group mb-2">
            <label>Description</label>
            <textarea name="description" value={form.description} onChange={handleChange} className="form-control w-full" />
          </div>
          <div className="form-group mb-2">
            <label>Price</label>
            <input type="number" name="price" value={form.price} onChange={handleChange} className="form-control w-full" step="0.01" required />
          </div>
          <div className="form-group mb-2">
            <label>Stock</label>
            <input type="number" name="stock" value={form.stock} onChange={handleChange} className="form-control w-full" />
          </div>
          <div className="form-group mb-2">
            <label>Category</label>
            <select name="category_id" value={form.category_id} onChange={handleChange} className="form-control w-full">
              <option value="">No Category</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id}>{cat.name}</option>
              ))}
            </select>
          </div>
          <div className="form-group mb-2">
            <label>Images</label>
            <input type="file" name="images" onChange={handleChange} multiple className="form-control w-full" />
          </div>
          <button type="submit" className="btn-submit mt-2">{product.id ? "Update" : "Save"}</button>
        </form>
      </div>
    </div>
  );
}
