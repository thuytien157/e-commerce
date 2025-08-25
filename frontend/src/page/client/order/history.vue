<script setup>
import SidebarAccount from "@/component/client/SidebarAccount.vue";
import FormatData from "@/component/store/FormatData";
import { useTokenUser } from "@/component/store/useTokenUser";
import axios from "axios";
import { onMounted, ref } from "vue";
import { Modal } from "bootstrap";
import Swal from "sweetalert2";

const store = useTokenUser();
const orders = ref(null);
const isLoading = ref(true);
const currentStatus = ref("");
const orderToReview = ref(null);
const isLoadingReview = ref(false);
const errors = ref({});

const getOrder = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get(
            `${import.meta.env.VITE_URL_API}api/order-history-user`,
            {
                headers: { Authorization: `Bearer ${store.token}` },
            }
        );
        orders.value = res.data.orders;
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const filterOrderByStatus = async (status) => {
    currentStatus.value = status;
    isLoading.value = true;
    try {
        const res = await axios.get(
            `${import.meta.env.VITE_URL_API}api/order-history-user?status=${status}`,
            {
                headers: { Authorization: `Bearer ${store.token}` },
            }
        );
        orders.value = res.data.orders;
    } catch (error) {
        console.log(error);
    } finally {
        isLoading.value = false;
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case "Chờ xác nhận":
            return "bg-secondary text-white";
        case "Đã xác nhận":
            return "bg-info text-white";
        case "Đang xử lý":
            return "bg-primary text-white";
        case "Đang giao hàng":
            return "bg-warning text-dark";
        case "Hoàn thành":
            return "bg-success text-white";
        case "Đã hủy":
        case "Thất bại":
            return "bg-danger text-white";
        default:
            return "bg-secondary text-white";
    }
};

const repayOrder = async (orderId) => {
    try {
        const response = await axios.post(
            `${import.meta.env.VITE_URL_API}api/order/${orderId}/repay`
        );
        if (response.data.return_url) {
            window.location.href = response.data.return_url;
        }
    } catch (error) {
        console.log(error);
    }
};

const isLoadingModal = ref(false);
const openReviewModal = async (orderId) => {
    isLoadingModal.value = true;
    try {
        const res = await axios.get(
            `${import.meta.env.VITE_URL_API}api/order-info/${orderId}`,
            {
                headers: { Authorization: `Bearer ${store.token}` },
            }
        );
        const orderData = res.data.order;
        errors.value = {};
        if (orderData && orderData.order_items) {
            orderData.order_items.forEach((detail) => {
                if (!detail.review_temp) {
                    detail.review_temp = {
                        rating: 0,
                        comment: "",
                        images: [],
                    };
                }
            });
        }
        orderToReview.value = orderData;
        const reviewModal = new Modal(document.getElementById("reviewModal"));
        reviewModal.show();
    } catch (error) {
        console.log(error);
    } finally {
        isLoadingModal.value = false;
    }
};

const getRatingText = (rating) => {
    switch (rating) {
        case 1:
            return "Rất tệ";
        case 2:
            return "Tệ";
        case 3:
            return "Bình thường";
        case 4:
            return "Tốt";
        case 5:
            return "Tuyệt vời";
        default:
            return "";
    }
};

const handleImageUpload = (detail, event) => {
    detail.review_temp.images = Array.from(event.target.files);
};

