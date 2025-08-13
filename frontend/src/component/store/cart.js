import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import { useTokenUser } from "./useTokenUser";

export const useCartStore = defineStore("cart", () => {
  const store = useTokenUser();
  const items = ref([]);

  // Định nghĩa một key động dựa trên userId
  const cartKey = computed(() => {
    // Trả về key riêng cho user nếu đã đăng nhập, ngược lại dùng key cho khách
    return store.userId ? `cart_huce_${store.userId}` : "cart_huce_guest";
  });

  // Hàm để lưu giỏ hàng
  const saveCart = () => {
    localStorage.setItem(cartKey.value, JSON.stringify(items.value));
  };

  // Hàm để tải giỏ hàng
  const loadCart = () => {
    const savedCart = localStorage.getItem(cartKey.value);
    items.value = savedCart ? JSON.parse(savedCart) : [];
  };

  watch(
    cartKey,
    (newKey, oldKey) => {
      if (oldKey) {
        localStorage.setItem(oldKey, JSON.stringify(items.value));
      }
      loadCart();
    },
    { immediate: true }
  ); // `immediate: true` để chạy loadCart ngay khi store được khởi tạo

  // Các computed properties
  const cartLength = computed(() => items.value.length);
  const totalPrice = computed(() => {
    return items.value.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
  });

  // Các actions
  const addItem = (product) => {
    const existingItem = items.value.find(
      (item) => item.variantId === product.variantId
    );
    if (existingItem) {
      existingItem.quantity++;
    } else {
      items.value.push({ ...product, quantity: 1 });
    }
    saveCart();
  };

  const removeItem = (variantId) => {
    items.value = items.value.filter((item) => item.variantId !== variantId);
    saveCart();
  };

  const removeCart = () => {
    localStorage.removeItem(cartKey.value);
    items.value = [];
  };

  const increment = (variantId) => {
    const item = items.value.find((i) => i.variantId === variantId);
    if (item) {
      item.quantity++;
      saveCart();
    }
  };

  const decrement = (variantId) => {
    const item = items.value.find((i) => i.variantId === variantId);
    if (item && item.quantity > 1) {
      item.quantity--;
      saveCart();
    }
  };

  return {
    items,
    cartLength,
    totalPrice,
    addItem,
    removeItem,
    removeCart,
    increment,
    decrement,
    loadCart,
  };
});
