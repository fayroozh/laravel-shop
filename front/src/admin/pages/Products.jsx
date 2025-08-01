import React, { useState } from "react";

export default function Products() {
  const [products, setProducts] = useState([]); // تفترض جلبها من API لاحقًا
  const [categories, setCategories] = useState([]); // نفس الشيء
  const [showAddModal, setShowAddModal] = useState(false);
  const [selectedProduct, setSelectedProduct] = useState(null);

  const openEditModal = (product) => setSelectedProduct(product);
  const closeModals = () => {
    setShowAddModal(false);
    setSelectedProduct(null);
  };

  return (
    <div className="p-4">
      <div className="flex justify-between items-center mb-4">
        <h1 className="text-2xl font-bold">📦 Products Management</h1>
        <button onClick={() => setShowAddModal(true)} className="btn-add">
          ➕ Add Product
        </button>
      </div>

      <div className="card">
        <table className="styled-table w-full">
          <thead>
            <tr>
              <th>ID</th><th>Title</th><th>Price</th><th>Category</th><th>Stock</th><th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {products.length === 0 ? (
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
                    <button onClick={() => alert("Confirm delete")} className="btn-delete">🗑️ Delete</button>
                  </td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      {/* Add Modal */}
      {showAddModal && (
        <ProductModal
          title="Add New Product"
          categories={categories}
          onClose={closeModals}
          onSubmit={(data) => {
            // أضف المنتج أو أرسل للـ API هنا
            console.log("New product:", data);
            closeModals();
          }}
        />
      )}

      {/* Edit Modal */}
      {selectedProduct && (
        <ProductModal
          title="Edit Product"
          categories={categories}
          product={selectedProduct}
          onClose={closeModals}
          onSubmit={(data) => {
            // عدل المنتج أو أرسل للـ API هنا
            console.log("Edit product:", data);
            closeModals();
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
