<script setup>
import axios from "axios";
import { computed, onMounted, ref, watch } from "vue";
import FormatData from "@/component/store/FormatData";
import useProductVariants from "@/component/store/useProductVariants";
import { useCartStore } from "@/component/store/cart";
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
            `http://127.0.0.1:8000/api/products/${productId}`
        );
        product.value = res.data.product;
        // console.log("sss" + product.value);
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false
    }
};
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

const cartStore = useCartStore();

const handleAddToCart = (itemToAdd) => {
    cartStore.addItem(itemToAdd);
};

onMounted(async () => {
    getProductById(props.productId);
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
                            <h3 class="price">
                                {{ FormatData.formatNumber(product.price) }}VND
                            </h3>
                            <!-- <p class="info-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod
                                tempor incididunt
                                ut labore et dolore magna aliqua.</p> -->
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="form-group color-option">
                                        <label class="fs-6" for="size">Chọn màu sắc</label>
                                        <div class="d-flex gap-2">
                                            <button @click="selectOption('color', value.attribute_value_id)"
                                                v-for="value in uniqueColors" :key="value.attribute_value_id" :class="[
                                                    'color-option',
                                                    {
                                                        'color-selected':
                                                            selectedColor === value.attribute_value_id,
                                                    },
                                                ]" class="d-inline-block rounded-circle" :style="{
                                                    width: '1.75rem',
                                                    height: '1.75rem',
                                                    'background-color': value.attribute_name,
                                                    cursor: 'pointer',
                                                    border: '1px solid #ccc',
                                                }"></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-4 col-12">
                                    <div class="form-group color-option">
                                        <label class="fs-6" for="size">Chọn kích thước</label>
                                        <div class="d-flex gap-1">
                                            <button v-for="value in uniqueSizes" :key="value.attribute_value_id"
                                                class="d-inline-block btn btn-outline-primary" :class="{
                                                    active: selectedSize === value.attribute_value_id,
                                                }" @click="selectOption('size', value.attribute_value_id)">
                                                {{ value.attribute_name }}
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
                                                +
                                            </button>
                                            <input type="text"
                                                class="form-control text-center border-0 bg-light text-dark"
                                                v-model="cartStore.quantity" readonly />
                                            <button class="btn btn-outline-primary rounded-1 fw-bold"
                                                @click="increment">
                                                -
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
                                                    selectedColor: uniqueColors.find(
                                                        (c) => c.attribute_value_id === selectedColor
                                                    )?.attribute_name,
                                                    selectedSize: uniqueSizes.find(
                                                        (s) => s.attribute_value_id === selectedSize
                                                    )?.attribute_name,
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
                                {{ product.description }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-12">
                        <div class="single-block give-review">
                            <h4>{{ product.rating }} (Trung bình)</h4>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="5" id="rating-5" />
                                <label class="form-check-label" for="rating-5" style="font-size: 14px">
                                    5 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="4_5" id="rating-4_5" />
                                <label class="form-check-label" for="rating-4_5" style="font-size: 14px">
                                    4 - 5 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="3_4" id="rating-3_4" />
                                <label class="form-check-label" for="rating-3_4" style="font-size: 14px">
                                    3 - 4 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="duoi_3" id="rating-below-3" />
                                <label class="form-check-label" for="rating-below-3" style="font-size: 14px">
                                    Dưới 3 <i class="bi bi-star-fill text-warning"></i>
                                </label>
                            </div>
                            <!-- Button trigger modal -->
                            <button type="button" class="btn review-btn" data-bs-toggle="modal"
                                data-bs-target="#exampleModal">
                                Leave a Review
                            </button>
                        </div>
                    </div>
                    <div class="col-lg-8 col-12">
                        <div class="single-block">
                            <div class="reviews">
                                <h4 class="title">Latest Reviews</h4>
                                <!-- Start Single Review -->
                                <div class="single-review">
                                    <img src="/images/blog/comment1.jpg" alt="#" />
                                    <div class="review-info">
                                        <h4>
                                            Awesome quality for the price
                                            <span>Jacob Hammond </span>
                                        </h4>
                                        <ul class="stars">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                        </ul>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                            sed do eiusmod tempor...
                                        </p>
                                    </div>
                                </div>
                                <!-- End Single Review -->
                                <!-- Start Single Review -->
                                <div class="single-review">
                                    <img src="/images/blog/comment2.jpg" alt="#" />
                                    <div class="review-info">
                                        <h4>
                                            My husband love his new...
                                            <span>Alex Jaza </span>
                                        </h4>
                                        <ul class="stars">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star"></i></li>
                                        </ul>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                            sed do eiusmod tempor...
                                        </p>
                                    </div>
                                </div>
                                <!-- End Single Review -->
                                <!-- Start Single Review -->
                                <div class="single-review">
                                    <img src="/images/blog/comment3.jpg" alt="#" />
                                    <div class="review-info">
                                        <h4>
                                            I love the built quality...
                                            <span>Jacob Hammond </span>
                                        </h4>
                                        <ul class="stars">
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                            <li><i class="lni lni-star-filled"></i></li>
                                        </ul>
                                        <p>
                                            Lorem ipsum dolor sit amet, consectetur adipiscing elit,
                                            sed do eiusmod tempor...
                                        </p>
                                    </div>
                                </div>
                                <!-- End Single Review -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Item Details -->

    <!-- Review Modal -->
    <div class="modal fade review-modal" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Lịch sử</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="review-name">Your Name</label>
                                <input class="form-control" type="text" id="review-name" required />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="review-email">Your Email</label>
                                <input class="form-control" type="email" id="review-email" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="review-subject">Subject</label>
                                <input class="form-control" type="text" id="review-subject" required />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="review-rating">Rating</label>
                                <select class="form-control" id="review-rating">
                                    <option>5 Stars</option>
                                    <option>4 Stars</option>
                                    <option>3 Stars</option>
                                    <option>2 Stars</option>
                                    <option>1 Star</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="review-message">Review</label>
                        <textarea class="form-control" id="review-message" rows="8" required></textarea>
                    </div>
                </div>
                <div class="modal-footer button">
                    <button type="button" class="btn">Submit Review</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Review Modal -->
</template>
<style scoped>
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
</style>
