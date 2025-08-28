import React, { useState, useEffect } from "react";
import { motion } from "framer-motion";
import { useNavigate } from "react-router-dom";
import useProfile from "../hooks/useProfile";
import { authClient } from "../services/api-client";
import LoadingSpinner from "../components/LoadingSpinner";
import ErrorMessage from "../components/Error";
import { FiEdit, FiUser, FiMail, FiLogIn } from "react-icons/fi";

const containerVariants = {
  hidden: { opacity: 0, scale: 0.95 },
  visible: {
    opacity: 1,
    scale: 1,
    transition: { delayChildren: 0.2, staggerChildren: 0.1 },
  },
};

const itemVariants = {
  hidden: { y: 20, opacity: 0 },
  visible: { y: 0, opacity: 1 },
};

const OrderHistory = ({ userId, token }) => {
  const [orders, setOrders] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!userId || !token) return;
    setIsLoading(true);
    authClient
      .get(`/frontend/users/${userId}/orders`, {
        headers: { Authorization: `Bearer ${token}` },
      })
      .then((res) => setOrders(res.data))
      .catch((err) => {
        console.error(err);
        setError("Failed to fetch orders.");
      })
      .finally(() => setIsLoading(false));
  }, [userId, token]);

  if (isLoading) return <p>Loading orders...</p>;
  if (error) return <ErrorMessage error={error} />;

  return (
    <motion.div variants={itemVariants} className="mt-10">
      <h3 className="text-2xl font-bold text-gray-900 dark:text-white mb-4">
        Order History
      </h3>
      <div className="space-y-4">
        {orders.length > 0 ? (
          orders.map((order) => (
            <div
              key={order.id}
              className="bg-gray-100 dark:bg-gray-700 p-4 rounded-lg"
            >
              <div className="flex justify-between items-center">
                <div>
                  <p className="font-semibold">Order #{order.id}</p>
                  <p className="text-sm text-gray-500 dark:text-gray-400">
                    {new Date(order.created_at).toLocaleDateString()}
                  </p>
                </div>
                <div className="text-right">
                  <p className="font-semibold">${Number(order.total).toFixed(2)}</p>
                  <span
                    className={`px-2 py-1 text-xs rounded-full ${
                      order.status === "Delivered"
                        ? "bg-green-200 text-green-800"
                        : "bg-yellow-200 text-yellow-800"
                    }`}
                  >
                    {order.status}
                  </span>
                </div>
              </div>
            </div>
          ))
        ) : (
          <p>You have no orders yet.</p>
        )}
      </div>
    </motion.div>
  );
};

const ProfilePage = () => {
  const { profile, isLoading, error } = useProfile();
  const navigate = useNavigate();

  const token = localStorage.getItem("token"); // أو استعمل authStore إذا عندك

  if (isLoading) return <LoadingSpinner />;
  if (error) return <ErrorMessage error={error.message || "Failed to load profile."} />;

  const InfoField = ({ icon, label, value }) => (
    <motion.div
      className="flex items-center space-x-4 rtl:space-x-reverse"
      variants={itemVariants}
    >
      <div className="bg-gray-200 dark:bg-gray-700 p-2 rounded-full">{icon}</div>
      <div>
        <p className="text-sm text-gray-500 dark:text-gray-400">{label}</p>
        <p className="font-semibold text-gray-800 dark:text-white">
          {value || "Not set"}
        </p>
      </div>
    </motion.div>
  );

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4">
      <motion.div
        className="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden"
        variants={containerVariants}
        initial="hidden"
        animate="visible"
      >
        <div className="p-8">
          <motion.div
            className="flex flex-col sm:flex-row items-center justify-between mb-8"
            variants={itemVariants}
          >
            <div className="flex items-center space-x-4 rtl:space-x-reverse mb-4 sm:mb-0">
              <img
                src={
                  profile?.avatar ||
                  `https://ui-avatars.com/api/?name=${profile?.firstName}+${profile?.lastName}&background=random`
                }
                alt="Profile"
                className="w-24 h-24 rounded-full object-cover border-4 border-blue-500"
              />
              <div>
                <h2 className="text-3xl font-bold text-gray-900 dark:text-white">
                  {`${profile?.firstName || ""} ${profile?.lastName || ""}`}
                </h2>
                <p className="text-gray-500 dark:text-gray-400">
                  @{profile?.username || "username"}
                </p>
              </div>
            </div>
            <button
              onClick={() => navigate("/edit-profile")}
              className="flex items-center space-x-2 rtl:space-x-reverse bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
            >
              <FiEdit />
              <span>Edit Profile</span>
            </button>
          </motion.div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <InfoField icon={<FiUser className="text-blue-500" />} label="First Name" value={profile?.firstName} />
            <InfoField icon={<FiUser className="text-blue-500" />} label="Last Name" value={profile?.lastName} />
            <InfoField icon={<FiMail className="text-purple-500" />} label="Email" value={profile?.email} />
            <InfoField
              icon={<FiLogIn className="text-green-500" />}
              label="Last Login"
              value={profile?.lastLogin ? new Date(profile.lastLogin).toLocaleDateString() : "N/A"}
            />
          </div>

          <OrderHistory userId={profile?.id} token={token} />
        </div>
      </motion.div>
    </div>
  );
};

export default ProfilePage;
