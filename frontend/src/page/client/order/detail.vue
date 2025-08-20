<script setup>
import FormatData from "@/component/store/FormatData";
import axios from "axios";
import Swal from "sweetalert2";
import { computed, onMounted, ref, watch } from "vue";

const props = defineProps({
    orderId: {
        type: [String, Number],
        default: null,
    },
});

const order = ref(null);
const isLoading = ref(true);

const getOrderbyId = async (orderId) => {
    try {
        isLoading.value = true;
        const res = await axios.get(
            `http://127.0.0.1:8000/api/order-info/${orderId}`
        );
        order.value = res.data.order;
        // console.log(order.value);
    } catch (error) {
        console.log(error);
        order.value = null;
    } finally {
        isLoading.value = false;
    }
};

const getVariantAttributes = (attributes) => {
    if (!attributes || attributes.length === 0) {
        return "N/A";
    }
    const sizeAttribute = attributes.find((attr) => attr.attribute_id === 1);
    return sizeAttribute ? sizeAttribute.value_name : "N/A";
};
const getStatusClass = (status) => {
    switch (status) {
        case "Chờ xác nhận":
            return "text-dark";
        case "Đã xác nhận":
            return "text-info";
        case "Đang xử lý":
            return "text-primary";
        case "Đang giao hàng":
            return "text-warning";
        case "Hoàn thành":
            return "text-success";
        case "Đã hủy":
        case "Thất bại":
            return "text-danger";
        default:
            return "text-secondary";
    }
};
const cancellation_reason = ref('Tôi không muốn mua nữa')
const check = ref(false)
const errors = ref({})
const cancelOrder = async (orderId) => {
    try {
        await axios.put("http://127.0.0.1:8000/api/cancel-order", {
            id: orderId,
            cancellation_reason: cancellation_reason.value
        });

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Huỷ đơn thành công",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
        await getOrderbyId(props.orderId);
    } catch (error) {
        if (error.response && error.response.status === 422) {
            errors.value = {};
            errors.value = error.response.data.errors;
        }
    }
};
const ghnToken = import.meta.env.VITE_GHN_TOKEN;

const provinces = ref([]);
const districts = ref([]);
const wards = ref([]);

const selectedProvince = ref(null);
const selectedDistrict = ref(null);
const selectedWard = ref(null);
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
const address = ref('')
const newAddress = computed(() => {
    const provinceName = provinces.value.find(p => p.ProvinceID === selectedProvince.value)?.ProvinceName || '';
    const districtName = districts.value.find(d => d.DistrictID === selectedDistrict.value)?.DistrictName || '';
    const wardName = wards.value.find(w => w.WardCode === selectedWard.value)?.WardName || '';

    return [address.value, wardName, districtName, provinceName].filter(Boolean).join(', ');
});

const updateAddressOrder = async (orderId) => {
    try {
        await axios.put("http://127.0.0.1:8000/api/update-order-address", {
            id: orderId,
            address: newAddress.value,
            provinces: selectedProvince.value,
            districts: selectedDistrict.value,
            wards: selectedWard.value,
        });

        Swal.fire({
            toast: true,
            position: "top-end",
            icon: "success",
            title: "Cập nhật địa chỉ thành công",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });

        errors.value = {};
        await getOrderbyId(props.orderId);

    } catch (error) {
        if (error.response && error.response.status === 422) {
            console.log("Validation errors:", error.response.data.errors); // Debug
            errors.value = error.response.data.errors;
        }
    }
};


watch(selectedProvince, (newVal) => {
    if (newVal) fetchDistricts(newVal);
});
watch(selectedDistrict, (newVal) => {
    if (newVal) fetchWards(newVal);
});

onMounted(async () => {
    await getOrderbyId(props.orderId);
    fetchProvinces();
});

</script>

