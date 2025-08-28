import React, { useState, useEffect } from "react";
import axios from "axios";

const FilterProduct = ({ setSearchQuery, isLoading }) => {
  const [activeFilter, setActiveFilter] = useState("");
  const [categories, setCategories] = useState([]);
  const [loadingCategories, setLoadingCategories] = useState(true);

  // جلب التصنيفات من API
  useEffect(() => {
    const fetchCategories = async () => {
      try {
        const res = await axios.get("http://localhost:8000/api/frontend/categories");

        console.log("Full API Response:", res.data);

        // ✅ محاولة قراءة التصنيفات حسب أكثر من احتمال
        let cats = [];

        if (Array.isArray(res.data)) {
          cats = res.data;
        } else if (Array.isArray(res.data.data)) {
          cats = res.data.data;
        } else if (Array.isArray(res.data.categories)) {
          cats = res.data.categories;
        }

        setCategories(cats);
      } catch (error) {
        console.error("Error fetching categories:", error);
      } finally {
        setLoadingCategories(false);
      }
    };

    fetchCategories();
  }, []);

  if (isLoading || loadingCategories) {
    return <div className="text-center text-gray-600">Loading ... </div>;
  }

  const handleFilterClick = (filter) => {
    setActiveFilter(filter);
    setSearchQuery(filter);
  };

  return (
    <div className="flex flex-wrap items-center justify-center gap-4 mb-10 px-4 md:mt-10">
      {/* زر all */}
      <button
        onClick={() => {
          setActiveFilter("");
          setSearchQuery("");
        }}
        className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${
          activeFilter === ""
            ? "bg-blue-600 text-white shadow-lg shadow-blue-300"
            : "bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600"
        }`}
      >
        All
      </button>

      {/* الأزرار الجاية من API */}
      {categories.map((cat) => (
        <button
          key={cat.id || cat.name}
          onClick={() => handleFilterClick(cat.name)}
          className={`px-6 py-2 rounded-full font-medium capitalize transition-all duration-300 ${
            activeFilter === cat.name
              ? "bg-blue-600 text-white shadow-lg shadow-blue-300"
              : "bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600"
          }`}
        >
          {cat.name}
        </button>
      ))}
    </div>
  );
};

export default FilterProduct;