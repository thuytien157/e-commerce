<template>
    <section class="checkout-wrapper section pt-2">
        <div class="container py-4">
            <div class="row gx-5">
                <div class="col-md-6">
                    <div class="p-4 border rounded shadow-sm bg-white">
                        <h5 class="mb-4">Thông tin đặt hàng</h5>
                        <form>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Tên của bạn" />
                            </div>
                            <div class="mb-3">
                                <input type="email" class="form-control" placeholder="Email của bạn" />
                            </div>
                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Số điện thoại" />
                            </div>
                            <div class="mb-3">
                                <select class="form-control">
                                    <option value="">Chọn tỉnh / thành</option>
                                    <option value="1">Hồ Chí Minh</option>
                                    <option value="2">Hà Nội</option>
                                    <option value="3">Đà Nẵng</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <select class="form-control">
                                    <option value="">Chọn quận / huyện</option>
                                    <option value="1">Quận 1</option>
                                    <option value="2">Quận 3</option>
                                    <option value="3">Quận 5</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <select class="form-control">
                                    <option value="">Chọn phường / xã</option>
                                    <option value="1">Phường Bến Nghé</option>
                                    <option value="2">Phường 6</option>
                                    <option value="3">Phường 8</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" placeholder="Địa chỉ" />
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" rows="3" placeholder="Ghi chú"></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/cart" class="btn btn-outline-secondary">
                                    <i class="bi bi-chevron-left"></i> Quay về giỏ hàng
                                </a>
                                <button type="submit" class="btn btn-check-out">
                                    Đặt hàng
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-6 pe-0">
                    <div class="p-4 border rounded shadow-sm bg-white">
                        <h5 class="mb-3">Đơn hàng (2 sản phẩm)</h5>
                        <hr />

                        <div class="list-product-scroll mb-2">
                            <div class="d-flex mb-3">
                                <img src="/images/products/product-1.jpg" alt="" class="me-3 rounded" width="50"
                                    height="50" />
                                <div class="flex-grow-1">
                                    <strong>Tên Sản Phẩm 1</strong>

                                    <div class="text-muted small ps-2 mb-1" style="font-size: 11px">
                                        <div>+ Size: S - Màu: đỏ</div>
                                    </div>
                                    <div style="font-size: 12px">Số lượng: 1</div>
                                    <div style="font-size: 12px">Giá: 100.000 VNĐ</div>
                                </div>
                                <div class="text-end ms-2">
                                    <strong>135.000 VNĐ</strong>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <img src="/images/products/product-1.jpg" alt="" class="me-3 rounded" width="50"
                                    height="50" />
                                <div class="flex-grow-1">
                                    <strong>Tên Sản Phẩm 2</strong>

                                    <div class="text-muted small ps-2 mb-1" style="font-size: 11px">
                                        <div>+ Size: S - Màu: đỏ</div>
                                    </div>
                                    <div style="font-size: 12px">Số lượng: 1</div>
                                    <div style="font-size: 12px">Giá: 100.000 VNĐ</div>
                                </div>
                                <div class="text-end ms-2">
                                    <strong>80.000 VNĐ</strong>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính</span><span>215.000 VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí ship</span><span>15.000 VNĐ</span>
                        </div>
                        <!-- <div class="d-flex justify-content-between mb-2">
                            <span>10 Tpoints</span>
                            <label class="toggle-switch">
                                <input type="checkbox" />
                                <div class="toggle-switch-background">
                                    <div class="toggle-switch-handle"></div>
                                </div>
                            </label>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success mb-2">
                            Giảm 10.000 VNĐ từ Tpoints
                            <br />
                            <span class="text-muted">
                                Còn lại 0 Tpoints
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá sản phẩm</span> <span>-20.000 VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Giảm giá phí ship</span> <span>-15.000 VNĐ</span>
                        </div> -->
                        <div style="color: #c92c3c" class="d-flex justify-content-between mb-2 fw-bold">
                            <span>Tổng thanh toán:</span><span>180.000 VNĐ</span>
                        </div>

                        <!-- <div class="mb-3">
                            <div class="text-green-600 mb-2">
                                Mã <strong style="color: #c92c3c">SALE10</strong> đã được áp dụng ✅.
                            </div>
                            <label for="discount" class="form-label">Mã giảm giá</label>
                            <div class="input-group">
                                <input type="text" id="discount" class="form-control"
                                    placeholder="Nhập mã giảm giá..." />
                                <button class="btn btn-outline-primary">Áp dụng</button>
                            </div>
                            <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4 mt-3">
                                <div class="tab-item active">
                                    Tất cả mã
                                </div>
                                <div class="tab-item">
                                    Mã của tôi
                                </div>
                            </div>
                            <div class="discount-scroll-wrapper">
                                <div class="voucher-card mb-3 disabled-voucher">
                                    <div class="voucher-card-left freeship">
                                        <img src="/img/freeship-icon.png" alt="icon" />
                                        <div class="voucher-card-label">FREESHIP</div>
                                    </div>
                                    <div class="voucher-card-right">
                                        <div>
                                            <div class="voucher-code">Mã: FREESHIP25</div>
                                            <div class="voucher-condition">
                                                <i class="fa-regular fa-clock me-1"></i>Hết hạn: 2025-08-31
                                            </div>
                                            <div class="voucher-condition">
                                                Miễn phí vận chuyển cho đơn trên 100k
                                            </div>
                                        </div>
                                        <div class="voucher-footer">
                                            <div class="voucher-coins">
                                                Đã dùng: 50/100
                                            </div>
                                            <button class="voucher-button">Dùng ngay</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="voucher-card mb-3">
                                    <div class="voucher-card-left salefood">
                                        <img src="/img/discount-icon.png" alt="icon" />
                                        <div class="voucher-card-label">GIẢM GIÁ</div>
                                    </div>
                                    <div class="voucher-card-right">
                                        <div>
                                            <div class="voucher-code">Mã: SALE20</div>
                                            <div class="voucher-condition">
                                                <i class="fa-regular fa-clock me-1"></i>Hết hạn: 2025-08-31
                                            </div>
                                            <div class="voucher-condition">
                                                Giảm 20k cho đơn trên 150k
                                            </div>
                                        </div>
                                        <div class="voucher-footer">
                                            <div class="voucher-coins">
                                                Đã dùng: 10/50
                                            </div>
                                            <button class="voucher-button">Dùng ngay</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div>
                            <h6 class="mb-2">Phương thức thanh toán</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="vnpay" value="VNPAY" />
                                <label class="form-check-label d-flex align-items-center" for="vnpay">
                                    <span class="me-2">Thanh toán qua VNPAY</span>
                                    <img src="/images/products/product-1.jpg" height="20" width="60" alt="" />
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="momo" value="MOMO" />
                                <label class="form-check-label d-flex align-items-center" for="momo">
                                    <span class="me-2">Thanh toán qua Momo</span>
                                    <img src="/images/products/product-1.jpg" height="20" width="20" alt="" />
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment" id="cod" value="COD"
                                    checked />
                                <label class="form-check-label d-flex align-items-center" for="cod">
                                    <span class="me-2">Thanh toán khi nhận hàng (COD)</span>
                                    <img src="/images/products/product-1.jpg" height="30" width="30" alt="" />
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--====== Checkout Form Steps Part Ends ======-->
</template>
<style scoped>
.isLoading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background-color: rgba(148, 142, 142, 0.8);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-backdrop {
    z-index: 1040;
}

