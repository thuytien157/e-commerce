<template>
    <div class="shopping-cart section pt-4">
        <div class="container bg-white p-3">
            <h5 class="">Giỏ hàng của bạn</h5>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="list-group mt-5" id="cart">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                <input class="form-check-input me-3" type="checkbox" checked>
                                <img src="/images/products/product-1.jpg" alt="Sản phẩm 1" class="img-thumbnail">
                                <div class="ms-3">
                                    <p class="mb-1 fw-bold">Tên Sản Phẩm 1</p>
                                    <div class="text-muted small">Số lượng: 2</div>
                                    <div class="text-muted small">Số lượng: 2</div>
                                </div>
                            </div>
                            <div class="cart-action-col d-flex justify-content-between">
                                <p class="fw-bold mb-2">250.000<sup class="fw-bold">đ</sup></p>
                                <div class="input-group input-group-sm quantity-control mb-2">
                                    <button class="btn btn-quantity" type="button">−</button>
                                    <input type="number" class="form-control text-center form-control-quantity"
                                        value="1" min="1" readonly>
                                    <button class="btn btn-quantity" type="button">+</button>
                                </div>
                                <a class="btn btn-danger btn-sm" href="#">Xoá</a>
                            </div>
                        </div>

                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center flex-grow-1">
                                <input class="form-check-input me-3" type="checkbox" checked>
                                <img src="/images/products/product-2.jpg" alt="Sản phẩm 2" class="img-thumbnail">
                                <div class="ms-3">
                                    <p class="mb-1 fw-bold">Tên Sản Phẩm 2</p>
                                    <div class="text-muted small">Số lượng: 2</div>
                                    <div class="text-muted small">Số lượng: 2</div>
                                </div>
                            </div>
                            <div class="cart-action-col d-flex justify-content-between">
                                <p class="fw-bold mb-2">150.000<sup class="fw-bold">đ</sup></p>
                                <div class="input-group input-group-sm quantity-control mb-2">
                                    <button class="btn btn-quantity" type="button">−</button>
                                    <input type="number" class="form-control text-center form-control-quantity"
                                        value="1" min="1" readonly>
                                    <button class="btn btn-quantity" type="button">+</button>
                                </div>
                                <a class="btn btn-danger btn-sm" href="#">Xoá</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 mt-lg-0">
                    <div class="card mt-5">
                        <div class="card-body" id="tomtat">
                            <h5 class="card-title fw-bold">Tóm tắt đơn hàng</h5>
                            <div class="d-flex justify-content-between">
                                <p>Tổng sản phẩm:</p>
                                <p>3</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>Tổng tiền:</p>
                                <p class="fw-bold">650.000 VND</p>
                            </div>
                            <router-link to="/checkout" class="btn btn-dark w-100 mt-3">Tiến hành thanh toán</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* CSS cho desktop */
.shopping-cart .container {
    background-color: #fff;
    padding: 1.5rem;
    border-radius: 8px;
}

.img-thumbnail {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.list-group-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #dee2e6;
    margin-bottom: 1rem;
    border-radius: 8px;
}

.quantity-control {
    width: 120px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}

.btn-quantity {
    background-color: #f8f9fa;
    border: none;
    font-weight: bold;
    color: #333;
    padding: 0.25rem 0.5rem;
}

.form-control-quantity {
    text-align: center;
    border-left: 1px solid #ced4da;
    border-right: 1px solid #ced4da;
    border-top: none;
    border-bottom: none;
}

.form-control-quantity:focus {
    box-shadow: none;
    border-color: #ced4da;
}

/* Ẩn mũi tên mặc định của input type="number" */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.cart-action-col {
    width: 50%;
}

/* --- Responsive CSS --- */
/* Điều chỉnh cho màn hình nhỏ hơn 992px (máy tính bảng và điện thoại) */
@media (max-width: 991px) {
    .list-group-item {
        flex-direction: column;
        align-items: flex-start;
        padding: 1rem;
    }

    .list-group-item .d-flex.align-items-center {
        width: 100%;
        margin-bottom: 1rem;
    }

    .cart-action-col {
        width: 100%;
        flex-direction: row !important;
        justify-content: space-between;
        align-items: center !important;
    }

    .cart-action-col .fw-bold {
        margin-bottom: 0 !important;
    }

}
</style>