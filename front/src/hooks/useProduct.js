// hooks/useProduct.js
import { useState, useEffect } from "react";
import { apiClient } from "../services/api-client";

export const useProduct = (filters = {}) => {
  const { category, search, min_price, max_price } = filters;
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchProducts = async () => {
      setLoading(true);
      try {
        const params = {};
        if (search) {
          params.search = search;
        }
        if (category) {
          params.category = category;
        }
        if (min_price) {
          params.min_price = min_price;
        }
        if (max_price) {
          params.max_price = max_price;
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
  }, [category, search, min_price, max_price]);

  return { data, loading, error };
};