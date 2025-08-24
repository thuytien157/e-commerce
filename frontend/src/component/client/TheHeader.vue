<template>
  <div class="header position-sticky top-0 bg-white bg-opacity-90 shadow-sm z-3">
    <div class="container">
      <div class="d-flex align-items-center pt-2 pb-2 border-bottom">
        <a class="navbar-brand me-auto" href="/home">
          <img src="/images/logo/logo.svg" alt="Logo" class="logo" width="100px" />
        </a>

        <div class="d-none d-lg-flex align-items-center">
          <form>
            <div class="input-wrapper position-relative me-3">
              <button class="icon-search-submit" type="submit">
                <svg width="22px" height="22px" viewBox="0 0 24 24" fill="none">
                  <path
                    d="M11.5 21C16.7 21 21 16.7 21 11.5C21 6.2 16.7 2 11.5 2C6.2 2 2 6.2 2 11.5C2 16.7 6.2 21 11.5 21Z"
                    stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>
              </button>
              <input type="text" class="input-search" placeholder="search..." />
            </div>
          </form>

          <div class="me-2">
            <router-link to="/login" class="text-decoration-none text-primary-black" v-if="!store.username">
              <button class="icon-btn me-2">
                <i class="bi bi-people"></i>
              </button>
            </router-link>
            <template v-else>
              <div class="d-flex align-items-center">
                <router-link to="/account/address-list" class="text-decoration-none me-2" style="color: #3866bc">
                  <p v-if="store.username" class="mb-0 username-display">
                    {{ store.username }}
                  </p>
                </router-link>
                <button class="icon-btn" @click="logout" title="Đăng xuất">
                  <i class="bi bi-box-arrow-right"></i>
                </button>
              </div>
            </template>
          </div>

          <div>
            <div class="cart-dropdown position-relative">
              <div class="icon-btn text-dark" title="Giỏ hàng">
                <i class="bi bi-cart position-relative">
                  <span class="badge rounded-circle position-absolute top-0 start-100 translate-middle"
                    style="background-color: #3866bc">{{ cartStore.cartLength }}</span>
                </i>
              </div>
              <div class="dropdown-menu-cart p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                  <router-link to="/cart" class="text-primary text-decoration-none">Xem giỏ hàng</router-link>
                </div>
                <ul class="list-unstyled shopping-list m-0 p-0" v-if="cartStore.cartLength">
                  <li class="d-flex align-items-center mb-3" v-for="item in cartStore.items" :key="item.variantId">
                    <template v-if="item.isCheck">
                      <a href="product-details.html" class="flex-shrink-0 me-3">
                        <img :src="item.image" alt="Apple Watch Series 6" class="cart-item-img rounded-3" />
                      </a>
                      <div class="content flex-grow-1">
                        <h6 class="mb-1">
                          <a href="product-details.html" class="text-dark text-decoration-none product-name-short">
                            {{ item.productName }}
                          </a>
                        </h6>
                        <p class="mb-0 text-muted small">
                          {{ item.quantity }}x
                          <template v-if="item.selectedAttributes && item.selectedAttributes.length > 0">
                            - {{ item.selectedAttributes[0] }}
                          </template>
                          - <span class="fw-bold">{{ formatNumber(item.price) }}VNĐ</span>
                        </p>
                      </div>

                      <i class="bi bi-x-lg" @click.prevent="cartStore.removeItem(item.variantId)"></i>
                    </template>
                  </li>
                </ul>
                <ul class="list-unstyled shopping-list m-0 p-0" v-else>
                  <small>Chưa có sản phẩm được chọn</small>
                </ul>

                <div class="bottom mt-3 border-top pt-3">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Tổng cộng</span>
                    <span class="fw-bold fs-5 text-primary">{{ formatNumber(cartStore.totalPrice) }} VND</span>
                  </div>
                  <div class="d-grid gap-2">
                    <router-link to="/checkout" class="btn btn-primary btn-sm">Thanh toán</router-link>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex d-lg-none align-items-center">
          <button class="navbar-toggler me-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasMenu">
            <span class="navbar-toggler-icon"></span>
          </button>
          
          <div class="me-2">
            <router-link to="/login" class="text-decoration-none text-primary-black" v-if="!store.username">
              <button class="icon-btn me-2">
                <i class="bi bi-people"></i>
              </button>
            </router-link>
            <template v-else>
              <div class="d-flex align-items-center">
                <router-link to="/account/address-list" class="text-decoration-none me-2" style="color: #3866bc">
                  <p v-if="store.username" class="mb-0 username-display">
                    {{ store.username }}
                  </p>
                </router-link>
                <button class="icon-btn" @click="logout" title="Đăng xuất">
                  <i class="bi bi-box-arrow-right"></i>
                </button>
              </div>
            </template>
          </div>
          <div class="cart-dropdown position-relative">
            <a href="/cart" class="icon-btn text-dark" title="Giỏ hàng">
              <i class="bi bi-cart position-relative">
                <span class="badge bg-danger rounded-circle position-absolute top-0 start-100 translate-middle">2</span>
              </i>
            </a>
          </div>
        </div>
      </div>

      <nav class="navbar navbar-expand-lg navbar-light py-0 d-none d-lg-block">
        <ul class="navbar-nav main-nav-links w-100">
          <li class="nav-item">
            <router-link to="/home" class="nav-link">Trang chủ</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/products" class="nav-link">Sản phẩm</router-link>
          </li>
        </ul>
      </nav>
    </div>

    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasMenuLabel">Menu</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <ul class="navbar-nav offcanvas-nav-links mb-4">
          <li class="nav-item">
            <router-link to="/home" class="nav-link">Trang chủ</router-link>
          </li>
          <li class="nav-item">
            <router-link to="/products" class="nav-link">Sản phẩm</router-link>
          </li>
        </ul>

        <div class="mobile-actions">
          <div class="input-wrapper position-relative mb-3">
            <button class="icon-search-submit" type="button">
              <svg width="23px" height="23px" viewBox="0 0 24 24" fill="none">
                <path
                  d="M11.5 21C16.7 21 21 16.7 21 11.5C21 6.2 16.7 2 11.5 2C6.2 2 2 6.2 2 11.5C2 16.7 6.2 21 11.5 21Z"
                  stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                <path d="M22 22L20 20" stroke="#000" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </button>
            <input type="text" class="input-search" placeholder="search..." />
          </div>
          
          <div class="d-flex flex-column align-items-start">
            <a href="/login" class="text-decoration-none text-primary-black">
              <button class="icon-btn text-dark mb-2">
                <i class="bi bi-people me-2"></i> Đăng nhập
              </button>
            </a>

            <a href="/cart" class="icon-btn text-dark mb-2">
              <i class="bi bi-cart me-2"></i> Giỏ hàng
            </a>

            <a href="tel:YOUR_PHONE_NUMBER" class="icon-btn text-dark">
              <i class="bi bi-telephone me-2"></i> Liên hệ
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import axios from "axios";
import { useTokenUser } from "../store/useTokenUser";
import Swal from "sweetalert2";
import { useCartStore } from "../store/cart";
import { useRouter } from "vue-router";

