import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Feedback() {
  const [feedback, setFeedback] = useState([]);
  const [loading, setLoading] = useState(true);
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    fetchFeedback();
  }, []);

  const fetchFeedback = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/feedback`);
      setFeedback(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching feedback:", error);
      setLoading(false);
    }
  };

  const handleDelete = async (feedbackId) => {
    if (window.confirm("هل أنت متأكد من حذف هذه الملاحظة؟")) {
      try {
        await axios.delete(`${API_URL}/feedback/${feedbackId}`);
        setSuccessMessage("تم حذف الملاحظة بنجاح");
        fetchFeedback();
      } catch (error) {
        console.error("Error deleting feedback:", error);
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
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-6">
        <h1 className="text-2xl font-bold">📝 إدارة الملاحظات</h1>
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
              <th>الملاحظة</th>
              <th>التاريخ</th>
              <th>الإجراءات</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr>
                <td colSpan="5" className="text-center p-4">جاري التحميل...</td>
              </tr>
            ) : feedback.length > 0 ? (
              feedback.map((f) => (
                <tr key={f.id}>
                  <td>{f.id}</td>
                  <td>{f.user?.name || f.name}</td>
                  <td>{f.feedback.substring(0, 50)}...</td>
                  <td>{new Date(f.created_at).toLocaleDateString()}</td>
                  <td>
                    <button onClick={() => openModal(`viewFeedbackModal${f.id}`)} className="btn-view mx-1 px-2 py-1 bg-blue-500 text-white rounded">👁️</button>
                    <button onClick={() => handleDelete(f.id)} className="btn-delete mx-1 px-2 py-1 bg-red-500 text-white rounded">🗑️</button>
                  </td>
                </tr>
              ))
            ) : (
              <tr>
                <td colSpan="5" className="text-center p-4">لا توجد ملاحظات</td>
              </tr>
            )}
          </tbody>
        </table>
      </div>

      {/* View Feedback Modals */}
      {feedback.map(f => (
        <div key={`modal-${f.id}`} id={`viewFeedbackModal${f.id}`} className="modal hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center">
          <div className="modal-content bg-white p-6 rounded-lg w-1/2">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">تفاصيل الملاحظة</h2>
              <span onClick={() => closeModal(`viewFeedbackModal${f.id}`)} className="close text-2xl cursor-pointer">&times;</span>
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2 font-bold">من:</label>
              <p>{f.user?.name || f.name}</p>
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2 font-bold">التاريخ:</label>
              <p>{new Date(f.created_at).toLocaleString()}</p>
            </div>
            <div className="form-group mb-4">
              <label className="block mb-2 font-bold">الملاحظة:</label>
              <p className="p-3 bg-gray-100 rounded">{f.feedback}</p>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}

export default Feedback;
