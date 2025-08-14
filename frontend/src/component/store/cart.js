import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import { useTokenUser } from "./useTokenUser";

export const useCartStore = defineStore("cart", () => {
  const store = useTokenUser();
  const items = ref([]);
  const quantity = ref(1)
  const cartKey = computed(() => {
    return store.userId ? `cart_huce_${store.userId}` : "cart_huce_guest";
  });

  const saveCart = () => {
    localStorage.setItem(cartKey.value, JSON.stringify(items.value));
  };

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
  );
  
  const cartLength = computed(() => items.value.length);
  const totalPrice = computed(() => {
    return items.value.reduce(
      (total, item) => total + item.price * item.quantity,
      0
    );
  });

  const addItem = (product) => {
    const existingItem = items.value.find(
      (item) => item.variantId === product.variantId
    );
    if (existingItem) {
      existingItem.quantity++;
    } else {
      items.value.push({ ...product, quantity: quantity.value });
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
    quantity
  };
});