.modal {
    z-index: 1050;
}

.order-tabs {
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
    display: none;
}

.tab-item {
    padding: 8px 16px;
    cursor: pointer;
    white-space: nowrap;
    border-bottom: 2px solid transparent;
}

.tab-item.active {
    border-color: #c92c3c;
    color: #c92c3c;
    font-weight: 600;
}

.voucher-card {
    display: flex;
    min-height: 100px;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    cursor: pointer;
    transition: box-shadow 0.2s;
}

.voucher-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.voucher-card.disabled-voucher {
    opacity: 0.6;
    cursor: not-allowed;
}

.voucher-card-left {
    width: 26%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    padding: 10px;
}

.voucher-card-left.freeship {
    background-color: #00bfa5;
}

.voucher-card-left.salefood {
    background-color: #f44336;
}

.voucher-card-left img {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

.voucher-card-label {
    font-size: 12px;
    font-weight: bold;
    text-align: center;
    margin-top: 5px;
}

.voucher-card-right {
    width: 74%;
    padding: 10px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.voucher-code {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.voucher-condition {
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
    line-height: 1.3;
}

.voucher-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 6px;
}

.voucher-coins {
    font-size: 13px;
    color: #777;
    display: flex;
    align-items: center;
}

.voucher-coins img {
    width: 16px;
    height: 16px;
    margin-left: 5px;
}

.voucher-button {
    font-size: 13px;
    padding: 5px 12px;
    border-radius: 5px;
    border: 1px solid #c92c3c;
    color: #c92c3c;
    background-color: transparent;
    transition: all 0.2s ease;
}

.voucher-button:hover {
    background-color: #c92c3c;
    color: white;
}

.voucher-button.has-voucher {
    border-color: #007d00;
    color: #007d00;
    cursor: default;
}

.voucher-button.has-voucher:hover {
    background-color: #007d00;
    color: white;
}

/* togle btn tpoint */
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 36px;
    height: 18px;
    cursor: pointer;
}

.toggle-switch input[type="checkbox"] {
    display: none;
}

.toggle-switch-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: #ddd;
    border-radius: 9px;
    box-shadow: inset 0 0 0 1px #ccc;
    transition: background-color 0.3s ease-in-out;
}

.toggle-switch-handle {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 14px;
    height: 14px;
    background-color: #fff;
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease-in-out;
}

.toggle-switch::before {
    content: "";
    position: absolute;
    top: -18px;
    right: -20px;
    font-size: 10px;
    font-weight: bold;
    color: #aaa;
    text-shadow: 1px 1px #fff;
    transition: color 0.3s ease-in-out;
}

.toggle-switch input[type="checkbox"]:checked+.toggle-switch-background {
    background-color: #05c46b;
    box-shadow: inset 0 0 0 1px #04b360;
}

.toggle-switch input[type="checkbox"]:checked+.toggle-switch-background .toggle-switch-handle {
    transform: translateX(18px);
}

/* Responsive cho thiết bị di động */
@media (max-width: 767.98px) {
    .checkout-wrapper .row {
        flex-direction: column-reverse;
    }

    .checkout-wrapper .col-md-5,
    .checkout-wrapper .col-md-7 {
        margin-bottom: 24px;
    }

    .p-4 {
        padding: 16px !important;
    }

    h4 {
        font-size: 1.25rem;
    }

    .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
    }

    .d-flex img {
        margin-bottom: 8px;
    }
}
</style>
