<script setup>
import { ref } from "vue";
import SidebarAccount from "@/component/client/SidebarAccount.vue";
import axios from "axios";

const address = ref([]);
const isLoading = ref(true);
const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);
const ghnToken = import.meta.env.VITE_GHN_TOKEN;

const fetchProvinces = async () => {
    try {
        const res = await axios.get(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/province",
            { headers: { Token: ghnToken } }
        );
        provinces.value = res.data.data || [];
    } catch (err) {
        console.error("fetchProvinces error:", err);
    }
};

const fetchDistricts = async (provinceId) => {
    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/district",
            { province_id: provinceId },
            { headers: { Token: ghnToken } }
        );
        districts.value = res.data.data || [];
    } catch (err) {
        console.error("fetchDistricts error:", err);
    }
};

const fetchWards = async (districtId) => {
    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward",
            { district_id: districtId },
            { headers: { Token: ghnToken } }
        );
        wards.value = res.data.data;
        // console.log(res.data.data);

    } catch (err) {
        console.error("fetchWards error:", err);
    }
};

const handleUserData = async (data) => {
    if (data && data.address) {
        address.value = data.address;
        isLoading.value = true;

        await fetchProvinces();

        for (let addr of address.value) {
            await fetchDistricts(addr.province_id);
            await fetchWards(addr.district_id);

            const provinceName = provinces.value.find(p => p.ProvinceID === addr.province_id)?.ProvinceName || '';
            const districtName = districts.value.find(d => d.DistrictID === addr.district_id)?.DistrictName || '';
            const wardName = wards.value.find(w => w.WardCode === String(addr.ward_code))?.WardName || '';
            addr.fullAddress = `${addr.address || ''}, ${wardName}, ${districtName}, ${provinceName}`;
        }
    } else {
        console.error('Lỗi: Dữ liệu "address" không tồn tại');
    }

    isLoading.value = false;
};
</script>

<template>
    <section class="account-page py-5 fade-in">
        <div class="container">
            <div class="row g-4">
                <SidebarAccount @user-loaded="handleUserData"></SidebarAccount>

                <div class="col-12 col-md-8 col-lg-9">
                    <div class="card shadow-sm border-0 p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                            <h4 class="fw-bold m-0 mb-3 mb-md-0">Địa chỉ của tôi</h4>
                        </div>
                        <hr />

                        <!-- Loading -->
                        <div v-if="isLoading">
                            <div class="card p-3 mb-3 border-light bg-light" v-for="n in 2" :key="n">
                                <div class="skeleton-line w-75 mb-2"></div>
                                <div class="skeleton-line w-50"></div>
                            </div>
                        </div>

                        <!-- Danh sách địa chỉ -->
                        <div v-else>
                            <div class="card p-3 mb-3 border-primary bg-light" v-for="value in address" :key="value.id">
                                <div class="d-flex justify-content-between align-items-start flex-column flex-sm-row">
                                    <div class="mb-2 mb-sm-0">
                                        <h6 class="fw-bold m-0 d-inline-block">
                                            {{ value.customer_name }}
                                        </h6>
                                        <span v-if="value.default" class="badge bg-primary ms-2">Mặc định</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <router-link :to="`/account/address-edit/${value.id}`"
                                            class="btn btn-sm btn-outline-secondary">
                                            Sửa
                                        </router-link>
                                    </div>
                                </div>
                                <p class="text-muted mb-1">Điện thoại: +84{{ value.phone }}</p>
                                <p class="text-muted m-0">Địa chỉ: {{ value.fullAddress }}</p>
                            </div>
                            <router-link to="/account/address-new" class="btn btn-primary w-100 w-md-auto">
                                <i class="bi bi-plus-lg me-2"></i>Thêm địa chỉ mới
                            </router-link>
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
    border-radius: 4px;
    height: 1rem;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line.w-75 {
    width: 75%;
}

.skeleton-line.w-50 {
    width: 50%;
}

@keyframes pulse {
    0% {
        background-color: #e2e5e7;
    }

    50% {
        background-color: #f2f3f5;
    }

    100% {
        background-color: #e2e5e7;
    }
}

:root {
    --primary-color: #ca111f;
    --secondary-color: #f8f9fa;
    --text-color: #495057;
    --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
}

.account-page {
    background-color: #f0f2f5;
    /* Màu nền nhẹ nhàng hơn */
}

.card {
    border-radius: 12px;
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
