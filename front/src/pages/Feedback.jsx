import { useState, useEffect } from "react";
import { apiClient } from "../services/api-client";

export default function Feedback() {
  const [name, setName] = useState("");
  const [feedback, setFeedback] = useState("");
  const [submitted, setSubmitted] = useState(false);
  const [feedbackList, setFeedbackList] = useState([]);
  const [error, setError] = useState("");

  // Fetch feedbacks from backend
  const fetchFeedbacks = async () => {
    try {
      const response = await apiClient.get(`/frontend/feedback`);
      const data = Array.isArray(response.data)
        ? response.data
        : response.data?.data;
      setFeedbackList(data || []);
    } catch (err) {
      console.error("Error fetching feedbacks:", err);
    }
  };

  useEffect(() => {
    fetchFeedbacks();
  }, []);

  // Submit feedback
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (!name || !feedback) {
      setError("Please fill in both name and feedback fields.");
      return;
    }

    try {
      await apiClient.post(`/frontend/feedback`, { name, feedback });

      setSubmitted(true);
      setName("");
      setFeedback("");
      setError("");

      // Refetch after submit
      fetchFeedbacks();
    } catch (error) {
      setError("An error occurred while submitting feedback. Please try again.");
      console.error("Error submitting feedback:", error);
    }
  };

  return (
    <div className="min-h-screen flex flex-col items-center bg-gray-100 p-6 mt-8">
      <div className="bg-white shadow-lg rounded-2xl p-8 w-full max-w-md mb-8">
        <h1 className="text-2xl font-bold text-gray-800 mb-6 text-center">
          Submit Your Feedback
        </h1>

        {submitted && (
          <div className="text-center text-green-600 font-semibold mb-4">
            ✅ Thank you! Your feedback has been submitted successfully.
          </div>
        )}

        {error && (
          <div className="text-center text-red-500 font-semibold mb-4">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-gray-700 mb-1">Name</label>
            <input
              type="text"
              value={name}
              onChange={(e) => setName(e.target.value)}
              className="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Enter your name"
            />
          </div>

          <div>
            <label className="block text-gray-700 mb-1">Feedback</label>
            <textarea
              value={feedback}
              onChange={(e) => setFeedback(e.target.value)}
              className="w-full p-3 border rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="4"
              placeholder="Write your feedback here..."
            ></textarea>
          </div>

          <button
            type="submit"
            className="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition"
          >
            Submit
          </button>
        </form>
      </div>

      {/* Feedback List */}
      <div className="w-full max-w-md">
        <h2 className="text-xl font-semibold text-gray-800 mb-4 text-center">
          Latest Feedback
        </h2>

        {Array.isArray(feedbackList) && feedbackList.length > 0 ? (
          <ul className="space-y-4">
            {feedbackList.map((fb) => (
              <li
                key={fb.id}
                className="bg-white shadow-md rounded-xl p-4 border"
              >
                <p className="text-gray-900 font-semibold">{fb.name}</p>
                <p className="text-gray-700 mt-1">{fb.feedback}</p>
              </li>
            ))}
          </ul>
        ) : (
          <p className="text-gray-500 text-center">No feedback yet.</p>
        )}
      </div>
    </div>
  );
}
