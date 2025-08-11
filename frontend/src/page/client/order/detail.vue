<template>
    <div class="container py-4">

        <div class="card p-3 shadow-sm mb-4">
            <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
            <div class="row">
                <div class="col-6">Họ tên:</div>
                <div class="col-6 text-end">Nguyễn Văn A</div>
                <div class="col-6">Số điện thoại:</div>
                <div class="col-6 text-end">0912345678</div>
                <div class="col-6">Email:</div>
                <div class="col-6 text-end">nguyenvana@gmail.com</div>
                <div class="col-6">Địa chỉ:</div>
                <div class="col-6 text-end">Số 123, Đường Nguyễn Trãi, Quận 1, TP. Hồ Chí Minh</div>
            </div>
        </div>

        <div class="card p-3 shadow-sm mb-4">
            <h5 class="border-bottom pb-2">Thông tin</h5>
            <div class="row">
                <div class="col-6">Ngày đặt hàng:</div>
                <div class="col-6 text-end">08/08/2025 10:30</div>
                <div class="col-6">Phương thức thanh toán:</div>
                <div class="col-6 text-end">Thanh toán khi nhận hàng</div>
                <div class="col-6">Trạng thái thanh toán:</div>
                <div class="col-6 text-end text-success">Đã thanh toán</div>
                <div class="col-6">Trạng thái đơn:</div>
                <div class="col-6 text-end text-warning">Chờ xác nhận</div>
            </div>
        </div>

        <div class="card p-3 shadow-sm mb-4">
            <h5 class="border-bottom pb-2">Chi tiết hóa đơn</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Mặt hàng</th>
                            <th>Giá bán</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/products/product-1.jpg" style="width: 50px; height: auto;"
                                        class="me-2 rounded-3" alt="food">
                                    <div>
                                        <div class="fw-bold">Phở Bò</div>
                                        <ul class="mb-0 ps-3 small text-muted">
                                            <li>Size: L, Màu: đỏ</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td>80.000 VNĐ</td>
                            <td>2</td>
                            <td>160.000 VNĐ</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="/images/products/product-2.jpg" style="width: 50px; height: auto;"
                                        class="me-2 rounded-3" alt="food">
                                    <div>
                                        <div class="fw-bold">Cơm gà xé</div>
                                        <ul class="mb-0 ps-3 small text-muted">
                                            <li>Size: L, Màu: đỏ</li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                            <td>50.000 VNĐ</td>
                            <td>1</td>
                            <td>50.000 VNĐ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <p class="mb-1">Tạm tính: 210.000 VNĐ</p>
                <p class="mb-1">Phí vận chuyển: 20.000 VNĐ</p>
                <h5 class="fw-bold text-danger">Tổng cộng: 230.000 VNĐ</h5>
            </div>
        </div>

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <button class="btn btn-secondary flex-grow-1 flex-sm-grow-0">Quay lại</button>
            <button class="btn btn-info flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal"
                data-bs-target="#addressModal">Thay đổi địa chỉ</button>
            <button class="btn btn-danger flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal"
                data-bs-target="#cancelModal">Hủy đơn</button>
        </div>

        <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addressModalLabel">Thay đổi địa chỉ nhận hàng</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="province" class="form-label">Tỉnh / Thành phố</label>
                                <select class="form-select" id="province" required>
                                    <option selected disabled value="">Chọn Tỉnh / Thành phố</option>
                                    <option value="ho-chi-minh">TP. Hồ Chí Minh</option>
                                    <option value="ha-noi">Hà Nội</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="district" class="form-label">Quận / Huyện</label>
                                <select class="form-select" id="district" required disabled>
                                    <option selected disabled value="">Chọn Quận / Huyện</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ward" class="form-label">Phường / Xã</label>
                                <select class="form-select" id="ward" required disabled>
                                    <option selected disabled value="">Chọn Phường / Xã</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="street" class="form-label">Số nhà, tên đường</label>
                                <input type="text" class="form-control" id="street"
                                    placeholder="Ví dụ: Số 12, đường Nguyễn Trãi">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="cancelModalLabel">Bạn có chắc muốn hủy đơn hàng?</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form>
                        <div class="modal-body">
                            <p>Vui lòng cho chúng tôi biết lý do bạn hủy đơn hàng:</p>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancelReason" id="reason1"
                                    value="Tôi không muốn mua nữa" checked>
                                <label class="form-check-label" for="reason1">
                                    Tôi không muốn mua nữa
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancelReason" id="reason2"
                                    value="Đơn hàng giao lâu">
                                <label class="form-check-label" for="reason2">
                                    Đơn hàng giao lâu
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancelReason" id="reason3"
                                    value="Đổi ý">
                                <label class="form-check-label" for="reason3">
                                    Đổi ý
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancelReason" id="reason4"
                                    value="Khác">
                                <label class="form-check-label" for="reason4">
                                    Khác (vui lòng nhập bên dưới)
                                </label>
                            </div>
                            <textarea class="form-control mt-3" rows="3" placeholder="Nhập lý do khác..."
                                disabled></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</template>
<style>
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

.table {
    min-width: 600px;
}
</style>