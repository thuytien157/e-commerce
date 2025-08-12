<script setup>
import Rating from "@/component/Rating.vue";
import axios from "axios";
import { onMounted, ref } from "vue";
import FormatData from "@/component/store/FormatData";

const new_arrivals = ref([]);
const best_sellers = ref([]);
const top_rated = ref([]);
const categories = ref([]);
const isLoading = ref(true);

const selectedCategoryNewArrivals = ref(null);
const selectedCategoryBestSellers = ref(null);
const selectedCategoryTopRated = ref(null);

const getAllProducts = async () => {
  try {
    const res = await axios.get("http://127.0.0.1:8000/api/product");
    const response = await axios.get("http://127.0.0.1:8000/api/category");
    top_rated.value = res.data.top_rated;
    best_sellers.value = res.data.best_sellers;
    new_arrivals.value = res.data.new_arrivals;
    categories.value = response.data.categories;
  } catch (error) {
    console.log(error);
  } finally {
    isLoading.value = false;
  }
};

const sortProducts = async (value, section) => {
  isLoading.value = false;
  try {
    if (section === "new_arrivals") {
      selectedCategoryNewArrivals.value = value;
    } else if (section === "best_sellers") {
      selectedCategoryBestSellers.value = value;
    } else if (section === "top_rated") {
      selectedCategoryTopRated.value = value;
    }

    const apiUrl = `http://127.0.0.1:8000/api/product?categories=${value}`;
    const res = await axios.get(apiUrl);

    if (section === "new_arrivals") {
      new_arrivals.value = res.data.new_arrivals;
      // console.log('sss' + new_arrivals.value);
    } else if (section === "best_sellers") {
      best_sellers.value = res.data.best_sellers;
    } else if (section === "top_rated") {
      top_rated.value = res.data.top_rated;
    }
  } catch (error) {
    console.log(error);
  }
};

const getRatingsArray = (reviews) => {
  if (!Array.isArray(reviews)) return [];
  return reviews.map((r) => Number(r) || 0);
};

