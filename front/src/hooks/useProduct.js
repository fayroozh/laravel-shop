// hooks/useProduct.js
import { useState, useEffect } from "react";
import axios from "axios"; 
import { apiClient } from '../services/api-client'; // استيراد apiClient المكون مسبقاً

export const useProduct = () => {
  const [data, setData] = useState([]);      
  const [loading, setLoading] = useState(true); 
  const [error, setError] = useState(null);    

  useEffect(() => {
    const fetchProducts = async () => {
      try {
        setLoading(true); 
        setError(null);   

        // استخدام apiClient بدلاً من axios مباشرة
        const response = await apiClient.get('/frontend/products'); // إزالة /api المكرر
        
        // التحقق من هيكل البيانات والتعامل معه بشكل صحيح
        if (response.data && response.data.products) {
          // إذا كانت البيانات تأتي بتنسيق { products: [...] }
          setData(response.data.products);
        } else if (Array.isArray(response.data)) {
          // إذا كانت البيانات مصفوفة مباشرة
          setData(response.data);
        } else {
          // إذا كان هناك هيكل آخر غير متوقع
          console.warn("Unexpected data structure:", response.data);
          setData([]);
        }
        
        console.log("Products fetched successfully:", response.data);
      } catch (err) {
        console.error("Error fetching products:", err);
        setError(err); 
      } finally {
        setLoading(false); 
      }
    };

    fetchProducts(); 
  }, []); 

  return { data, loading, error };
};