const store = useTokenUser();
const cartStore = useCartStore();
const router = useRouter()
const logout = async () => {
  try {
    await axios.get(`${import.meta.env.VITE_URL_API}api/logout`, {
      headers: {
        Authorization: `Bearer ${store.token}`,
      },
    });

    store.clearAuth();
    router.push('/login')
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "success",
      title: "Đăng xuất thành công!",
      showConfirmButton: false,
      timer: 1000,
      timerProgressBar: true,
    });
  } catch (error) {
    console.error(error);
    Swal.fire({
      toast: true,
      position: "top-end",
      icon: "error",
      title: "Đăng xuất thất bại",
      showConfirmButton: false,
      timer: 1000,
      timerProgressBar: true,
    });
  }
};

const formatNumber = (num) => {
  return new Intl.NumberFormat("vi-VN").format(num);
};
</script>

<style>
/* Header Styles */
.product-name-short {
  display: block;
  width: 150px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.text-primary-red {
  color: #3866bc !important;
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.1);
}

.suggestion-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  max-height: 300px;
  max-height: 270px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #ddd;
  z-index: 999;
  list-style: none;
  margin: 0;
  padding: 5px 0;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.suggestion-dropdown li {
  display: flex;
  align-items: flex-start;
  padding: 8px 12px;
  gap: 10px;
  cursor: pointer;
}

.suggestion-dropdown li:hover {
  background-color: #f6f6f6;
}

.img-search {
  width: 50px;
  object-fit: cover;
  border-radius: 5px;
}

.info-search {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1;
}

.name-search {
  font-size: 16px;
  font-weight: 500;
  color: #333;
}

.price-search {
  font-size: 14px;
  color: red;
}

.text-primary-black {
  color: black;
}

.loading,
.no-more {
  padding: 10px;
  text-align: center;
  color: #888;
}

.loader {
  width: fit-content;
  height: fit-content;
  display: flex;
  align-items: center;
  justify-content: center;
}

.truckWrapper {
  margin-right: 16px;
  width: 38px;
  height: 22px;
  display: flex;
  flex-direction: column;
  position: relative;
  align-items: center;
  justify-content: flex-end;
  overflow-x: hidden;
}