onMounted(async () => {
  await getAllProducts();
});
</script>
<template>
  <div class="container-xxl mb-3 p-3">
    <div
      id="carouselExampleAutoplaying"
      class="carousel slide"
      data-bs-ride="carousel"
    >
      <div class="carousel-inner rounded">
        <div class="carousel-item active">
          <picture>
            <img
              src="/images/banner/mlb_desktop_en_be560d199f054efa9d99ae5826e2cdde.webp"
              class="d-block w-100 img-fluid"
              alt="Banner 1"
            />
          </picture>
        </div>
        <div class="carousel-item">
          <picture>
            <img
              src="/images/banner/msloyalty_mlb_web.webp"
              class="d-block w-100 img-fluid"
              alt="Banner 2"
            />
          </picture>
        </div>
        <div class="carousel-item">
          <picture>
            <img
              src="/images/banner/1960x640-mua-ngay.webp"
              class="d-block w-100 img-fluid"
              alt="Banner 3"
            />
          </picture>
        </div>
      </div>
      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#carouselExampleAutoplaying"
        data-bs-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#carouselExampleAutoplaying"
        data-bs-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>

  <div class="container-xl">
    <div class="row align-items-center">
      <div class="d-flex justify-content-between w-100 flex-wrap">
        <h3 class="fw-bolder text-start ms-2">Hàng mới về</h3>
        <div class="d-flex mb-1">
          <div class="me-2">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts('all', 'new_arrivals')"
              :class="{
                'btn-dark text-white':
                  selectedCategoryNewArrivals === 'all' ||
                  selectedCategoryNewArrivals === null,
              }"
            >
              Tất cả
            </button>
          </div>
          <div v-if="isLoading" v-for="n in 3" :key="n" class="me-2">
            <div class="skeleton-button"></div>
          </div>
          <div v-else class="me-2" v-for="value in categories" :key="value.id">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts(value.id, 'new_arrivals')"
              :class="{
                'btn-dark text-white': selectedCategoryNewArrivals === value.id,
              }"
            >
              {{ value.name }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6 d-flex">
        <div v-if="isLoading" class="skeleton-banner rounded w-100"></div>
        <div
          v-else
          class="position-relative overflow-hidden w-100 h-100 banner-hover"
        >
          <img
            src="/images/banner/hangmoive_main.jpg"
            class="w-100 h-100 object-fit-cover rounded"
            alt="Hàng mới về"
          />
          <div
            class="hover-overlay d-flex align-items-center justify-content-center"
          >
            <router-link to="/sanpham" class="btn btn-outline-light btn-sm"
              >Xem tất cả</router-link
            >
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6">
        <div class="row g-2 h-100">
          <div
            v-if="isLoading"
            v-for="n in 4"
            :key="n"
            class="col-6"
            style="height: 420px"
          >
            <div class="skeleton-product h-100">
              <div class="skeleton-image"></div>
              <div class="skeleton-info">
                <div class="skeleton-line-long"></div>
                <div class="skeleton-line-short"></div>
                <div class="d-flex gap-1 mt-2">
                  <span
                    class="skeleton-circle-color"
                    v-for="i in 3"
                    :key="i"
                  ></span>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else
            v-for="product in new_arrivals"
            :key="product.id"
            class="col-6"
            style="height: 420px"
          >
            <router-link
              :to="`/product-detail/${product.slug}/${product.id}`"
              class="single-product h-100"
            >
              <div class="product-image">
                <img
                  :src="product.image"
                  alt="#"
                  style="height: 250px; object-fit: cover"
                />
                <div class="button">
                  <a href="product-details.html" class="btn">
                    <i class="lni lni-cart"></i> Thêm vào giỏ
                  </a>
                </div>
              </div>
              <div class="product-info">
                <h4 class="title">
                  <a href="product-grids.html">{{ product.name }}</a>
                </h4>
                <Rating :ratings="getRatingsArray(product.rating)" />
                <div class="price">
                  <span>{{ FormatData.formatNumber(product.price) }}VNĐ</span>
                </div>
                <div class="d-flex gap-1 mt-2">
                  <span
                    v-for="(value, index) in product.colorName"
                    :key="index"
                    class="d-inline-block rounded-circle"
                    style="
                      width: 0.75rem;
                      height: 0.75rem;
                      border: 1px solid #ccc;
                    "
                    :style="{ 'background-color': value }"
                  >
                  </span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <div class="row align-items-center mt-5">
      <div class="d-flex justify-content-between w-100 flex-wrap">
        <h3 class="fw-bolder text-start ms-2">Hàng bán chạy</h3>
        <div class="d-flex mb-1">
          <div class="me-2">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts('all', 'best_sellers')"
              :class="{
                'btn-dark text-white':
                  selectedCategoryBestSellers === 'all' ||
                  selectedCategoryBestSellers === null,
              }"
            >
              Tất cả
            </button>
          </div>
          <div v-if="isLoading" v-for="n in 3" :key="n" class="me-2">
            <div class="skeleton-button"></div>
          </div>
          <div v-else class="me-2" v-for="value in categories" :key="value.id">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts(value.id, 'best_sellers')"
              :class="{
                'btn-dark text-white': selectedCategoryBestSellers === value.id,
              }"
            >
              {{ value.name }}
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-5">
      <div
        v-if="isLoading"
        v-for="n in 8"
        :key="n"
        class="col-lg-3 col-md-6 col-12 mb-3"
      >
        <div class="skeleton-product">
          <div class="skeleton-image"></div>
          <div class="skeleton-info">
            <div class="skeleton-line-long"></div>
            <div class="skeleton-line-short"></div>
            <div class="d-flex gap-1 mt-2">
              <span
                class="skeleton-circle-color"
                v-for="i in 3"
                :key="i"
              ></span>
            </div>
          </div>
        </div>
      </div>

      <div
        v-else
        class="col-lg-3 col-md-6 col-12 mb-3"
        v-for="product in best_sellers"
        :key="product.id"
      >
        <div class="single-product">
          <div class="product-image">
            <img
              :src="product.image"
              style="height: 250px; object-fit: cover"
              alt="#"
            />
            <div class="button">
              <a href="product-details.html" class="btn"
                ><i class="lni lni-cart"></i> Add to Cart</a
              >
            </div>
          </div>
          <div class="product-info">
            <h4 class="title">
              <a href="product-grids.html">{{ product.name }}</a>
            </h4>
            <Rating :ratings="getRatingsArray(product.rating)" />
            <div class="price">
              <span>{{ FormatData.formatNumber(product.price) }}VNĐ</span>
            </div>
            <div class="d-flex gap-1 mt-2">
              <span
                v-for="(value, index) in product.colorName"
                :key="index"
                class="d-inline-block rounded-circle"
                style="width: 0.75rem; height: 0.75rem; border: 1px solid #ccc"
                :style="{ 'background-color': value }"
              >
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row align-items-center">
      <div class="d-flex justify-content-between w-100 flex-wrap">
        <h3 class="fw-bolder text-start ms-2">Sản phẩm đánh giá cao nhất</h3>
        <div class="d-flex mb-1">
          <div class="me-2">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts('all', 'top_rated')"
              :class="{
                'btn-dark text-white':
                  selectedCategoryTopRated === 'all' ||
                  selectedCategoryTopRated === null,
              }"
            >
              Tất cả
            </button>
          </div>
          <div v-if="isLoading" v-for="n in 3" :key="n" class="me-2">
            <div class="skeleton-button"></div>
          </div>
          <div v-else class="me-2" v-for="value in categories" :key="value.id">
            <button
              type="button"
              class="btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold"
              @click="sortProducts(value.id, 'top_rated')"
              :class="{
                'btn-dark text-white': selectedCategoryTopRated === value.id,
              }"
            >
              {{ value.name }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-12 col-md-6">
        <div class="row g-2 h-100">
          <div
            v-if="isLoading"
            v-for="n in 4"
            :key="n"
            class="col-6"
            style="height: 420px"
          >
            <div class="skeleton-product h-100">
              <div class="skeleton-image"></div>
              <div class="skeleton-info">
                <div class="skeleton-line-long"></div>
                <div class="skeleton-line-short"></div>
                <div class="d-flex gap-1 mt-2">
                  <span
                    class="skeleton-circle-color"
                    v-for="i in 3"
                    :key="i"
                  ></span>
                </div>
              </div>
            </div>
          </div>

          <div
            v-else
            v-for="product in top_rated"
            :key="product.id"
            class="col-6"
            style="height: 420px"
          >
            <router-link to="/product-detail" class="single-product h-100">
              <div class="product-image">
                <img
                  :src="product.image"
                  alt="#"
                  style="height: 250px; object-fit: cover"
                />
                <div class="button">
                  <a href="product-details.html" class="btn">
                    <i class="lni lni-cart"></i> Thêm vào giỏ
                  </a>
                </div>
              </div>
              <div class="product-info">
                <h4 class="title">
                  <a href="product-grids.html">{{ product.name }}</a>
                </h4>
                <Rating :ratings="getRatingsArray(product.rating)" />
                <div class="price">
                  <span>{{ FormatData.formatNumber(product.price) }}VNĐ</span>
                </div>
                <div class="d-flex gap-1 mt-2">
                  <span
                    v-for="(value, index) in product.colorName"
                    :key="index"
                    class="d-inline-block rounded-circle"
                    style="
                      width: 0.75rem;
                      height: 0.75rem;
                      border: 1px solid #ccc;
                    "
                    :style="{ 'background-color': value }"
                  >
                  </span>
                </div>
              </div>
            </router-link>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 d-flex">
        <div v-if="isLoading" class="skeleton-banner rounded w-100"></div>
        <div
          v-else
          class="position-relative overflow-hidden w-100 h-100 banner-hover"
        >
          <img
            src="/images/banner/hangmoive_main.jpg"
            class="w-100 h-100 object-fit-cover rounded"
            alt="Hàng mới về"
          />
          <div
            class="hover-overlay d-flex align-items-center justify-content-center"
          >
            <router-link to="/sanpham" class="btn btn-outline-light btn-sm"
              >Xem tất cả</router-link
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
@keyframes pulse {
  0% {
    background-color: rgba(220, 220, 220, 0.4);
  }

  50% {
    background-color: rgba(200, 200, 200, 0.8);
  }

  100% {
    background-color: rgba(220, 220, 220, 0.4);
  }
}

.skeleton-product {
  border: 1px solid #eee;
  border-radius: 4px;
  padding: 8px;
  background: #fff;
}

.skeleton-image {
  width: 100%;
  height: 250px;
  background-color: #ddd;
  border-radius: 4px;
  animation: pulse 1.5s infinite;
}

.skeleton-info {
  padding: 10px;
}

.skeleton-line-long,
.skeleton-line-short {
  height: 1rem;
  background-color: #ddd;
  border-radius: 4px;
  animation: pulse 1.5s infinite;
  margin-bottom: 0.5rem;
}

.skeleton-line-long {
  width: 90%;
}

.skeleton-line-short {
  width: 60%;
}

.skeleton-circle-color {
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 50%;
  background-color: #ddd;
  animation: pulse 1.5s infinite;
  display: inline-block;
  margin-right: 0.25rem;
}

.skeleton-button {
  width: 80px;
  height: 30px;
  background-color: #ddd;
  border-radius: 4px;
  animation: pulse 1.5s infinite;
  margin-top: 0.5rem;
}

.skeleton-banner {
  width: 100%;
  height: 100%;
  background-color: #ddd;
  animation: pulse 1.5s infinite;
}

/* Existing styles */
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
  padding: 10px;
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
