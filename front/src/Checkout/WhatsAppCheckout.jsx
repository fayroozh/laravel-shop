const generateWhatsAppMessage = (cartItems, shippingInfo, subtotal, tax, total) => {
  const itemsText = cartItems.map((item) =>
    `• ${item.title} - $${Number(item.price).toFixed(2)} x ${item.quantity || 1}`
  ).join("\\n");

  const totalsText = `\\nSubtotal: $${Number(subtotal).toFixed(2)}\\nTax: $${Number(tax).toFixed(2)}\\nTotal: $${Number(total).toFixed(2)}`;

  const customerDetails = `\\n\\nCustomer Details:\nName: ${shippingInfo.firstName} ${shippingInfo.lastName}\nEmail: ${shippingInfo.email}\nPhone: ${shippingInfo.phone}\nAddress: ${shippingInfo.address}, ${shippingInfo.city}, ${shippingInfo.zipCode}\nPayment Method: ${shippingInfo.paymentMethod}`;

  // Combine all parts
  const message = `New Order Received!\\n\\nItems:\\n${itemsText}${totalsText}${customerDetails}`;

  return encodeURIComponent(message);
};

export const handleWhatsAppCheckout = (cartItems, shippingInfo, subtotal, tax, total) => {
  const phoneNumber = "+306997331033";
  const message = generateWhatsAppMessage(cartItems, shippingInfo, subtotal, tax, total);
  const whatsappUrl = `https://api.whatsapp.com/send?phone=${phoneNumber}&text=${message}`;

  window.location.href = whatsappUrl;
};