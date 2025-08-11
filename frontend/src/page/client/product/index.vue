<template>


    <!-- Start Product Grids -->
    <section class="product-grids section pt-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-12">
                    <!-- Start Product Sidebar -->
                    <div class="product-sidebar">
                        <!-- Start Single Widget -->
                        <div class="single-widget search">
                            <h3>Search Product</h3>
                            <form action="#">
                                <input type="text" placeholder="Search Here...">
                                <button type="submit"><i class="lni lni-search-alt"></i></button>
                            </form>
                        </div>
                        <!-- End Single Widget -->
                        <!-- Start Single Widget -->
                        <div class="single-widget">
                            <h3>All Categories</h3>
                            <ul class="list">
                                <li>
                                    <a href="product-grids.html">Computers & Accessories </a><span>(1138)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">Smartphones & Tablets</a><span>(2356)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">TV, Video & Audio</a><span>(420)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">Cameras, Photo & Video</a><span>(874)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">Headphones</a><span>(1239)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">Wearable Electronics</a><span>(340)</span>
                                </li>
                                <li>
                                    <a href="product-grids.html">Printers & Ink</a><span>(512)</span>
                                </li>
                            </ul>
                        </div>
                        <!-- End Single Widget -->
                        <!-- Start Single Widget -->
                        <div class="single-widget range">
                            <h3>Price Range</h3>
                            <input type="range" class="form-range" name="range" step="1" min="100" max="10000"
                                value="10" onchange="rangePrimary.value=value">
                            <div class="range-inner">
                                <label>$</label>
                                <input type="text" id="rangePrimary" placeholder="100" />
                            </div>
                        </div>
                        <!-- End Single Widget -->
                        <!-- Start Single Widget -->
                        <div class="single-widget condition">
                            <h3>Filter by Price</h3>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1">
                                <label class="form-check-label" for="flexCheckDefault1">
                                    $50 - $100L (208)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2">
                                <label class="form-check-label" for="flexCheckDefault2">
                                    $100L - $500 (311)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3">
                                <label class="form-check-label" for="flexCheckDefault3">
                                    $500 - $1,000 (485)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault4">
                                <label class="form-check-label" for="flexCheckDefault4">
                                    $1,000 - $5,000 (213)
                                </label>
                            </div>
                        </div>
                        <!-- End Single Widget -->
                        <!-- Start Single Widget -->
                        <div class="single-widget condition">
                            <h3>Filter by Brand</h3>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault11">
                                <label class="form-check-label" for="flexCheckDefault11">
                                    Apple (254)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault22">
                                <label class="form-check-label" for="flexCheckDefault22">
                                    Bosh (39)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault33">
                                <label class="form-check-label" for="flexCheckDefault33">
                                    Canon Inc. (128)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault44">
                                <label class="form-check-label" for="flexCheckDefault44">
                                    Dell (310)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault55">
                                <label class="form-check-label" for="flexCheckDefault55">
                                    Hewlett-Packard (42)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault66">
                                <label class="form-check-label" for="flexCheckDefault66">
                                    Hitachi (217)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault77">
                                <label class="form-check-label" for="flexCheckDefault77">
                                    LG Electronics (310)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault88">
                                <label class="form-check-label" for="flexCheckDefault88">
                                    Panasonic (74)
                                </label>
                            </div>
                        </div>
                        <!-- End Single Widget -->
                    </div>
                    <!-- End Product Sidebar -->
                </div>
                <div class="col-lg-9 col-12">
                    <div class="product-grids-head">
                        <div class="product-grid-topbar">
                            <div class="row align-items-center">
                                <div class="col-lg-7 col-md-8 col-12">
                                    <div class="product-sorting">
                                        <label for="sorting">Sort by:</label>
                                        <select class="form-control" id="sorting">
                                            <option>Popularity</option>
                                            <option>Low - High Price</option>
                                            <option>High - Low Price</option>
                                            <option>Average Rating</option>
                                            <option>A - Z Order</option>
                                            <option>Z - A Order</option>
                                        </select>
                                        <h3 class="total-show-product">Showing: <span>1 - 12 items</span></h3>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-4 col-12">
                                    <nav>
                                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                                            <button class="nav-link active" id="nav-grid-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-grid" type="button" role="tab"
                                                aria-controls="nav-grid" aria-selected="true"><i
                                                    class="bi bi-list-task"></i></button>
                                            <button class="nav-link" id="nav-list-tab" data-bs-toggle="tab"
                                                data-bs-target="#nav-list" type="button" role="tab"
                                                aria-controls="nav-list" aria-selected="false"><i
                                                    class="bi bi-grid"></i></button>
                                        </div>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-grid" role="tabpanel"
                                aria-labelledby="nav-grid-tab">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-1.jpg" alt="#">
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-2.jpg" alt="#">
                                                <span class="sale-tag">-25%</span>
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <span class="category">Speaker</span>
                                                <h4 class="title">
                                                    <a href="product-grids.html">Bluetooth Speaker</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-3.jpg" alt="#">
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-4.jpg" alt="#">
                                                <span class="new-tag">New</span>
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-5.jpg" alt="#">
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-6.jpg" alt="#">
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-7.jpg" alt="#">
                                                <span class="sale-tag">-50%</span>
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-8.jpg" alt="#">
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
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
                                    <div class="col-lg-4 col-md-6 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="product-image">
                                                <img src="/images/products/product-2.jpg" alt="#">
                                                <span class="sale-tag">-25%</span>
                                                <div class="button">
                                                    <a href="product-details.html" class="btn"><i
                                                            class="lni lni-cart"></i> Add to Cart</a>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <span class="category">Speaker</span>
                                                <h4 class="title">
                                                    <a href="product-grids.html">Bluetooth Speaker</a>
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
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Pagination -->
                                        <div class="pagination left">
                                            <ul class="pagination-list">
                                                <li><a href="javascript:void(0)">1</a></li>
                                                <li class="active"><a href="javascript:void(0)">2</a></li>
                                                <li><a href="javascript:void(0)">3</a></li>
                                                <li><a href="javascript:void(0)">4</a></li>
                                                <li><a href="javascript:void(0)"><i
                                                            class="lni lni-chevron-right"></i></a></li>
                                            </ul>
                                        </div>
                                        <!--/ End Pagination -->
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="nav-list" role="tabpanel" aria-labelledby="nav-list-tab">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="row align-items-center">
                                                <div class="col-lg-4 col-md-4 col-12">
                                                    <div class="product-image">
                                                        <img src="/images/products/product-1.jpg" alt="#">
                                                        <div class="button">
                                                            <a href="product-details.html" class="btn"><i
                                                                    class="lni lni-cart"></i> Add to
                                                                Cart</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-12">
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
                                            </div>
                                        </div>
                                        <!-- End Single Product -->
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="row align-items-center">
                                                <div class="col-lg-4 col-md-4 col-12">
                                                    <div class="product-image">
                                                        <img src="/images/products/product-2.jpg" alt="#">
                                                        <span class="sale-tag">-25%</span>
                                                        <div class="button">
                                                            <a href="product-details.html" class="btn"><i
                                                                    class="lni lni-cart"></i> Add to
                                                                Cart</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-12">
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
                                            </div>
                                        </div>
                                        <!-- End Single Product -->
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="row align-items-center">
                                                <div class="col-lg-4 col-md-4 col-12">
                                                    <div class="product-image">
                                                        <img src="/images/products/product-3.jpg" alt="#">
                                                        <div class="button">
                                                            <a href="product-details.html" class="btn"><i
                                                                    class="lni lni-cart"></i> Add to
                                                                Cart</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-12">
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
                                            </div>
                                        </div>
                                        <!-- End Single Product -->
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="row align-items-center">
                                                <div class="col-lg-4 col-md-4 col-12">
                                                    <div class="product-image">
                                                        <img src="/images/products/product-4.jpg" alt="#">
                                                        <span class="new-tag">New</span>
                                                        <div class="button">
                                                            <a href="product-details.html" class="btn"><i
                                                                    class="lni lni-cart"></i> Add to
                                                                Cart</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-12">
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
                                            </div>
                                        </div>
                                        <!-- End Single Product -->
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-12">
                                        <!-- Start Single Product -->
                                        <div class="single-product">
                                            <div class="row align-items-center">
                                                <div class="col-lg-4 col-md-4 col-12">
                                                    <div class="product-image">
                                                        <img src="/images/products/product-7.jpg" alt="#">
                                                        <span class="sale-tag">-50%</span>
                                                        <div class="button">
                                                            <a href="product-details.html" class="btn"><i
                                                                    class="lni lni-cart"></i> Add to
                                                                Cart</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-8 col-md-8 col-12">
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
                                            </div>
                                        </div>
                                        <!-- End Single Product -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Pagination -->
                                        <div class="pagination left">
                                            <ul class="pagination-list">
                                                <li><a href="javascript:void(0)">1</a></li>
                                                <li class="active"><a href="javascript:void(0)">2</a></li>
                                                <li><a href="javascript:void(0)">3</a></li>
                                                <li><a href="javascript:void(0)">4</a></li>
                                                <li><a href="javascript:void(0)"><i
                                                            class="lni lni-chevron-right"></i></a></li>
                                            </ul>
                                        </div>
                                        <!--/ End Pagination -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- End Product Grids -->
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