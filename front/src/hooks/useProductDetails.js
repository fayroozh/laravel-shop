// hooks/useProductDetails.js
import { useEffect, useState } from "react";
import { apiClient } from "../services/api-client";

const API_BASE = import.meta.env.VITE_API_URL || "http://localhost:8000";

// يحوّل أي مسار نسبي لرابط مطلق
const toAbsolute = (u) => {
  if (!u) return null;
  if (u.startsWith("http")) return u;
  if (u.startsWith("/")) return `${API_BASE}${u}`;
  // مثل: "products/xxx.png" => "/storage/products/xxx.png"
  return `${API_BASE}/storage/${u.replace(/^storage\//, "")}`;
};

export const useProductDetails = (id) => {
  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!id) {
      setError("لا يوجد معرف منتج.");
      setLoading(false);
      return;
    }

    (async () => {
      try {
        setLoading(true);
        setError(null);

        // مهم: apiClient.baseURL لازم يكون = `${API_BASE}/api`
        const res = await apiClient.get(`/frontend/products/${id}`);

        // الـ API عندك يرجّع غالباً { data: {...} }
        const p = res.data?.data ?? res.data ?? null;
        if (!p) throw new Error("لم يتم العثور على المنتج");

        const main =
          p.image_url ||
          p.images?.[0]?.url ||
          p.images?.[0]?.image_url ||
          p.image ||
          null;

        const normalized = {
          id: p.id,
          title: p.title ?? p.name ?? "Untitled",
          description: p.description ?? "",
          price: p.price ?? 0,
          stock: p.stock ?? 0,
          rating: p.rating ?? null,
          category: p.category?.name ?? p.category?.title ?? "",
          image_url: toAbsolute(main),
          gallery: (p.images || []).map((img) =>
            toAbsolute(img.url || img.image_url || img.image_path)
          ),
          _raw: p,
        };

        setProduct(normalized);
      } catch (e) {
        console.error(e);
        setError(e.message || "فشل جلب تفاصيل المنتج.");
      } finally {
        setLoading(false);
      }
    })();
  }, [id]);

  return { product, loading, error };
};
