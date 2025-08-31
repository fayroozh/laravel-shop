import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { motion } from "framer-motion";
import { authClient } from "../services/api-client";
import useProfile from "../hooks/useProfile";


const containerVariants = {
  hidden: { opacity: 0, y: -50, scale: 0.95 },
  visible: { opacity: 1, y: 0, scale: 1, transition: { duration: 0.3 } },
  exit: { opacity: 0, scale: 0.9, transition: { duration: 0.2 } },
};

const itemVariants = {
  hidden: { opacity: 0, y: 20 },
  visible: { opacity: 1, y: 0 },
};

const EditProfilePage = () => {
  const { profile, isLoading, error } = useProfile();
  const [formData, setFormData] = useState({
    firstName: "",
    lastName: "",
    email: "",
    password: "",
  });
  const navigate = useNavigate();

  useEffect(() => {
    if (profile) {
      const nameParts = profile.name ? profile.name.split(' ') : ['', ''];
      const firstName = nameParts[0];
      const lastName = nameParts.slice(1).join(' ');
      setFormData({
        firstName: firstName || "",
        lastName: lastName || "",
        email: profile.email || "",
        password: "",
      });
    }
  }, [profile]);

  const handleChange = (e) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const dataToUpdate = { ...formData };
      if (!dataToUpdate.password) delete dataToUpdate.password;

      await authClient.put("/profile", dataToUpdate);
      alert("Profile updated successfully!");
      navigate("/profile");
    } catch (err) {
      console.error("Failed to update profile:", err);
      const errorMessage = err.response?.data?.message || "Failed to update profile. Please try again.";
      alert(errorMessage);
    }
  };

  const handleCancel = () => navigate("/profile");

  if (isLoading) return <p>Loading...</p>;
  if (error) return <ErrorMessage error={error.message || "Failed to load profile."} />;

  return (
    <div
      className="fixed inset-0 z-50 flex items-center justify-center"
      onClick={handleCancel}
    >
      <div className="absolute inset-0 bg-black bg-opacity-30 backdrop-blur-sm"></div>

      <motion.div
        className="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-lg p-6 sm:p-8 z-10"
        onClick={(e) => e.stopPropagation()}
        variants={containerVariants}
        initial="hidden"
        animate="visible"
        exit="exit"
      >
        <h2 className="text-2xl font-bold mb-6 text-gray-800 dark:text-white">
          Edit Profile
        </h2>

        <form className="space-y-4" onSubmit={handleSubmit}>
          {Object.keys(formData).map((field) => (
            <div key={field}>
              <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 capitalize mb-1">
                {field.replace(/([A-Z])/g, " $1")}
              </label>
              <input
                name={field}
                value={formData[field]}
                onChange={handleChange}
                type={field === "password" ? "password" : "text"}
                placeholder={`Enter your ${field}`}
                className="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              />
            </div>
          ))}

          <motion.button
            type="submit"
            className="w-full mt-6 bg-blue-600 text-white py-3 rounded-md font-semibold hover:bg-blue-700 transition-colors"
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            variants={itemVariants}
          >
            Save Changes
          </motion.button>

          <motion.button
            type="button"
            onClick={handleCancel}
            className="w-full mt-3 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-white py-3 rounded-md font-semibold hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors"
            whileHover={{ scale: 1.02 }}
            whileTap={{ scale: 0.98 }}
            variants={itemVariants}
          >
            Cancel
          </motion.button>
        </form>
      </motion.div>
    </div>
  );
};

export default EditProfilePage;