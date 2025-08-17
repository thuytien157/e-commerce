<template>
    <section class="account-page py-5 fade-in">
        <div class="container">
            <div class="row g-4">
                <SidebarAccount></SidebarAccount>

                <div class="col-12 col-md-8 col-lg-9">
                    <div v-if="isEditMode && loading" class="card shadow-sm border-0 p-4">
                        <div class="skeleton-line w-50 mb-4 mx-auto"></div>
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-md-6">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-12">
                                <div class="skeleton-line w-25 mb-2"></div>
                                <div class="skeleton-input"></div>
                            </div>
                            <div class="col-12 mt-4">
                                <div class="skeleton-button"></div>
                            </div>
                        </div>
                    </div>


                    <div class="card shadow-sm border-0 p-4" v-else>
                        <h4 class="mb-4 fw-bold text-center text-md-start">
                            {{ isEditMode ? 'Sửa địa chỉ' : 'Thêm địa chỉ' }}
                        </h4>
                        <form @submit.prevent="saveAddress">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="fullname" class="form-label">Họ và tên</label>

                                    <input type="text" class="form-control" id="fullname" placeholder="Nhập họ và tên"
                                        v-model="customer_name" />
                                    <small class="text-danger" v-if="errors.customer_name">{{
                                        errors.customer_name[0]
                                    }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="default-address" class="form-label">Đặt làm địa chỉ mặc định</label>

                                    <select v-model="isDefault" class="form-control" id="default-address">
                                        <option :value="true">Có</option>
                                        <option :value="false">Không</option>
                                    </select>
                                    <small class="text-danger" v-if="errors.default">{{
                                        errors.default[0]
                                    }}</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại</label>

                                    <input type="tel" class="form-control" id="phone" placeholder="Nhập số điện thoại"
                                        v-model="phone" />
                                    <small class="text-danger" v-if="errors.phone">{{
                                        errors.phone[0]
                                    }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label for="province-select" class="form-label">Tỉnh / Thành phố</label>

                                    <select v-model="selectedProvince" @change="fetchDistricts" class="form-control"
                                        id="province-select">
                                        <option value="">Tỉnh / Thành phố</option>
                                        <option v-for="province in provinces" :key="province.ProvinceID"
                                            :value="province.ProvinceID">
                                            {{ province.ProvinceName }}
                                        </option>
                                    </select>
                                    <small class="text-danger" v-if="errors.province_id">{{
                                        errors.province_id[0]
                                    }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="district-select" class="form-label">Quận / Huyện</label>

                                    <select v-model="selectedDistrict" @change="fetchWards"
                                        :disabled="!selectedProvince" class="form-control" id="district-select">
                                        <option value="">Quận / Huyện</option>
                                        <option v-for="district in districts" :key="district.DistrictID"
                                            :value="district.DistrictID">
                                            {{ district.DistrictName }}
                                        </option>
                                    </select>
                                    <small class="text-danger" v-if="errors.district_id">{{
                                        errors.district_id[0]
                                    }}</small>
                                </div>
                                <div class="col-md-4">
                                    <label for="ward-select" class="form-label">Phường / Xã</label>

                                    <select v-model="selectedWard" :disabled="!selectedDistrict" class="form-control"
                                        id="ward-select">
                                        <option value="">Phường / Xã</option>
                                        <option v-for="ward in wards" :key="ward.WardCode" :value="ward.WardCode">
                                            {{ ward.WardName }}
                                        </option>
                                    </select>
                                    <small class="text-danger" v-if="errors.ward_code">{{
                                        errors.ward_code[0]
                                    }}</small>
                                </div>

                                <div class="col-12">
                                    <label for="street" class="form-label">Số nhà, tên đường</label>

                                    <input type="text" class="form-control" id="street"
                                        placeholder="Ví dụ: Số 12, đường Nguyễn Trãi" v-model="street" />
                                    <small class="text-danger" v-if="errors.address">{{
                                        errors.address[0]
                                    }}</small>
                                </div>

                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isEditMode ? 'Cập nhật địa chỉ' : 'Lưu địa chỉ' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref, onMounted, watch, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import Swal from "sweetalert2";
import SidebarAccount from "@/component/client/SidebarAccount.vue";
import { useTokenUser } from "@/component/store/useTokenUser";

const router = useRouter();

const customer_name = ref("");
const phone = ref("");
const street = ref("");
const isDefault = ref(false);
const errors = ref({});
const loading = ref(false);
const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);

const selectedProvince = ref(null);
const selectedDistrict = ref(null);
const selectedWard = ref(null);

const props = defineProps({
    addressId: {
        type: [String, Number],
        default: null,
    },
});

const isEditMode = computed(() => !!props.addressId);
// console.log(props.addressId);


const ghnToken = import.meta.env.VITE_GHN_TOKEN;
const store = useTokenUser();

const fetchProvinces = async () => {
    try {
        const res = await axios.get(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/province",
            { headers: { Token: ghnToken } }
        );
        provinces.value = res.data.data;
    } catch (err) {
        console.error("fetchProvinces error:", err);
    }
};

const fetchDistricts = async (provinceId = selectedProvince.value) => {
    selectedDistrict.value = null;
    selectedWard.value = null;
    districts.value = [];
    wards.value = [];

    if (!provinceId) return;

    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/district",
            { province_id: provinceId },
            { headers: { Token: ghnToken } }
        );
        districts.value = res.data.data;
    } catch (err) {
        console.error("fetchDistricts error:", err);
    }
};

