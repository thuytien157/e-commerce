<script setup>
import Rating from "@/component/client/Rating.vue";
import axios from "axios";
import { computed, onMounted, ref } from "vue";
import FormatData from "@/component/store/FormatData";
import { useCartStore } from "@/component/store/cart";
import ProductModal from "@/component/client/ProductModal.vue";
const products = ref([]);
const categories = ref([]);
const attributes = ref([]); // Giữ nguyên, sẽ chứa tất cả thuộc tính
const isLoading = ref(true);

const getAllProducts = async () => {
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/product");
    const response = await axios.get("http://127.0.0.1:8000/api/category");
    const response1 = await axios.get("http://127.0.0.1:8000/api/attribute");
    products.value = res.data.products;
    categories.value = response.data.categories;
    // Sửa ở đây: Gán toàn bộ mảng attributes vào biến ref
    attributes.value = response1.data.attributes;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const selectedCategories = ref([]);
const selectedSort = ref("");
const selectedAttributeValues = ref({});
const selectedRating = ref([]);
const selectedPrice = ref([]);

// Hàm chung để cập nhật giá trị thuộc tính
const toggleAttributeFilter = (attributeId, valueName) => {
  // Khởi tạo mảng nếu chưa có
  if (!selectedAttributeValues.value[attributeId]) {
    selectedAttributeValues.value[attributeId] = [];
  }
  const index = selectedAttributeValues.value[attributeId].indexOf(valueName);
  if (index > -1) {
    selectedAttributeValues.value[attributeId].splice(index, 1);
  } else {
    selectedAttributeValues.value[attributeId].push(valueName);
  }
  fetchProducts();
};

const isAttributeSelected = computed(() => (attributeId, valueName) => {
  return (
    selectedAttributeValues.value[attributeId] &&
    selectedAttributeValues.value[attributeId].includes(valueName)
  );
});

const queryParams = computed(() => {
  const params = new URLSearchParams();
  if (selectedSort.value) {
    params.append("sort", selectedSort.value);
  }
  if (selectedCategories.value.length) {
    params.append("categories", selectedCategories.value.join(","));
  }
  if (selectedRating.value.length) {
    params.append("rating", selectedRating.value.join(","));
  }
  if (selectedPrice.value.length) {
    params.append("price", selectedPrice.value.join(","));
  }

  // Lặp qua object thuộc tính đã chọn để thêm vào query
  for (const attributeId in selectedAttributeValues.value) {
    const selectedValues = selectedAttributeValues.value[attributeId];
    if (selectedValues.length) {
      params.append(`attribute_${attributeId}`, selectedValues.join(","));
    }
  }

  return params.toString();
});

const fetchProducts = async () => {
  try {
    const url = `http://127.0.0.1:8000/api/product?${queryParams.value}`;
    const res = await axios.get(url);
    products.value = res.data.products;
  } catch (error) {
    console.error("Lỗi khi tải sản phẩm:", error);
  }
};
const sortProducts = (value) => {
  selectedSort.value = value;
  fetchProducts();
};

const showModal = ref(false);
const productData = ref(null);
const openModal = async (productId) => {
  showModal.value = true;
  const res = await axios.get(
    `http://127.0.0.1:8000/api/products_detail/${productId}`
  );
  productData.value = res.data.product;
};
function closeModal() {
  showModal.value = false;
  productData.value = null;
}
const cartStore = useCartStore();

const handleAddToCart = (itemToAdd) => {
  cartStore.addItem(itemToAdd);
};

onMounted(async () => {
  isLoading.value = true;
  await getAllProducts();
  isLoading.value = false;
});
</script>

<template>
  <section class="product-grids section pt-3">
    <div class="container">
      <div class="row">
        <div class="col-lg-3 col-12">
          <div class="product-sidebar">
            <div class="single-widget condition">
              <h3>Danh mục</h3>
              <div v-if="isLoading">
                <div class="form-check my-2" v-for="n in 5" :key="n">
                  <div class="skeleton-checkbox"></div>
                  <div class="skeleton-text"></div>
                </div>
              </div>
              <div class="form-check" v-else v-for="value in categories" :key="value.id">
                <input class="form-check-input" type="checkbox" :value="value.id" :id="'category-' + value.id"
                  v-model="selectedCategories" @change="fetchProducts" />
                <label class="form-check-label" :for="'category-' + value.id">
                  {{ value.name }} ({{ value.all_products_count }})
                </label>

                <div class="form-check ps-4" v-if="value.children.length > 0">
                  <div class="mt-1" v-for="item in value.children" :key="item.id">
                    <input class="form-check-input" type="checkbox" :value="item.id" :id="'category-' + item.id"
                      v-model="selectedCategories" @change="fetchProducts" />
                    <label class="form-check-label" :for="'category-' + item.id">
                      {{ item.name }} ({{ item.products_count }})
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div v-for="attribute in attributes" :key="attribute.id">
              <div class="single-widget condition">
                <h3>{{ attribute.name }}</h3>
                <div v-if="isLoading">
                  <div v-if="attribute.name === 'Màu sắc'" class="d-flex gap-1 mt-2">
                    <div v-for="n in 5" :key="'skeleton-color-' + n" class="skeleton-color"></div>
                  </div>
                  <div v-else class="d-flex gap-4">
                    <div class="form-check" v-for="n in 3" :key="'skeleton-attribute-' + n">
                      <div class="skeleton-checkbox"></div>
                      <div class="skeleton-text short"></div>
                    </div>
                  </div>
                </div>
                <div v-else :class="{ 'd-flex gap-4': attribute.name !== 'Màu sắc', 'd-flex gap-1 mt-2': attribute.name === 'Màu sắc' }">
                  <div v-for="value in attribute.attribute_values" :key="value.id">
                    <template v-if="attribute.name === 'Màu sắc'">
                      <div class="d-inline-block rounded-circle" :class="{
                          'border-2 border-primary': isAttributeSelected(
                            attribute.id,
                            value.value_name
                          ),
                          'color-selected': isAttributeSelected(
                            attribute.id,
                            value.value_name
                          ),
                        }" @click="toggleAttributeFilter(attribute.id, value.value_name)" :style="{
                          width: '1.35rem',
                          height: '1.35rem',
                          'background-color': value.value_name,
                          cursor: 'pointer',
                          border: isAttributeSelected(attribute.id, value.value_name)
                            ? '2px solid #0d6efd'
                            : '1px solid #ccc',
                        }"></div>
                    </template>
                    <template v-else>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" :value="value.value_name" :id="'attribute-' + value.id"
                          @change="toggleAttributeFilter(attribute.id, value.value_name)"
                          :checked="isAttributeSelected(attribute.id, value.value_name)" />
                        <label class="form-check-label" :for="'attribute-' + value.id">
                          {{ value.value_name }}
                        </label>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </div>
            <div class="single-widget condition">
              <h3>Giá</h3>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="flexCheckDefault1" value="1" v-model="selectedPrice"
                  @change="fetchProducts" />
                <label class="form-check-label" for="flexCheckDefault1" style="font-size: 14px">
                  Dưới 1,000,000VNĐ
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1_2" id="flexCheckDefault2" v-model="selectedPrice"
                  @change="fetchProducts" />
                <label class="form-check-label" for="flexCheckDefault2" style="font-size: 14px">
                  1,000,000VNĐ - 2,000,000VNĐ
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="2_3" id="flexCheckDefault3" v-model="selectedPrice"
                  @change="fetchProducts" />
                <label class="form-check-label" for="flexCheckDefault3" style="font-size: 14px">
                  2,000,000VNĐ - 3,000,000VNĐ
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="3_4" id="flexCheckDefault3" v-model="selectedPrice"
                  @change="fetchProducts" />
                <label class="form-check-label" for="flexCheckDefault3" style="font-size: 14px">
                  3,000,000VNĐ - 4,000,000VNĐ
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="tren_4" id="flexCheckDefault4"
                  v-model="selectedPrice" @change="fetchProducts" />
                <label class="form-check-label" for="flexCheckDefault4" style="font-size: 14px">
                  Trên 4,000,000VNĐ
                </label>
              </div>
            </div>
            <div class="single-widget condition">
              <h3>Đánh giá</h3>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="5" id="rating-5" v-model="selectedRating"
                  @change="fetchProducts" />
                <label class="form-check-label" for="rating-5" style="font-size: 14px">
                  5 <i class="bi bi-star-fill text-warning"></i>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="4_5" id="rating-4_5" v-model="selectedRating"
                  @change="fetchProducts" />
                <label class="form-check-label" for="rating-4_5" style="font-size: 14px">
                  4 - 5 <i class="bi bi-star-fill text-warning"></i>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="3_4" id="rating-3_4" v-model="selectedRating"
                  @change="fetchProducts" />
                <label class="form-check-label" for="rating-3_4" style="font-size: 14px">
                  3 - 4 <i class="bi bi-star-fill text-warning"></i>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="duoi_3" id="rating-below-3" v-model="selectedRating"
                  @change="fetchProducts" />
                <label class="form-check-label" for="rating-below-3" style="font-size: 14px">
                  Dưới 3 <i class="bi bi-star-fill text-warning"></i>
                </label>
              </div>
            </div>
            </div>
          </div>
        <div class="col-lg-9 col-12">
          <div class="product-grids-head">
            <div class="product-grid-topbar">
              <div class="row align-items-center">
                <div class="col-lg-7 col-md-8 col-12">
                  <div class="product-sorting">
                    <label for="sorting">Lọc theo:</label>
                    <select @change="sortProducts($event.target.value)" class="form-control" id="sorting">
                      <option value="">Sắp xếp mặc định</option>
                      <option value="name_asc">Tên: A-Z</option>
                      <option value="name_desc">Tên: Z-A</option>
                      <option value="price_asc">Giá: Thấp đến cao</option>
                      <option value="price_desc">Giá: Cao đến thấp</option>
                    </select>
                  </div>
                </div>
                <div class="col-lg-5 col-md-4 col-12">
                  <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                      <button class="nav-link active" id="nav-grid-tab" data-bs-toggle="tab" data-bs-target="#nav-grid"
                        type="button" role="tab" aria-controls="nav-grid" aria-selected="true">
                        <i class="bi bi-list-task"></i>
                      </button>
                      <button class="nav-link" id="nav-list-tab" data-bs-toggle="tab" data-bs-target="#nav-list"
                        type="button" role="tab" aria-controls="nav-list" aria-selected="false">
                        <i class="bi bi-grid"></i>
                      </button>
                    </div>
                  </nav>
                </div>
              </div>
            </div>
            <div class="tab-content mt-1" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-grid" role="tabpanel" aria-labelledby="nav-grid-tab">
                <div class="row">
                  <div class="col-lg-4 col-md-6 col-12" v-if="isLoading" v-for="n in 6" :key="'skeleton-grid-' + n">
                    <div class="single-product h-100">
                      <div class="product-image skeleton-image"></div>
                      <div class="product-info">
                        <div class="skeleton-title"></div>
                        <div class="skeleton-rating"></div>
                        <div class="skeleton-price"></div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-4 col-md-6 col-12" v-else v-for="product in products" :key="product.id">
                    <div class="single-product h-100">
                      <div class="product-image">
                        <img :src="product.variants[0].image" alt="#" style="height: 250px; object-fit: cover" />
                        <div class="button">
                          <button @click="openModal(product.id)" class="btn">
                            <i class="lni lni-cart"></i> Thêm vào giỏ
                          </button>
                        </div>
                      </div>
                      <div class="product-info">
                        <h4 class="title">
                          <router-link :to="`/product-detail/${product.variants[0].slug}/${product.id}`">{{
                            product.name }}</router-link>
                        </h4>
                        <Rating :rating="product.rating" :reviewCount="product.rating" />
                        <div class="price">
                          <span>{{ FormatData.formatNumber(product.price) }}VNĐ</span>
                        </div>
                        <div class="d-flex gap-1 mt-2">
                          <span v-for="value in FormatData.uniqueColors(product.variants)" :key="value.attribute_value_id"
                            class="d-inline-block rounded-circle" style="
                                      width: 0.75rem;
                                      height: 0.75rem;
                                      border: 1px solid #ccc;
                                    " :style="{
                                      'background-color': value.attribute_name,
                                    }">
                          </span>
                        </div>
                      </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="pagination left">
                      <ul class="pagination-list">
                        <li><a href="javascript:void(0)">1</a></li>
                        <li class="active">
                          <a href="javascript:void(0)">2</a>
                        </li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">4</a></li>
                        <li>
                          <a href="javascript:void(0)"><i class="lni lni-chevron-right"></i></a>
                        </li>
                      </ul>
                    </div>
                    </div>
                </div>
              </div>
              <div class="tab-pane fade" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                <div class="row">
                  <div class="col-lg-12 col-md-12 col-12" v-if="isLoading" v-for="n in 4" :key="'skeleton-list-' + n">
                    <div class="single-product">
                      <div class="row align-items-center">
                        <div class="col-lg-4 col-md-4 col-12">
                          <div class="product-image skeleton-image-list"></div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-12">
                          <div class="product-info">
                            <div class="skeleton-title-list"></div>
                            <div class="skeleton-rating"></div>
                            <div class="skeleton-price"></div>
                            <div class="skeleton-description"></div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>


                  <div class="col-lg-12 col-md-12 col-12" v-else v-for="product in products" :key="product.id">
                    <div class="single-product">
                      <div class="row align-items-center">
                        <div class="col-lg-4 col-md-4 col-12">
                          <div class="product-image">
                            <img :src="product.variants[0].image" alt="#" />
                            <div class="button">
                              <button @click="openModal(product.id)" class="btn">
                                <i class="lni lni-cart"></i> Thêm vào giỏ
                              </button>
                            </div>
                          </div>
                        </div>
                        <div class="col-lg-8 col-md-8 col-12">
                          <div class="product-info">
                            <h4 class="title">
                              <router-link :to="`/product-detail/${product.variants[0].slug}/${product.id}`">{{
                                product.name }}</router-link>
                            </h4>
                            <Rating :rating="product.rating" :reviewCount="product.rating" />

                            <div class="price">
                              <span>{{ FormatData.formatNumber(product.price) }}VNĐ</span>
                            </div>
                            <div class="d-flex gap-1 mt-2">
                              <span v-for="value in FormatData.uniqueColors(
                                  product.variants
                                )" :key="value.attribute_value_id" class="d-inline-block rounded-circle" style="
                                      width: 1.75rem;
                                      height: 1.75rem;
                                      border: 1px solid #ccc;
                                    " :style="{
                                      'background-color': value.attribute_name,
                                    }">
                              </span>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <div class="pagination left">
                      <ul class="pagination-list">
                        <li><a href="javascript:void(0)">1</a></li>
                        <li class="active">
                          <a href="javascript:void(0)">2</a>
                        </li>
                        <li><a href="javascript:void(0)">3</a></li>
                        <li><a href="javascript:void(0)">4</a></li>
                        <li>
                          <a href="javascript:void(0)"><i class="lni lni-chevron-right"></i></a>
                        </li>
                      </ul>
                    </div>
                    </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <ProductModal v-if="productData" :product="productData" :is-visible="showModal" @close="closeModal"
      @add-to-cart="handleAddToCart" />
  </section>
  </template>