const submitAllReviews = async () => {
    const reviewsToSubmit = orderToReview.value.order_items.filter(
        (detail) => detail.review_temp && detail.review_temp.rating > 0 && detail.reviewed == false
    );

    if (reviewsToSubmit.length === 0) {
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Vui lòng đánh giá ít nhất 1 sản phẩm",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
        return;
    }

    isLoadingReview.value = true;
    errors.value = {};

    const reviewPromises = reviewsToSubmit.map((detail) => {
        const formData = new FormData();
        formData.append("order_detail_id", detail.id);
        formData.append("rating", detail.review_temp.rating);
        formData.append("comment", detail.review_temp.comment);
        detail.review_temp.images.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
        });

        return axios.post(`${import.meta.env.VITE_URL_API}api/review`, formData, {
            headers: {
                Authorization: `Bearer ${store.token}`,
            },
        });
    });

    try {
        await Promise.all(reviewPromises);

        const reviewModal = Modal.getInstance(document.getElementById("reviewModal"));
        if (reviewModal) {
            reviewModal.hide();
        }

        await getOrder();

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Đánh giá thành công!",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    } catch (error) {
        console.log(error);
        if (error.response && error.response.status === 422) {
            const errs = error.response.data.errors;
            if (errs) {
                for (const key in errs) {
                    if (key.startsWith("images")) {
                        if (!errors.value.images) errors.value.images = [];
                        errors.value.images.push(...errs[key]);
                    } else {
                        errors.value[key] = errs[key];
                    }
                }
            }
        }

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Gửi đánh giá thất bại!",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
    } finally {
        isLoadingReview.value = false;
    }
};

