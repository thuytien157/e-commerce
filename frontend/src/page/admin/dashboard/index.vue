<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Thống kê</h2>
        <a class="btn btn-dark" href="#">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
        <a class="btn btn-dark" href="#">
            <i class="bi bi-box-arrow-right"></i> Chuyển lại web
        </a>
    </div>

    <form class="row g-3 align-items-end mb-3">
        <div class="col-md-3">
            <label for="date_from" class="form-label">Từ ngày:</label>
            <input type="date" id="date_from" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="date_to" class="form-label">Đến ngày:</label>
            <input type="date" id="date_to" class="form-control">
        </div>
        <div class="col-md-3">
            <label for="group_by" class="form-label">Thống kê theo:</label>
            <select id="group_by" class="form-select">
                <option value="day">Ngày</option>
                <option value="month">Tháng</option>
                <option value="year">Năm</option>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-50 loc">Lọc</button>
        </div>
    </form>

    <div class="row">
        <div class="col-lg-6 col-md-12 col-12">
            <canvas id="barChart"></canvas>
        </div>
        <div class="col-lg-6 col-md-12 col-12">
            <canvas id="pieChart"></canvas>
        </div>
    </div>

    <hr>

    <div class="row thongke">
        <div class="col-lg-6 col-md-12 col-12">
            <div class="card shadow-sm">
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
                            <tr v-for="(product, index) in topSellingProducts" :key="index">
                                <td>{{ product.id }}</td>
                                <td>{{ product.name }}</td>
                                <td class="text-center">{{ product.total_sold }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-12 col-12">
            <div class="card shadow-sm">
                <div class="card-header">Số lượng đơn hàng theo trạng thái</div>
                <div class="card-body table-wrapper-scroll-y my-custom-scrollbar">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Trạng thái</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(status, index) in orderStatusStats" :key="index">
                                <td>{{ index + 1 }}</td>
                                <td>{{ status.status }}</td>
                                <td>{{ status.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted } from "vue";
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
    setup() {

        onMounted(() => {
            const barCtx = document.getElementById("barChart").getContext("2d");
            new Chart(barCtx, {
                type: "bar",
                data: {
                    labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5"],
                    datasets: [{
                        label: "Sản phẩm bán ra",
                        data: [120, 190, 300, 250, 220],
                        backgroundColor: "#c53f51",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            const pieCtx = document.getElementById("pieChart").getContext("2d");
            new Chart(pieCtx, {
                type: "pie",
                data: {
                    labels: ["Đã giao", "Đang xử lý", "Đã hủy"],
                    datasets: [{
                        data: [60, 25, 15],
                        backgroundColor: ["#28a745", "#ffc107", "#dc3545"]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    }
};
</script>

<style scoped>
.loc {
    background-color: #c53f51;
    border: none;
}

canvas {
    width: 100%;
    height: 300px;
}
</style>