<style scoped>
.skeleton-image {
    background-color: #e2e2e2;
    height: 250px;
    width: 100%;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-image-list {
    background-color: #e2e2e2;
    height: 200px;
    width: 100%;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-title,
.skeleton-price,
.skeleton-rating,
.skeleton-description {
    background-color: #e2e2e2;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
    margin-bottom: 8px;
}

.skeleton-title {
    height: 20px;
    width: 80%;
}

.skeleton-price {
    height: 18px;
    width: 60%;
}

.skeleton-rating {
    height: 16px;
    width: 40%;
}

.skeleton-description {
    height: 14px;
    width: 100%;
}

@keyframes pulse {
    0% {
        background-color: #e2e2e2;
    }

    50% {
        background-color: #f0f0f0;
    }

    100% {
        background-color: #e2e2e2;
    }
}

.skeleton-checkbox {
    width: 1rem;
    height: 1rem;
    background-color: #f0f0f0;
    display: inline-block;
    border-radius: 4px;
}

.skeleton-text {
    width: 70%;
    height: 1rem;
    background-color: #f0f0f0;
    display: inline-block;
    margin-left: 8px;
    border-radius: 4px;
}

.skeleton-color {
    width: 1.35rem;
    height: 1.35rem;
    background-color: #f0f0f0;
    border-radius: 50%;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-text.short {
    width: 30%;
}

.color-selected {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
    transform: scale(1.1);
    transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
}

.form-check-label {
    font-size: 14px;
}

@media (max-width: 767px) {
    .home-product-list .custom-responsive-margin {
        margin-bottom: 40px;
    }
}

.home-product-list .list-title {
    position: relative;
    margin-bottom: 24px;
    padding-bottom: 12px;
    border-bottom: 1px solid #e5e5e5;
    color: #232323;
    font-size: 15px;
    font-weight: 500;
}

.home-product-list .list-title::before {
    display: block;
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 90px;
    height: 1px;
    background-color: #0167f3;
    content: "";
}

.home-product-list .single-list {
    margin-bottom: 20px;
}

.home-product-list .single-list:last-child {
    margin: 0;
}

.home-product-list .single-list .list-image {
    width: 60px;
    padding-right: 12px;
    float: left;
    position: relative;
    top: 10px;
}

.home-product-list .single-list .list-image a {
    display: block;
    border-radius: 5px;
    overflow: hidden;
}

.home-product-list .single-list .list-image a img {
    width: 100%;
}

.home-product-list .single-list .list-info {
    display: table-cell;
    vertical-align: top;
}

.home-product-list .single-list .list-info h3 a {
    font-size: 14px;
    font-weight: 500;
    color: #081828;
}

.home-product-list .single-list .list-info h3 a:hover {
    color: #0167f3;
}

.home-product-list .single-list .list-info span {
    display: block;
    margin-top: 2px;
    font-size: 13px;
}

.single-product {
    border: 1px solid #eee;
    border-radius: 4px;
    -webkit-transition: all 0.4s ease;
    transition: all 0.4s ease;
    -webkit-box-shadow: 0px 0px 20px #00000012;
    box-shadow: 0px 0px 20px #00000012;
    padding: 8px;
    background: #fff;
}

.single-product .product-image {
    overflow: hidden;
    position: relative;
}

.single-product .product-image .sale-tag {
    background: #f73232;
    border-radius: 2px;
    font-size: 12px;
    color: #fff;
    font-weight: bold;
    position: absolute;
    top: 0;
    padding: 5px 10px;
    left: 0;
}

.single-product .product-image .new-tag {
    background: #0167f3;
    border-radius: 2px;
    font-size: 12px;
    color: #fff;
    font-weight: bold;
    position: absolute;
    top: 0;
    padding: 5px 10px;
    left: 0;
}

.single-product .product-image .button {
    position: absolute;
    left: 50%;
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
    bottom: -60px;
    -webkit-transition: all 0.4s ease;
    transition: all 0.4s ease;
    opacity: 0;
    visibility: hidden;
}

.single-product .product-image .button .btn {
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 600;
    width: 140px;
}

.single-product .product-image .button .btn i {
    font-size: 18px;
    position: relative;
    top: 2px;
}

.single-product .product-image img {
    width: 100%;
    -webkit-transition: all 0.4s ease;
    transition: all 0.4s ease;
}

.single-product:hover .product-image .button {
    bottom: 30px;
    opacity: 1;
    visibility: visible;
}

.single-product:hover .product-image img {
    -webkit-transform: scale(1.1);
    transform: scale(1.1);
}

.single-product .product-info {
    padding: 20px;
    background-color: #fff;
}

.single-product .product-info .category {
    color: #888;
    font-size: 13px;
    display: block;
    margin-bottom: 2px;
}

.single-product .product-info .title a {
    font-size: 16px;
    font-weight: 700;
    color: #081828;
}

@media only screen and (min-width: 768px) and (max-width: 991px),
(max-width: 767px) {
    .single-product .product-info .title a {
        font-size: 15px;
    }
}

.single-product .product-info .title a:hover {
    color: #0167f3;
}

.single-product .product-info .review {
    margin-top: 5px;
}

.single-product .product-info .review li {
    display: inline-block;
}

.single-product .product-info .review li i {
    color: #fecb00;
    font-size: 13px;
}

.single-product .product-info .review li span {
    display: inline-block;
    margin-left: 4px;
    color: #888;
    font-size: 13px;
}

.single-product .product-info .price {
    margin-top: 15px;
}

.single-product .product-info .price span {
    font-size: 17px;
    font-weight: 700;
    color: #0167f3;
    display: inline-block;
}

.single-product .product-info .price .discount-price {
    margin: 0;
    color: #aaaaaa;
    text-decoration: line-through;
    font-weight: normal;
    margin-left: 10px;
    font-size: 14px;
    display: inline-block;
}

a {
    text-decoration: none;
}

.banner-hover {
    position: relative;
    overflow: hidden;
    cursor: pointer;
}

.banner-hover .hover-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.banner-hover:hover .hover-overlay {
    opacity: 1;
}
</style>
