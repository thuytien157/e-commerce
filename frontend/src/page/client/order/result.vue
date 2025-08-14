<template>
    <div class="payment-result-wrapper">
        <!-- Loading -->
        <div v-if="isLoading" class="result-section loading">
            <div class="spinner"></div>
            <p class="loading-text">Đang xác minh kết quả thanh toán...</p>
        </div>

        <!-- Thành công -->
        <div v-else-if="isSuccess" class="result-section success">
            <div class="icon-box">
                <i class="fa-solid fa-circle-check success-icon"></i>
            </div>
            <h2 class="result-title">{{ paymentMessage }}</h2>
            <p class="result-message">Chúng tôi đã nhận được đơn hàng và sẽ sớm liên hệ với bạn.</p>

            <div class="order-summary-box">
                <div class="summary-details">
                    <div class="detail-row">
                        <span class="detail-label">Mã đơn hàng:</span>
                        <span class="detail-value">#{{ orderInfo.id }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ngày đặt:</span>
                        <span class="detail-value">{{ formatDateTime(orderInfo.order_date)
                        }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Tổng cộng:</span>
                        <span class="detail-value">{{ formatNumber(orderInfo.total_amount) }} VNĐ</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phương thức:</span>
                        <span class="detail-value">{{ methodLabel }}</span>
                    </div>
                </div>
            </div>

            <div class="action-buttons">
                <router-link to="/" class="btn btn-outline-primary">Về trang chủ</router-link>
                <router-link to="/order-history" class="btn btn-outline-dark">Lịch sử mua hàng</router-link>
                <router-link :to="`/order-history-detail/${orderInfo.id}`" class="btn btn-outline-secondary">
                    Xem chi tiết đơn hàng
                </router-link>
            </div>
        </div>

        <!-- Thất bại -->
        <div v-else class="result-section error">
            <div class="icon-box">
                <i class="fa-solid fa-circle-xmark error-icon"></i>
            </div>
            <h2 class="result-title">Thanh toán thất bại</h2>
            <p class="result-message">
                {{ backendMessage || 'Thanh toán thất bại hoặc đơn bị hủy trong lúc giao dịch.' }}
            </p>
            <div class="action-buttons">
                <router-link to="/" class="btn primary-btn">Về trang chủ</router-link>
                <button class="btn btn-outline-primary" type="button" @click="repayOrder(orderInfo.id)">Thanh toán
                    lại</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import axios from "axios";
import FormatData from "@/component/store/FormatData";


const route = useRoute();
const orderId = ref(route.params.id);

const orderInfo = ref({});
const paymentStatus = ref("");
const isLoading = ref(true);
const backendMessage = ref("");
const methodLabel = ref("");
const paymentMessage = ref("");

const { formatDateTime, formatNumber } = FormatData;

const isSuccess = computed(() => paymentStatus.value === "Đã thanh toán");

const fetchOrderStatus = async () => {
    if (!orderId.value) {
        backendMessage.value = "Không tìm thấy mã đơn hàng.";
        isLoading.value = false;
        return;
    }
    try {
        const res = await axios.get(`http://127.0.0.1:8000/api/order-info/${orderId.value}`);
        orderInfo.value = res.data.order || {};
        paymentStatus.value = res.data.order?.status_payments || "";
        methodLabel.value = res.data.order?.payment_method || "Không xác định";
        paymentMessage.value = res.data.message || "Cảm ơn bạn đã mua hàng!";
    } catch (err) {
        backendMessage.value = "Không thể tải thông tin đơn hàng.";
    } finally {
        isLoading.value = false;
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
onMounted(fetchOrderStatus);
</script>

<style scoped>
.payment-result-wrapper {
    display: flex;
    justify-content: center;
    padding: 3rem 1rem;
}

.result-section {
    width: 100%;
    max-width: 900px;
    text-align: center;
    background: #fff;
    padding: 3rem;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    animation: fadeIn 0.5s ease-in-out;
}

.success {
    border-top: 6px solid #2ecc71;
}

.error {
    border-top: 6px solid #e74c3c;
}

.icon-box {
    margin-bottom: 1.5rem;
}

.success-icon {
    font-size: 5rem;
    background: linear-gradient(90deg, #2ecc71, #27ae60);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.error-icon {
    font-size: 5rem;
    background: linear-gradient(90deg, #e74c3c, #c0392b);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.result-title {
    font-size: 2.3rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.result-message {
    font-size: 1.2rem;
    color: #555;
    margin-bottom: 2rem;
}

.order-summary-box {
    background-color: #fdfdfd;
    border-radius: 10px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #eee;
}

.summary-details .detail-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
}

.detail-label {
    color: #777;
}

.detail-value {
    font-weight: 600;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

.btn {
    padding: 0.9rem 2rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    text-decoration: none;
    transition: all 0.3s ease;
}

.primary-btn {
    background: blue;
    color: #fff;
}

.secondary-btn {
    background-color: #fff;
    color: #333;
    border: 1px solid #ccc;
}

.secondary-btn:hover {
    background-color: #f5f5f5;
    transform: translateY(-2px);
}

.spinner {
    border: 4px solid rgba(0, 0, 0, 0.1);
    border-left-color: #3498db;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto 1rem;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.loading-text {
    font-size: 1.1rem;
    color: #777;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