const fetchWards = async (districtId = selectedDistrict.value) => {
    selectedWard.value = null;
    wards.value = [];

    if (!districtId) return;

    try {
        const res = await axios.post(
            "https://online-gateway.ghn.vn/shiip/public-api/master-data/ward",
            { district_id: districtId },
            { headers: { Token: ghnToken } }
        );
        wards.value = res.data.data;
    } catch (err) {
        console.error("fetchWards error:", err);
    }
};

const fetchAddressForEdit = async (addressId) => {
    if (!isEditMode.value) return;

    loading.value = true;

    try {
        const res = await axios.get(
            `http://127.0.0.1:8000/api/addresses/${addressId}`,
            {
                headers: { Authorization: `Bearer ${store.token}` }
            }
        );
        const addressData = res.data.address;
        customer_name.value = addressData.customer_name;
        phone.value = addressData.phone;
        isDefault.value = addressData.default;

        selectedProvince.value = addressData.province_id;
        await fetchDistricts();
        selectedDistrict.value = addressData.district_id;
        await fetchWards();
        selectedWard.value = addressData.ward_code;
        street.value = addressData.address;
    } catch (error) {
        console.error("Lỗi khi lấy địa chỉ để sửa:", error);
    } finally {
        loading.value = false;
    }
};

const saveAddress = async () => {

    const payload = {
        customer_name: customer_name.value,
        phone: phone.value,
        address: street.value,
        default: isDefault.value,
        province_id: selectedProvince.value,
        district_id: selectedDistrict.value,
        ward_code: selectedWard.value,
        street: street.value,
    };

    try {
        if (isEditMode.value) {
            await axios.put(
                `http://127.0.0.1:8000/api/edit-address/${props.addressId}`,
                payload,
                { headers: { Authorization: `Bearer ${store.token}` } }
            );
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Cập nhật địa chỉ thành công!",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
            });
        } else {
            await axios.post(
                "http://127.0.0.1:8000/api/insert-address",
                payload,
                { headers: { Authorization: `Bearer ${store.token}` } }
            );
            Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Thêm địa chỉ thành công!",
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true,
            });
        }

        router.push('/account/address-list');
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        } else if (error.response && error.response.status === 404) {
            errors.value = {};
            errors.value = {
                email: [error.response.data.message],
            };
        }

    }
};

watch(selectedProvince, (newVal) => {
    if (newVal) fetchDistricts(newVal);
});
watch(selectedDistrict, (newVal) => {
    if (newVal) fetchWards(newVal);
});

onMounted(() => {
    fetchProvinces();
    fetchAddressForEdit(props.addressId);
});
</script>

<style scoped>
.skeleton-line {
    background-color: #e2e5e7;
    border-radius: 4px;
    height: 1rem;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-line.w-50 {
    width: 50%;
}

.skeleton-line.w-25 {
    width: 25%;
}

.skeleton-input {
    height: 2.5rem;
    /* Chiều cao tương đương với form-control */
    background-color: #e2e5e7;
    border-radius: 8px;
    animation: pulse 1.5s infinite ease-in-out;
}

.skeleton-button {
    height: 2.5rem;
    width: 150px;
    background-color: var(--primary-color);
    border-radius: 8px;
    opacity: 0.6;
    animation: pulse 1.5s infinite ease-in-out;
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
    --primary-color: #3866bc;
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
    box-shadow: 0 0 0 0.25rem rgba(17, 66, 202, 0.25);
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
