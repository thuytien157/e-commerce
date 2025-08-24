<template>
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h2 class="text-primary">Thống kê</h2>
            <div class="mt-2 mt-lg-0">
                <button @click="logout" class="btn btn-primary me-2" href="#"><i class="bi bi-box-arrow-right"></i> Đăng
                    xuất</button>
                <router-link to="/home" class="btn btn-primary" href="#"><i class="bi bi-house"></i> Chuyển lại
                    web</router-link>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <span>Doanh thu</span>
                        <div class="d-flex mt-2 mt-lg-0">
                            <input type="date" v-model="revenueFrom" class="form-control form-control-sm me-2" />
                            <input type="date" v-model="revenueTo" class="form-control form-control-sm me-2" />
                            <button @click.prevent="fetchRevenue" class="btn btn-primary btn-sm">
                                Lọc
                            </button>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px">
                        <div v-show="loadingRevenue" class="skeleton-chart"></div>
                        <canvas v-show="!loadingRevenue" ref="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <span>Trạng thái đơn hàng</span>
                        <div class="d-flex mt-2 mt-lg-0">
                            <input type="date" v-model="statusFrom" class="form-control form-control-sm me-2" />
                            <input type="date" v-model="statusTo" class="form-control form-control-sm me-2" />
                            <button @click.prevent="fetchStatus" class="btn btn-primary btn-sm">
                                Lọc
                            </button>
                        </div>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px">
                        <div v-show="loadingStatus" class="skeleton-chart"></div>
                        <canvas v-show="!loadingStatus" ref="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Tỷ lệ đánh giá so với đơn hàng</div>
                    <div class="card-body d-flex justify-content-center align-items-center" style="min-height: 300px">
                        <div v-show="loadingOrderReview" class="skeleton-chart"></div>
                        <canvas v-show="!loadingOrderReview" ref="orderReviewChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Top khách hàng</div>
                    <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên KH</th>
                                    <th>Tổng chi tiêu</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loadingTopCustomers">
                                    <td colspan="3">
                                        <div class="skeleton-row"></div>
                                    </td>
                                </tr>
                                <tr v-for="value in topCustomers" :key="value.id" v-else>
                                    <td>{{ value.id }}</td>
                                    <td>{{ value.username }}</td>
                                    <td>{{ FormatData.formatNumber(value.total_amount) }} VND</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Top 5 sản phẩm bán chạy</div>
                    <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng bán</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loadingBestSellers">
                                    <td colspan="3">
                                        <div class="skeleton-row"></div>
                                    </td>
                                </tr>
                                <tr v-for="product in transformedBestSellers" :key="product.id" v-else>
                                    <td>{{ product.id }}</td>
                                    <td class="text-truncate" :title="product.name">
                                        {{ product.name }}
                                    </td>
                                    <td>{{ product.order_items_sum_quantity }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 col-12 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Tồn kho & cảnh báo</div>
                    <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sản phẩm</th>
                                    <th>SKU</th>
                                    <th>Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="loadingSoldOuts">
                                    <td colspan="4">
                                        <div class="skeleton-row"></div>
                                    </td>
                                </tr>
                                <tr v-for="product in transformedSoldOuts" :key="product.id" v-else>
                                    <td>{{ product.id }}</td>
                                    <td class="text-truncate" :title="product.name">
                                        {{ product.name }}
                                    </td>
                                    <td>
                                        {{product.variants.map((v) => v.sku).join(", ")}}
                                    </td>
                                    <td>
                                        {{
                                            product.variants.map((v) => v.stock_quantity).join(", ")
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Chart, registerables } from "chart.js";
import axios from "axios";
import FormatData from "@/component/store/FormatData";
import { nextTick } from "vue";
import Swal from "sweetalert2";
import { useTokenUser } from "@/component/store/useTokenUser";
import { useRouter } from "vue-router";
const store = useTokenUser();
const router = useRouter()
const logout = async () => {
    try {
        await axios.get(`${import.meta.env.VITE_URL_API}api/logout`, {
            headers: {
                Authorization: `Bearer ${store.token}`,
            },
        });

        store.clearAuth();
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Đăng xuất thành công!",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
        router.push('/home')
    } catch (error) {
        console.error(error);
        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "error",
            title: "Đăng xuất thất bại",
            showConfirmButton: false,
            timer: 1000,
            timerProgressBar: true,
        });
    }
};

Chart.register(...registerables);
const loadingRevenue = ref(true);
const loadingStatus = ref(true);
const loadingOrderReview = ref(true);
const loadingTopCustomers = ref(true);
const loadingBestSellers = ref(true);
const loadingSoldOuts = ref(true);
const revenueFrom = ref("");
const revenueTo = ref("");
const revenueChart = ref(null);
let revenueChartInstance = null;

