// src/Products/FetchProduct.jsx
import React, { useMemo, useState } from "react";
import { useProduct } from "../hooks/useProduct";   // نفس الهوك الحالي عندك
import useCartStore from "../app/store";
import ProductCard from "./ProducstCard";
import FilterProduct from "./FilterProduct";

const API_BASE = import.meta.env.VITE_API_URL || "http://localhost:8000";
const toAbsolute = (u) => {
  if (!u) return null;
  if (typeof u !== "string") return null;
  if (u.startsWith("http")) return u;
  if (u.startsWith("/")) return `${API_BASE}${u}`;
  return `${API_BASE}/storage/${u.replace(/^storage\//, "")}`;
};

const normalize = (p) => {
  const main =
    p.image_url ||
    p.image ||
    p.thumbnail ||
    p?.images?.[0]?.url ||
    p?.images?.[0]?.image_url ||
    p?.images?.[0]?.image_path ||
    null;

  return {
    id: p.id,
    title: p.title ?? p.name ?? "Untitled",
    description: p.description ?? "",
    price: p.price ?? 0,
    stock: p.stock ?? 0,
    category: p.category?.name ?? p.category ?? "",
    image_url: toAbsolute(main),
    images: (p.images || [])
      .map((img) => toAbsolute(img.url || img.image_url || img.image_path))
      .filter(Boolean),
    _raw: p,
  };
};

const FetchProduct = () => {
  const { data, loading, error } = useProduct(); // ممكن ترجع {data:[...]} أو {data:{data:[...]}} أو مصفوفة
  const { addToCart } = useCartStore();
  const [searchQuery, setSearchQuery] = useState("");

  // حوّل أي شكل لإستجابة إلى مصفوفة جاهزة
  const products = useMemo(() => {
    const arr = Array.isArray(data)
      ? data
      : Array.isArray(data?.data)
      ? data.data
      : Array.isArray(data?.products)
      ? data.products
      : Array.isArray(data?.items)
      ? data.items
      : [];
    return arr.map(normalize);
  }, [data]);

  // فلترة بسيطة بالاسم/العنوان
  const visible = useMemo(() => {
    if (!searchQuery) return products;
    const q = searchQuery.toLowerCase();
    return products.filter(
      (p) =>
        p.title?.toLowerCase().includes(q) ||
        p.category?.toLowerCase().includes(q)
    );
  }, [products, searchQuery]);

  if (loading) return <div className="p-6 text-gray-600">Loading…</div>;
  if (error)   return <div className="p-6 text-red-600">Failed to load products.</div>;

  return (
    <div className="max-w-7xl mx-auto px-4 py-8">
      <FilterProduct setSearchQuery={setSearchQuery} isLoading={loading} />

      {visible.length === 0 ? (
        <div className="text-center text-gray-500">No products found.</div>
      ) : (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {visible.map((p) => (
            <ProductCard key={p.id} project={p} addToCart={addToCart} />
          ))}
        </div>
      )}
    </div>
  );
};

export default FetchProduct;
