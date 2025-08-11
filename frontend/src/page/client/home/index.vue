<template>
    <div class="container-xxl mb-3 p-3">
        <div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner rounded">
                <div class="carousel-item active">
                    <picture>
                        <img src="/images/banner/mlb_desktop_en_be560d199f054efa9d99ae5826e2cdde.webp"
                            class="d-block w-100 img-fluid" alt="Banner 1">
                    </picture>
                </div>
                <div class="carousel-item">
                    <picture>
                        <img src="/images/banner/msloyalty_mlb_web.webp" class="d-block w-100 img-fluid" alt="Banner 2">
                    </picture>
                </div>
                <div class="carousel-item">
                    <picture>
                        <img src="/images/banner/1960x640-mua-ngay.webp" class="d-block w-100 img-fluid" alt="Banner 3">
                    </picture>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying"
                data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

    <div class="container-xl">

        <!-- hàng mới về -->
        <div class="row align-items-center">
            <div class="d-flex justify-content-between w-100 flex-wrap">
                <h3 class="fw-bolder text-start ms-2">Hàng mới về</h3>
                <div class="d-flex mb-1">
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6 d-flex">
                <div class="position-relative overflow-hidden w-100 h-100 banner-hover">
                    <img src="/images/banner/hangmoive_main.jpg" class="w-100 h-100 object-fit-cover rounded"
                        alt="Hàng mới về">
                    <div class="hover-overlay d-flex align-items-center justify-content-center">
                        <router-link to="/sanpham" class="btn btn-outline-light btn-sm">Xem tất cả</router-link>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="row g-2 h-100">
                    <div v-for="n in 4" :key="n" class="col-6">
                        <router-link to="/product-detail" class="single-product h-100">
                            <div class="product-image">
                                <img src="/images/products/product-1.jpg" alt="#">
                                <div class="button">
                                    <a href="product-details.html" class="btn">
                                        <i class="lni lni-cart"></i> Thêm vào giỏ
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="category">Watches</span>
                                <h4 class="title">
                                    <a href="product-grids.html">Xiaomi Mi Band 5</a>
                                </h4>
                                <ul class="review p-0">
                                    <li v-for="star in 4" :key="'star-' + star"><i class="bi bi-star-fill"></i></li>
                                    <li><i class="lni lni-star"></i></li>
                                    <li><span>4.0 Review(s)</span></li>
                                </ul>
                                <div class="price">
                                    <span>$199.00</span>
                                </div>
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>

        <div class="row align-items-center mt-5">
            <div class="d-flex justify-content-between w-100 flex-wrap">
                <h3 class="fw-bolder text-start ms-2">Hàng mới về</h3>
                <div class="d-flex mb-1">
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-1.jpg" alt="#">
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Watches</span>
                        <h4 class="title">
                            <a href="product-grids.html">Xiaomi Mi Band 5</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="lni lni-star"></i></li>
                            <li><span>4.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$199.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-2.jpg" alt="#">
                        <span class="sale-tag">-25%</span>
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Speaker</span>
                        <h4 class="title">
                            <a href="product-grids.html">Big Power Sound Speaker</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$275.00</span>
                            <span class="discount-price">$300.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-3.jpg" alt="#">
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Camera</span>
                        <h4 class="title">
                            <a href="product-grids.html">WiFi Security Camera</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$399.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-4.jpg" alt="#">
                        <span class="new-tag">New</span>
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Phones</span>
                        <h4 class="title">
                            <a href="product-grids.html">iphone 6x plus</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$400.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-5.jpg" alt="#">
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Headphones</span>
                        <h4 class="title">
                            <a href="product-grids.html">Wireless Headphones</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$350.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-6.jpg" alt="#">
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Speaker</span>
                        <h4 class="title">
                            <a href="product-grids.html">Mini Bluetooth Speaker</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="lni lni-star"></i></li>
                            <li><span>4.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$70.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-7.jpg" alt="#">
                        <span class="sale-tag">-50%</span>
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Headphones</span>
                        <h4 class="title">
                            <a href="product-grids.html">PX7 Wireless Headphones</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="lni lni-star"></i></li>
                            <li><span>4.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$100.00</span>
                            <span class="discount-price">$200.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <!-- Start Single Product -->
                <div class="single-product">
                    <div class="product-image">
                        <img src="/images/products/product-8.jpg" alt="#">
                        <div class="button">
                            <a href="product-details.html" class="btn"><i class="lni lni-cart"></i> Add to Cart</a>
                        </div>
                    </div>
                    <div class="product-info">
                        <span class="category">Laptop</span>
                        <h4 class="title">
                            <a href="product-grids.html">Apple MacBook Air</a>
                        </h4>
                        <ul class="review p-0">
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><i class="bi bi-star-fill"></i></li>
                            <li><span>5.0 Review(s)</span></li>
                        </ul>
                        <div class="price">
                            <span>$899.00</span>
                        </div>
                    </div>
                </div>
                <!-- End Single Product -->
            </div>
        </div>

        <!-- hàng mới về -->
        <div class="row align-items-center">
            <div class="d-flex justify-content-between w-100 flex-wrap">
                <h3 class="fw-bolder text-start ms-2">Hàng mới về</h3>
                <div class="d-flex mb-1">
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                    <div class="me-2">
                        <button type="button" class='btn btn-outline-dark btn-sm p-2 mt-2 fw-semibold'>
                            aaaaaaaaaa
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-md-6">
                <div class="row g-2 h-100">
                    <div v-for="n in 4" :key="n" class="col-6">
                        <div class="single-product h-100">
                            <div class="product-image">
                                <img src="/images/products/product-1.jpg" alt="#">
                                <div class="button">
                                    <a href="product-details.html" class="btn">
                                        <i class="lni lni-cart"></i> Thêm vào giỏ
                                    </a>
                                </div>
                            </div>
                            <div class="product-info">
                                <span class="category">Watches</span>
                                <h4 class="title">
                                    <a href="product-grids.html">Xiaomi Mi Band 5</a>
                                </h4>
                                <ul class="review p-0">
                                    <li v-for="star in 4" :key="'star-' + star"><i class="bi bi-star-fill"></i></li>
                                    <li><i class="lni lni-star"></i></li>
                                    <li><span>4.0 Review(s)</span></li>
                                </ul>
                                <div class="price">
                                    <span>$199.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 col-md-6 d-flex">
                <div class="position-relative overflow-hidden w-100 h-100 banner-hover">
                    <img src="/images/banner/hangmoive_main.jpg" class="w-100 h-100 object-fit-cover rounded"
                        alt="Hàng mới về">
                    <div class="hover-overlay d-flex align-items-center justify-content-center">
                        <router-link to="/sanpham" class="btn btn-outline-light btn-sm">Xem tất cả</router-link>
                    </div>
                </div>
            </div>


        </div>
    </div>


</template>
<style scoped>
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
    background-color: #0167F3;
    content: '';
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
    color: #0167F3;
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
    background: #0167F3;
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
    color: #0167F3;
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
    color: #0167F3;
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