/* Truck body */
.truckBody {
  width: 26px;
  height: fit-content;
  margin-bottom: 1px;
  animation: motion 1s linear infinite;
}

/* Suspension animation */
@keyframes motion {
  0% {
    transform: translateY(0px);
  }

  50% {
    transform: translateY(0.5px);
  }

  100% {
    transform: translateY(0px);
  }
}

/* Tires */
.truckTires {
  width: 26px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0px 2px 0px 3px;
  position: absolute;
  bottom: 0;
}

.truckTires svg {
  width: 5px;
  height: 5px;
}

/* Road */
.road {
  width: 100%;
  height: 1px;
  background-color: #282828;
  position: relative;
  bottom: 0;
  align-self: flex-end;
  border-radius: 1px;
}

.road::before {
  content: "";
  position: absolute;
  width: 4px;
  height: 100%;
  background-color: #282828;
  right: -50%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 1px solid white;
}

.road::after {
  content: "";
  position: absolute;
  width: 2px;
  height: 100%;
  background-color: #282828;
  right: -65%;
  border-radius: 1px;
  animation: roadAnimation 1.4s linear infinite;
  border-left: 0.5px solid white;
}

/* Lamp post */
.lampPost {
  position: absolute;
  bottom: 0;
  right: -90%;
  height: 18px;
  animation: roadAnimation 1.4s linear infinite;
}

@keyframes roadAnimation {
  0% {
    transform: translateX(0px);
  }

  100% {
    transform: translateX(-90px);
  }
}

.nav-link.active {
  color: #dc3545 !important;
  font-weight: 500;
}

.booking-form-container {
  background-color: #ffffff;
  border-radius: 0 0 0.5rem 0.5rem;
  border-top: none;
}

.booking-form-container .form-label {
  font-size: 0.85rem;
  margin-bottom: 0.25rem;
  color: #495057;
}

.slide-fade-enter-active {
  transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
  transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-15px);
  opacity: 0;
}

.lable {
  line-height: 22px;
  font-size: 17px;
  color: #fff;
  font-family: sans-serif;
  letter-spacing: 1px;
}

.button:hover .svg-icon {
  animation: slope 1s linear infinite;
}

@keyframes slope {
  0% {}

  50% {
    transform: rotate(10deg);
  }

  100% {}
}

.header .navbar {
  padding-top: 0.5rem;
  padding-bottom: 0.5rem;
}

.header .navbar-brand img.logo {
  display: block;
}

.header .navbar-toggler {
  padding: 0.25rem 0.5rem;
  font-size: 1rem;
  border: 1px solid transparent;
}

.header .navbar-toggler:focus {
  box-shadow: none;
}

.header .navbar-bottom {
  border-top: 1px solid #e0e0e0;
}

.header .main-nav-links .nav-link {
  font-size: 0.85rem;
  font-weight: bold;
  text-transform: uppercase;
  transition: color 0.3s ease, transform 0.3s ease;
  padding: 0.5rem 1rem;
}

.header .main-nav-links .nav-link:hover {
  color: #3866bc;
  transform: scale(1.05);
}

.icon-btn {
  border: none;
  background: none;
  font-size: 1.3rem;
  color: #333;
  transition: color 0.3s ease, transform 0.3s ease;
  padding: 0.25rem 0.5rem;
  cursor: pointer;
}

.icon-btn:hover {
  color: #3866bc;
  transform: scale(1.1);
}

.username-display {
  font-weight: 500;
}

.text-red {
  color: #3866bc !important;
}

.text-red:hover {
  color: #ffffff !important;
}

/* Search Input Styles */
.input-wrapper {
  display: flex;
  align-items: center;
}

.input-search {
  border-style: none;
  height: 40px;
  width: 40px;
  padding: 0.5rem;
  outline: none;
  border-radius: 50%;
  transition: width 0.5s ease-in-out, border-radius 0.5s ease-in-out,
    background-color 0.5s ease-in-out, border-bottom 0.5s ease-in-out;
  background-color: transparent;
  padding-right: 35px;
  font-size: 0.9rem;
}

.input-search::placeholder {
  color: #8f8f8f;
  font-size: 0.9rem;
}

.icon-search-submit {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  right: 0px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  width: 40px;
  height: 40px;
  outline: none;
  border-style: none;
  border-radius: 50%;
  background-color: transparent;
  transition: 0.2s linear;
  padding: 0;
}

/* Animation khi focus search */
.input-wrapper .icon-search-submit:focus-within~.input-search,
.input-wrapper .input-search:focus {
  box-shadow: none;
  width: 130px;
  border-radius: 0px;
  background-color: transparent;
  border-bottom: 3px solid #3866bc;
  transition: all 500ms cubic-bezier(0, 0.11, 0.35, 2);
}

