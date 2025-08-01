import { useState, useEffect } from "react";
import axios from "axios";
import { API_URL } from "../../constant/api";

function Activities() {
  const [activities, setActivities] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchActivities();
  }, []);

  const fetchActivities = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/activities`);
      setActivities(response.data);
      setLoading(false);
    } catch (error) {
      console.error("Error fetching activities:", error);
      setLoading(false);
    }
  };

  return (
    <div className="p-4">
      <div className="dashboard-header mb-4">
        <h1 className="text-2xl font-bold">🕒 سجل النشاطات</h1>
      </div>
      
      <div className="bg-white rounded-lg shadow p-4">
        <div className="activity-list space-y-4">
          {loading ? (
            <div className="text-center py-4">جاري التحميل...</div>
          ) : activities.length > 0 ? (
            activities.map((activity) => (
              <div key={activity.id} className="activity-item flex items-start p-3 border-b border-gray-200">
                <div className="activity-icon mr-3 text-2xl">{activity.icon || '📋'}</div>
                <div className="activity-content">
                  <div className="activity-text font-medium">{activity.description}</div>
                  <div className="activity-time text-sm text-gray-500">{activity.created_at}</div>
                </div>
              </div>
            ))
          ) : (
            <div className="activity-item flex items-start p-3 border-b border-gray-200">
              <div className="activity-icon mr-3 text-2xl">ℹ️</div>
              <div className="activity-content">
                <div className="activity-text font-medium">لا توجد نشاطات مسجلة حتى الآن</div>
                <div className="activity-time text-sm text-gray-500">-</div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default Activities;
