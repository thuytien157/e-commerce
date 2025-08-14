<script setup>
import SidebarAccount from "@/component/SidebarAccount.vue";
import FormatData from "@/component/store/FormatData";
import { useTokenUser } from "@/component/store/useTokenUser";
import axios from "axios";
import { onMounted, ref } from "vue";
const store = useTokenUser();
const orders = ref(null);
const isLoading = ref(true);
const getOrder = async () => {
    isLoading.value = true;
    try {
        const res = await axios.get(
            "http://127.0.0.1:8000/api/order-history-user",
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
const currentStatus = ref("pendingConfirmation");
const filterOrderByStatus = async (status) => {
    currentStatus.value = status;
    isLoading.value = true;
    try {
        const res = await axios.get(
            `http://127.0.0.1:8000/api/order-history-user?status=${status}`,
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
        const response = await axios.post(`http://127.0.0.1:8000/api/order/${orderId}/repay`);
        if (response.data.return_url) {
            window.location.href = response.data.return_url;
        }
    } catch (error) {
        console.log(error);

    }
}
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
                    <!-- Tabs -->
                    <div class="order-tabs d-flex flex-nowrap overflow-auto gap-3 mb-1">
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

                    <!-- Table for desktop -->
                    <div class="card border-0 p-0 d-none d-md-block">
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
                                                href="/history-order-detail/123456"
                                                class="btn btn-sm btn-outline-primary">Xem</router-link>
                                            <button class="btn btn-sm btn-outline-dark" @click="repayOrder(value.id)"
                                                v-if="value.payment_method == 'VNPAY' && value.status_payments == 'Chưa thanh toán'">Thanh
                                                toán</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Cards for mobile -->
                    <div class="d-md-none">
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
                                    <button class="btn btn-sm btn-outline-dark"
                                        v-if="value.payment_method == 'VNPAY' && value.status_payments == 'Chưa thanh toán'">Thanh
                                        toán</button>
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
