import { useParams } from "react-router-dom";
import ErrorMessage from "../../components/Error";
import LoadingSpinner from "../../components/LoadingSpinner";
import ProductDetailsContent from "./ProductDetailsContent";
import ProductImageGallery from "./ProductImageGallery";
import useCartStore from "../../app/store";
import useFavoriteStore from "../../app/addToFavorite";
import { motion } from "framer-motion";
import { useProductDetails } from "../../hooks/useProductDetails"; // <-- الهُوك الجديد

const pageVariants = {
  initial: { opacity: 0 },
  animate: { opacity: 1 },
  exit: { opacity: 0 },
};

const ProductDetails = () => {
  const { id } = useParams();

  const { addToCart } = useCartStore();
  const { addToFav } = useFavoriteStore();

  // استخدم هوك التفاصيل
  const { product, loading, error } = useProductDetails(id);

  const handleAddToCart = () => product && addToCart(product);
  const handleAddToFavorite = () => product && addToFav(product);

  if (loading) return <LoadingSpinner />;
  if (error)   return <ErrorMessage error={error} />;
  if (!product) return <ErrorMessage error="المنتج غير متاح." />;

  return (
    <motion.div
      variants={pageVariants}
      initial="initial"
      animate="animate"
      exit="exit"
      className="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-12 px-4"
    >
      <div className="max-w-7xl mx-auto">
        <div className="bg-white rounded-2xl shadow-xl overflow-hidden">
          <div className="grid md:grid-cols-2 gap-8 p-8">
            {/* المعرض يقرأ product.image_url و product.gallery */}
            <ProductImageGallery product={product} />
            <ProductDetailsContent
              product={product}
              onAddToCart={handleAddToCart}
              onAddToFavorite={handleAddToFavorite}
            />
          </div>
        </div>
      </div>
    </motion.div>
  );
};

export default ProductDetails;