const fetchRevenue = async () => {
    loadingRevenue.value = true;
    const params = new URLSearchParams();
    if (revenueFrom.value) params.append("from", revenueFrom.value);
    if (revenueTo.value) params.append("to", revenueTo.value);

    const res = await fetch(
        `${import.meta.env.VITE_URL_API}api/revenue?` + params.toString()
    );
    const data = await res.json();

    const labels = [];
    const revenues = [];

    data.revenue.forEach((item) => {
        labels.push(item.label);
        revenues.push(item.revenue);
    });

    if (revenueChartInstance) {
        revenueChartInstance.destroy();
    }

    await nextTick();
    if (revenueChart.value) {
        const ctx = revenueChart.value.getContext("2d");
        revenueChartInstance = new Chart(ctx, {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        label: "Doanh thu (VND)",
                        data: revenues,
                        backgroundColor: "blue",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 80000000,
                        ticks: {
                            stepSize: 10000000,
                            callback: (value) => value.toLocaleString() + " VND",
                        },
                    },
                },
            },
        });
    }
    loadingRevenue.value = false;
};

const statusFrom = ref("");
const statusTo = ref("");
const statusChart = ref(null);
let statusChartInstance = null;

const fetchStatus = async () => {
    loadingStatus.value = true;
    const params = new URLSearchParams();
    if (statusFrom.value) params.append("from", statusFrom.value);
    if (statusTo.value) params.append("to", statusTo.value);

    const res = await fetch(
        `${import.meta.env.VITE_URL_API}api/status-order?` + params.toString()
    );
    const data = await res.json();

    const labels = data.order.map((item) => item.status);
    const orders = data.order.map((item) => item.total);

    if (statusChartInstance) statusChartInstance.destroy();

    await nextTick();
    if (statusChart.value) {
        const ctx = statusChart.value.getContext("2d");
        statusChartInstance = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: orders,
                        backgroundColor: ["#008c8f", "#3a5685", "blue", "#042d64"],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
            },
        });
    }

    loadingStatus.value = false;
};

const orderReviewChart = ref(null);
let orderReviewChartInstance = null;

const fetchOrderReview = async () => {
    loadingOrderReview.value = true;
    try {
        const res = await axios.get(`${import.meta.env.VITE_URL_API}api/order-review`);
        const data = res.data;

        const labels = ["Đã đánh giá", "Chưa đánh giá"];
        const values = [
            data.reviewed_orders,
            data.total_orders - data.reviewed_orders,
        ];
        const percentages = values.map(
            (v) => ((v / data.total_orders) * 100).toFixed(1) + "%"
        );

        if (orderReviewChartInstance) orderReviewChartInstance.destroy();

        await nextTick()
        if (orderReviewChart.value) {
            const ctx = orderReviewChart.value.getContext("2d");
            orderReviewChartInstance = new Chart(ctx, {
                type: "pie",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: ["blue", "#042d64"],
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.label + ": " + percentages[context.dataIndex];
                                    },
                                },
                            },
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        datalabels: {
                            formatter: (value, context) => {
                                const total = context.chart.data.datasets[0].data.reduce(
                                    (a, b) => a + b,
                                    0
                                );
                                return ((value / total) * 100).toFixed(0) + "%";
                            },
                            color: "#fff",
                            font: { weight: "bold", size: 14 },
                        },
                    },
                },
            });
        }

    } catch (error) {
        console.error(error);
    } finally {
        loadingOrderReview.value = false;
    }
};

const transformedBestSellers = ref([]);
const fetchProductSelling = async () => {
    loadingBestSellers.value = true;
    try {
        const res = await axios.get(`${import.meta.env.VITE_URL_API}api/product-selling`);
        transformedBestSellers.value = res.data.transformedBestSellers;
    } catch (error) {
        console.log(error);
    } finally {
        loadingBestSellers.value = false;
    }
};

const transformedSoldOuts = ref([]);
const fetchProductSoldOut = async () => {
    loadingSoldOuts.value = true;
    try {
        const res = await axios.get(`${import.meta.env.VITE_URL_API}api/product-soldout`);
        transformedSoldOuts.value = res.data.transformedSoldOuts;
    } catch (error) {
        console.log(error);
    } finally {
        loadingSoldOuts.value = false;
    }
};

const topCustomers = ref([]);
const fetchTopCustomers = async () => {
    loadingTopCustomers.value = true;
    try {
        const res = await axios.get(`${import.meta.env.VITE_URL_API}api/top-customer`);
        topCustomers.value = res.data.customers;
    } catch (error) {
        console.log(error);
    } finally {
        loadingTopCustomers.value = false;
    }
};

onMounted(() => {
    fetchRevenue();
    fetchStatus();
    fetchProductSelling();
    fetchProductSoldOut();
    fetchTopCustomers();
    fetchOrderReview();
});
</script>

<style scoped>
canvas {
    width: 100%;
    height: 300px;
}

.text-truncate {
    max-width: 150px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.my-custom-scrollbar {
    max-height: 330px;
    min-height: 330px;
    overflow-y: auto;
}

.skeleton-chart {
    width: 100%;
    height: 300px;
    background: linear-gradient(-90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 400% 400%;
    animation: pulse 1.5s ease-in-out infinite;
    border-radius: 5px;
}

.skeleton-row {
    height: 30px;
    background: linear-gradient(-90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 400% 400%;
    animation: pulse 1.5s ease-in-out infinite;
    border-radius: 5px;
}

@keyframes pulse {
    0% {
        background-position: 100% 0;
    }

    100% {
        background-position: -100% 0;
    }
}

.my-custom-scrollbar {
    max-height: 330px;
    overflow-y: auto;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
