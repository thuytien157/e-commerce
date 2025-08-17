<template>
    <div v-if="isVisible" class="modal-overlay" @click.self="closeModal">
        <div class="modal-content">
            <span class="close-button" @click="closeModal">&times;</span>
            <div class="product-info-container">
                <div class="product-images">
                    <img :src="mainImage" alt="Sản phẩm chính" class="main-product-image" />
                    <div class="image-previews">
                        <img v-for="image in currentVariantImages" :key="image.id" :src="image.image_url"
                            alt="Ảnh xem trước" class="preview-image" :class="{ active: mainImage === image.image_url }"
                            @click="mainImage = image.image_url" />
                    </div>
                </div>

                <div class="product-details">
                    <h2>{{ product.name }}</h2>
                    <h3 class="price" style="color: blue">
                        {{ FormatData.formatNumber(product.price) }}VND
                    </h3>
                    <div v-if="product.variants" class="option-group">
                        <p class="option-label">Màu sắc:</p>
                        <div class="options">
                            <button v-for="color in uniqueColors" :key="color.attribute_value_id" :class="[
                                'color-option',
                                {
                                    'color-selected':
                                        selectedColor === color.attribute_value_id,
                                },
                            ]" @click="selectOption('color', color.attribute_value_id)"
                                class="d-inline-block rounded-circle" :style="{
                                    width: '1.75rem',
                                    height: '1.75rem',
                                    'background-color': color.attribute_name,
                                    cursor: 'pointer',
                                    border: '1px solid #ccc',
                                }"></button>
                        </div>
                    </div>

                    <div v-if="product.variants" class="option-group">
                        <p class="option-label">Kích cỡ:</p>
                        <div class="options">
                            <button v-for="size in uniqueSizes" :key="size.attribute_value_id"
                                class="option-button size-option" :class="{
                                    active: selectedSize === size.attribute_value_id,
                                    disabled: !isSizeAvailable(size.attribute_value_id),
                                }" @click="selectOption('size', size.attribute_value_id)"
                                :disabled="!isSizeAvailable(size.attribute_value_id)">
                                {{ size.attribute_name }}
                            </button>
                        </div>
                    </div>

                    <div class="quantity-control">
                        <button class="btn btn-outline-primary" @click="decrement">
                            -
                        </button>
                        <div min="1" class="w-100 text-center">{{ cartStore.quantity }}</div>
                        <button class="btn btn-outline-primary" @click="increment">
                            +
                        </button>
                    </div>

                    <div class="action-buttons">
                        <button class="add-to-cart-btn" @click="addToCart">
                            Thêm vào giỏ
                        </button>
                        <button class="buy-now-btn">Mua ngay</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, toRefs, watch } from "vue";
import useProductVariants from "../store/useProductVariants";
import FormatData from "../store/FormatData";
import { useCartStore } from "../store/cart";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    isVisible: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(["close", "add-to-cart"]);
const cartStore = useCartStore()
const { product } = toRefs(props);
const {
    mainImage,
    selectedColor,
    selectedSize,
    uniqueColors,
    uniqueSizes,
    currentVariant,
    currentVariantImages,
    isSizeAvailable,
} = useProductVariants(product);

const closeModal = () => {
    emit("close");
};

const selectOption = (type, value) => {
    if (type === "color") {
        selectedColor.value = value;
    } else if (type === "size") {
        selectedSize.value = value;
    }
};

const decrement = () => {
    if (cartStore.quantity > 1) {
        cartStore.quantity--;
    }
};

const increment = () => {
    cartStore.quantity++;
};

const addToCart = () => {
    if (!currentVariant.value) {
        alert("Vui lòng chọn đầy đủ màu sắc và kích cỡ.");
        return;
    }
    const color = uniqueColors.value.find(
        (c) => c.attribute_value_id === selectedColor.value
    );
    const size = uniqueSizes.value.find(
        (s) => s.attribute_value_id === selectedSize.value
    );

    const item = {
        id: props.product.id,
        variantId: currentVariant.value.id,
        quantity: cartStore.quantity,
        price: props.product.price,
        productName: props.product.name,
        selectedColor: color.attribute_name,
        selectedSize: size.attribute_name,
        image: mainImage.value,
    };

    emit("add-to-cart", item);
    // quantity.value = 1;
    closeModal();
};
</script>
<style scope>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    position: relative;
    max-width: 900px;
    width: 90%;
    display: flex;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-button {
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #888;
}

.product-info-container {
    display: flex;
    width: 100%;
}

.product-images {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.main-product-image {
    width: 100%;
    height: 400px;
    border-radius: 5px;
    object-fit: cover;
}

.image-previews {
    display: flex;
    gap: 10px;
    overflow-x: auto;
}

.preview-image {
    width: 60px;
    height: 60px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid transparent;
    transition: border-color 0.2s;
}

.preview-image.active {
    border-color: #000;
}

.product-details {
    flex: 1;
    padding-left: 20px;
}

.option-group {
    margin-bottom: 15px;
}

.options {
    display: flex;
    gap: 8px;
}

.option-button {
    background-color: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 8px 15px;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s;
}

.option-button.active {
    background-color: blue;
    color: #fff;
    border-color: #fff;
}

.option-button.disabled {
    background-color: #eee;
    color: #999;
    cursor: not-allowed;
    text-decoration: line-through;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-control input {
    width: 50px;
    text-align: center;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 5px;
}

.action-buttons {
    margin-top: 20px;
    display: flex;
    gap: 10px;
}

.add-to-cart-btn,
.buy-now-btn {
    padding: 12px 20px;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-weight: bold;
}

.add-to-cart-btn {
    background-color: #fff;
    border: 1px solid blue;
    color: blue;
}

.buy-now-btn {
    background-color: blue;
    color: #fff;
    border: none;
}

.color-selected {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transform: scale(1.3);
    transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
}
</style>
