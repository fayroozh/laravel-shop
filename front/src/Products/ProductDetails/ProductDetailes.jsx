import { useParams } from "react-router-dom";
import ErrorMessage from "../../components/Error";
import LoadingSpinner from "../../components/LoadingSpinner";
import ProductDetailsContent from "./ProductDetailsContent";
import ProductImageGallery from "./ProductImageGallery";
import useCartStore from "../../app/store";
import { useProduct } from "../../hooks/useProduct"; // Correct the import if needed
import useFavoriteStore from "../../app/addToFavorite";
import { motion } from "framer-motion";
const pageVariants = {
  initial: { opacity: 0 },
  animate: { opacity: 1 },
  exit: { opacity: 0 },
};
const ProductDetails = () => {
  const { id } = useParams();
  const { addToCart } = useCartStore();
  // Use the correct hook
  const { data: product, isLoading, error } = useProduct(id);
  const { addToFav } = useFavoriteStore();

  const handleAddToCart = () => addToCart(product);
  const handleAddToFavorite = () => addToFav(product);

  if (isLoading) return <LoadingSpinner />;
  if (error) return <ErrorMessage error={error} />;

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
