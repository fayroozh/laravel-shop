import React, { useState } from "react";
import { useQuery } from "@tanstack/react-query";
import { getCategories } from "../services/api-client";

const FilterProduct = ({ onFilterChange, isLoading }) => {
  const [activeFilter, setActiveFilter] = useState("");

  const { data: categories, isLoading: loadingCategories } = useQuery({
    queryKey: ["categories"],
    queryFn: getCategories,
    select: (res) => res.data || [],
  });

  const handleFilterClick = (categoryId) => {
    setActiveFilter(categoryId);
    onFilterChange(categoryId);
  };

  const handleReset = () => {
    setActiveFilter("");
    onFilterChange("");
  };


  if (isLoading || loadingCategories) {
    return <div className="text-center text-gray-600">Loading ...</div>;
  }

  return (
    <div className="flex flex-col items-center justify-center gap-4 mb-10 px-4 md:mt-10">
      <div className="flex flex-wrap items-center justify-center gap-4">
        <button
          onClick={handleReset}
          className={`px-6 py-2 rounded-full font-medium transition-all duration-300 ${activeFilter === ""
            ? "bg-blue-600 text-white shadow-lg shadow-blue-300"
            : "bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600"
            }`}
        >
          All
        </button>

        {categories?.map((cat) => (
          <button
            key={cat.id}
            onClick={() => handleFilterClick(cat.id)}
            className={`px-6 py-2 rounded-full font-medium capitalize transition-all duration-300 ${activeFilter === cat.id
              ? "bg-blue-600 text-white shadow-lg shadow-blue-300"
              : "bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600"
              }`}
          >
            {cat.name}
          </button>
        ))}
      </div>
    </div>
  );
};

export default FilterProduct;