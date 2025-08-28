// hooks/useProduct.js
import { useState, useEffect } from "react";
import { apiClient } from "../services/api-client"; 

export const useProduct = (searchQuery = "") => {
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchProducts = async () => {
      setLoading(true);
      try {
        const params = {};
        if (searchQuery) {
          params.search = searchQuery;
        }
        const response = await apiClient.get("/frontend/products", { params });
        setData(response.data);
      } catch (err) {
        setError(err);
        console.error("Failed to fetch products:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, [searchQuery]);

  return { data, loading, error };
};
