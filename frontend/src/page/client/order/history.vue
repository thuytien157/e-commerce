<template>
    <section class="account-page py-5 fade-in">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm border-0 text-center p-4">
                        <div class="avatar-wrapper mb-3 mx-auto">
                            <img src="/images/products/product-1.jpg" alt="Ảnh đại diện"
                                class="rounded-circle avatar-img" />
                            <div class="avatar-overlay">
                                <label class="btn btn-light btn-sm m-0 cursor-pointer">
                                    Đổi ảnh
                                    <input type="file" hidden />
                                </label>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-1">Chưa có tên</h5>
                        <p class="text-muted small mb-3">Tên người dùng</p>

                        <hr class="my-3">

                        <div class="list-group list-group-flush text-start">
                            <a href="/update-user"
                                class="list-group-item list-group-item-action text-decoration-none rounded mb-2">
                                <i class="bi bi-person me-2"></i>
                                Thông tin tài khoản
                            </a>
                            <a href="/history-order"
                                class="list-group-item list-group-item-action text-decoration-none rounded mb-2">
                                <i class="bi bi-box-seam me-2"></i>
                                Lịch sử đơn hàng
                            </a>
                            <a href="#" class="list-group-item list-group-item-action text-danger rounded">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-8 col-lg-9 bg-white p-3" style="border-radius: 10px;">
                    <h4 class="fw-bold mb-4">Đơn hàng đã mua</h4>


                    <!-- Tabs -->
                    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
                        <a href="#" class="tab-item active">Chờ xác nhận</a>
                        <a href="#" class="tab-item">Đã xác nhận</a>
                        <a href="#" class="tab-item">Đang xử lý</a>
                        <a href="#" class="tab-item">Bắt đầu giao</a>
                        <a href="#" class="tab-item">Giao thành công</a>
                        <a href="#" class="tab-item">Đã hủy</a>
                    </div>

                    <!-- Table for desktop -->
                    <div class="card border-0 p-0 d-none d-md-block">
                        <table class="table table-hover table-borderless align-middle">
                            <thead class="table-light rounded">
                                <tr>
                                    <th scope="col">Mã đơn</th>
                                    <th scope="col">Ngày đặt</th>
                                    <th scope="col">Tổng tiền</th>
                                    <th scope="col">Trạng thái</th>
                                    <th scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>#123456</td>
                                    <td>08/08/2025 10:30</td>
                                    <td>150.000 VND</td>
                                    <td><span class="badge bg-warning text-dark">Chờ xác nhận</span></td>
                                    <td><a href="/history-order-detail/123456"
                                            class="btn btn-sm btn-outline-primary">Xem</a></td>
                                </tr>
                                <tr>
                                    <td>#123457</td>
                                    <td>07/08/2025 15:00</td>
                                    <td>250.000 VND</td>
                                    <td><span class="badge bg-success">Giao thành công</span></td>
                                    <td><a href="/history-order-detail/123457"
                                            class="btn btn-sm btn-outline-primary">Xem</a></td>
                                </tr>
                                <tr>
                                    <td>#123458</td>
                                    <td>06/08/2025 11:45</td>
                                    <td>95.000 VND</td>
                                    <td><span class="badge bg-danger">Đã hủy</span></td>
                                    <td><a href="/history-order-detail/123458"
                                            class="btn btn-sm btn-outline-primary">Xem</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Cards for mobile -->
                    <div class="d-md-none">
                        <div class="order-card card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Mã đơn: #123456</h6>
                                    <span class="badge bg-warning text-dark">Chờ xác nhận</span>
                                </div>
                                <p class="mb-1 text-muted"><i class="bi bi-calendar me-2"></i>08/08/2025 10:30</p>
                                <p class="mb-2 text-muted"><i class="bi bi-cash me-2"></i>Tổng tiền: <strong>150.000
                                        VND</strong></p>
                                <div class="text-end">
                                    <a href="/history-order-detail/123456" class="btn btn-sm btn-outline-primary">Xem
                                        chi tiết</a>
                                </div>
                            </div>
                        </div>

                        <div class="order-card card border-0 shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Mã đơn: #123457</h6>
                                    <span class="badge bg-success">Giao thành công</span>
                                </div>
                                <p class="mb-1 text-muted"><i class="bi bi-calendar me-2"></i>07/08/2025 15:00</p>
                                <p class="mb-2 text-muted"><i class="bi bi-cash me-2"></i>Tổng tiền: <strong>250.000
                                        VND</strong></p>
                                <div class="text-end">
                                    <a href="/history-order-detail/123457" class="btn btn-sm btn-outline-primary">Xem
                                        chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </section>
</template>

<style scoped>
.tab-item.active {

    border: 1px solid #0d6efd;
    /* border-color: #0d6efd; */
}

.tab-item:hover {
    background-color: #0d6efd;
    color: white !important;
}

.table th,
.table td {
    vertical-align: middle;
}

:root {
    --primary-color: #ca111f;
    --text-color: #495057;
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
}

.account-page {
    background-color: #f0f2f5;
}

.card {
    border-radius: 12px;
}

.tab-item {
    flex: 0 0 auto;
    padding: 10px;
    white-space: nowrap;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* Khu vực Avatar */
.avatar-wrapper {
    width: 120px;
    height: 120px;
    position: relative;
    transition: transform 0.3s ease;
}

.avatar-wrapper:hover {
    transform: scale(1.05);
}

.avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 4px solid var(--primary-color);
    box-shadow: 0 0 0 2px rgba(202, 17, 31, 0.2);
}

.avatar-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    opacity: 0;
    transition: opacity 0.3s ease;
    cursor: pointer;
}

.avatar-wrapper:hover .avatar-overlay {
    opacity: 1;
}

/* Menu điều hướng */
.list-group-item {
    border: none;
    border-radius: 8px;
    color: var(--text-color);
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: var(--secondary-color);
    color: #000;
}

.list-group-item.active {
    background-color: var(--primary-color);
    color: #fff;
    font-weight: bold;
}

.list-group-item.text-danger:hover {
    background-color: rgba(202, 17, 31, 0.1);
    color: var(--primary-color) !important;
}

/* Form inputs */
.form-control:focus,
.form-control:active {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 0.25rem rgba(202, 17, 31, 0.25);
}

/* Responsive adjustments */
@media (max-width: 767.98px) {
    .list-group {
        overflow-x: auto;
        flex-wrap: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .list-group::-webkit-scrollbar {
        display: none;
    }

    .list-group-item {
        display: flex;
        flex-wrap: wrap;
        text-align: center;
    }

    .list-group-item i {
        display: none;
        /* Ẩn icon trên mobile để tiết kiệm diện tích */
    }

    .list-group-item p {
        margin-bottom: 0;
    }
}

.fade-in {
    animation: fadeIn 0.4s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
