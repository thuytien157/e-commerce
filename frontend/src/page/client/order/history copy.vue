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

                <div class="col-12 col-md-8 col-lg-9 bg-white">
                    <h4 class="fw-bold mb-4">Đơn hàng đã mua</h4>

                    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-4">
                        <a href="#" class="tab-item active">Chờ xác nhận</a>
                        <a href="#" class="tab-item">Đã xác nhận</a>
                        <a href="#" class="tab-item">Đang xử lý</a>
                        <a href="#" class="tab-item">Bắt đầu giao</a>
                        <a href="#" class="tab-item">Giao thành công</a>
                        <a href="#" class="tab-item">Đã hủy</a>
                    </div>

                    <div class="card shadow border-0 p-0 d-none d-md-block">
                        <table class="table table-hover w-100 custom-borderless-table">
                            <thead class="table-secondary">
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="order-id">#123456</td>
                                    <td>08/08/2025 10:30</td>
                                    <td>150.000 VND</td>
                                    <td><span class="order-status pending">Chờ xác nhận</span></td>
                                    <td>
                                        <a href="/history-order-detail/123456"
                                            class="btn btn-outline-primary btn-sm">Xem</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="order-id">#123457</td>
                                    <td>07/08/2025 15:00</td>
                                    <td>250.000 VND</td>
                                    <td><span class="order-status delivered">Giao thành công</span></td>
                                    <td>
                                        <a href="/history-order-detail/123457"
                                            class="btn btn-outline-primary btn-sm">Xem</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="order-id">#123458</td>
                                    <td>06/08/2025 11:45</td>
                                    <td>95.000 VND</td>
                                    <td><span class="order-status cancelled">Đã hủy</span></td>
                                    <td>
                                        <a href="/history-order-detail/123458"
                                            class="btn btn-outline-primary btn-sm">Xem</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-md-none">
                        <div class="order-card card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold m-0">Mã đơn: #123456</h6>
                                    <span class="order-status pending">Chờ xác nhận</span>
                                </div>
                                <p class="mb-1 text-muted"><i class="bi bi-calendar me-2"></i>Ngày đặt: 08/08/2025 10:30
                                </p>
                                <p class="mb-2 text-muted"><i class="bi bi-cash me-2"></i>Tổng tiền: <strong>150.000
                                        VND</strong></p>
                                <div class="text-end">
                                    <a href="/history-order-detail/123456" class="btn btn-outline-primary btn-sm">Xem
                                        chi tiết</a>
                                </div>
                            </div>
                        </div>
                        <div class="order-card card shadow-sm mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold m-0">Mã đơn: #123457</h6>
                                    <span class="order-status delivered">Giao thành công</span>
                                </div>
                                <p class="mb-1 text-muted"><i class="bi bi-calendar me-2"></i>Ngày đặt: 07/08/2025 15:00
                                </p>
                                <p class="mb-2 text-muted"><i class="bi bi-cash me-2"></i>Tổng tiền: <strong>250.000
                                        VND</strong></p>
                                <div class="text-end">
                                    <a href="/history-order-detail/123457" class="btn btn-outline-primary btn-sm">Xem
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
/*
  Cải thiện giao diện người dùng
  - Sử dụng biến CSS để quản lý màu sắc dễ hơn
  - Bổ sung hiệu ứng chuyển động
*/
:root {
  --primary-color: #ca111f;
  --secondary-color: #f8f9fa;
  --text-color: #495057;
  --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
}

.account-page {
  background-color: #f0f2f5;
}

.card {
  border-radius: 12px;
}

/* Tab trạng thái đơn hàng */
.order-tabs {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
}

.order-tabs::-webkit-scrollbar {
  display: none;
}

.tab-item {
  flex: 0 0 auto;
  padding: 0.75rem 1.25rem;
  white-space: nowrap;
  border-radius: 8px;
  font-weight: 500;
  color: var(--text-color);
  background-color: var(--secondary-color);
  text-decoration: none;
  transition: all 0.3s ease;
}

.tab-item:hover {
  background-color: #e9ecef;
}

.tab-item.active {
  color: #fff;
  background-color: var(--primary-color);
  font-weight: 600;
  box-shadow: 0 4px 10px rgba(202, 17, 31, 0.2);
}

/* Bảng đơn hàng trên máy tính */
.custom-borderless-table {
  border-collapse: separate;
  border-spacing: 0 10px;
}

.custom-borderless-table thead th {
  padding-top: 1rem;
  padding-bottom: 1rem;
  font-size: 0.9rem;
}

.custom-borderless-table tbody tr {
  background-color: #fff;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
  transition: all 0.2s ease;
}

.custom-borderless-table tbody tr:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.08);
}

.custom-borderless-table td {
  vertical-align: middle;
  padding: 1rem;
}

.order-id {
  font-weight: bold;
}

.order-status {
  padding: 0.4rem 0.8rem;
  border-radius: 50px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-block; /* Quan trọng để badge hoạt động */
}

.order-status.pending {
  background-color: #fff3cd;
  color: #856404;
}

.order-status.delivered {
  background-color: #d4edda;
  color: #155724;
}

.order-status.cancelled {
  background-color: #f8d7da;
  color: #721c24;
}

/* Thẻ đơn hàng trên điện thoại */
.order-card {
  border-radius: 12px;
}

.order-card .card-body p {
  font-size: 0.9rem;
}

.btn-outline-primary {
  color: var(--primary-color);
  border-color: var(--primary-color);
}

.btn-outline-primary:hover {
  background-color: var(--primary-color);
  color: #fff;
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

@media (max-width: 767.98px) {
  .order-tabs {
    flex-wrap: nowrap;
  }
  .tab-item {
    padding: 0.6rem 1rem;
    font-size: 0.9rem;
  }
}
</style>
