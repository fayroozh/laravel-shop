import React, { useState } from "react";
import { motion } from "framer-motion";
import { useNavigate } from "react-router-dom";
import OrderSummary from "./OrderSummary";
import ShippingInfo from "./ShippingInfo";
import PaymentInfo from "./PaymentInfo";
import OrderSuccess from "./OrderSuccess";
import { handleWhatsAppCheckout } from "./WhatsAppCheckout";
import useCartStore from "../app/store";
import { authClient } from "../services/api-client";

const Checkout = () => {
  const navigate = useNavigate();
  const [step, setStep] = useState(1);
  const [isProcessing, setIsProcessing] = useState(false);
  const [paymentMethod, setPaymentMethod] = useState("card");
  const [formError, setFormError] = useState(""); // To display validation errors
  const [shippingInfo, setShippingInfo] = useState({
    firstName: "",
    lastName: "",
    email: "",
    phone: "",
    address: "",
    city: "",
    zipCode: "",
  });
  const { cartItems, getTotalPrice } = useCartStore();

  const subtotal = getTotalPrice();
  const tax = subtotal * 0.08;
  const total = subtotal + tax;

  const validateShippingInfo = () => {
    const requiredFields = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'zipCode'];
    const missingFields = requiredFields.filter(field => !shippingInfo[field]);
    if (missingFields.length > 0) {
      setFormError(`Please fill in all required fields: ${missingFields.join(', ')}`);
      return false;
    }
    setFormError("");
    return true;
  };

  const handleNextStep = () => {
    if (step === 2) {
      if (!validateShippingInfo()) {
        return; // Stop if validation fails
      }
    }
    if (step < 3) {
      setStep(step + 1);
    } else {
      handleSubmit();
    }
  };

  const handleSubmit = async () => {
    // Final validation before submitting
    if (!validateShippingInfo()) {
      setStep(2); // Go back to shipping step if info is missing
      return;
    }
    setIsProcessing(true);

    const simplifiedCartItems = cartItems.map(item => ({
      id: item.id,
      quantity: item.quantity,
    }));

    const orderData = {
      cartItems: simplifiedCartItems,
      shippingInfo,
      total,
    };

    try {
      await authClient.post("/frontend/orders", orderData);
      handleWhatsAppCheckout(cartItems, shippingInfo, subtotal, tax, total);
      setStep(4);
    } catch (error) {
      console.error("Error placing order:", error);
      if (error.response && error.response.status === 400) {
        setFormError(error.response.data.message); // Stock error
      } else if (error.response && error.response.status === 422) {
        // Extract and display backend validation errors
        const errors = error.response.data.errors;
        const errorMessages = Object.values(errors).flat().join(' ');
        setFormError(`Submission failed: ${errorMessages}`);
        console.error("Validation Errors:", errors);
      } else {
        setFormError("An unexpected error occurred. Please try again.");
      }
    } finally {
      setIsProcessing(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-purple-50 py-12 px-4 sm:px-6 lg:px-8">
      <motion.div
        className="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden z-10 relative"
        initial={{ opacity: 0, y: 50 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.5 }}
      >
        <div className="bg-gradient-to-r from-blue-500 to-purple-600 py-6 px-8">
          <h1 className="text-2xl font-bold text-white">Checkout</h1>
          <div className="flex mt-4 items-center">
            {[1, 2, 3].map((i) => (
              <React.Fragment key={i}>
                <div
                  className={`w-8 h-8 rounded-full flex items-center justify-center ${i <= step ? "bg-white text-purple-600" : "bg-purple-400 text-white"
                    } font-semibold`}
                >
                  {i}
                </div>
                {i < 3 && (
                  <div
                    className={`h-1 flex-1 mx-2 ${i < step ? "bg-white" : "bg-purple-400"
                      }`}
                  ></div>
                )}
              </React.Fragment>
            ))}
          </div>
        </div>

        <div className="p-8">
          {formError && (
            <div className="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
              <p className="font-bold">Error</p>
              <p>{formError}</p>
            </div>
          )}
          {step === 1 && <OrderSummary cartItems={cartItems} subtotal={subtotal} tax={tax} total={total} />}
          {step === 2 && <ShippingInfo shippingInfo={shippingInfo} setShippingInfo={setShippingInfo} />}
          {step === 3 && <PaymentInfo paymentMethod={paymentMethod} setPaymentMethod={setPaymentMethod} />}
          {step === 4 && <OrderSuccess navigate={navigate} />}

          {step < 4 && (
            <div className="flex justify-between mt-10">
              {step > 1 && (
                <button
                  className="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg font-medium hover:bg-gray-300"
                  onClick={() => setStep(step - 1)}
                >
                  Back
                </button>
              )}
              <button
                className={`px-8 py-3 rounded-lg font-medium shadow-md ${isProcessing ? "bg-gray-400 cursor-not-allowed" : "bg-gradient-to-r from-blue-500 to-purple-600 text-white hover:shadow-lg"
                  }`}
                onClick={handleNextStep}
                disabled={isProcessing}
              >
                {isProcessing ? "Processing..." : step === 3 ? "Complete Payment" : "Continue"}
              </button>
            </div>
          )}
        </div>
      </motion.div>
    </div>
  );
};

export default Checkout;