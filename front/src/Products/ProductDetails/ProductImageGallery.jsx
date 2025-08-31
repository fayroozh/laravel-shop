import { motion } from "framer-motion";
import { slideInVariants } from "./animation";

const fallback = "/no-image.png"; // ضع صورة داخل public/no-image.png

const ProductImageGallery = ({ product }) => {
  const main =
    product?.image_url ||
    product?.images?.[0]?.url ||
    product?.images?.[0]?.image_url ||
    product?.thumbnail ||
    null;

  const thumbs = (product?.gallery ?? product?.images ?? [])
    .map((img) =>
      typeof img === "string" ? img : (img?.url || img?.image_url || img?.image_path)
    )
    .filter(Boolean);

  return (
    <motion.div custom={-1} variants={slideInVariants} className="space-y-4">
      {/* الصورة الرئيسية */}
      <div className="aspect-square rounded-lg overflow-hidden bg-gray-100 h-80">
        <img
          src={main || fallback}
          alt={product?.title || "Product"}
          onError={(e) => (e.currentTarget.src = fallback)}
          className="w-full h-full object-cover"
        />
      </div>

      {/* الصور المصغرة */}
      {thumbs.length > 1 && (
        <div className="grid grid-cols-4 gap-4 ">
          {thumbs.slice(0, 4).map((src, index) => (
            <div
              key={index}
              className="aspect-square rounded-lg overflow-hidden bg-gray-100"
            >
              <img
                src={src || fallback}
                alt={`${product?.title || "Product"} ${index + 1}`}
                onError={(e) => (e.currentTarget.src = fallback)}
                className="w-full h-full object-cover hover:scale-110 transition-transform"
              />
            </div>
          ))}
        </div>
      )}
    </motion.div>
  );
};

export default ProductImageGallery;
