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

                    <div v-if="product.variants">
                        <div v-for="attribute in uniqueAttributes" :key="attribute.id" class="option-group">
                            <p class="option-label">{{ attribute.name }}:</p>
                            <div class="options">
                                <button v-for="value in attribute.values" :key="value.attribute_value_id" :class="[
                                    'option-button',
                                    {
                                        active:
                                            selectedAttributes[attribute.id] ===
                                            value.attribute_value_id,
                                        disabled: !isAttributeAvailable(
                                            attribute.id,
                                            value.attribute_value_id
                                        ),
                                        // Thêm class đặc biệt cho màu sắc
                                        'color-selected':
                                            attribute.name === 'Màu sắc' &&
                                            selectedAttributes[attribute.id] ===
                                            value.attribute_value_id,
                                    },
                                ]" @click="selectAttribute(attribute.id, value.attribute_value_id)" :disabled="!isAttributeAvailable(attribute.id, value.attribute_value_id)
                                    " :style="attribute.name === 'Màu sắc'
                                        ? {
                                            width: '1.75rem',
                                            height: '1.75rem',
                                            'background-color': value.attribute_name,
                                            cursor: 'pointer',
                                            border: '1px solid #ccc',
                                            'border-radius': '50%',
                                            padding: '0',
                                        }
                                        : {}
                                        ">
                                    <span v-if="attribute.name !== 'Màu sắc'">{{
                                        value.attribute_name
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="quantity-control">
                        <button class="btn btn-outline-primary" @click="decrement">-</button>
                        <div min="1" class="w-100 text-center">{{ cartStore.quantity }}</div>
                        <button class="btn btn-outline-primary" @click="increment">+</button>
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
const cartStore = useCartStore();
const { product } = toRefs(props);

// Thêm 'selectAttribute' vào danh sách các hàm/biến được lấy ra
const {
    mainImage,
    selectedAttributes,
    uniqueAttributes,
    currentVariant,
    currentVariantImages,
    isAttributeAvailable,
    selectAttribute,
} = useProductVariants(product);

const closeModal = () => {
    emit("close");
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
        alert("Vui lòng chọn đầy đủ các thuộc tính của sản phẩm.");
        return;
    }

    const selectedAttributesData = {};
    for (const attributeId in selectedAttributes.value) {
        const attribute = uniqueAttributes.value.find(
            (attr) => attr.id.toString() === attributeId
        );
        const value = attribute.values.find(
            (val) => val.attribute_value_id === selectedAttributes.value[attributeId]
        );
        selectedAttributesData[attribute.name] = value.attribute_name;
    }

    const selectedAttributesValues = Object.values(selectedAttributesData);

    const item = {
        id: props.product.id,
        variantId: currentVariant.value.id,
        quantity: cartStore.quantity,
        price: props.product.price,
        productName: props.product.name,
        selectedAttributes: selectedAttributesValues,
        image: mainImage.value,
    };

    emit("add-to-cart", item);
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
