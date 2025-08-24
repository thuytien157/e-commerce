<script setup>
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import FormatData from "@/component/store/FormatData";
import useProductVariants from "@/component/store/useProductVariants";
import { useCartStore } from "@/component/store/cart";
import Rating from "@/component/client/Rating.vue";
import Swal from "sweetalert2";
const props = defineProps({
    productId: {
        type: [String, Number],
        default: null,
    },
});
const product = ref(null);
const isLoading = ref(true)
const getProductById = async (productId) => {
    isLoading.value = true
    try {
        const res = await axios.get(
            `${import.meta.env.VITE_URL_API}api/products_detail/${productId}?${queryParams.value}`
        );
        product.value = res.data.product;
        // console.log("sss" + product.value);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false
    }
};
const selectedRating = ref([]);
const queryParams = computed(() => {
    const params = new URLSearchParams();
    if (selectedRating.value.length) {
        params.append("rating", selectedRating.value.join(","));
    }
    return params.toString();
});
watch(queryParams, (newParams, oldParams) => {
    if (newParams !== oldParams) {
        getProductById(props.productId);
    }
})
const {
    mainImage,
    selectedAttributes,
    uniqueAttributes,
    currentVariant,
    currentVariantImages,
    isAttributeAvailable,
    selectAttribute,
} = useProductVariants(product);

const toggleAttributeFilter = (selectedAttributes) => {
    const selectedAttributesData = {};
    for (const attributeId in selectedAttributes) {
        const attribute = uniqueAttributes.value.find(
            (attr) => attr.id.toString() === attributeId
        );
        const value = attribute.values.find(
            (val) => val.attribute_value_id === selectedAttributes[attributeId]
        );
        selectedAttributesData[attribute.name] = value.attribute_name;
    }

    const selectedAttributesValues = Object.values(selectedAttributesData);
    return selectedAttributesValues
};

const isAttributeSelected = computed(() => (attributeId, valueName) => {
    return (
        selectedAttributeValues.value[attributeId] &&
        selectedAttributeValues.value[attributeId].includes(valueName)
    );
});

const decrement = () => {
    if (cartStore.quantity > 1) {
        cartStore.quantity--;
    }
};

const increment = () => {
    if (cartStore.quantity > currentVariant.value?.stock_quantity) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Số lượng vượt quá tồn kho!",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
    } else {
        cartStore.quantity++;
    }
};

const cartStore = useCartStore();

const handleAddToCart = (itemToAdd) => {
    const availableQuantity = currentVariant.value?.stock_quantity ?? 0;
    const requestedQuantity = cartStore.quantity;

    if (requestedQuantity > availableQuantity) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Số lượng vượt quá tồn kho!",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
    } else if (requestedQuantity <= 0) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Số lượng không hợp lệ!",
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
        });
    } else {
        cartStore.addItem(itemToAdd);
        cartStore.quantity = 1
    }
};

const showModal = ref(false);
const selectedImage = ref('');

function openImage(img) {
    selectedImage.value = img;
    showModal.value = true;
}