<template>
    <div class="container py-4">

        <div v-if="isLoading">
            <div class="card p-3 shadow-sm mb-4">
                <div class="skeleton-line-title mb-3"></div>
                <div class="row">
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                </div>
            </div>

            <div class="card p-3 shadow-sm mb-4">
                <div class="skeleton-line-title mb-3"></div>
                <div class="row">
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                    <div class="col-6">
                        <div class="skeleton-line"></div>
                    </div>
                    <div class="col-6 text-end">
                        <div class="skeleton-line short ms-auto"></div>
                    </div>
                </div>
            </div>

            <div class="card p-3 shadow-sm mb-4">
                <div class="skeleton-line-title mb-3"></div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <div class="skeleton-line-header"></div>
                                </th>
                                <th>
                                    <div class="skeleton-line-header"></div>
                                </th>
                                <th>
                                    <div class="skeleton-line-header"></div>
                                </th>
                                <th>
                                    <div class="skeleton-line-header"></div>
                                </th>
                                <th>
                                    <div class="skeleton-line-header"></div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="n in 2" :key="n">
                                <td>
                                    <div class="skeleton-line-header"></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="skeleton-image me-2 rounded-3"></div>
                                        <div>
                                            <div class="skeleton-line fw-bold"></div>
                                            <div class="skeleton-line small mt-1"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="skeleton-line-header"></div>
                                </td>
                                <td>
                                    <div class="skeleton-line-header"></div>
                                </td>
                                <td>
                                    <div class="skeleton-line-header"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <div class="skeleton-line ms-auto"></div>
                    <div class="skeleton-line ms-auto"></div>
                    <div class="skeleton-line-title ms-auto"></div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <div class="skeleton-button"></div>
                <div class="skeleton-button"></div>
                <div class="skeleton-button"></div>
            </div>
        </div>

        <div v-else-if="order">
            <div class="card p-3 shadow-sm mb-4">
                <h5 class="border-bottom pb-2">Thông tin khách hàng</h5>
                <div class="row">
                    <div class="col-6">Họ tên:</div>
                    <div class="col-6 text-end">{{ order.guest_name }}</div>
                    <div class="col-6">Số điện thoại:</div>
                    <div class="col-6 text-end">{{ order.guest_phone }}</div>
                    <div class="col-6">Địa chỉ:</div>
                    <div class="col-6 text-end">{{ order.guest_address }}</div>
                </div>
            </div>

            <div class="card p-3 shadow-sm mb-4">
                <h5 class="border-bottom pb-2">Thông tin</h5>
                <div class="row">
                    <div class="col-6">Ngày đặt hàng:</div>
                    <div class="col-6 text-end">
                        {{ FormatData.formatDateTime(order.order_date) }}
                    </div>
                    <div class="col-6">Phương thức thanh toán:</div>
                    <div class="col-6 text-end">{{ order.payment_method }}</div>
                    <div class="col-6">Trạng thái thanh toán:</div>
                    <div class="col-6 text-end text-success">
                        {{ order.status_payments }}
                    </div>
                    <div class="col-6">Trạng thái đơn:</div>
                    <div class="col-6 text-end" :class="getStatusClass(order.status)">{{ order.status }}</div>
                </div>
            </div>

            <div class="card p-3 shadow-sm mb-2">
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
                            <tr v-for="(item, index) in order.order_items" :key="item.id">
                                <td>{{ index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img :src="item.variant.main_image_url" style="width: 50px; height: auto"
                                            class="me-2 rounded-3" :alt="item.variant.slug" />
                                        <div>
                                            <div class="fw-bold">{{ item.variant.product.name }}</div>
                                            <ul class="mb-0 ps-3 small text-muted">
                                                <li>
                                                    Kích thước:
                                                    {{ getVariantAttributes(item.variant.attributes) }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ FormatData.formatNumber(item.unit_price) }} VNĐ</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ FormatData.formatNumber(item.subtotal) }} VNĐ</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-end mt-3">
                    <p class="mb-1">
                        Tạm tính:
                        {{
                            FormatData.formatNumber(order.total_amount - order.shipping_money)
                        }}
                        VNĐ
                    </p>
                    <p class="mb-1">
                        Phí vận chuyển:
                        {{ FormatData.formatNumber(order.shipping_money) }} VNĐ
                    </p>
                    <h5 class="fw-bold text-danger">
                        Tổng cộng: {{ FormatData.formatNumber(order.total_amount) }} VNĐ
                    </h5>
                </div>
            </div>


            <div class="d-flex flex-wrap gap-2 justify-content-end">
                <button class="btn btn-secondary flex-grow-1 flex-sm-grow-0" @click="$router.back()">
                    Quay lại
                </button>
                <button class="btn btn-info flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal"
                    data-bs-target="#addressModal"
                    v-if="order.status == 'Chờ xác nhận' || order.status == 'Đã xác nhận'">
                    Thay đổi địa chỉ
                </button>
                <button class="btn btn-danger flex-grow-1 flex-sm-grow-0" data-bs-toggle="modal"
                    data-bs-target="#cancelModal"
                    v-if="order.status == 'Chờ xác nhận' || order.status == 'Đã xác nhận'">
                    Hủy đơn
                </button>
            </div>

        </div>

        <div v-else class="text-center py-5">
            <p>Không tìm thấy đơn hàng.</p>
        </div>
    </div>

    <!-- huỷ đơn -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="cancelModalLabel">Bạn có chắc muốn hủy đơn hàng?</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="cancelOrder(orderId)">
                    <div class="modal-body">
                        <p>Vui lòng cho chúng tôi biết lý do bạn hủy đơn hàng:</p>
                        <small class="text-danger" v-if="errors.cancellation_reason">{{
                            errors.cancellation_reason[0]
                        }}</small>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelReason" id="reason1"
                                value="Tôi không muốn mua nữa" v-model="cancellation_reason">
                            <label class="form-check-label" for="reason1" @click="check = false">
                                Tôi không muốn mua nữa
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelReason" id="reason2"
                                value="Đơn hàng giao lâu" v-model="cancellation_reason" @click="check = false">
                            <label class="form-check-label" for="reason2">
                                Đơn hàng giao lâu
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelReason" id="reason3" value="Đổi ý"
                                v-model="cancellation_reason" @click="check = false">
                            <label class="form-check-label" for="reason3">
                                Đổi ý
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelReason" id="reason4" value="Khác"
                                @click="check = !check, cancellation_reason = null">
                            <label class="form-check-label" for="reason4">
                                Khác (vui lòng nhập bên dưới)
                            </label>
                        </div>

                        <textarea v-if="check" class="form-control mt-3" rows="3" placeholder="Nhập lý do khác..."
                            v-model="cancellation_reason">
                    </textarea>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal">Xác nhận hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true"
        v-if="order">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addressModalLabel">Thay đổi địa chỉ nhận hàng</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="updateAddressOrder(orderId)">
                    <div class="modal-body d-flex gap-3">
                        <div class="old w-50 border-end pe-2">
                            <div>
                                <label class="form-label fw-semibold">Địa chỉ hiện tại</label>
                                <textarea class="form-control" readonly disabled>{{ order.guest_address }}</textarea>
                            </div>
                            <div class="mt-4">
                                <label class="form-label fw-semibold">Địa chỉ thay đổi</label>
                                <textarea class="form-control" readonly>{{ newAddress }}</textarea>
                            </div>
                        </div>
                        <div class="new w-50">
                            <label class="form-label fw-semibold">Chọn địa chỉ thay đổi</label>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.provinces">{{
                                    errors.provinces[0]
                                    }}</small>

                                <select v-model="selectedProvince" @change="fetchDistricts" class="form-control"
                                    id="province-select">
                                    <option :value="null">Tỉnh / Thành phố</option>
                                    <option v-for="province in provinces" :key="province.ProvinceID"
                                        :value="province.ProvinceID">
                                        {{ province.ProvinceName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.districts">{{
                                    errors.districts[0]
                                    }}</small>
                                <select v-model="selectedDistrict" @change="fetchWards" :disabled="!selectedProvince"
                                    class="form-control" id="district-select">
                                    <option :value="null">Quận / Huyện</option>
                                    <option v-for="district in districts" :key="district.DistrictID"
                                        :value="district.DistrictID">
                                        {{ district.DistrictName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <small class="text-danger" v-if="errors.wards">{{
                                    errors.wards[0]
                                    }}</small>
                                <select v-model="selectedWard" :disabled="!selectedDistrict" class="form-control"
                                    id="ward-select">
                                    <option :value="null">Phường / Xã</option>
                                    <option v-for="ward in wards" :key="ward.WardCode" :value="ward.WardCode">
                                        {{ ward.WardName }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="street" class="form-label">Số nhà, tên đường <br> <small class="text-danger"
                                        v-if="errors.address">{{
                                            errors.address[0]
                                        }}</small></label>

                                <input type="text" class="form-control" id="street"
                                    placeholder="Ví dụ: Số 12, đường Nguyễn Trãi" v-model="address">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">Lưu thay đổi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style>
/* CSS cho Skeleton Loading */
@keyframes pulse-bg {
    0% {
        background-color: #eee;
    }

    50% {
        background-color: #e0e0e0;
    }

    100% {
        background-color: #eee;
    }
}

.skeleton-line {
    animation: pulse-bg 1.5s infinite;
    height: 1em;
    border-radius: 4px;
    width: 100%;
    margin-bottom: 0.5rem;
}

.skeleton-line.short {
    width: 60%;
}

.skeleton-line-title {
    animation: pulse-bg 1.5s infinite;
    height: 1.5em;
    width: 50%;
    border-radius: 4px;
}

.skeleton-line-header {
    animation: pulse-bg 1.5s infinite;
    height: 1em;
    width: 80%;
    border-radius: 4px;
}

.skeleton-image {
    animation: pulse-bg 1.5s infinite;
    width: 50px;
    height: 50px;
    border-radius: 4px;
}

.skeleton-button {
    animation: pulse-bg 1.5s infinite;
    width: 150px;
    height: 38px;
    border-radius: 0.375rem;
    /* Tương đương với btn-secondary */
}

/* Các style khác */
.table-responsive {
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
}

.table {
    min-width: 600px;
}
</style>
