import { defineStore } from "pinia";
import { ref, computed, watch } from "vue";
import { useTokenUser } from "./useTokenUser";
import Swal from "sweetalert2";
import axios from "axios";

export const useCartStore = defineStore("cart", () => {
  const store = useTokenUser();
  const items = ref([]);
  const quantity = ref(1);
  const cartKey = computed(() => {
    return store.userId ? `cart_huce_${store.userId}` : "cart_huce_guest";
  });

  const saveCart = () => {
    localStorage.setItem(cartKey.value, JSON.stringify(items.value));
  };

  const loadCart = () => {
    const savedCart = localStorage.getItem(cartKey.value);
    if (savedCart) {
      const parsedCart = JSON.parse(savedCart);

      items.value = parsedCart.map((item) => {
        return {
          ...item,
          isCheck: item.isCheck !== undefined ? item.isCheck : true,
        };
      });
    } else {
      items.value = [];
    }
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

  const cartLength = computed(() => {
    return items.value.filter((item) => item.isCheck).length;
  });
  const totalPrice = computed(() => {
    return items.value
      .filter((item) => item.isCheck)
      .reduce((total, item) => total + item.price * item.quantity, 0);
  });

  const addItem = (product) => {
    const existingItem = items.value.find(
      (item) => item.variantId === product.variantId
    );

    if (existingItem) {
      existingItem.quantity++;
    } else {
      items.value.push({ ...product, quantity: quantity.value, isCheck: true });
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

  const increment = async (variantId) => {
    const item = items.value.find((i) => i.variantId === variantId);
    if (!item) {
      console.log("Sản phẩm không tìm thấy trong giỏ hàng!");
      return;
    }

    const res = await axios.get(
      `http://127.0.0.1:8000/api/variant/${variantId}`
    );
    const stockQuantity = res.data.variant.stock_quantity;

    if (item.quantity >= stockQuantity) {
      Swal.fire({
        toast: true,
        position: "top-end",
        icon: "error",
        title: `Số lượng sản phẩm đã đạt tối đa trong kho (${stockQuantity})!`,
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
      });
      return;
    }

    item.quantity++;
    saveCart();
  };

  const decrement = (variantId) => {
    const item = items.value.find((i) => i.variantId === variantId);
    if (item && item.quantity > 1) {
      item.quantity--;
      saveCart();
    }
  };
  const checkbox = () => {
    saveCart();
  };

  const removeCheckedItems = () => {
    items.value = items.value.filter((item) => !item.isCheck);
    saveCart();
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
    quantity,
    checkbox,
    removeCheckedItems,
  };
});