function closeImage() {
    showModal.value = false;
}
onMounted(async () => {
    getProductById(props.productId);
    cartStore.quantity = 1
});
</script>
<template>
    <!-- Start Item Details -->
    <section class="item-details section pt-2">
        <div class="container">

            <div v-if="isLoading">
                <div class="row">
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="skeleton-box" style="height: 500px; width: 100%;"></div>
                        <div class="images d-flex mt-2 gap-2">
                            <div class="skeleton-box" v-for="n in 4" :key="n" style="height: 100px; width: 100px;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="product-info-skeleton">
                            <div class="skeleton-text" style="width: 80%; height: 2rem;"></div>
                            <div class="skeleton-text short my-2" style="width: 40%;"></div>
                            <div class="skeleton-text short my-2" style="width: 50%;"></div>
                            <div class="skeleton-text" style="width: 100%;"></div>
                            <div class="skeleton-text" style="width: 90%;"></div>
                            <div class="skeleton-text" style="width: 95%;"></div>
                            <div class="row mt-4">
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="skeleton-text short mb-2"></div>
                                    <div class="d-flex gap-2">
                                        <div class="skeleton-circle" v-for="n in 3" :key="n"></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="skeleton-text short mb-2"></div>
                                    <div class="d-flex gap-2">
                                        <div class="skeleton-button" v-for="n in 3" :key="n"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="skeleton-button-large mt-4"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="top-area" v-else>
                <div class="row" v-if="product">
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="product-images">
                            <main id="gallery">
                                <div class="main-img">
                                    <img :src="mainImage" id="current" alt="#"
                                        style="height: 500px; object-fit: cover" />
                                </div>
                                <div class="images">
                                    <img class="img" v-for="image in currentVariantImages" :key="image.id"
                                        :src="image.image_url" @click="mainImage = image.image_url" />
                                </div>
                            </main>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 col-12">
                        <div class="product-info">
                            <h2 class="title">{{ product.name }}</h2>
                            <p class="category">
                                <i class="lni lni-tag"></i> Danh mục:
                                <a href="javascript:void(0)">{{ product.category_name }}</a>
                            </p>
                            <p class="category">
                                <i class="lni lni-tag"></i> Tồn kho: {{ currentVariant.stock_quantity }}
                            </p>
                            <h3 class="price">
                                {{ FormatData.formatNumber(product.price) }}VND
                            </h3>

                            <div class="row" v-for="attribute in uniqueAttributes" :key="attribute.id">
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="form-group color-option">
                                        <label class="fs-6" for="size">{{ attribute.name }}</label>
                                        <div class="d-flex gap-2">
                                            <button v-for="value in attribute.values" :key="value.attribute_value_id"
                                                :class="[
                                                    'option-button',
                                                    {
                                                        active:
                                                            selectedAttributes[attribute.id] ===
                                                            value.attribute_value_id,
                                                        disabled: !isAttributeAvailable(
                                                            attribute.id,
                                                            value.attribute_value_id
                                                        ),
                                                        'color-selected':
                                                            attribute.name === 'Màu sắc' &&
                                                            selectedAttributes[attribute.id] ===
                                                            value.attribute_value_id,
                                                    },
                                                ]" @click="selectAttribute(attribute.id, value.attribute_value_id)"
                                                :disabled="!isAttributeAvailable(attribute.id, value.attribute_value_id)
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
                            </div>

                            <div class="bottom-content">
                                <div class="row align-items-end">
                                    <div class="col-12 my-3">
                                        <label for="quantity" class="form-label fw-bold">Số lượng</label>
                                        <div class="input-group quantity-control border overflow-hidden rounded-1">
                                            <button class="btn btn-outline-primary rounded-1 fw-bold"
                                                @click="decrement">
                                                -
                                            </button>
                                            <input type="text"
                                                class="form-control text-center border-0 bg-light text-dark"
                                                v-model="cartStore.quantity" />
                                            <button class="btn btn-outline-primary rounded-1 fw-bold"
                                                @click="increment">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="button cart-button">
                                            <button class="btn" style="width: 100%" @click="
                                                handleAddToCart({
                                                    id: product.id,
                                                    variantId: currentVariant.id,
                                                    quantity: cartStore.quantity, price: product.price,
                                                    productName: product.name,
                                                    selectedAttributes: toggleAttributeFilter(selectedAttributes),
                                                    image: mainImage,
                                                })
                                                ">
                                                Thêm vào giỏ hàng
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product-details-info" v-if="product">
                <div class="single-block">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-body custom-responsive-margin">
                                <h4>Chi tiết sản phẩm</h4>
                                <div v-html="product.description"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="single-block give-review">

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="5" id="rating-5"
                                    v-model="selectedRating" />
                                <label class="form-check-label" for="rating-5" style="font-size: 14px">
                                    5 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="4_5" id="rating-4_5"
                                    v-model="selectedRating" />
                                <label class="form-check-label" for="rating-4_5" style="font-size: 14px">
                                    4 - 5 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="3_4" id="rating-3_4"
                                    v-model="selectedRating" />
                                <label class="form-check-label" for="rating-3_4" style="font-size: 14px">
                                    3 - 4 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="duoi_3" id="rating-below-3"
                                    v-model="selectedRating" />
                                <label class="form-check-label" for="rating-below-3" style="font-size: 14px">
                                    Dưới 3 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-8 col-12">
                        <div class="single-block">
                            <div class="reviews" v-if="product.reviews.length > 0"
                                style="max-height: 800px; overflow-y: auto;">
                                <h4 class="title">Đánh giá</h4>
                                <!-- Start Single Review -->
                                <template v-for="value in product.reviews">
                                    <div class="single-review" v-if="value.status == 'published'">
                                        <img :src="value.customer_avatar" alt="#" class="border avatar" />
                                        <div class="review-info">
                                            <div class="d-flex justify-content-between">
                                                <h4>
                                                    {{ value.comment }}
                                                    <span>{{ value.customer_name }}</span>
                                                </h4>
                                                <Rating :rating="value.rating" />
                                            </div>

                                            <img v-for="img in value.image" :key="img.id" :src="img.image_url" alt=""
                                                class="w-25" @click="openImage(img.image_url)"
                                                style="cursor: pointer" />
                                            <hr>
                                            <div v-if="value.reply_text" class="admin-reply-container">
                                                <div class="admin-reply-header">
                                                    <span class="admin-tag">Shop <i
                                                            class="bi bi-cart-check-fill"></i></span>
                                                </div>
                                                <div class="admin-reply-text">
                                                    <p>{{ value.reply_text }}</p>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Modal xem ảnh -->
                                        <div v-if="showModal" class="modal-backdrop" @click="closeImage">
                                            <img :src="selectedImage" class="modal-image" />
                                        </div>

                                    </div>
                                </template>

                                <!-- End Single Review -->

                            </div>
                            <div v-else>
                                Chưa có đánh giá
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>
<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(179, 177, 177, 0.377);
    display: flex;
    justify-content: center;
    align-items: center;
}