onMounted(async () => {
    await getOrder();
});
</script>
<template>
    <section class="account-page py-5 fade-in">
        <div class="container">
            <div class="row g-4">
                <SidebarAccount></SidebarAccount>
                <div class="col-12 col-md-8 col-lg-9 bg-white p-3" style="border-radius: 10px">
                    <h4 class="fw-bold mb-4">Đơn hàng đã mua</h4>
                    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-1">
                        <button class="tab-item" :class="{ active: currentStatus === '' }"
                            @click="filterOrderByStatus('')">
                            Tất cả
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'pendingConfirmation' }"
                            @click="filterOrderByStatus('pendingConfirmation')">
                            Chờ xác nhận
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'confirmation' }"
                            @click="filterOrderByStatus('confirmation')">
                            Đã xác nhận
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'pending' }"
                            @click="filterOrderByStatus('pending')">
                            Đang xử lý
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'shipping' }"
                            @click="filterOrderByStatus('shipping')">
                            Đang giao hàng
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'done' }"
                            @click="filterOrderByStatus('done')">
                            Hoàn thành
                        </button>
                        <button class="tab-item" :class="{ active: currentStatus === 'cancel' }"
                            @click="filterOrderByStatus('cancel')">
                            Đã hủy
                        </button>
                    </div>

                    <div class="card border-0 p-0 d-none d-lg-block">
                        <table class="table" v-if="isLoading">
                            <tbody>
                                <tr v-for="n in 10" :key="n">
                                    <td>
                                        <div class="skeleton-line" style="width: 60px"></div>
                                    </td>
                                    <td>
                                        <div class="skeleton-line" style="width: 120px"></div>
                                    </td>
                                    <td>
                                        <div class="skeleton-line" style="width: 100px"></div>
                                    </td>
                                    <td>
                                        <div class="skeleton-line" style="width: 80px"></div>
                                    </td>
                                    <td>
                                        <div class="skeleton-line" style="width: 50px"></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="table-responsive-scroll" v-else>
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
                                    <tr v-for="value in orders" :key="value.id">
                                        <td>#{{ value.id }}</td>
                                        <td>{{ FormatData.formatDateTime(value.order_date) }}</td>
                                        <td>
                                            {{ FormatData.formatNumber(value.total_amount) }} VND
                                        </td>
                                        <td>
                                            <span class="badge" :class="getStatusClass(value.status)">
                                                {{ value.status }}
                                            </span>
                                        </td>
                                        <td class="d-flex gap-2">
                                            <router-link :to="`/order-history-detail/${value.id}`"
                                                class="btn btn-sm btn-outline-primary">Xem</router-link>
                                            <button class="btn btn-sm btn-outline-dark" @click="repayOrder(value.id)"
                                                v-if="
                                                    value.payment_method == 'VNPAY' &&
                                                    value.status_payments == 'Chưa thanh toán'
                                                ">
                                                Thanh toán
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning"
                                                v-if="value.status === 'Hoàn thành' && value.order_items && (value.order_items).some(item => item.reviewed == false)"
                                                @click="openReviewModal(value.id)">
                                                Đánh giá
                                            </button>
                                            <button class="btn btn-sm btn-outline-info" @click="printInvoice(order.id)">
                                                Xuất hoá đơn
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="d-lg-none">
                        <div class="order-card card border-0 shadow-sm mb-3" v-if="isLoading" v-for="n in 3" :key="n">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="skeleton-line" style="width: 120px"></div>
                                    <div class="skeleton-line" style="width: 80px"></div>
                                </div>
                                <div class="skeleton-line mb-1" style="width: 150px"></div>
                                <div class="skeleton-line mb-2" style="width: 180px"></div>
                                <div class="text-end">
                                    <div class="skeleton-line" style="width: 80px; height: 32px"></div>
                                </div>
                            </div>
                        </div>
                        <div class="order-card card border-0 shadow-sm mb-3" v-else v-for="value in orders"
                            :key="value.id">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0">Mã đơn: #{{ value.id }}</h6>
                                    <span class="badge bg-warning text-dark">{{
                                        value.status
                                    }}</span>
                                </div>
                                <p class="mb-1 text-muted">
                                    <i class="bi bi-calendar me-2"></i>{{ FormatData.formatDateTime(value.order_date) }}
                                </p>
                                <p class="mb-2 text-muted">
                                    <i class="bi bi-cash me-2"></i>Tổng tiền:
                                    <strong>{{
                                        FormatData.formatNumber(value.total_amount)
                                    }}
                                        VND</strong>
                                </p>
                                <div class="text-end">
                                    <router-link :to="`/order-history-detail/${value.id}`"
                                        class="btn btn-sm btn-outline-primary">Xem chi tiết</router-link>
                                    <button class="btn btn-sm btn-outline-dark" v-if="
                                        value.payment_method == 'VNPAY' &&
                                        value.status_payments == 'Chưa thanh toán'
                                    ">
                                        Thanh toán
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" v-if="value.status === 'Hoàn thành'"
                                        @click="openReviewModal(value.id)">
                                        Đánh giá
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div v-if="isLoadingModal" class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow" v-if="orderToReview">
                <div v-if="isLoadingReview" class="loading-overlay-modal">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold" id="reviewModalLabel">
                        Đánh giá sản phẩm đã mua
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <div class="review-item mb-4 pb-4 border-bottom" v-for="detail in orderToReview.order_items"
                        :key="detail.id">
                        <div class="d-flex align-items-start mb-3" v-if="detail.reviewed == false">
                            <img :src="detail.variant.main_image_url" alt="Product Image"
                                class="product-thumb me-3 rounded-2"
                                style="width: 80px; height: 80px; object-fit: cover" />
                            <div>
                                <h6 class="mb-1 fw-bold">{{ detail.variant.product.name }}</h6>
                                <small class="text-muted" v-for="(attribute, index) in detail.variant.attributes"
                                    :key="index">
                                    <span v-if="attribute.attribute_id == 1">
                                        Kích thước: {{ attribute.value_name }}
                                    </span>
                                </small>
                            </div>
                        </div>

                        <div class="review-form" v-if="detail.review_temp && detail.reviewed == false">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chọn số sao của bạn</label>
                                <small class="text-danger" v-if="errors.rating">{{
                                    errors.rating[0]
                                }}</small>
                                <div class="d-flex align-items-center">
                                    <i v-for="star in 5" :key="star" class="bi me-1" :class="{
                                        'bi-star-fill text-warning':
                                            star <= detail.review_temp.rating,
                                        'bi-star': star > detail.review_temp.rating,
                                    }" @click="detail.review_temp.rating = star"
                                        style="cursor: pointer; font-size: 1.5rem">
                                    </i>
                                    <span class="ms-2 fw-bold text-warning">{{
                                        getRatingText(detail.review_temp.rating)
                                    }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label :for="'review-comment-' + detail.id" class="form-label fw-bold small">Nội dung
                                    đánh giá</label><br>
                                <small class="text-danger" v-if="errors.comment">{{
                                    errors.comment[0]
                                }}</small>
                                <textarea class="form-control rounded-3" :id="'review-comment-' + detail.id" rows="3"
                                    placeholder="Chia sẻ cảm nhận của bạn về sản phẩm này"
                                    v-model="detail.review_temp.comment"></textarea>
                            </div>
                            <div class="mb-3">
                                <label :for="'review-images-' + detail.id" class="form-label fw-bold small">Thêm ảnh
                                    (Tùy chọn)</label><br>
                                <small class="text-danger" v-if="errors.images">
                                    {{ errors.images[0] }}
                                </small>
                                <input class="form-control form-control-sm" type="file"
                                    :id="'review-images-' + detail.id" multiple
                                    @change="handleImageUpload(detail, $event)" />
                                <small class="form-text text-muted">Tối đa 5 ảnh.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2 p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        Đóng
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" @click="submitAllReviews"
                        :disabled="isLoadingReview">
                        <span v-if="isLoadingReview" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <span v-if="isLoadingReview"> Đang gửi...</span>
                        <span v-else>Gửi tất cả đánh giá</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-dialog-scrollable .modal-content {
    max-height: 80vh;
    overflow-y: auto;
}

.modal-content {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    position: relative;
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    padding: 1.5rem;
}

.modal-title {
    font-weight: 600;
    font-size: 1.5rem;
    color: #333;
}

/* Review Item Section */
.review-item {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1.5rem;
}

.review-item:last-child {
    border-bottom: none;
}

.product-thumb {
    object-fit: cover;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

/* Star Rating Section */
.rating-stars i {
    color: #e0e0e0;
    font-size: 1.5rem;
    cursor: pointer;
    transition: color 0.2s ease-in-out;
}

.rating-stars i:hover,
.rating-stars i.rated {
    color: #ffc107;
    /* Màu vàng */
}

.rating-text {
    font-size: 0.9rem;
    color: #6c757d;
    font-weight: 500;
}

/* Form inputs and buttons */
.review-form .form-label {
    font-weight: 500;
    color: #495057;
    font-size: 0.9rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* Thêm CSS cho loading overlay chỉ trong modal */
.loading-overlay-modal {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1050;
    /* Z-index cao hơn nội dung modal nhưng thấp hơn modal backdrop */
    border-radius: 0.5rem;
}

.review-form .form-control {
    border-radius: 8px;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
}

.btn-primary {
    background-color: #0d6efd;
    border-color: #0d6efd;
    font-weight: 500;
    transition: all 0.2s ease-in-out;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    font-weight: 500;
}

.skeleton-line {
    background-color: #e2e5e7;
    height: 1em;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line.mb-1 {
    margin-bottom: 0.5rem;
}

.skeleton-line.mb-2 {
    margin-bottom: 1rem;
}

@keyframes pulse {
    0% {
        background-color: #e2e5e7;
    }

    50% {
        background-color: #c9cbcc;
    }

    100% {
        background-color: #e2e5e7;
    }
}

.table-responsive-scroll {
    max-height: 300px;
    /* Đặt chiều cao tối đa mà bạn muốn */
    overflow-y: auto;
    /* Thêm thanh cuộn dọc khi nội dung tràn ra */
    display: block;
    /* Quan trọng để cuộn hoạt động */
    width: 100%;
    /* Đảm bảo div chiếm toàn bộ chiều rộng */
}

.order-tabs {
    position: relative;
    border-bottom: 2px solid #e0e0e0;
}

.tab-item {
    background: none;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    font-size: 16px;
    color: #333;
    outline: none;
    transition: color 0.3s ease;
    position: relative;
    /* Thêm position relative để gạch dưới con hoạt động */
}

.tab-item:hover {
    color: #1e88e5;
}

.tab-item.active {
    color: #1e88e5;
}

.tab-item.active::after {
    content: "";
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background-color: #1e88e5;
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