/* Suggestion Dropdown */
.suggestion-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 350px;
  background-color: white;
  border: 1px solid #ddd;
  border-top: none;
  border-radius: 0 0 4px 4px;
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.suggestion-dropdown li {
  padding: 10px 15px;
  cursor: pointer;
  font-size: 0.9rem;
}

.suggestion-dropdown li:hover {
  background-color: #f5f5f5;
}

.suggestion-dropdown .loading,
.suggestion-dropdown .no-more {
  padding: 10px 15px;
  font-style: italic;
  color: #777;
}

/* Offcanvas Styles */
.navbar-nav .nav-link {
  transition: transform 0.3s ease, color 0.3s ease;
  display: inline-block;
}

.navbar-nav .nav-link:hover {
  transform: scale(1.1);
  color: #c92c3c;
}

.offcanvas {
  width: 280px;
}

.offcanvas-header {
  border-bottom: 1px solid #e0e0e0;
}

.offcanvas-title {
  font-weight: bold;
}

.offcanvas-nav-links .nav-link {
  padding: 0.75rem 1rem;
  font-size: 1rem;
  font-weight: 500;
  color: #333;
  border-bottom: 1px solid #f0f0f0;
}

.offcanvas-nav-links .nav-link:hover {
  background-color: #f8f9fa;
  color: #c92c3c;
}

.offcanvas-body .mobile-actions {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e0e0e0;
}

.offcanvas-body .mobile-actions .input-wrapper {
  width: 100%;
}

.offcanvas-body .mobile-actions .input-search {
  width: 100%;
  border-radius: 4px;
  background-color: #f5f5f5;
  border-bottom: 2px solid transparent;
}

.offcanvas-body .mobile-actions .input-search:focus {
  background-color: #fff;
  border-bottom: 2px solid #c92c3c;
}

.offcanvas-body .mobile-actions .icon-btn {
  font-size: 1rem;
  display: flex;
  align-items: center;
}

.offcanvas-body .mobile-actions .icon-btn i {
  font-size: 1.25rem;
}

@media (min-width: 992px) {
  .header .navbar-brand {
    margin-right: auto;
    /* Đẩy logo sang trái */
  }
}

/* Responsive Adjustments (Sử dụng breakpoints của Bootstrap nếu có thể) */
/* Bootstrap lg breakpoint là 992px */
/* Ví dụ cho các màn hình nhỏ hơn nữa nếu cần */
@media (max-width: 575.98px) {
  .header .navbar-brand img.logo {
    width: 60px;
  }

  .offcanvas {
    width: 250px;
  }
}

.cart-dropdown {
  position: relative;
}

.cart-dropdown:hover .dropdown-menu-cart {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.dropdown-menu-cart {
  position: absolute;
  top: 100%;
  right: 0;
  width: 300px;
  /* Độ rộng của dropdown */
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px);
  transition: all 0.3s ease;
  z-index: 1000;
}

.cart-item-img {
  width: 60px;
  height: 60px;
  object-fit: cover;
}

.shopping-list .content h6 {
  font-size: 0.9rem;
  font-weight: 600;
}

.shopping-list .content p {
  font-size: 0.8rem;
  color: #6c757d;
}

.text-primary-red {
  color: #ca111f !important;
}

.btn-danger {
  background-color: #3866bc;
  border-color: #3866bc;
}

.btn-danger:hover {
  background-color: #3866bc;
  border-color: #3866bc;
}

/* Các styles cũ được giữ lại và chỉnh sửa để tránh xung đột */
.text-primary-red {
  color: #ca111f;
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.1);
}

.suggestion-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  max-height: 270px;
  overflow-y: auto;
  background: #fff;
  border: 1px solid #ddd;
  z-index: 999;
  list-style: none;
  margin: 0;
  padding: 5px 0;
  border-radius: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.suggestion-dropdown li {
  display: flex;
  align-items: flex-start;
  padding: 8px 12px;
  gap: 10px;
  cursor: pointer;
}

.suggestion-dropdown li:hover {
  background-color: #f6f6f6;
}

.img-search {
  width: 50px;
  object-fit: cover;
  border-radius: 5px;
}

.info-search {
  display: flex;
  flex-direction: column;
  justify-content: center;
  flex: 1;
}

.name-search {
  font-size: 16px;
  font-weight: 500;
  color: #333;
}

.price-search {
  font-size: 14px;
  color: red;
}

.text-primary-black {
  color: black;
}

.loading,
.no-more {
  padding: 10px;
  text-align: center;
  color: #888;
}

/**/
</style>