.modal-image {
    max-width: 90%;
    max-height: 90%;
    border-radius: 8px;
}

.review {
    margin-top: 0px;
}

.admin-reply-container {
    padding: 15px;
    background-color: #f0f2f5;
    border-radius: 8px;
    border-left: 4px solid #007bff;

}

.admin-reply-header {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-weight: bold;
}

/* Tag for "Admin" */
.admin-tag {
    background-color: #007bff;
    /* Blue background for the tag */
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    margin-right: 10px;
}

/* Admin's name */
.reply-user-name {
    color: #007bff;
    /* Blue color for the name */
}

/* Reply text content */
.admin-reply-text p {
    margin: 0;
    line-height: 1.5;
    color: #495057;
    /* Darker text for readability */
}

.color-selected {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transform: scale(1.2);
    transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
}

.skeleton-box,
.skeleton-text,
.skeleton-circle,
.skeleton-button {
    background: #f0f0f0;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
    border-radius: 4px;
}

.skeleton-text {
    height: 1.25rem;
    margin-bottom: 0.5rem;
}

.skeleton-text.short {
    width: 50%;
}

.skeleton-circle {
    width: 1.75rem;
    height: 1.75rem;
    border-radius: 50%;
}

.skeleton-button {
    width: 60px;
    height: 38px;
    border-radius: 4px;
}

.skeleton-button-large {
    width: 100%;
    height: 48px;
    border-radius: 4px;
    background: #f0f0f0;
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        background-position: 200% 0;
    }

    100% {
        background-position: -200% 0;
    }
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
</style>
