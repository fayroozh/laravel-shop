import React, { useState } from "react";
import useCartStore from "../app/store";
import { useNavigate } from "react-router-dom";
import Drawer from "../components/Drawer";
import { BsInfoCircle } from "react-icons/bs";
import { FaTrash } from "react-icons/fa";

const API_BASE = import.meta.env.VITE_API_URL || "http://localhost:8000";
const toAbsolute = (u) => {
  if (!u) return null;
  if (typeof u !== "string") return null;
  if (u.startsWith("http")) return u;
  if (u.startsWith("/")) return `${API_BASE}${u}`;
  return `${API_BASE}/storage/${u.replace(/^storage\//, "")}`;
};
const fallback = "/no-image.png";

const DrawerProduct = () => {
  const [isOpen, setIsOpen] = useState(false);
  const { cartItems, removeFromCart, updateQuantity, getTotalPrice } = useCartStore();
  const navigate = useNavigate();

  const toggleDrawer = () => setIsOpen((v) => !v);
  const cartIcon = <BsInfoCircle className="w-4 h-4 me-2.5" />;

  return (
    <div>
      <div className="text-center">
        <button
          className="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5"
          type="button"
          onClick={toggleDrawer}
        >
          Cart{" "}
          <span className="ml-1 bg-white text-blue-700 rounded-full px-2 py-0.5">
            {cartItems.length}
          </span>
        </button>
      </div>

      <Drawer
        isOpen={isOpen}
        onClose={toggleDrawer}
        title={`Your Cart (${cartItems.length} items)`}
        icon={cartIcon}
      >
        {cartItems.length === 0 ? (
          <p className="mb-6 text-sm text-gray-500 dark:text-gray-400">
            Your cart is empty. Add some products to get started!
          </p>
        ) : (
          <div className="space-y-5">
            {cartItems.map((item) => {
              const first =
                item?.images?.[0] && typeof item.images[0] === "object"
                  ? (item.images[0].url ||
                     item.images[0].image_url ||
                     item.images[0].image_path)
                  : (typeof item?.images?.[0] === "string" ? item.images[0] : null);

              const rawImg =
                item?.image_url || first || item?.thumbnail || item?.image || null;
              const src = toAbsolute(rawImg) || fallback;

              return (
                <div
                  key={item.id}
                  className="border-b pb-5 grid grid-cols-[84px_1fr] gap-5 items-start"
                >
                  {/* يسار: الصورة فقط */}
                  <div className="flex items-start">
                    <img
                      src={src}
                      alt={item.title || item.name || "Product"}
                      onError={(e) => (e.currentTarget.src = fallback)}
                      className="w-20 h-20 object-cover rounded"
                    />
                  </div>

                  {/* يمين: الاسم والسعر + الكنترولز تحتهم وبالنص */}
                  <div className="flex flex-col">
                    <h6 className="font-medium leading-tight mb-1 line-clamp-2">
                      {item.title || item.name}
                    </h6>
                    <p className="text-sm text-gray-500">
                      ${Number(item.price ?? 0).toFixed(2)} × {item.quantity}
                    </p>

                    {/* ↓↓↓ الكنترولز باليمين تحت وبالنص */}
                    <div className="mt-4 flex items-center justify-center gap-6">
                      <div className="inline-flex items-center border rounded-lg overflow-hidden shadow-sm">
                        <button
                          onClick={() =>
                            updateQuantity(item.id, Math.max(1, item.quantity - 1))
                          }
                          className="px-3 py-2 hover:bg-gray-100"
                          aria-label="Decrease quantity"
                        >
                          −
                        </button>
                        <span className="px-4 py-2 min-w-10 text-center select-none">
                          {item.quantity}
                        </span>
                        <button
                          onClick={() => updateQuantity(item.id, item.quantity + 1)}
                          className="px-3 py-2 hover:bg-gray-100"
                          aria-label="Increase quantity"
                        >
                          +
                        </button>
                      </div>

                      <button
                        onClick={() => removeFromCart(item.id)}
                        className="text-red-500 hover:text-red-600"
                        aria-label="Remove from cart"
                        title="Remove"
                      >
                        <FaTrash className="w-5 h-5" />
                      </button>
                    </div>
                    {/* إذا بدّكها يمين بدل بالنص: غيّر justify-center => justify-end */}
                  </div>
                </div>
              );
            })}

            <div className="mt-2 pt-2 border-t">
              <div className="flex justify-between font-medium">
                <span>Total:</span>
                <span>${Number(getTotalPrice() ?? 0).toFixed(2)}</span>
              </div>

              <div className="mt-4">
                <button
                  onClick={() => navigate("/checkout")}
                  className="w-full bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors"
                >
                  Regular Checkout
                </button>
              </div>
            </div>
          </div>
        )}
      </Drawer>
    </div>
  );
};

export default DrawerProduct;